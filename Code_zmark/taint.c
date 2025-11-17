#include "php.h"
#include "php_zmark.h"

/* Macro để đánh dấu/kiểm tra taint flag */
#define ZMARK_FLAG (1UL << 0)
#define ZMARK_FLAG_SET(str) do { GC_FLAGS(str) |= ZMARK_FLAG; } while(0)
#define ZMARK_FLAG_CLEAR(str) do { GC_FLAGS(str) &= ~ZMARK_FLAG; } while(0)
#define ZMARK_FLAG_CHECK(str) (GC_FLAGS(str) & ZMARK_FLAG)

/* Đánh dấu string bị tainted */
static zend_always_inline int zmark_zstr(zval *z_str)
{
    if (!ZMARK_FLAG_CHECK(Z_STR_P(z_str))) {
        zend_string *str = zend_string_init(Z_STRVAL_P(z_str), Z_STRLEN_P(z_str), 0);
        ZSTR_LEN(str) = Z_STRLEN_P(z_str);
        zend_string_release(Z_STR_P(z_str));
        ZMARK_FLAG_SET(str);
        ZVAL_STR(z_str, str);
    }

    return SUCCESS;
}

/* PHP function: bool zmark(string &$str); */
PHP_FUNCTION(zmark)
{
    zval *z_str;

    if (!ZMARK_G(enable)) {
        RETURN_FALSE;
    }

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "z", &z_str) == FAILURE) {
        return;
    }

    ZVAL_DEREF(z_str);
    if (IS_STRING != Z_TYPE_P(z_str) || Z_STRLEN_P(z_str) == 0) {
        RETURN_FALSE;
    }

    if (zmark_zstr(z_str) == FAILURE) {
        RETURN_FALSE;
    }

    RETURN_TRUE;
}

/* PHP function: bool zclear(string &$str); */
PHP_FUNCTION(zclear)
{
    zval *z_str;

    if (!ZMARK_G(enable)) {
        RETURN_FALSE;
    }

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "z", &z_str) == FAILURE) {
        return;
    }

    ZVAL_DEREF(z_str);
    if (IS_STRING != Z_TYPE_P(z_str) || Z_STRLEN_P(z_str) == 0 || !ZMARK_FLAG_CHECK(Z_STR_P(z_str))) {
        RETURN_FALSE;
    }

    ZMARK_FLAG_CLEAR(Z_STR_P(z_str));
    RETURN_TRUE;
}

/* PHP function: bool zcheck(string &$str); */
PHP_FUNCTION(zcheck)
{
    zval *z_str;

    if (!ZMARK_G(enable)) {
        RETURN_FALSE;
    }

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "z", &z_str) == FAILURE) {
        return;
    }

    ZVAL_DEREF(z_str);
    if (IS_STRING != Z_TYPE_P(z_str) || Z_STRLEN_P(z_str) == 0 || !ZMARK_FLAG_CHECK(Z_STR_P(z_str))) {
        RETURN_FALSE;
    }

    RETURN_TRUE;
}
