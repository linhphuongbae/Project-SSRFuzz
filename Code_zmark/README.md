# Hướng Dẫn Sử Dụng 4 File Mới

Các file đã được tách ra từ `zmark.c` như sau:

## 1. **zmark.c** - File chính (Module core)
- Chứa: MINIT, MSHUTDOWN, RINIT, RSHUTDOWN, MINFO lifecycle hooks
- INI entries configuration
- Module entry struct
- Danh sách hàm PHP công khai
- Module dependencies

## 2. **taint.c** - Taint marking functions
- `zmark_zstr()` - Helper đánh dấu string bị tainted (sử dụng bit flag)
- `PHP_FUNCTION(zmark)` - Hàm PHP để đánh dấu string
- `PHP_FUNCTION(zcheck)` - Hàm PHP để kiểm tra string bị tainted hay không
- `PHP_FUNCTION(zclear)` - Hàm PHP để gỡ taint mark

## 3. **rename.c** - Rename functions/classes
- `rename_hash_key()` - Rehash và rename entry trong hash table (cốt lõi)
- `rename_hash_str_key()` - Helper wrapper cho rename_hash_key
- `clear_function_run_time_cache()` - Clear runtime cache của function
- `clear_run_time_cache()` - Clear tất cả runtime cache
- `rename_from_ini_value()` - Parse INI value "old:new,old2:new2" và tự động rename
- `PHP_FUNCTION(zrename_function)` - Hàm PHP để rename function
- `PHP_FUNCTION(zrename_class)` - Hàm PHP để rename class

## 4. **opcode.c** - Opcode handlers (lớn nhất)
Gồm 3 phần chính:

### A. Helper functions (từ zend_execute.c):
- `php_zmark_make_real_object()` - Chuyển empty value thành object
- `php_zmark_check_string_offset()` - Kiểm tra string offset
- `php_zmark_fetch_dimension_address_inner()` - Lấy element từ array/object
- `php_zmark_fetch_dimension_address()` - Wrapper cho dimension address
- `php_zmark_get_zval_ptr_*()` - Family functions để lấy zval từ operand

### B. Binary assign op helpers:
- `php_zmark_assign_op_overloaded_property()` - Handle assign op trên property
- `php_zmark_binary_assign_op_obj_dim()` - Handle assign op trên object dimension
- `php_zmark_binary_assign_op_helper()` - Core assign op handler
- `php_zmark_binary_assign_op_obj_helper()` - Assign op trên object property
- `php_zmark_binary_assign_op_dim_helper()` - Assign op trên array dimension

### C. Opcode handlers:
- `php_zmark_op1_handler()` - Handler cho opcode dùng operand 1 (ECHO, EXIT, INCLUDE_OR_EVAL)
- `php_zmark_op2_handler()` - Handler cho opcode dùng operand 2 (INIT_*_CALL)
- `php_zmark_concat_handler()` - Handler cho CONCAT opcode
- `php_zmark_assign_concat_handler()` - Handler cho .= assignment
- `php_zmark_assign_op_handler()` - (PHP 7.4+) Assign op handler
- `php_zmark_assign_dim_op_handler()` - (PHP 7.4+) Assign dimension op handler
- `php_zmark_assign_obj_op_handler()` - (PHP 7.4+) Assign object op handler
- `php_zmark_rope_end_handler()` - Handler cho ROPE_END opcode
- `php_zmark_fcall_handler()` - Handler cho function call opcode
- `php_zmark_init_fcall()` - Handler để tính stack size cho init fcall
- `php_zmark_register_opcode_handlers()` - Đăng ký tất cả handlers
- `PHP_FUNCTION(zregister_opcode_callback)` - Hàm PHP để đăng ký callback cho opcode

---

## Cách Compile:

```bash
# Build extension
phpize
./configure --enable-zmark
make
make install
```

---


