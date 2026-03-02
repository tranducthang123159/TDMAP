<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Map UI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
html, body {
    height: 100%;
    margin: 0;
    font-family: Arial, sans-serif;
    overflow: hidden;
}

#map {
    height: 100%;
    width: 100%;
}

/* ================= HEADER ================= */
.top-bar {
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 2000;
    background: #fff;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.logo {
    font-weight: 700;
    font-size: 22px;
    color: #f4a000;
}

.menu-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.menu-right span {
    cursor: pointer;
    font-size: 15px;
}

/* ================= TOP TOOLS ================= */
.top-tools {
    position: absolute;
    top: 65px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1500;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

.top-tools .btn {
    border-radius: 30px;
    padding: 6px 18px;
    font-size: 14px;
    white-space: nowrap;
}

/* ================= LEFT TOOLBAR ================= */
.left-toolbar {
    position: absolute;
    top: 150px;
    left: 15px;
    z-index: 1500;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.circle-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: none;
    background: white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ================= BOTTOM FILTER ================= */
.bottom-filter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1500;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

.bottom-filter .btn {
    border-radius: 30px;
    padding: 6px 15px;
    font-size: 14px;
    white-space: nowrap;
}

/* ================= MENU SLIDE ================= */


.menu-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(2px);
    display: none;
    z-index: 3000;
}

.menu-overlay.active {
    display: block;
}

.menu-box {
    position: absolute;
    top: 0;
    left: -380px;
    width: 360px;
    height: 100%;
    background: #ffffff;
    padding: 0;
    overflow-y: auto;
    transition: 0.35s ease;
    box-shadow: 4px 0 20px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
}

.menu-overlay.active .menu-box {
    left: 0;
}

/* ===== HEADER MENU ===== */
.menu-header {
    background: linear-gradient(135deg, #f4a000, #ffb300);
    padding: 25px 20px;
    color: white;
    text-align: center;
}

.menu-logo {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 5px;
    
}

.menu-header p {
    font-size: 13px;
    opacity: 0.9;
    margin: 0;
}
.menu-trigger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 16px;
    border-radius: 30px;
    border: 2px solid #f4a000;
    color: #f4a000;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
}

/* Hover */
.menu-trigger:hover {
    background: #f4a000;
    color: white;
}
/* ===== MENU GRID ===== */
.menu-grid {
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* ===== ITEM ===== */
.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    background: #f9f9f9;
    border: 1px solid #eee;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.25s ease;
}

.menu-item:hover {
    background: #fff3e0;
    border-color: #f4a000;
    transform: translateX(4px);
}

/* Icon */
.menu-item span {
    font-size: 18px;
}

/* Scroll đẹp */
.menu-box::-webkit-scrollbar {
    width: 6px;
}

.menu-box::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

/* MOBILE */
@media (max-width: 576px) {
    .menu-box {
        width: 85%;
    }
}
/* ================= TABLET ================= */
@media (max-width: 992px) {

    .logo {
        font-size: 18px;
    }

    .menu-right span {
        font-size: 14px;
    }

    .left-toolbar {
        top: 130px;
    }

    .circle-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

}

/* ================= MOBILE ================= */
@media (max-width: 576px) {

    .menu-right .hide-mobile {
        display: none;
    }

    .logo {
        font-size: 16px;
    }

    .top-tools {
        top: 55px;
        left: 0;
        transform: none;
        width: 100%;
        padding: 10px 10px;
        flex-wrap: nowrap;
        overflow-x: auto;
        justify-content: flex-start;
        scrollbar-width: none;
    }

    .top-tools::-webkit-scrollbar {
        display: none;
    }

    .bottom-filter {
        left: 0;
        transform: none;
        width: 100%;
        padding: 0 10px;
        flex-wrap: nowrap;
        overflow-x: auto;
        justify-content: flex-start;
        scrollbar-width: none;
    }

    .bottom-filter::-webkit-scrollbar {
        display: none;
    }

    .left-toolbar {
        top: 110px;
        left: 10px;
    }

    .circle-btn {
        width: 38px;
        height: 38px;
        font-size: 14px;
    }

    .menu-box {
        width: 85%;
    }

}
</style>
</head>
<body>

<!-- MENU SLIDE -->
<div id="categoryMenu" class="menu-overlay">
    <div class="menu-box">
        <div class="text-center mb-3">
            <h2 class="menu-logo logo">TDMAP-PRO</h2>
            <p>THÔNG TIN THẬT - GIÁ TRỊ THẬT</p>
        </div>

        <div class="menu-grid">
            <div class="menu-item">👤 Đăng nhập</div>
            <div class="menu-item">🧑‍🤝‍🧑 Đăng ký</div>
            <div class="menu-item">🏢 Mua bán</div>
            <div class="menu-item">🏠 Cho thuê</div>
            <div class="menu-item">🗺 Bản đồ quy hoạch</div>
            <div class="menu-item">📂 Kho bản đồ quy hoạch</div>
            <div class="menu-item">📍 Bản đồ giá nhà đất</div>
            <div class="menu-item">🏗 Dự án bất động sản</div>
            <div class="menu-item">📅 Bảng giá đất 2026</div>
            <div class="menu-item">👨‍💼 Danh sách môi giới</div>
            <div class="menu-item">📖 Hướng dẫn check quy hoạch</div>
            <div class="menu-item">📝 Đăng tin & ký gửi</div>
            <div class="menu-item">⭐ Nạp tiền đăng tin VIP</div>
            <div class="menu-item">👑 Nâng VIP xem quy hoạch</div>
        </div>
    </div>
</div>

<!-- HEADER -->
<div class="top-bar">
    <div class="logo">TDMAP-PRO</div>

    <div class="menu-right">
        <span class="hide-mobile menu-trigger">Mua bán</span>
        <span class="hide-mobile menu-trigger">Check giá</span>
       <span class="menu-trigger" onclick="openMenu()">
    ☰ Danh mục
</span>
    </div>
</div>

<!-- TOP BUTTONS -->
<div class="top-tools">
    <button class="btn btn-light shadow">Khu vực</button>
    <button class="btn btn-light shadow">Địa điểm</button>
    <button class="btn btn-light shadow">Góc ranh</button>
    <button class="btn btn-light shadow">Tờ thửa</button>
</div>

<!-- LEFT TOOLBAR -->
<div class="left-toolbar">
    <button class="circle-btn">+</button>
    <button class="circle-btn">−</button>
    <button class="circle-btn"></button>
    <button class="circle-btn">↻</button>
    <button class="circle-btn text-danger">🗑</button>
</div>

<!-- BOTTOM FILTER -->
<div class="bottom-filter">
    <button class="btn btn-light shadow">QH 2030</button>
    <button class="btn btn-light shadow">KH 2026</button>
    <button class="btn btn-primary shadow">QH phân khu</button>
    <button class="btn btn-light shadow">QH 2040</button>
    <button class="btn btn-light shadow">QH khác</button>
</div>

<!-- MAP -->
<div id="map"></div>

<script>

const menu = document.getElementById("categoryMenu");

function openMenu() {
    menu.classList.add("active");
}

function closeMenu() {
    menu.classList.remove("active");
}

menu.addEventListener("click", function(e){
    if(e.target === menu){
        closeMenu();
    }
});
</script>
<!-- Leaflet JS -->

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([10.76, 106.66], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
</script>

<script>
let map;

/* ================= INIT MAP ================= */
map = L.map('map', {
    zoomControl: false   // tắt zoom mặc định
}).setView([10.762622, 106.660172], 13);

/* ================= TILE LAYER (OSM FREE) ================= */
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

/* ================= CUSTOM ZOOM ================= */
document.querySelectorAll(".circle-btn")[0].onclick = () => {
    map.zoomIn();
};

document.querySelectorAll(".circle-btn")[1].onclick = () => {
    map.zoomOut();
};

/* ================= LẤY VỊ TRÍ HIỆN TẠI ================= */
document.querySelectorAll(".circle-btn")[2].onclick = () => {
    map.locate({ setView: true, maxZoom: 16 });
};

map.on('locationfound', function(e) {
    L.marker(e.latlng)
        .addTo(map)
        .bindPopup("Bạn đang ở đây")
        .openPopup();
});

/* ================= CLICK MAP HIỆN TOẠ ĐỘ ================= */
map.on('click', function(e) {
    console.log("Toạ độ:", e.latlng.lat, e.latlng.lng);
});
</script>

</body>
</html>