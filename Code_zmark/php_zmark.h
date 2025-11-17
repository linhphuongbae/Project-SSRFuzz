#ifndef PHP_ZMARK_H
#define PHP_ZMARK_H

#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include "php.h"
#include "zend_types.h"
#include "zend_API.h"
#include "zend_hash.h"
#include "zend_exceptions.h"
#include "zend_interfaces.h"
#include "zend_extensions.h"
#include "zend_compile.h"
#include "zend_execute.h"

/* 
 * Module entry
 */
extern zend_module_entry zmark_module_entry;
#define phpext_zmark_ptr &zmark_module_entry

/* 
 * Module globals
 */
ZEND_BEGIN_MODULE_GLOBALS(zmark)
    zend_bool enable;              /* zmark.enable */
    zend_bool enable_rename;       /* zmark.enable_rename */
    char *rename_functions;        /* zmark.rename_functions */
    char *rename_classes;          /* zmark.rename_classes */

    HashTable callbacks;           /* opcode -> PHP callback */
    zend_bool in_callback;         /* flag reentrancy */
ZEND_END_MODULE_GLOBALS(zmark)

ZEND_EXTERN_MODULE_GLOBALS(zmark)
#define ZMARK_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(zmark, v)

#if defined(ZTS) && defined(COMPILE_DL_ZMARK)
ZEND_TSRMLS_CACHE_EXTERN()
#endif

/*
 * PHP functions (API exposed cho PHP)
 */
PHP_FUNCTION(zid);
PHP_FUNCTION(zmark);
PHP_FUNCTION(zcheck);
PHP_FUNCTION(zclear);
PHP_FUNCTION(zrename_function);
PHP_FUNCTION(zrename_class);
PHP_FUNCTION(zregister_opcode_callback);

/*
 * Internal rename API (rename.c)
 */
extern void rename_from_ini_value(HashTable *ht, const char *ini_value, int type);

/*
 * Internal opcode hook API (opcode.c)
 */
extern void php_zmark_register_opcode_handlers(void);

/*
 * INI entries (được khai báo trong zmark.c)
 */
PHP_MINIT_FUNCTION(zmark);
PHP_MSHUTDOWN_FUNCTION(zmark);
PHP_RINIT_FUNCTION(zmark);
PHP_RSHUTDOWN_FUNCTION(zmark);
PHP_MINFO_FUNCTION(zmark);

#endif /* PHP_ZMARK_H */