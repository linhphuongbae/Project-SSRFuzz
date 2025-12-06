# --- CONFIGURATION ---
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$savePath = Join-Path $PSScriptRoot "images\products"

# List of High-Quality Images
$images = @{
    # --- YOUR CUSTOM LINKS (USER PROVIDED) ---
    "vi-da.jpg"        = "https://www.gento.vn/wp-content/uploads/2021/11/Vi-da-nam-v0112.jpg"
    "quan-short.jpg"   = "https://bizweb.dktcdn.net/100/524/840/products/sp25fh20c-ak-mid-nigh-1.jpg?v=1762308606677"

    # --- FASHION (Unsplash/Stable) ---
    "ao-thun.jpg"      = "https://images.unsplash.com/photo-1576566588028-4147f3842f27?auto=format&fit=crop&w=800&q=80"
    "quan-jeans.jpg"   = "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80"
    "ao-khoac.jpg"     = "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80"
    "mu.jpg"           = "https://images.unsplash.com/photo-1556306535-0f09a537f0a3?auto=format&fit=crop&w=800&q=80"
    "giay-sneaker.jpg" = "https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?auto=format&fit=crop&w=800&q=80"
    "tui-xach.jpg"     = "https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80"
    "balo.jpg"         = "https://images.unsplash.com/photo-1581605405669-fcdf81165afa?auto=format&fit=crop&w=800&q=80"

    # --- TECH ---
    "smartphone.jpg"   = "https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=800&q=80"
    "laptop.jpg"       = "https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80"
    "ban-phim.jpg"     = "https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&w=800&q=80"
    "chuot.jpg"        = "https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=800&q=80"
    "tai-nghe.jpg"     = "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80"
    "dong-ho.jpg"      = "https://images.unsplash.com/photo-1523170335258-f5ed11844a49?auto=format&fit=crop&w=800&q=80"

    # --- HOME APPLIANCES & LIFESTYLE ---
    "noi-com.jpg"      = "https://images.unsplash.com/photo-1595246140625-573b715d11dc?auto=format&fit=crop&w=800&q=80"
    "may-xay.jpg"      = "https://images.unsplash.com/photo-1585237672814-8f85a8118bf6?auto=format&fit=crop&w=800&q=80"
    "den-ngu.jpg"      = "https://images.unsplash.com/photo-1505330622279-bf7d7fc918f4?auto=format&fit=crop&w=800&q=80"
    "binh-nuoc.jpg"    = "https://images.unsplash.com/photo-1589365278144-c9e705f843ba?auto=format&fit=crop&w=800&q=80"
    "chan-ga.jpg"      = "https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80"

    # --- BOOKS & STATIONERY ---
    "sach-ky-nang.jpg" = "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80"
    "truyen.jpg"       = "https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=800&q=80"
    "vo.jpg"           = "https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80"
    "but.jpg"          = "https://images.unsplash.com/photo-1585336261022-680e295ce3fe?auto=format&fit=crop&w=800&q=80"
}

# --- EXECUTION ---
if (!(Test-Path $savePath)) { 
    New-Item -ItemType Directory -Force -Path $savePath | Out-Null
}

$count = 1
$total = $images.Count

Write-Host "Downloading images (Including your 2 custom links)..." -ForegroundColor Yellow

foreach ($key in $images.Keys) {
    $url = $images[$key]
    $fileOut = Join-Path $savePath $key
    
    Write-Host "[$count/$total] Downloading $key..." -NoNewline

    try {
        $webClient = New-Object System.Net.WebClient
        $webClient.Headers.Add("User-Agent", "Mozilla/5.0 (Windows NT 10.0; Win64; x64)")
        $webClient.DownloadFile($url, $fileOut)
        
        Write-Host " DONE" -ForegroundColor Green
    }
    catch {
        Write-Host " FAILED" -ForegroundColor Red
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Gray
    }
    $count++
}

Write-Host "`nAll done! Check images/products folder." -ForegroundColor Cyan