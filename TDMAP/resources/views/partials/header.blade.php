<link rel="stylesheet" href="{{ asset('css/header.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- MENU SLIDE -->
<div id="categoryMenu" class="menu-overlay">
    <div class="menu-box">
        <div class="text-center mb-3">
            <a href="{{ url('/') }}" class="menu-logo-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="Tài Đỗ Map" class="site-logo">
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

            <div class="menu-item">🗺 Bản đồ quy hoạch</div>
            <div class="menu-item">⭐ Nạp tiền VIP</div>

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

<!-- TOP BUTTONS -->
<!-- TOP TOOLS -->
<div class="map-tools">

    <!-- HEADER -->
    <div class="map-tools-header" onclick="toggleMapTools()">
        <span>🗺 Công cụ địa chính & quy hoạch</span>
        <span id="mapArrow">▲</span>
    </div>

    <!-- BODY -->
    <div class="map-tools-body" id="mapToolsBody">

        <div class="container-fluid p-2">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="mb-3 d-flex justify-content-between align-items-center" onclick="toggleUpload()"
                        style="cursor:pointer">

                        Tải dữ liệu bản đồ

                        <span id="uploadArrow">
                            ▲
                        </span>

                    </h6>


                    <div id="uploadBody">

                        <div class="row g-2">

                            <div class="col-md-4">
                                <label class="form-label small">Địa chính cũ</label>
                                <input type="file" class="form-control form-control-sm" id="dc_cu">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Địa chính mới</label>
                                <input type="file" class="form-control form-control-sm" id="dc_moi">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Quy hoạch</label>
                                <input type="file" class="form-control form-control-sm" id="quy_hoach">
                            </div>

                        </div>

                    </div>

                    <hr>

                    <!-- BUTTON -->
                    <div class="row g-2 mb-3">

                        <div class="col-md-2 col-4">
                            <button class="btn btn-primary btn-sm w-100" onclick="loadMap()">
                                Load Map
                            </button>
                        </div>
                        <hr>



                    </div>


                    <!-- ====================== -->
                    <!-- TRA CỨU THỬA ĐẤT -->
                    <!-- ====================== -->

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
                    <!-- ====================== -->
                    <!-- TÌM TỌA ĐỘ -->
                    <!-- ====================== -->
                    <hr>
                    <div class="search-panel">

                        <div class="search-header" onclick="toggleSearchPanel()">
                            <span>📍 Tìm tọa độ</span>
                            <span id="searchArrow">▲</span>
                        </div>

                        <div id="searchBody" class="search-body collapsed">

                            <div class="search-grid">

                                <!-- VN2000 -->
                                <div class="search-card">
                                    <div class="search-title">📍 VN2000</div>
                                    <div class="search-row">
                                        <input id="vn_input" placeholder="X, Y (vd: 250000, 1200000)">
                                    </div>

                                    <button class="search-btn" onclick="searchVN()">🔎 VN</button>
                                </div>

                                <!-- WGS84 -->
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


                    <!-- ====================== -->
                    <!-- VIP UPLOAD -->
                    <!-- ====================== -->

                    <!-- ====================== -->
                    <!-- VIP UPLOAD -->
                    <!-- ====================== -->


                </div>
            </div>

        </div>

    </div>

</div>





<div id="loginPopup" class="login-popup">

    <div class="popup-box">

        <div class="popup-icon">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h5>Yêu cầu đăng nhập</h5>

        <p>Bạn cần đăng nhập để tải file dữ liệu bản đồ lên hệ thống.</p>

        <div class="popup-buttons">

            <a href="/login" class="btn-login">Đăng nhập</a>

            <a href="/register" class="btn-register">Đăng ký</a>

        </div>

        <button class="btn btn-secondary" onclick="closeLoginPopup()">Đóng</button>

    </div>

</div>

<!-- LEFT TOOLBAR -->
<div class="map-toolbox">

    <!-- MENU -->
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

        <button onclick="startKC()">
            <i class="fa-solid fa-ruler"></i>
            <span>Đo KC</span>
        </button>

        <button onclick="startDT()">
            <i class="fa-solid fa-draw-polygon"></i>
            <span>Đo DT</span>
        </button>

        <button onclick="startGPS()">
            <i class="fa-solid fa-location-dot"></i>
            <span>Đo GPS</span>
        </button>
        <button onclick="undoMeasures()">
            <i class="fa-solid fa-rotate-left"></i>
            <span>Undo</span>
        </button>

        <button onclick="clearMeasures()">
            <i class="fa-solid fa-broom"></i>
            <span>Xóa đo</span>
        </button>

        <button onclick="reloadMap()">
            <i class="fa-solid fa-rotate-right"></i>
            <span>Load</span>
        </button>


    </div>

    <!-- NÚT CHÍNH -->
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
    function toggleToolbox() {

        let menu = document.getElementById("toolboxMenu");

        menu.classList.toggle("active");

    }
</script>
<script>

    let isLogin = {{ auth()->check() ? 'true' : 'false' }};

    function showLoginPopup() {
        document.getElementById("loginPopup").style.display = "flex";
    }

    function closeLoginPopup() {
        document.getElementById("loginPopup").style.display = "none";
    }
    document.addEventListener("DOMContentLoaded", function () {

        const inputs = ["dc_cu", "dc_moi", "quy_hoach"];

        inputs.forEach(function (id) {

            let el = document.getElementById(id);

            el.addEventListener("click", function (e) {

                if (!isLogin) {

                    e.preventDefault();
                    showLoginPopup();

                }

            });

        });

    });

</script>
<script>

    // Ẩn khi load trang
    document.addEventListener("DOMContentLoaded", function () {

        document.getElementById("mapToolsBody").classList.add("collapsed");

    });

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

</script>
<!--  -->
<script>
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
</script>

<script>
    function showExportPDF() {

        let btn = document.getElementById("exportPDFBtn");

        if (btn) {
            btn.style.display = "block";
        }

    }

    function hideExportPDF() {

        let btn = document.getElementById("exportPDFBtn");

        if (btn) {
            btn.style.display = "none";
        }

    }
</script>


<script>
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
</script>
<script>
    const menu = document.getElementById("categoryMenu");
    const mapTools = document.querySelector(".map-tools");

    function openMenu() {
        menu.classList.add("active");

        // Ẩn công cụ địa chính
        mapTools.style.display = "none";
    }

    function closeMenu() {
        menu.classList.remove("active");

        // Hiện lại công cụ địa chính
        mapTools.style.display = "block";
    }

    menu.addEventListener("click", function (e) {
        if (e.target === menu) {
            closeMenu();
        }
    });
</script>

<script>

</script>