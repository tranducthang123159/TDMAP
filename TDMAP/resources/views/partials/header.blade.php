@php
    $authUser = auth()->user();
    $vipLevel = $authUser?->getCurrentVipLevel() ?? -1;

    $vipLabel = match($vipLevel) {
        1 => 'VIP 1',
        2 => 'VIP 2',
        3 => 'VIP 3',
        0 => 'FREE',
        default => 'KHÁCH',
    };

    $vipClass = match($vipLevel) {
        1 => 'vip-badge vip1',
        2 => 'vip-badge vip2',
        3 => 'vip-badge vip3',
        0 => 'vip-badge free',
        default => 'vip-badge guest',
    };

    $vipDesc = match($vipLevel) {
        1 => 'Mỗi mục được tải tối đa 3 file',
        2 => 'Mỗi mục được tải tối đa 9 file',
        3 => 'Không giới hạn số file tải lên',
        0 => 'Mỗi mục được tải tối đa 1 file',
        default => 'Đăng nhập để tải dữ liệu và dùng đầy đủ tính năng',
    };
@endphp
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .lock-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        margin-left: 6px;
        font-size: 12px;
        color: #dc3545;
        font-weight: 700;
        vertical-align: middle;
    }

    .lock-badge i {
        font-size: 11px;
    }

    .toolbox-lock {
        display: block;
        font-size: 11px;
        color: #dc3545;
        font-weight: 700;
        line-height: 1;
        margin-top: 2px;
    }

    .protected-btn {
        position: relative;
    }

    .protected-btn i.fa-lock {
        color: #dc3545;
        font-size: 11px;
        margin-left: 4px;
    }

    .protected-upload label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
    }

    .protected-upload .upload-lock {
        color: #dc3545;
        font-size: 12px;
        font-weight: 700;
    }

    .upload-protected-title {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .upload-protected-title .fa-lock {
        color: #dc3545;
        font-size: 14px;
    }

    .protected-btn {
        position: relative;
    }

    .protected-btn .tool-lock {
        position: absolute;
        top: 6px;
        right: 8px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        color: #dc3545;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        line-height: 1;
        pointer-events: none;
    }

    .protected-btn span {
        display: block;
        margin-top: 4px;
    }

    .vip-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        line-height: 1;
        white-space: nowrap;
    }

    .vip-badge.guest {
        background: #f1f3f5;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }

    .vip-badge.free {
        background: #eef2ff;
        color: #3b5bdb;
        border: 1px solid #c7d2fe;
    }

    .vip-badge.vip1 {
        background: #fff3cd;
        color: #b26a00;
        border: 1px solid #ffe08a;
    }

    .vip-badge.vip2 {
        background: #e6fcf5;
        color: #087f5b;
        border: 1px solid #96f2d7;
    }

    .vip-badge.vip3 {
        background: linear-gradient(135deg, #111827, #374151);
        color: #ffd43b;
        border: 1px solid rgba(255, 212, 59, .35);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .18);
    }

    .user-vip-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 14px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        font-size: 13px;
        font-weight: 600;
    }

    .user-vip-box .vip-text {
        color: #495057;
    }

    .vip-info-box {
        margin-top: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        font-size: 13px;
        line-height: 1.6;
        transition: all .2s ease;
    }

    .vip-info-box strong {
        display: block;
        margin-bottom: 4px;
        color: #212529;
    }

    .vip-login-note {
        font-size: 12px;
        color: #dc3545;
        font-weight: 700;
    }

    .menu-item-vip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
    }

    .vip-status-error {
        background: #fff5f5 !important;
        border: 1px solid #ffc9c9 !important;
        color: #c92a2a !important;
    }

    .vip-status-warning {
        background: #fff9db !important;
        border: 1px solid #ffe066 !important;
        color: #8f5b00 !important;
    }

    .vip-status-success {
        background: #ebfbee !important;
        border: 1px solid #b2f2bb !important;
        color: #2b8a3e !important;
    }

      body.logged-in .lock-badge,
    body.logged-in .upload-lock,
    body.logged-in .tool-lock,
    body.logged-in .protected-btn i.fa-lock {
        display: none !important;
    }
</style>

<!-- MENU SLIDE -->
<div id="categoryMenu" class="menu-overlay">
    <div class="menu-box">
        <div class="text-center mb-3">
            <a href="{{ url('/') }}" class="menu-logo-wrap">
              
                <h2 class="menu-logo logo">Tài Đỗ Map</h2>
            </a>
            <p>THÔNG TIN THẬT - GIÁ TRỊ THẬT</p>
        </div>

        <div class="menu-grid">
            @guest
                <div class="menu-item">
                    <a href="{{ route('login') }}" class="menu-link">👤 Đăng nhập</a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('register') }}" class="menu-link">🧑‍🤝‍🧑 Đăng ký</a>
                </div>
            @endguest

            @auth
                <div class="menu-item">
                    👋 Xin chào {{ Auth::user()->name }}

                    <div class="user-vip-box">
                        <span class="vip-text">Gói hiện tại</span>
                        <span class="{{ $vipClass }}">{{ $vipLabel }}</span>
                    </div>
                </div>

                @role('admin')
                <div class="menu-item">
                    🛠 <a href="{{ url('/admin') }}" class="menu-link">Trang quản trị admin</a>
                </div>
                @endrole
            @endauth

            <a href="{{ url('/my-files') }}">
                <div class="menu-item">🏠 Thông tin người dùng</div>
            </a>

            <!-- <div class="menu-item">🗺 Bản đồ quy hoạch</div> -->

            <a href="{{ route('vip.payment') }}" style="text-decoration:none;color:inherit;">
                <div class="menu-item">
                    <div class="menu-item-vip">
                        <span>⭐ Nạp tiền VIP</span>
                      
                    </div>
                </div>
            </a>

            @auth
                <div class="menu-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="border:none;background:none;color:#dc3545;width:100%;text-align:left;">
                            🚪 Đăng xuất
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</div>

<!-- HEADER -->
<div class="top-bar">
    <a href="{{ url('/') }}" class="logo-wrap">
        <img src="{{ asset('images/logo.png') }}" class="site-logo">
        <div class="logo-text">TÀI ĐỔ MAP</div>
    </a>
    <div class="menu-right">
        <button id="exportPDFBtn" onclick="exportTXT()" class="export-pdf-btn">
            📄 Xuất TXT
        </button>
        <span class="menu-trigger" onclick="openMenu()">☰ Danh mục</span>
    </div>
</div>

<!-- TOP TOOLS -->
<div class="map-tools">
    <div class="map-tools-header" onclick="toggleMapTools()">
        <span>🗺 Công cụ địa chính & quy hoạch</span>
        <span id="mapArrow">▲</span>
    </div>

    <div class="map-tools-body" id="mapToolsBody">
        <div class="container-fluid p-2">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h6 class="mb-3 d-flex justify-content-between align-items-center" onclick="toggleUpload()" style="cursor:pointer">
                        <span class="upload-protected-title">
                            Tải dữ liệu bản đồ
                            @guest
                                <i class="fa-solid fa-lock"></i>
                                <span class="lock-badge">Đăng nhập</span>
                            @else
                                <span class="{{ $vipClass }}">{{ $vipLabel }}</span>
                            @endguest
                        </span>
                        <span id="uploadArrow">▲</span>
                    </h6>

                    <div id="uploadBody">
                        <div class="row g-2">
                            <div class="col-md-4 protected-upload">
                              <label class="form-label small">
    <span>Địa chính cũ</span>
    @guest
        <span class="upload-lock"><i class="fa-solid fa-lock"></i></span>
    @endguest
</label>
                                <input type="file" class="form-control form-control-sm" id="dc_cu">
                            </div>

                            <div class="col-md-4 protected-upload">
                               <label class="form-label small">
    <span>Địa chính mới</span>
    @guest
        <span class="upload-lock"><i class="fa-solid fa-lock"></i></span>
    @endguest
</label>
                                <input type="file" class="form-control form-control-sm" id="dc_moi">
                            </div>

                            <div class="col-md-4 protected-upload">
                                <label class="form-label small">
    <span>Quy hoạch</span>
    @guest
        <span class="upload-lock"><i class="fa-solid fa-lock"></i></span>
    @endguest
</label>
                                <input type="file" class="form-control form-control-sm" id="quy_hoach">
                            </div>
                        </div>

                        <div id="vipUploadStatus" class="vip-info-box">
                            <strong>Quyền tải file hiện tại: {{ $vipLabel }}</strong>
                            {{ $vipDesc }}
                        </div>
                    </div>

                    <hr>

                    <div class="row g-2 mb-3">
                        <div class="col-md-2 col-4">
                            <button class="btn btn-primary btn-sm w-100" onclick="loadMap()">
                                Load Map
                            </button>
                        </div>
                        <hr>
                    </div>

                    <div class="parcel-panel">
                        <div class="parcel-header" onclick="toggleParcelPanel()">
                            <span>🔎 Tra cứu thửa đất</span>
                            <span id="parcelArrow">▼</span>
                        </div>

                        <div id="parcelBody" class="parcel-body collapsed">
                            <div class="parcel-filter">
                                <input id="searchTo" placeholder="Tìm Tờ">
                                <input id="searchThua" placeholder="Tìm Thửa">
                                <input id="searchToCu" placeholder="Tờ cũ">
                                <input id="searchChu" placeholder="Tìm Chủ">
                            </div>

                            <div id="parcelList"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="search-panel">
                        <div class="search-header" onclick="toggleSearchPanel()">
                            <span>📍 Tìm tọa độ</span>
                            <span id="searchArrow">▲</span>
                        </div>

                        <div id="searchBody" class="search-body collapsed">
                            <div class="search-grid">
                                <div class="search-card">
                                    <div class="search-title">📍 VN2000</div>
                                    <div class="search-row">
                                        <input id="vn_input" placeholder="X, Y (vd: 250000, 1200000)">
                                    </div>
                                    <button class="search-btn" onclick="searchVN()">🔎 VN</button>
                                </div>

                                <div class="search-card">
                                    <div class="search-title">🌍 WGS84</div>
                                    <div class="search-row">
                                        <input id="wgs_input" placeholder="Lat, Lng (vd: 12.68, 108.03)">
                                    </div>
                                    <button class="search-btn" onclick="searchWGS()">🔎 WGS</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- LOGIN POPUP -->
<div id="loginPopup" class="login-popup">
    <div class="popup-box">
        <div class="popup-icon">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h5>Yêu cầu đăng nhập</h5>
        <p>Bạn cần đăng nhập để sử dụng chức năng này.</p>

        <div class="popup-buttons">
            <a href="/login" class="btn-login">Đăng nhập</a>
            <a href="/register" class="btn-register">Đăng ký</a>
        </div>

        <hr>

        <button class="btn btn-secondary" onclick="closeLoginPopup()">Đóng</button>
    </div>
</div>

<!-- LEFT TOOLBAR -->
<div class="map-toolbox">
    <div class="toolbox-menu" id="toolboxMenu">
        <button onclick="map.zoomIn()">
            <i class="fa-solid fa-plus"></i>
            <span>Zoom</span>
        </button>

        <button onclick="map.zoomOut()">
            <i class="fa-solid fa-minus"></i>
            <span>Thu</span>
        </button>

        <button onclick="locateMe()">
            <i class="fa-solid fa-location-crosshairs"></i>
            <span>GPS vị trí</span>
        </button>

        <button onclick="clearMarker()">
            <i class="fa-solid fa-trash"></i>
            <span>Xóa Ghim</span>
        </button>

        <button class="protected-btn" onclick="requireLogin(startKC)">
    <i class="fa-solid fa-ruler"></i>
    <span>Đo KC</span>
    @guest
        <small class="tool-lock"><i class="fa-solid fa-lock"></i></small>
    @endguest
</button>

<button class="protected-btn" onclick="requireLogin(startDT)">
    <i class="fa-solid fa-draw-polygon"></i>
    <span>Đo DT</span>
    @guest
        <small class="tool-lock"><i class="fa-solid fa-lock"></i></small>
    @endguest
</button>

<button class="protected-btn" onclick="requireLogin(startGPS)">
    <i class="fa-solid fa-location-dot"></i>
    <span>Đo GPS</span>
    @guest
        <small class="tool-lock"><i class="fa-solid fa-lock"></i></small>
    @endguest
</button>

<button class="protected-btn" onclick="requireLogin(undoMeasures)">
    <i class="fa-solid fa-rotate-left"></i>
    <span>Undo</span>
    @guest
        <small class="tool-lock"><i class="fa-solid fa-lock"></i></small>
    @endguest
</button>

<button class="protected-btn" onclick="requireLogin(clearMeasures)">
    <i class="fa-solid fa-broom"></i>
    <span>Xóa đo</span>
    @guest
        <small class="tool-lock"><i class="fa-solid fa-lock"></i></small>
    @endguest
</button>

        <button onclick="reloadMap()">
            <i class="fa-solid fa-rotate-right"></i>
            <span>Load</span>
        </button>
    </div>

    <button class="toolbox-main" onclick="toggleToolbox()">
        <i class="fa-solid fa-bars"></i>
    </button>
</div>

<div id="measureToast" class="measure-toast">
    <div class="toast-icon" id="toastIcon"></div>
    <div class="toast-text" id="toastText"></div>
</div>

<div id="savedMapBtn" class="saved-map-btn" onclick="toggleSavedMaps()">
    <i class="fa-solid fa-layer-group"></i>
</div>

<div id="savedMapPanel" class="saved-map-panel">
    <div class="saved-map-header">
        <span>Bản đồ đã lưu</span>
        <button type="button" onclick="toggleSavedMaps()">×</button>
    </div>

    <div class="saved-map-filters">
        <label><input type="checkbox" id="toggle_dc_moi" checked onchange="toggleMapGroup('dc_moi')"> Hiện ĐC</label>
        <label><input type="checkbox" id="toggle_dc_cu" checked onchange="toggleMapGroup('dc_cu')"> Hiện ĐC Cũ</label>
        <label><input type="checkbox" id="toggle_qh" checked onchange="toggleMapGroup('quy_hoach')"> Hiện QH</label>
        <label><input type="checkbox" id="toggle_canh" checked onchange="toggleMapGroup('canh')"> Hiện Canh</label>
    </div>

    <div id="savedMapList" class="saved-map-list">
        <div class="saved-map-empty">Chưa có dữ liệu</div>
    </div>
</div>
<script>
    let isLogin = {{ auth()->check() ? 'true' : 'false' }};
    let currentVip = {{ auth()->check() ? $vipLevel : -1 }};
    window.isLogin = isLogin;
    window.currentVip = currentVip;

    const menu = document.getElementById("categoryMenu");
    const mapTools = document.querySelector(".map-tools");

    function showLoginPopup() {
        document.getElementById("loginPopup").style.display = "flex";
    }

    function closeLoginPopup() {
        document.getElementById("loginPopup").style.display = "none";
    }

    function requireLogin(callback) {
        if (!isLogin) {
            showLoginPopup();
            return false;
        }

        if (typeof callback === "function") {
            callback();
        }

        return true;
    }

    function toggleToolbox() {
        let menuTool = document.getElementById("toolboxMenu");
        menuTool.classList.toggle("active");
    }

    function toggleMapTools() {
        const body = document.getElementById("mapToolsBody");
        const wrapper = document.querySelector(".map-tools");
        const arrow = document.getElementById("mapArrow");

        const isCollapsed = body.classList.contains("collapsed");

        body.classList.toggle("collapsed");
        wrapper.classList.toggle("active");

        if (isCollapsed) {
            arrow.style.transform = "rotate(180deg)";
            body.classList.add("opening");

            setTimeout(() => {
                body.classList.remove("opening");
            }, 450);
        } else {
            arrow.style.transform = "rotate(0deg)";
        }
    }

    function toggleSearchPanel() {
        let body = document.getElementById("searchBody");
        let arrow = document.getElementById("searchArrow");

        body.classList.toggle("collapsed");

        if (body.classList.contains("collapsed")) {
            arrow.innerHTML = "▲";
        } else {
            arrow.innerHTML = "▼";
        }
    }

    function toggleUpload() {
        const body = document.getElementById("uploadBody");
        const arrow = document.getElementById("uploadArrow");

        body.classList.toggle("collapsed");

        if (body.classList.contains("collapsed")) {
            arrow.innerHTML = "▲";
        } else {
            arrow.innerHTML = "▼";
        }
    }

    function showExportPDF() {
        let btn = document.getElementById("exportPDFBtn");
        if (btn) btn.style.display = "block";
    }

    function hideExportPDF() {
        let btn = document.getElementById("exportPDFBtn");
        if (btn) btn.style.display = "none";
    }

    function openMenu() {
        menu.classList.add("active");
        mapTools.style.display = "none";
    }

    function closeMenu() {
        menu.classList.remove("active");
        mapTools.style.display = "block";
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("mapToolsBody").classList.add("collapsed");

        const inputs = ["dc_cu", "dc_moi", "quy_hoach"];

        inputs.forEach(function (id) {
            let el = document.getElementById(id);

            if (el) {
                el.addEventListener("click", function (e) {
                    if (!isLogin) {
                        e.preventDefault();
                        showLoginPopup();
                        return;
                    }

                    if (el.disabled || el.dataset.locked === "1") {
                        e.preventDefault();

                        const labelMap = {
                            dc_cu: "Địa chính cũ",
                            dc_moi: "Địa chính mới",
                            quy_hoach: "Quy hoạch"
                        };

                        const label = labelMap[id] || id;
                        const box = document.getElementById("vipUploadStatus");

                        if (box) {
                            box.classList.remove("vip-status-error", "vip-status-warning", "vip-status-success");
                            box.classList.add("vip-status-warning");
                            box.innerHTML = `
                                <strong>Đã hết lượt tải</strong><br>
                                Bạn không thể tải thêm file cho mục ${label}.<br>
                                Vui lòng nâng cấp VIP để tiếp tục.
                            `;
                        }
                    }
                });
            }
        });

        if (typeof loadVipUploadStatus === "function") {
            loadVipUploadStatus();
        }
    });

    if (menu) {
        menu.addEventListener("click", function (e) {
            if (e.target === menu) {
                closeMenu();
            }
        });
    }
</script>