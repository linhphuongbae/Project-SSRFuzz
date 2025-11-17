#include "php.h"
#include "php_zmark.h"

#define ZMARK_IS_FUNCTION 0
#define ZMARK_IS_CLASS 1

/* Rehash và rename entry trong hash table */
static zend_always_inline Bucket *rename_hash_key(HashTable *ht, zend_string *orig_name, zend_string *new_name, int type)
{
    zend_ulong h;
    uint32_t nIndex;
    uint32_t idx;
    Bucket *p = NULL, *arData, *prev = NULL;
    zend_bool found = 0;

    orig_name = zend_string_tolower(orig_name);
    new_name = zend_string_tolower(new_name);

    if (zend_hash_exists(ht, new_name)) {
        zend_string_release(orig_name);
        zend_string_release(new_name);
        zend_error(E_ERROR, "function/class '%s' already exists", ZSTR_VAL(new_name));
        return NULL;
    }

    h = zend_string_hash_val(orig_name);
    arData = ht->arData;
    nIndex = h | ht->nTableMask;
    idx = HT_HASH_EX(arData, nIndex);
    
    while (EXPECTED(idx != HT_INVALID_IDX)) {
        prev = p;
        p = HT_HASH_TO_BUCKET_EX(arData, idx);
        if (EXPECTED(p->key == orig_name)) {
            found = 1;
            break;
        } else if (EXPECTED(p->h == h) &&
                   EXPECTED(p->key) &&
                   EXPECTED(ZSTR_LEN(p->key) == ZSTR_LEN(orig_name)) &&
                   EXPECTED(memcmp(ZSTR_VAL(p->key), ZSTR_VAL(orig_name), ZSTR_LEN(orig_name)) == 0)) {
            found = 1;
            break;
        }
        idx = Z_NEXT(p->val);
    }

    if (!found) {
        zend_string_release(orig_name);
        zend_string_release(new_name);
        zend_error(E_ERROR, "function/class '%s' does not exists", ZSTR_VAL(orig_name));
        return NULL;
    }

    /* Rehash: xóa entry cũ khỏi hash chain */
    if (!prev && Z_NEXT(p->val) == HT_INVALID_IDX) {
        HT_HASH(ht, nIndex) = HT_INVALID_IDX;
    } else if (prev && Z_NEXT(p->val) != HT_INVALID_IDX) {
        Z_NEXT(prev->val) = Z_NEXT(p->val);
    } else if (prev && Z_NEXT(p->val) == HT_INVALID_IDX) {
        Z_NEXT(prev->val) = HT_INVALID_IDX;
    } else if (!prev && Z_NEXT(p->val) != HT_INVALID_IDX) {
        HT_HASH(ht, nIndex) = Z_NEXT(p->val);
    }

    /* Cập nhật key */
    zend_string_release(p->key);
    p->key = zend_string_init_interned(ZSTR_VAL(new_name), ZSTR_LEN(new_name), 1);
    p->h = h = zend_string_hash_val(p->key);
    nIndex = h | ht->nTableMask;

    /* Cập nhật function_name hoặc class name */
    if (type == ZMARK_IS_FUNCTION) {
        zend_string_release(p->val.value.func->common.function_name);
        zend_string_addref(p->key);
        p->val.value.func->common.function_name = p->key;
    }

    /* Đưa entry vào hash chain mới */
    if (HT_HASH(ht, nIndex) != HT_INVALID_IDX)
        Z_NEXT(p->val) = HT_HASH(ht, nIndex);

    HT_HASH(ht, nIndex) = idx;

    zend_string_release(orig_name);
    zend_string_release(new_name);

    return p;
}

/* Helper: tạo zend_string từ char* và gọi rename_hash_key */
static zend_always_inline Bucket *rename_hash_str_key(HashTable *ht, const char *orig_name, const char *new_name, int type)
{
    zend_string *str_orig_name, *str_new_name;
    Bucket *p;

    str_orig_name = zend_string_init(orig_name, strlen(orig_name), 0);
    str_new_name = zend_string_init(new_name, strlen(new_name), 0);

    p = rename_hash_key(ht, str_orig_name, str_new_name, type);

    zend_string_release(str_orig_name);
    zend_string_release(str_new_name);

    return p;
}

/* Clear runtime cache của function */
static void clear_function_run_time_cache(zend_function *fbc)
{
    void **run_time_cache;
    
#if PHP_VERSION_ID >= 70400
    if (fbc->type != ZEND_USER_FUNCTION ||
        fbc->op_array.cache_size == 0 || 
        (RUN_TIME_CACHE(&fbc->op_array) == NULL)) {
        return;
    }

    run_time_cache = zend_arena_alloc(&CG(arena), fbc->op_array.cache_size);
    memset(run_time_cache, 0, fbc->op_array.cache_size);
    ZEND_MAP_PTR_SET(fbc->op_array.run_time_cache, run_time_cache);
#else
    if (fbc->type != ZEND_USER_FUNCTION ||
        fbc->op_array.cache_size == 0 || 
        fbc->op_array.run_time_cache == NULL) {
        return;
    }

    memset(fbc->op_array.run_time_cache, 0, fbc->op_array.cache_size);
#endif
}

/* Clear tất cả runtime cache */
static void clear_run_time_cache()
{
    zend_function *fbc;
    zend_class_entry *ce;

    ZEND_HASH_FOREACH_PTR(EG(function_table), fbc) {
        clear_function_run_time_cache(fbc);
    } ZEND_HASH_FOREACH_END();

    ZEND_HASH_FOREACH_PTR(EG(class_table), ce) {
        ZEND_HASH_FOREACH_PTR(&(ce->function_table), fbc) {
            clear_function_run_time_cache(fbc);
        } ZEND_HASH_FOREACH_END();
    } ZEND_HASH_FOREACH_END();
}

/* Parse INI value "old:new,old2:new2" và rename */
static void rename_from_ini_value(HashTable *ht, const char *ini_value, int type)
{
    char *e, *orig_name = NULL, *new_name = NULL;

    if (!ini_value) {
        return;
    }

    e = strdup(ini_value);
    if (e == NULL) {
        return;
    }

    while (*e) {
        switch (*e) {
            case ' ':
            case '\r':
            case '\n':
            case '\t':
            case ',':
                if (orig_name && new_name) {
                    *e = '\0';
                    rename_hash_str_key(ht, orig_name, new_name, type);
                }
                orig_name = NULL;
                new_name = NULL;
                break;
            case ':':
                if (orig_name) {
                    *e = '\0';
                }
                if (!new_name) {
                    new_name = e + 1;
                }
                break;
            default:
                if (!orig_name) {
                    orig_name = e;
                }
                break;
        }
        e++;
    }
    if (orig_name && new_name) {
        rename_hash_str_key(ht, orig_name, new_name, type);
    }
}

/* PHP function: bool zrename_function(string $old, string $new); */
PHP_FUNCTION(zrename_function)
{
    zend_string *orig_fname, *new_fname, *lc_orig_fname;
    zval *z_func;

    if (!ZMARK_G(enable) || !ZMARK_G(enable_rename)) {
        RETURN_FALSE;
    }

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "SS", &orig_fname, &new_fname) == FAILURE) {
        return;
    }

    lc_orig_fname = zend_string_tolower(orig_fname);
    z_func = zend_hash_find(EG(function_table), lc_orig_fname);
    zend_string_release(lc_orig_fname);
    
    if (!z_func) {
        zend_error(E_ERROR, "function '%s' does not exists", ZSTR_VAL(orig_fname));
        return;
    }

    if (Z_FUNC_P(z_func)->type != ZEND_USER_FUNCTION) {
        zend_error(E_ERROR, "zrename_function can only rename user function");
        return;
    }

    Bucket *p = rename_hash_key(EG(function_table), orig_fname, new_fname, ZMARK_IS_FUNCTION);
    if (!p) {
        zend_error(E_ERROR, "rename function '%s' to '%s' failed", ZSTR_VAL(orig_fname), ZSTR_VAL(new_fname));
        RETURN_FALSE;
    }

    zend_string_release(Z_FUNC(p->val)->common.function_name);
    Z_FUNC(p->val)->common.function_name = zend_string_init_interned(ZSTR_VAL(new_fname), ZSTR_LEN(new_fname), 1);

    clear_run_time_cache();

    RETURN_TRUE;
}

/* PHP function: bool zrename_class(string $old, string $new); */
PHP_FUNCTION(zrename_class)
{
    zend_string *orig_cname, *new_cname, *lc_orig_cname;
    zval *z_class;

    if (!ZMARK_G(enable) || !ZMARK_G(enable_rename)) {
        RETURN_FALSE;
    }

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "SS", &orig_cname, &new_cname) == FAILURE) {
        return;
    }

    lc_orig_cname = zend_string_tolower(orig_cname);
    z_class = zend_hash_find(EG(class_table), lc_orig_cname);
    zend_string_release(lc_orig_cname);
    
    if (!z_class) {
        zend_error(E_ERROR, "class '%s' does not exists", ZSTR_VAL(orig_cname));
        return;
    }

    if (Z_CE_P(z_class)->type == ZEND_INTERNAL_CLASS) {
        zend_error(E_ERROR, "zrename_class can only rename user class");
        return;
    }

    Bucket *p = rename_hash_key(EG(class_table), orig_cname, new_cname, ZMARK_IS_CLASS);
    if (!p) {
        zend_error(E_ERROR, "rename class '%s' to '%s' failed", ZSTR_VAL(orig_cname), ZSTR_VAL(new_cname));
        RETURN_FALSE;
    }

    zend_string_release(Z_CE(p->val)->name);
    Z_CE(p->val)->name = zend_string_init_interned(ZSTR_VAL(new_cname), ZSTR_LEN(new_cname), 1);

    RETURN_TRUE;
}
