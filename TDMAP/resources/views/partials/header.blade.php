<link rel="stylesheet" href="{{ asset('css/header.css') }}?v={{ time() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- MENU SLIDE -->
<div id="categoryMenu" class="menu-overlay">
    <div class="menu-box">
        <div class="text-center mb-3">
            <h2 class="menu-logo logo">TDMAP-PRO</h2>
            <p>THÔNG TIN THẬT - GIÁ TRỊ THẬT</p>
        </div>

        <div class="menu-grid">
            @guest
                <div class="menu-item">
                    <a href="{{ route('login') }}" class="menu-link">
                        👤 Đăng nhập
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('register') }}" class="menu-link">
                        🧑‍🤝‍🧑 Đăng ký
                    </a>
                </div>
            @endguest

            @auth
                <div class="menu-item">
                    👋 Xin chào {{ Auth::user()->name }}
                </div>

                @role('admin')
                <div class="menu-item">
                    🛠 <a href="{{ url('/admin') }}" class="menu-link">
                        Trang quản trị admin
                    </a>
                </div>
                @endrole
            @endauth
            <div class="menu-item">🏢 Mua bán</div>
            <div class="menu-item">🏠 Cho thuê</div>
            <div class="menu-item">🗺 Bản đồ quy hoạch</div>
            <div class="menu-item">📂 Kho bản đồ quy hoạch</div>
            <div class="menu-item">📍 Bản đồ giá nhà đất</div>
            <div class="menu-item">🏗 Dự án bất động sản</div>
            <div class="menu-item">📅 Bảng giá đất 2026</div>
            <div class="menu-item">👨‍💼 Danh sách môi giới</div>
            <div class="menu-item">⭐ Nạp tiền đăng tin VIP</div>

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
    <div class="logo">TDMAP-PRO</div>

    <div class="menu-right">
        <span class="hide-mobile menu-trigger">Mua bán</span>
        <span class="hide-mobile menu-trigger">Check giá</span>
        <span class="menu-trigger" onclick="openMenu()">☰ Danh mục</span>
    </div>
</div>

<!-- TOP BUTTONS -->
<!-- TOP TOOLS -->
<div class="map-tools">

    <div class="map-tools-header" onclick="toggleMapTools()">
        <span>🗺 Công cụ địa chính & quy hoạch</span>
        <span id="mapArrow">▲</span>
    </div>

    <div class="map-tools-body collapsed" id="mapToolsBody">



        <div id="info">
            <!-- <b>Click thửa đất để xem thông tin</b> -->
        </div>

        <!-- <div id="map"></div> -->


        <div id="toolbar">

            <input type="file" id="geojsonFile">

            <input type="text" id="to" placeholder="Tờ">
            <input type="text" id="thua" placeholder="Thửa">

            <button onclick="loadMap()">Load Map</button>
            <button onclick="searchParcel()">TÌM</button>
            <button onclick="clearLabels()">Clear</button>

        </div>



    </div>
</div>
<!-- LEFT TOOLBAR -->
<div class="left-toolbar">

    <button class="circle-btn" onclick="map.zoomIn()">
        <i class="fa-solid fa-plus"></i>
    </button>

    <button class="circle-btn" onclick="map.zoomOut()">
        <i class="fa-solid fa-minus"></i>
    </button>

    <button class="circle-btn" onclick="locateMe()">
        <i class="fa-solid fa-location-crosshairs"></i>
    </button>

    <button class="circle-btn" onclick="reloadMap()">
        <i class="fa-solid fa-rotate-right"></i>
    </button>

    <button class="circle-btn" onclick="clearMarker()">
        <i class="fa-solid fa-trash"></i>
    </button>

</div>

<!-- BOTTOM FILTER -->
<div class="bottom-filter">
    <button class="btn btn-light shadow">QH 2030</button>
    <button class="btn btn-light shadow">KH 2026</button>
    <button class="btn btn-primary shadow">QH phân khu</button>
    <button class="btn btn-light shadow">QH 2040</button>
    <button class="btn btn-light shadow">QH khác</button>
</div>

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
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="{{ asset('js/dulieumap.js') }}"></script>