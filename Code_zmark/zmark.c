/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2017 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:  zero                                                        |
  +----------------------------------------------------------------------+
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "zend_compile.h"
#include "zend_types.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_zmark.h"

ZEND_DECLARE_MODULE_GLOBALS(zmark)

/* Forward declaration */
extern void php_zmark_register_opcode_handlers();
extern void rename_from_ini_value(HashTable *ht, const char *ini_value, int type);

/* INI entries */
PHP_INI_BEGIN()
    STD_PHP_INI_BOOLEAN("zmark.enable", "0", PHP_INI_SYSTEM, OnUpdateBool, enable, zend_zmark_globals, zmark_globals)
    STD_PHP_INI_BOOLEAN("zmark.enable_rename", "0", PHP_INI_SYSTEM, OnUpdateBool, enable_rename, zend_zmark_globals, zmark_globals)
    STD_PHP_INI_ENTRY("zmark.rename_functions", "", PHP_INI_SYSTEM, OnUpdateString, rename_functions, zend_zmark_globals, zmark_globals)
    STD_PHP_INI_ENTRY("zmark.rename_classes", "", PHP_INI_SYSTEM, OnUpdateString, rename_classes, zend_zmark_globals, zmark_globals)
PHP_INI_END()

/* Initialize globals */
static void php_zmark_init_globals(zend_zmark_globals *g)
{
    g->enable = 0;
    g->enable_rename = 0;
    g->in_callback = 0;
}

/* Taint helper */
PHP_FUNCTION(zid)
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "z", &zv) == FAILURE) {
        return;
    }

    RETURN_LONG((zend_long)zv->value.ptr);
}

/* Rename helper - forward declare implementation from rename.c */
extern PHP_FUNCTION(zrename_function);
extern PHP_FUNCTION(zrename_class);

/* Opcode handler - forward declare implementation from opcode.c */
extern PHP_FUNCTION(zregister_opcode_callback);

/* Functions table */
const zend_function_entry zmark_functions[] = {
    PHP_FE(zid, NULL)
    PHP_FE(zmark, NULL)
    PHP_FE(zclear, NULL)
    PHP_FE(zcheck, NULL)
    PHP_FE(zrename_function, NULL)
    PHP_FE(zrename_class, NULL)
    PHP_FE(zregister_opcode_callback, NULL)
    PHP_FE_END
};

/* Module dependencies */
zend_module_dep zmark_deps[] = {
    ZEND_MOD_CONFLICTS("xdebug")
    ZEND_MOD_CONFLICTS("taint")
    {NULL, NULL, NULL}
};

/* MINIT */
PHP_MINIT_FUNCTION(zmark)
{
    ZEND_INIT_MODULE_GLOBALS(zmark, php_zmark_init_globals, NULL);
    REGISTER_INI_ENTRIES();

    if (!ZMARK_G(enable)) {
        return SUCCESS;
    }

    REGISTER_LONG_CONSTANT("ZMARK_ECHO", ZEND_ECHO, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_EXIT", ZEND_EXIT, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_INIT_METHOD_CALL", ZEND_INIT_METHOD_CALL, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_INIT_USER_CALL", ZEND_INIT_USER_CALL, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_INIT_DYNAMIC_CALL", ZEND_INIT_DYNAMIC_CALL, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_INCLUDE_OR_EVAL", ZEND_INCLUDE_OR_EVAL, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_CONCAT", ZEND_CONCAT, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_FAST_CONCAT", ZEND_FAST_CONCAT, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_ROPE_END", ZEND_ROPE_END, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_DO_FCALL", ZEND_DO_FCALL, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_DO_ICALL", ZEND_DO_ICALL, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_DO_UCALL", ZEND_DO_UCALL, CONST_CS|CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("ZMARK_DO_FCALL_BY_NAME", ZEND_DO_FCALL_BY_NAME, CONST_CS|CONST_PERSISTENT);

    php_zmark_register_opcode_handlers();
    rename_from_ini_value(CG(function_table), ZMARK_G(rename_functions), ZMARK_IS_FUNCTION);
    rename_from_ini_value(CG(class_table), ZMARK_G(rename_classes), ZMARK_IS_CLASS);

    return SUCCESS;
}

/* MSHUTDOWN */
PHP_MSHUTDOWN_FUNCTION(zmark)
{
    UNREGISTER_INI_ENTRIES();
    return SUCCESS;
}

/* RINIT */
PHP_RINIT_FUNCTION(zmark)
{
    if (ZMARK_G(enable)) {
        ZMARK_G(in_callback) = 0;
        zend_hash_init(&ZMARK_G(callbacks), 1, NULL, ZVAL_PTR_DTOR, 0);
    }

#if defined(COMPILE_DL_ZMARK) && defined(ZTS)
    ZEND_TSRMLS_CACHE_UPDATE();
#endif

    return SUCCESS;
}

/* RSHUTDOWN */
PHP_RSHUTDOWN_FUNCTION(zmark)
{
    if (ZMARK_G(enable)) {
        zend_hash_destroy(&ZMARK_G(callbacks));
    }

    return SUCCESS;
}

/* MINFO */
PHP_MINFO_FUNCTION(zmark)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "zmark support", "enabled");
    php_info_print_table_end();

    DISPLAY_INI_ENTRIES();
}

/* Module entry */
zend_module_entry zmark_module_entry = {
    STANDARD_MODULE_HEADER_EX, NULL,
    zmark_deps,
    "zmark",
    zmark_functions,
    PHP_MINIT(zmark),
    PHP_MSHUTDOWN(zmark),
    PHP_RINIT(zmark),
    PHP_RSHUTDOWN(zmark),
    PHP_MINFO(zmark),
    "0.1-dev",
    PHP_MODULE_GLOBALS(zmark),
    NULL,
    NULL,
    NULL,
    STANDARD_MODULE_PROPERTIES_EX
};

#ifdef COMPILE_DL_ZMARK
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(zmark)
#endif
