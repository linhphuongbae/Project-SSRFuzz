#include "php.h"
#include "zend_compile.h"
#include "zend_execute.h"
#include "php_zmark.h"

#if PHP_VERSION_ID >= 70400
#define ZMARK_ASSIGN_CONCAT 500
#else
#define ZMARK_ASSIGN_CONCAT ZEND_ASSIGN_CONCAT
#endif

/* ============================================================================
   Helper functions - được copy từ zend_execute.c
   ============================================================================ */

static int php_zmark_make_real_object(zval *object)
{
    if (UNEXPECTED(Z_TYPE_P(object) != IS_OBJECT)) {
        if (EXPECTED(Z_TYPE_P(object) <= IS_FALSE)) {
            /* nothing to destroy */
        } else if (EXPECTED((Z_TYPE_P(object) == IS_STRING && Z_STRLEN_P(object) == 0))) {
            zval_ptr_dtor_nogc(object);
        } else {
            return 0;
        }
        object_init(object);
        zend_error(E_WARNING, "Creating default object from empty value");
    }
    return 1;
}

static zend_long php_zmark_check_string_offset(zval *dim, int type)
{
    zend_long offset;

try_again:
    if (UNEXPECTED(Z_TYPE_P(dim) != IS_LONG)) {
        switch(Z_TYPE_P(dim)) {
            case IS_STRING:
                if (IS_LONG == is_numeric_string(Z_STRVAL_P(dim), Z_STRLEN_P(dim), NULL, NULL, -1)) {
                    break;
                }
                if (type != BP_VAR_UNSET) {
                    zend_error(E_WARNING, "Illegal string offset '%s'", Z_STRVAL_P(dim));
                }
                break;
            case IS_DOUBLE:
            case IS_NULL:
            case IS_FALSE:
            case IS_TRUE:
                zend_error(E_NOTICE, "String offset cast occurred");
                break;
            case IS_REFERENCE:
                dim = Z_REFVAL_P(dim);
                goto try_again;
            default:
                zend_error(E_WARNING, "Illegal offset type");
                break;
        }

        offset = zval_get_long(dim);
    } else {
        offset = Z_LVAL_P(dim);
    }

    return offset;
}

static zval *php_zmark_fetch_dimension_address_inner(HashTable *ht, const zval *dim, int dim_type, int type)
{
    zval *retval;
    zend_string *offset_key;
    zend_ulong hval;

try_again:
    if (EXPECTED(Z_TYPE_P(dim) == IS_LONG)) {
        hval = Z_LVAL_P(dim);
num_index:
        retval = zend_hash_index_find(ht, hval);
        if (retval == NULL) {
            switch (type) {
                case BP_VAR_R:
                    zend_error(E_NOTICE,"Undefined offset: " ZEND_LONG_FMT, hval);
                case BP_VAR_UNSET:
                case BP_VAR_IS:
                    retval = &EG(uninitialized_zval);
                    break;
                case BP_VAR_RW:
                    zend_error(E_NOTICE,"Undefined offset: " ZEND_LONG_FMT, hval);
                case BP_VAR_W:
                    retval = zend_hash_index_add_new(ht, hval, &EG(uninitialized_zval));
                    break;
            }
        }
    } else if (EXPECTED(Z_TYPE_P(dim) == IS_STRING)) {
        offset_key = Z_STR_P(dim);
        if (dim_type != IS_CONST) {
            if (ZEND_HANDLE_NUMERIC(offset_key, hval)) {
                goto num_index;
            }
        }
str_index:
        retval = zend_hash_find(ht, offset_key);
        if (retval) {
            if (UNEXPECTED(Z_TYPE_P(retval) == IS_INDIRECT)) {
                retval = Z_INDIRECT_P(retval);
                if (UNEXPECTED(Z_TYPE_P(retval) == IS_UNDEF)) {
                    switch (type) {
                        case BP_VAR_R:
                            zend_error(E_NOTICE, "Undefined index: %s", ZSTR_VAL(offset_key));
                        case BP_VAR_UNSET:
                        case BP_VAR_IS:
                            retval = &EG(uninitialized_zval);
                            break;
                        case BP_VAR_RW:
                            zend_error(E_NOTICE,"Undefined index: %s", ZSTR_VAL(offset_key));
                        case BP_VAR_W:
                            ZVAL_NULL(retval);
                            break;
                    }
                }
            }
        } else {
            switch (type) {
                case BP_VAR_R:
                    zend_error(E_NOTICE, "Undefined index: %s", ZSTR_VAL(offset_key));
                case BP_VAR_UNSET:
                case BP_VAR_IS:
                    retval = &EG(uninitialized_zval);
                    break;
                case BP_VAR_RW:
                    zend_error(E_NOTICE,"Undefined index: %s", ZSTR_VAL(offset_key));
                case BP_VAR_W:
                    retval = zend_hash_add_new(ht, offset_key, &EG(uninitialized_zval));
                    break;
            }
        }
    } else {
        switch (Z_TYPE_P(dim)) {
            case IS_NULL:
                offset_key = ZSTR_EMPTY_ALLOC();
                goto str_index;
            case IS_DOUBLE:
                hval = zend_dval_to_lval(Z_DVAL_P(dim));
                goto num_index;
            case IS_RESOURCE:
                zend_error(E_NOTICE, "Resource ID#%d used as offset, casting to integer (%d)", Z_RES_HANDLE_P(dim), Z_RES_HANDLE_P(dim));
                hval = Z_RES_HANDLE_P(dim);
                goto num_index;
            case IS_FALSE:
                hval = 0;
                goto num_index;
            case IS_TRUE:
                hval = 1;
                goto num_index;
            case IS_REFERENCE:
                dim = Z_REFVAL_P(dim);
                goto try_again;
            default:
                zend_error(E_WARNING, "Illegal offset type");
                retval = (type == BP_VAR_W || type == BP_VAR_RW) ?
#if PHP_VERSION_ID < 70100
                &EG(error_zval)
#else
                NULL
#endif
                : &EG(uninitialized_zval);
        }
    }
    return retval;
}

static void php_zmark_fetch_dimension_address(zval *result, zval *container, zval *dim, int dim_type, int type)
{
    zval *retval;

    if (EXPECTED(Z_TYPE_P(container) == IS_ARRAY)) {
try_array:
        SEPARATE_ARRAY(container);
fetch_from_array:
        if (dim == NULL) {
            retval = zend_hash_next_index_insert(Z_ARRVAL_P(container), &EG(uninitialized_zval));
            if (UNEXPECTED(retval == NULL)) {
                zend_error(E_WARNING, "Cannot add element to the array as the next element is already occupied");
#if PHP_VERSION_ID < 70100
                retval = &EG(error_zval);
#else
                ZVAL_ERROR(result);
                return;
#endif
            }
        } else {
            retval = php_zmark_fetch_dimension_address_inner(Z_ARRVAL_P(container), dim, dim_type, type);
        }
        ZVAL_INDIRECT(result, retval);
        return;
    } else if (EXPECTED(Z_TYPE_P(container) == IS_REFERENCE)) {
        container = Z_REFVAL_P(container);
        if (EXPECTED(Z_TYPE_P(container) == IS_ARRAY)) {
            goto try_array;
        }
    }
    if (EXPECTED(Z_TYPE_P(container) == IS_STRING)) {
        if (type != BP_VAR_UNSET && UNEXPECTED(Z_STRLEN_P(container) == 0)) {
            zval_ptr_dtor_nogc(container);
convert_to_array:
            ZVAL_NEW_ARR(container);
            zend_hash_init(Z_ARRVAL_P(container), 8, NULL, ZVAL_PTR_DTOR, 0);
            goto fetch_from_array;
        }

        if (dim == NULL) {
            zend_throw_error(NULL, "[] operator not supported for strings");
#if PHP_VERSION_ID < 70100
            ZVAL_INDIRECT(result, &EG(error_zval));
#else
            ZVAL_ERROR(result);
#endif
        } else {
            php_zmark_check_string_offset(dim, type);
#if PHP_VERSION_ID < 70100
            ZVAL_INDIRECT(result, NULL);
#else
            ZVAL_ERROR(result);
#endif
        }
    } else if (EXPECTED(Z_TYPE_P(container) == IS_OBJECT)) {
        if (!Z_OBJ_HT_P(container)->read_dimension) {
            zend_throw_error(NULL, "Cannot use object as array");
#if PHP_VERSION_ID < 70100
            retval = &EG(error_zval);
#else
            ZVAL_ERROR(result);
#endif
        } else {
            retval = Z_OBJ_HT_P(container)->read_dimension(container, dim, type, result);

            if (UNEXPECTED(retval == &EG(uninitialized_zval))) {
                zend_class_entry *ce = Z_OBJCE_P(container);
                ZVAL_NULL(result);
                zend_error(E_NOTICE, "Indirect modification of overloaded element of %s has no effect", ZSTR_VAL(ce->name));
            } else if (EXPECTED(retval && Z_TYPE_P(retval) != IS_UNDEF)) {
                if (!Z_ISREF_P(retval)) {
                    if (Z_REFCOUNTED_P(retval) && Z_REFCOUNT_P(retval) > 1) {
                        if (Z_TYPE_P(retval) != IS_OBJECT) {
                            Z_DELREF_P(retval);
                            ZVAL_DUP(result, retval);
                            retval = result;
                        } else {
                            ZVAL_COPY_VALUE(result, retval);
                            retval = result;
                        }
                    }
                    if (Z_TYPE_P(retval) != IS_OBJECT) {
                        zend_class_entry *ce = Z_OBJCE_P(container);
                        zend_error(E_NOTICE, "Indirect modification of overloaded element of %s has no effect", ZSTR_VAL(ce->name));
                    }
                } else if (UNEXPECTED(Z_REFCOUNT_P(retval) == 1)) {
                    ZVAL_UNREF(retval);
                }
                if (result != retval) {
                    ZVAL_INDIRECT(result, retval);
                }
            } else {
#if PHP_VERSION_ID < 70100
                ZVAL_INDIRECT(result, &EG(error_zval));
#else
                ZVAL_ERROR(result);
#endif
            }
        }
    } else if (EXPECTED(Z_TYPE_P(container) <= IS_FALSE)) {
        if (type != BP_VAR_UNSET) {
            goto convert_to_array;
        } else {
            ZVAL_NULL(result);
        }
    } else {
        if (type == BP_VAR_UNSET) {
            zend_error(E_WARNING, "Cannot unset offset in a non-array variable");
            ZVAL_NULL(result);
        } else {
            zend_error(E_WARNING, "Cannot use a scalar value as an array");
#if PHP_VERSION_ID < 70100
            ZVAL_INDIRECT(result, &EG(error_zval));
#else
            ZVAL_ERROR(result);
#endif
        }
    }
}

/* ============================================================================
   Get zval from operand
   ============================================================================ */

static zval *php_zmark_get_zval_ptr_tmpvar(zend_execute_data *execute_data, uint32_t var, zend_free_op *should_free)
{
    zval *ret = EX_VAR(var);

    if (should_free) {
        *should_free = ret;
    }
    ZVAL_DEREF(ret);

    return ret;
}

#ifndef CV_DEF_OF
#define CV_DEF_OF(i) (EX(func)->op_array.vars[i])
#endif

static zval *php_zmark_get_zval_ptr_cv(zend_execute_data *execute_data, uint32_t var, int type, int force_ret)
{
    zval *ret = EX_VAR(var);

    if (UNEXPECTED(Z_TYPE_P(ret) == IS_UNDEF)) {
        if (force_ret) {
            switch (type) {
                case BP_VAR_R:
                case BP_VAR_UNSET:
                    zend_error(E_NOTICE, "Undefined variable: %s", ZSTR_VAL(CV_DEF_OF(EX_VAR_TO_NUM(var))));
                case BP_VAR_IS:
                    ret = &EG(uninitialized_zval);
                    break;
                case BP_VAR_RW:
                    zend_error(E_NOTICE, "Undefined variable: %s", ZSTR_VAL(CV_DEF_OF(EX_VAR_TO_NUM(var))));
                case BP_VAR_W:
                    ZVAL_NULL(ret);
                    break;
            }
        } else {
            return NULL;
        }
    } else {
        ZVAL_DEREF(ret);
    }
    return ret;
}

static zval *php_zmark_get_zval_ptr(zend_execute_data *execute_data, int op_type, znode_op op, zend_free_op *should_free, int type, int force_ret)
{
    if (op_type & (IS_TMP_VAR|IS_VAR)) {
        return php_zmark_get_zval_ptr_tmpvar(execute_data, op.var, should_free);
    } else {
        *should_free = NULL;
        if (op_type == IS_CONST) {
            return EX_CONSTANT(op);
        } else if (op_type == IS_CV) {
            return php_zmark_get_zval_ptr_cv(execute_data, op.var, type, force_ret);
        } else {
            return NULL;
        }
    }
}

static zval *php_zmark_get_zval_ptr_ptr_var(zend_execute_data *execute_data, uint32_t var, zend_free_op *should_free)
{
    zval *ret = EX_VAR(var);

    if (EXPECTED(Z_TYPE_P(ret) == IS_INDIRECT)) {
        *should_free = NULL;
        ret = Z_INDIRECT_P(ret);
    } else {
        *should_free = ret;
    }
    return ret;
}

static zval *php_zmark_get_zval_ptr_ptr(zend_execute_data *execute_data, int op_type, znode_op op, zend_free_op *should_free, int type)
{
    if (op_type == IS_CV) {
        *should_free = NULL;
        return php_zmark_get_zval_ptr_cv(execute_data, op.var, type, 1);
    } else if (op_type == IS_VAR) {
        ZEND_ASSERT(op_type == IS_VAR);
        return php_zmark_get_zval_ptr_ptr_var(execute_data, op.var, should_free);
    } else if (op_type == IS_UNUSED) {
        *should_free = NULL;
        return &EX(This);
    } else {
        ZEND_ASSERT(0);
    }
}

/* ============================================================================
   Simple opcode handlers (op1, op2)
   ============================================================================ */

static int php_zmark_op1_handler(zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zend_free_op free_op1;
    zval *op1;
    zval *z_fname;
    zval call_func_ret;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    z_fname = zend_hash_index_find(&ZMARK_G(callbacks), opline->opcode);
    if (!z_fname) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

    op1 = php_zmark_get_zval_ptr(execute_data, opline->op1_type, opline->op1, &free_op1, BP_VAR_R, 0);

    if (op1) {
        if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 1, op1)) {
            zend_error(E_WARNING, "call function error");
        }

        zval_ptr_dtor_nogc(&call_func_ret);
    }

    ZMARK_G(in_callback) = 0;
    return ZEND_USER_OPCODE_DISPATCH;
}

static int php_zmark_op2_handler(zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zend_free_op free_op2;
    zval *op2;
    zval *z_fname;
    zval call_func_ret;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    z_fname = zend_hash_index_find(&ZMARK_G(callbacks), opline->opcode);
    if (!z_fname) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;
    op2 = php_zmark_get_zval_ptr(execute_data, opline->op2_type, opline->op2, &free_op2, BP_VAR_R, 0);

    if (op2) {
        if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 1, op2)) {
            zend_error(E_WARNING, "call function error");
        }
        zval_ptr_dtor_nogc(&call_func_ret);
    }

    ZMARK_G(in_callback) = 0;
    return ZEND_USER_OPCODE_DISPATCH;
}

/* ============================================================================
   Binary assign op helper
   ============================================================================ */

static void php_zmark_assign_op_overloaded_property(zval *object, zval *property, void **cache_slot, zval *value, binary_op_type binary_op, zval *result)
{
    zval *z;
    zval rv, obj;
    zval *zptr;
    zval *z_fname;
    zval call_func_ret, call_func_params[2];

    ZVAL_OBJ(&obj, Z_OBJ_P(object));
    Z_ADDREF(obj);
    if (Z_OBJ_HT(obj)->read_property &&
        (z = Z_OBJ_HT(obj)->read_property(&obj, property, BP_VAR_R, cache_slot, &rv)) != NULL) {
        if (EG(exception)) {
            OBJ_RELEASE(Z_OBJ(obj));
            return;
        }
        if (Z_TYPE_P(z) == IS_OBJECT && Z_OBJ_HT_P(z)->get) {
            zval rv2;
            zval *value = Z_OBJ_HT_P(z)->get(z, &rv2);

            if (z == &rv) {
                zval_ptr_dtor(&rv);
            }
            ZVAL_COPY_VALUE(z, value);
        }
        zptr = z;
        ZVAL_DEREF(z);
        SEPARATE_ZVAL_NOREF(z);

        z_fname = zend_hash_index_find(&ZMARK_G(callbacks), ZMARK_ASSIGN_CONCAT);
        if (z_fname) {
            ZVAL_COPY_VALUE(&call_func_params[0], z);
            ZVAL_COPY_VALUE(&call_func_params[1], value);
            if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 2, call_func_params)) {
                zend_error(E_WARNING, "call function error");
            }
            zval_ptr_dtor_nogc(z);
            ZVAL_COPY_VALUE(z, &call_func_ret);
        } else {
            binary_op(z, z, value);
        }

        Z_OBJ_HT(obj)->write_property(&obj, property, z, cache_slot);
        if (result) {
            ZVAL_COPY(result, z);
        }

        zval_ptr_dtor(zptr);
    } else {
        zend_error(E_WARNING, "Attempt to assign property of non-object");
        if (result) {
            ZVAL_NULL(result);
        }
    }
    OBJ_RELEASE(Z_OBJ(obj));
}

static void php_zmark_binary_assign_op_obj_dim(zval *object, zval *property, zval *value, zval *retval, binary_op_type binary_op)
{
    zval *z;
    zval rv, res;
    zval *z_fname;
    zval call_func_ret, call_func_params[2];

    if (Z_OBJ_HT_P(object)->read_dimension &&
        (z = Z_OBJ_HT_P(object)->read_dimension(object, property, BP_VAR_R, &rv)) != NULL) {

        if (Z_TYPE_P(z) == IS_OBJECT && Z_OBJ_HT_P(z)->get) {
            zval rv2;
            zval *value = Z_OBJ_HT_P(z)->get(z, &rv2);

            if (z == &rv) {
                zval_ptr_dtor(&rv);
            }
            ZVAL_COPY_VALUE(z, value);
        }

        z_fname = zend_hash_index_find(&ZMARK_G(callbacks), ZMARK_ASSIGN_CONCAT);
        if (z_fname) {
            ZVAL_COPY_VALUE(&call_func_params[0], Z_ISREF_P(z) ? Z_REFVAL_P(z) : z);
            ZVAL_COPY_VALUE(&call_func_params[1], value);
            if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 2, call_func_params)) {
                zend_error(E_WARNING, "call function error");
            }

            ZVAL_COPY_VALUE(&res, &call_func_ret);
        } else {
            binary_op(&res, Z_ISREF_P(z) ? Z_REFVAL_P(z) : z, value);
        }

        Z_OBJ_HT_P(object)->write_dimension(object, property, &res);
        if (z == &rv) {
            zval_ptr_dtor(&rv);
        }
        if (retval) {
            ZVAL_COPY(retval, &res);
        }

        zval_ptr_dtor(&res);
    } else {
        zend_error(E_WARNING, "Attempt to assign property of non-object");
        if (retval) {
            ZVAL_NULL(retval);
        }
    }
}

static int php_zmark_binary_assign_op_helper(binary_op_type binary_op, zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zval *var_ptr, *value;
    zend_free_op free_op1, free_op2;
    zval *z_fname;
    zval call_func_ret, call_func_params[2];

    value = php_zmark_get_zval_ptr(execute_data, opline->op2_type, opline->op2, &free_op2, BP_VAR_R, 1);
    var_ptr = php_zmark_get_zval_ptr_ptr(execute_data, opline->op1_type, opline->op1, &free_op1, BP_VAR_RW);

    if (opline->op1_type == IS_VAR) {
        if (var_ptr == NULL || Z_TYPE_P(var_ptr) == IS_ERROR) {
            return ZEND_USER_OPCODE_DISPATCH;
        }
    }

    z_fname = zend_hash_index_find(&ZMARK_G(callbacks), ZMARK_ASSIGN_CONCAT);
    if (z_fname) {
        ZVAL_COPY_VALUE(&call_func_params[0], var_ptr);
        ZVAL_COPY_VALUE(&call_func_params[1], value);
        if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 2, call_func_params)) {
            zend_error(E_WARNING, "call function error");
        }

        SEPARATE_ZVAL_NOREF(var_ptr);
        zval_ptr_dtor_nogc(var_ptr);
        ZVAL_COPY_VALUE(var_ptr, &call_func_ret);
    } else {
        SEPARATE_ZVAL_NOREF(var_ptr);
        binary_op(var_ptr, var_ptr, value);
    }

    if (ZEND_RESULT_USED(opline)) {
        ZVAL_COPY(EX_VAR(opline->result.var), var_ptr);
    }

    if ((opline->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op1) {
        zval_ptr_dtor_nogc(free_op1);
    }

    if ((opline->op2_type & (IS_VAR|IS_TMP_VAR)) && free_op2) {
        zval_ptr_dtor_nogc(free_op2);
    }

    execute_data->opline++;

    return ZEND_USER_OPCODE_CONTINUE;
}

static int php_zmark_binary_assign_op_obj_helper(binary_op_type binary_op, zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zval *object, *property, *var_ptr, *value;
    zend_free_op free_op1, free_op2, free_op_data;
    zval *z_fname;
    zval call_func_ret, call_func_params[2];

    object = php_zmark_get_zval_ptr_ptr(execute_data, opline->op1_type, opline->op1, &free_op1, BP_VAR_RW);
    if (opline->op1_type == IS_UNUSED && Z_OBJ_P(object) == NULL) {
        return ZEND_USER_OPCODE_DISPATCH;
    }
    if (opline->op1_type == IS_VAR && object == NULL) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    property = php_zmark_get_zval_ptr(execute_data, opline->op2_type, opline->op2, &free_op2, BP_VAR_R, 1);

    do {
        if (opline->op1_type == IS_UNUSED || Z_TYPE_P(object) != IS_OBJECT) {
            if (!php_zmark_make_real_object(object)) {
                zend_error(E_WARNING, "Attempt to assign property of non-object");
                if (ZEND_RESULT_USED(opline)) {
                    ZVAL_NULL(EX_VAR(opline->result.var));
                }
                break;
            }
        }

        value = php_zmark_get_zval_ptr(execute_data, (opline + 1)->op1_type, (opline + 1)->op1, &free_op_data, BP_VAR_R, 1);

        if (Z_OBJ_HT_P(object)->get_property_ptr_ptr
            && (var_ptr = Z_OBJ_HT_P(object)->get_property_ptr_ptr(object, property, BP_VAR_RW, NULL)) != NULL) {
            ZVAL_DEREF(var_ptr);
            SEPARATE_ZVAL_NOREF(var_ptr);

            z_fname = zend_hash_index_find(&ZMARK_G(callbacks), ZMARK_ASSIGN_CONCAT);
            if (z_fname) {
                ZVAL_COPY_VALUE(&call_func_params[0], var_ptr);
                ZVAL_COPY_VALUE(&call_func_params[1], value);
                if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 2, call_func_params)) {
                    zend_error(E_WARNING, "call function error");
                }

                zval_ptr_dtor_nogc(var_ptr);
                ZVAL_COPY_VALUE(var_ptr, &call_func_ret);
            } else {
                binary_op(var_ptr, var_ptr, value);
            }

            if (ZEND_RESULT_USED(opline)) {
                ZVAL_COPY(EX_VAR(opline->result.var), var_ptr);
            }
        } else {
            php_zmark_assign_op_overloaded_property(object, property, NULL, value, binary_op, EX_VAR(opline->result.var));
            if (!ZEND_RESULT_USED(opline)) {
                zval_ptr_dtor_nogc(EX_VAR(opline->result.var));
            }
        }
    } while (0);

    if ((opline->op2_type & (IS_VAR|IS_TMP_VAR)) && free_op2) {
        zval_ptr_dtor_nogc(free_op2);
    }
    if (((opline + 1)->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op_data)   {
        zval_ptr_dtor_nogc(free_op_data);
    }
    if ((opline->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op1) {
        zval_ptr_dtor_nogc(free_op1);
    }
    execute_data->opline += 2;

    return ZEND_USER_OPCODE_CONTINUE;
}

static int php_zmark_binary_assign_op_dim_helper(binary_op_type binary_op, zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zval *container, *dim, *var_ptr, *value, rv;
    zend_free_op free_op1, free_op2, free_op_data;
    zval *z_fname;
    zval call_func_ret, call_func_params[2];

    container = php_zmark_get_zval_ptr_ptr(execute_data, opline->op1_type, opline->op1, &free_op1, BP_VAR_RW);
    if (opline->op1_type == IS_UNUSED && Z_OBJ_P(container) == NULL) {
        return ZEND_USER_OPCODE_DISPATCH;
    }
    if (opline->op1_type == IS_VAR && container == NULL) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    dim = php_zmark_get_zval_ptr(execute_data, opline->op2_type, opline->op2, &free_op2, BP_VAR_R, 1);

    do {
        if (opline->op1_type == IS_UNUSED || Z_TYPE_P(container) == IS_OBJECT) {
            value = php_zmark_get_zval_ptr(execute_data, (opline + 1)->op1_type, (opline + 1)->op1, &free_op_data, BP_VAR_R, 1);
            php_zmark_binary_assign_op_obj_dim(container, dim, value, EX_VAR(opline->result.var), binary_op);

            if (!ZEND_RESULT_USED(opline)) {
                zval_ptr_dtor_nogc(EX_VAR(opline->result.var));
            }
            break;
        }

        php_zmark_fetch_dimension_address(&rv, container, dim, opline->op2_type, BP_VAR_RW);
        value = php_zmark_get_zval_ptr(execute_data, (opline + 1)->op1_type, (opline + 1)->op1, &free_op_data, BP_VAR_R, 1);

        if (Z_TYPE(rv) != IS_INDIRECT) {
            var_ptr = NULL;
        } else {
            var_ptr = Z_INDIRECT(rv);
        }

        if (var_ptr == NULL) {
            zend_throw_error(NULL, "Cannot use assign-op operators with overloaded objects nor string offsets");
            if ((opline->op2_type & (IS_VAR|IS_TMP_VAR)) && free_op2) {
                zval_ptr_dtor_nogc(free_op2);
            }
            if (((opline + 1)->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op_data)   {
                zval_ptr_dtor_nogc(free_op_data);
            }
            if ((opline->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op1) {
                zval_ptr_dtor_nogc(free_op1);
            }
            execute_data->opline += 2;
            return ZEND_USER_OPCODE_CONTINUE;
        }

        if (Z_TYPE_P(var_ptr) == IS_ERROR) {
            if (ZEND_RESULT_USED(opline)) {
                ZVAL_NULL(EX_VAR(opline->result.var));
            }
        } else {
            ZVAL_DEREF(var_ptr);
            SEPARATE_ZVAL_NOREF(var_ptr);

            z_fname = zend_hash_index_find(&ZMARK_G(callbacks), ZMARK_ASSIGN_CONCAT);
            if (z_fname) {
                ZVAL_COPY_VALUE(&call_func_params[0], var_ptr);
                ZVAL_COPY_VALUE(&call_func_params[1], value);

                if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 2, call_func_params)) {
                    zend_error(E_WARNING, "call function error");
                }
                zval_ptr_dtor_nogc(var_ptr);
                ZVAL_COPY_VALUE(var_ptr, &call_func_ret);
            } else {
                binary_op(var_ptr, var_ptr, value);
            }
            if (ZEND_RESULT_USED(opline)) {
                ZVAL_COPY(EX_VAR(opline->result.var), var_ptr);
            }
        }
    } while (0);

    if ((opline->op2_type & (IS_VAR|IS_TMP_VAR)) && free_op2) {
        zval_ptr_dtor_nogc(free_op2);
    }
    if (((opline + 1)->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op_data)   {
        zval_ptr_dtor_nogc(free_op_data);
    }
    if ((opline->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op1) {
        zval_ptr_dtor_nogc(free_op1);
    }
    execute_data->opline += 2;

    return ZEND_USER_OPCODE_CONTINUE;
}

/* ============================================================================
   Opcode handlers
   ============================================================================ */

static int php_zmark_concat_handler(zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zval *op1, *op2, *result;
    zend_free_op free_op1, free_op2;
    zval *z_fname;
    zval call_func_ret, call_func_params[2];

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    z_fname = zend_hash_index_find(&ZMARK_G(callbacks), opline->opcode);
    if (!z_fname) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

    op1 = php_zmark_get_zval_ptr(execute_data, opline->op1_type, opline->op1, &free_op1, BP_VAR_R, 1);
    op2 = php_zmark_get_zval_ptr(execute_data, opline->op2_type, opline->op2, &free_op2, BP_VAR_R, 1);

    result = EX_VAR(opline->result.var);

    if (op1 && op2) {
        ZVAL_COPY_VALUE(&call_func_params[0], op1);
        ZVAL_COPY_VALUE(&call_func_params[1], op2);
        if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 2, call_func_params)) {
            zend_error(E_WARNING, "call function error");
        }

        ZVAL_COPY_VALUE(result, &call_func_ret);
    }

    if ((opline->op1_type & (IS_VAR|IS_TMP_VAR)) && free_op1) {
        zval_ptr_dtor_nogc(free_op1);
    }

    if ((opline->op2_type & (IS_VAR|IS_TMP_VAR)) && free_op2) {
        zval_ptr_dtor_nogc(free_op2);
    }

    execute_data->opline++;
    ZMARK_G(in_callback) = 0;

    return ZEND_USER_OPCODE_CONTINUE;
}

static int php_zmark_assign_concat_handler(zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    int result = 0;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

    if (EXPECTED(opline->extended_value == 0)) {
        result = php_zmark_binary_assign_op_helper(concat_function, execute_data);
    } else if (EXPECTED(opline->extended_value == ZEND_ASSIGN_DIM)) {
        result = php_zmark_binary_assign_op_dim_helper(concat_function, execute_data);
    } else {
        result = php_zmark_binary_assign_op_obj_helper(concat_function, execute_data);
    }

    ZMARK_G(in_callback) = 0;
    return result;
}

#if PHP_VERSION_ID >= 70400
static int php_zmark_assign_op_handler(zend_execute_data *execute_data)
{
	const zend_op *opline = execute_data->opline;
    int result = 0;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

	if (UNEXPECTED(opline->extended_value == ZEND_CONCAT)) {
		result = php_zmark_binary_assign_op_helper(concat_function, execute_data);
	}

	ZMARK_G(in_callback) = 0;
    return result;
}

static int php_zmark_assign_dim_op_handler(zend_execute_data *execute_data)
{
	const zend_op *opline = execute_data->opline;
    int result = 0;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

	if (UNEXPECTED(opline->extended_value == ZEND_CONCAT)) {
		result = php_zmark_binary_assign_op_dim_helper(concat_function, execute_data);
	}

	ZMARK_G(in_callback) = 0;
    return result;
}

static int php_zmark_assign_obj_op_handler(zend_execute_data *execute_data)
{
	const zend_op *opline = execute_data->opline;
    int result = 0;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

	if (UNEXPECTED(opline->extended_value == ZEND_CONCAT)) {
		result = php_zmark_binary_assign_op_obj_helper(concat_function, execute_data);
	}

	ZMARK_G(in_callback) = 0;
    return result;
}
#endif

static int php_zmark_rope_end_handler(zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zval *op2, *result;
    zend_free_op free_op2;
    zend_string **rope;
    int i;
    zval *z_fname;
    zval call_func_ret, call_func_params[1];
    zval z_rope;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    z_fname = zend_hash_index_find(&ZMARK_G(callbacks), opline->opcode);
    if (!z_fname) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

    rope = (zend_string **)EX_VAR(opline->op1.var);
    op2 = php_zmark_get_zval_ptr(execute_data, opline->op2_type, opline->op2, &free_op2, BP_VAR_R, 1);
    result = EX_VAR(opline->result.var);

    ZVAL_NEW_ARR(&z_rope);
    zend_hash_init(Z_ARRVAL(z_rope), opline->extended_value+1, NULL, ZVAL_PTR_DTOR, 0);

    rope[opline->extended_value] = zval_get_string(op2);

    zval tmp;
    for (i=0; i<=opline->extended_value; i++) {
        ZVAL_STR(&tmp, rope[i]);
        zend_hash_next_index_insert(Z_ARRVAL(z_rope), &tmp);
    }

    ZVAL_COPY_VALUE(&call_func_params[0], &z_rope);

    if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 1, call_func_params)) {
        zend_error(E_WARNING, "call function error");
    }

    if (Z_REFCOUNT(z_rope) <= 1)
        zend_array_destroy(Z_ARRVAL(z_rope));
    else
        Z_DELREF(z_rope);

    ZVAL_COPY_VALUE(result, &call_func_ret);
    execute_data->opline++;
    ZMARK_G(in_callback) = 0;

    return ZEND_USER_OPCODE_CONTINUE;
}

static int php_zmark_fcall_handler(zend_execute_data *execute_data)
{
    const zend_op *opline = execute_data->opline;
    zend_execute_data *call = execute_data->call;
    zend_function *fbc = call->func;
    zval *z_fname;
    zval call_func_ret, call_func_params[2];
    zval z_call, z_params, tmp;
    zend_string *fname, *cname;
    uint32_t i;

    if (ZMARK_G(in_callback)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    z_fname = zend_hash_index_find(&ZMARK_G(callbacks), opline->opcode);
    if (!z_fname) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    if (!fbc->common.function_name) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    ZMARK_G(in_callback) = 1;

    uint32_t arg_count = ZEND_CALL_NUM_ARGS(call);

    ZVAL_NEW_ARR(&z_params);
    zend_hash_init(Z_ARRVAL(z_params), arg_count, NULL, ZVAL_PTR_DTOR, 0);

    for (i=0; i<arg_count; i++) {
        zval *p = ZEND_CALL_ARG(call, i + 1);
        if (Z_REFCOUNTED_P(p)) Z_ADDREF_P(p);
        zend_hash_next_index_insert(Z_ARRVAL(z_params), p);
    }

    ZVAL_COPY_VALUE(&call_func_params[1], &z_params);

    if (fbc->common.scope == NULL) {
        fname = fbc->common.function_name;
        ZVAL_STR_COPY(&z_call, fname);
        ZVAL_COPY_VALUE(&call_func_params[0], &z_call);
    } else {
        cname = fbc->common.scope->name;
        fname = fbc->common.function_name;

        ZVAL_NEW_ARR(&z_call);
        zend_hash_init(Z_ARRVAL(z_call), 2, NULL, ZVAL_PTR_DTOR, 0);

        ZVAL_STR_COPY(&tmp, cname);
        zend_hash_next_index_insert(Z_ARRVAL(z_call), &tmp);
        ZVAL_STR_COPY(&tmp, fname);
        zend_hash_next_index_insert(Z_ARRVAL(z_call), &tmp);

        ZVAL_COPY_VALUE(&call_func_params[0], &z_call);
    }

    if (SUCCESS != call_user_function(EG(function_table), NULL, z_fname, &call_func_ret, 2, call_func_params)) {
        zend_error(E_WARNING, "call function error");
    }

    if (IS_ARRAY == Z_TYPE_P(&z_call)) {
        if (Z_REFCOUNT(z_call) <= 1)
            zend_array_destroy(Z_ARRVAL(z_call));
        else
            Z_DELREF(z_call);
    } else {
        zend_string_release(Z_STR(z_call));
    }

    if (Z_REFCOUNT(z_params) <= 1)
        zend_array_destroy(Z_ARRVAL(z_params));
    else
        Z_DELREF(z_params);

    ZMARK_G(in_callback) = 0;

    return ZEND_USER_OPCODE_DISPATCH;
}

static int php_zmark_init_fcall(zend_execute_data *execute_data)
{
    zend_op *opline = (zend_op *)execute_data->opline;

    zval *fname = EX_CONSTANT(opline->op2);
    zval *func;
    zend_function *fbc;

    func = zend_hash_find(EG(function_table), Z_STR_P(fname));
    if (UNEXPECTED(func == NULL)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }
    fbc = Z_FUNC_P(func);

    if (UNEXPECTED(fbc->type != ZEND_USER_FUNCTION)) {
        return ZEND_USER_OPCODE_DISPATCH;
    }

    opline->op1.num = zend_vm_calc_used_stack(opline->extended_value, fbc);
    return ZEND_USER_OPCODE_DISPATCH;
}

/* ============================================================================
   Register opcode handlers
   ============================================================================ */

void php_zmark_register_opcode_handlers()
{
    zend_set_user_opcode_handler(ZEND_ECHO, php_zmark_op1_handler);
    zend_set_user_opcode_handler(ZEND_EXIT, php_zmark_op1_handler);
    zend_set_user_opcode_handler(ZEND_INIT_METHOD_CALL, php_zmark_op2_handler);
    zend_set_user_opcode_handler(ZEND_INIT_USER_CALL, php_zmark_op2_handler);
    zend_set_user_opcode_handler(ZEND_INIT_DYNAMIC_CALL, php_zmark_op2_handler);
    zend_set_user_opcode_handler(ZEND_INCLUDE_OR_EVAL, php_zmark_op1_handler);
    zend_set_user_opcode_handler(ZEND_CONCAT, php_zmark_concat_handler);
    zend_set_user_opcode_handler(ZEND_FAST_CONCAT, php_zmark_concat_handler);
    zend_set_user_opcode_handler(ZEND_ROPE_END, php_zmark_rope_end_handler);
    zend_set_user_opcode_handler(ZEND_DO_FCALL, php_zmark_fcall_handler);
    zend_set_user_opcode_handler(ZEND_DO_ICALL, php_zmark_fcall_handler);
    zend_set_user_opcode_handler(ZEND_DO_UCALL, php_zmark_fcall_handler);
    zend_set_user_opcode_handler(ZEND_DO_FCALL_BY_NAME, php_zmark_fcall_handler);

#if PHP_VERSION_ID >= 70400
    zend_set_user_opcode_handler(ZEND_ASSIGN_OP, php_zmark_assign_op_handler);
    zend_set_user_opcode_handler(ZEND_ASSIGN_OBJ_OP, php_zmark_assign_obj_op_handler);
    zend_set_user_opcode_handler(ZEND_ASSIGN_DIM_OP, php_zmark_assign_dim_op_handler);
#else
    zend_set_user_opcode_handler(ZEND_ASSIGN_CONCAT, php_zmark_assign_concat_handler);
#endif

    if (ZMARK_G(enable_rename))
        zend_set_user_opcode_handler(ZEND_INIT_FCALL, php_zmark_init_fcall);
}

/* PHP function: bool zregister_opcode_callback(long $opcode, callable $cb); */
PHP_FUNCTION(zregister_opcode_callback)
{
    zend_fcall_info callable;
    zend_fcall_info_cache call_cache;
    zend_ulong opcode;

    if (!ZMARK_G(enable)) {
        RETURN_FALSE;
    }

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "lf", &opcode, &callable, &call_cache) == FAILURE) {
        RETURN_FALSE;
    }

    zend_string_addref(Z_STR(callable.function_name));
    if (!zend_hash_index_update(&ZMARK_G(callbacks), opcode, &callable.function_name)) {
        RETURN_FALSE;
    }

    RETURN_TRUE;
}
