# Hướng Dẫn Build Extension Zmark Với File Mới

## 📋 Yêu cầu trước khi build

```bash
# 1. PHP development headers
sudo apt-get install php-dev

# 2. Build tools
sudo apt-get install build-essential

# 3. Autoconf/Automake (nếu chưa có)
sudo apt-get install autoconf automake libtool

# 4. Check PHP version
php -v

# 5. Check phpize
which phpize
phpize --version
```

---

## 🔧 Cách Build (Bước Chi Tiết)

```bash
# Verify
ls -la *.c | grep -E "^-.*zmark\.c|taint\.c|rename\.c|opcode\.c"
```

**Kết quả mong đợi:**
```
-rw-r--r-- ... zmark.c
-rw-r--r-- ... taint.c
-rw-r--r-- ... rename.c
-rw-r--r-- ... opcode.c
-rw-r--r-- ... Full_zmark.c  (original, keep for reference)
```

---

### **Bước 1: Chạy phpize**

```bash
# Từ thư mục extension
phpize

# Output mong đợi:
# Configuring for:
# PHP Api Version:         20160303
# Zend Module Api No:      20160303
# Zend Extension Api No:   320160303
```

**Nếu gặp lỗi:**
```bash
# Nếu không tìm thấy phpize
which phpize

# Nếu cần đặt đường dẫn
/usr/bin/phpize
```

---

### **Bước 2: Configure extension**

```bash
# Build as shared module (khuyến cáo)
./configure --enable-zmark

# Hoặc nếu cần build static (ít phổ biến)
./configure --enable-zmark

# Output cuối:
# config.status: creating config.h
# config.status: creating Makefile
```

**Nếu gặp lỗi config:**
```bash
# Check PHP config
php-config --version

# Check extension path
php-config --extension-dir

# Debug
./configure --enable-zmark --with-php-config=/usr/bin/php-config
```

---

### **Bước 3: Build (Make)**

```bash
# Compile extension
make

# Hoặc với parallel build (nhanh hơn)
make -j4

# Output mong đợi:
# /bin/bash /path/to/libtool --mode=compile gcc ...
# cc ... -c zmark.c
# cc ... -c taint.c
# cc ... -c rename.c
# cc ... -c opcode.c
# [linking steps...]
# Build complete. Don't forget to run 'make install'
```

**Nếu gặp lỗi compile:**

**Lỗi 1: Undefined reference to `php_zmark_register_opcode_handlers`**
```
ld: zmark.o: in function `PHP_MINIT_FUNCTION':
zmark.c:(.text+0x...): undefined reference to `php_zmark_register_opcode_handlers'
```
**Giải pháp:** Kiểm tra `php_zmark_register_opcode_handlers()` được define trong `opcode.c` line ~950

**Lỗi 2: Undefined reference to `rename_from_ini_value`**
```
ld: zmark.o: undefined reference to `rename_from_ini_value'
```
**Giải pháp:** Kiểm tra `rename_from_ini_value()` được define trong `rename.c` line ~110

**Lỗi 3: Macro không tìm thấy**
```
error: 'ZMARK_FLAG' undeclared
```
**Giải pháp:** Macro được define trong `taint.c` line ~6-8, không cần extern

---

### **Bước 4: Install extension**

```bash
# Install (cần root/sudo)
sudo make install

# Output mong đợi:
# Installing shared extensions:     /usr/lib/php/20160303/
# Installing header files:          /usr/include/php/
```

**Xác nhận cài đặt:**
```bash
# Tìm file .so được install
find /usr -name "zmark.so" 2>/dev/null

# Kết quả:
# /usr/lib/php/20160303/zmark.so
```

---

### **Bước 5: Cấu hình PHP để load extension**

**Phương pháp 1: Thêm vào php.ini chính**

```bash
# Tìm php.ini
php -i | grep "php.ini"

# Hoặc
php -r "echo php_ini_loaded_file();"

# Thêm vào file
sudo nano /etc/php/7.4/cli/php.ini

# Thêm dòng này:
extension=zmark.so

# Hoặc (nếu cần configure options)
zmark.enable=1
zmark.enable_rename=0
zmark.rename_functions=""
zmark.rename_classes=""
```

**Phương pháp 2: Tạo file .conf riêng (khuyến cáo)**

```bash
# Tạo file cấu hình
sudo nano /etc/php/7.4/mods-available/zmark.ini

# Nội dung:
extension=zmark.so
zmark.enable=1
zmark.enable_rename=0
```

**Phương pháp 3: Load động (CLI test)**

```bash
# Test với option command-line
php -d extension=zmark.so -r "echo 'Extension loaded';"
```

---

### **Bước 6: Verify extension đã load**

```bash
# Check extension
php -m | grep zmark

# Hoặc
php -r "echo extension_loaded('zmark') ? 'OK' : 'FAIL';"

# Hoặc
php -i | grep zmark

# Output mong đợi:
# zmark
# zmark support => enabled
# zmark.enable => 1
# zmark.enable_rename => 0
```

**Nếu không load:**
```bash
# Check error log
php -r "php_sapi_name();" 2>&1

# Hoặc check syslog
tail -f /var/log/syslog | grep zmark

# Hoặc check php error log
tail -f /var/log/php7.4-fpm.log | grep zmark
```

---

## 🧪 Test Basic Functions

### **Test 1: Verify functions exist**

```bash
php -d extension=zmark.so << 'EOF'
<?php
$functions = ['zid', 'zmark', 'zcheck', 'zclear', 'zrename_function', 'zrename_class', 'zregister_opcode_callback'];

foreach ($functions as $func) {
    echo ($func . ": " . (function_exists($func) ? "✓\n" : "✗\n"));
}
EOF
```

**Output mong đợi:**
```
zid: ✓
zmark: ✓
zcheck: ✓
zclear: ✓
zrename_function: ✓
zrename_class: ✓
zregister_opcode_callback: ✓
```

### **Test 2: Test zmark() function**

```bash
php -d extension=zmark.so << 'EOF'
<?php
$str = "hello world";
echo "Before mark: " . var_export(zcheck($str), true) . "\n";

zmark($str);
echo "After mark: " . var_export(zcheck($str), true) . "\n";

zclear($str);
echo "After clear: " . var_export(zcheck($str), true) . "\n";
EOF
```

**Output mong đợi:**
```
Before mark: false
After mark: true
After clear: false
```

### **Test 3: Check INI settings**

```bash
php -d extension=zmark.so -d zmark.enable=1 << 'EOF'
<?php
echo "zmark.enable: " . ini_get('zmark.enable') . "\n";
echo "zmark.enable_rename: " . ini_get('zmark.enable_rename') . "\n";
EOF
```

**Output mong đợi:**
```
zmark.enable: 1
zmark.enable_rename: 0
```

---

## 🐛 Troubleshooting

### **Lỗi 1: "Cannot load extension"**
```
PHP Warning:  PHP Startup: Unable to load dynamic library 'zmark.so'
```

**Giải pháp:**
```bash
# 1. Check file tồn tại
ls -la /usr/lib/php/20160303/zmark.so

# 2. Check permission
chmod 644 /usr/lib/php/20160303/zmark.so

# 3. Check shared library dependencies
ldd /usr/lib/php/20160303/zmark.so

# 4. Check symbol table
nm -D /usr/lib/php/20160303/zmark.so | grep "php_zmark"
```

### **Lỗi 2: "Undefined symbol"**
```
PHP Warning: Module "zmark" is already loaded
```

**Giải pháp:**
```bash
# 1. Check php.ini (không load 2 lần)
grep -n "extension=zmark" /etc/php/*/cli/php.ini

# 2. Clean previous builds
make clean

# 3. Rebuild từ đầu
rm -rf autom4te.cache
phpize --clean
phpize
./configure --enable-zmark
make clean
make
```

### **Lỗi 3: "Compile error on macro"**
```
error: 'ZEND_RESULT_USED' undeclared
```

**Giải pháp:** Thêm vào `opcode.c`:
```bash
# Check PHP version
php -r "echo PHP_VERSION;"

# Nếu < 7.1, thêm macro định nghĩa
#define ZEND_RESULT_USED(opline) (opline->result.var)
```

---

