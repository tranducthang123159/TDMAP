<style>
.map-tools{
    position:absolute;
    top:70px;
    left:50%;
    transform:translateX(-50%);
    width:94%;
    max-width:1250px;
    backdrop-filter:blur(8px);
    background:rgba(255,255,255,0.92);
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,0.07);
    z-index:3000;
    font-family:Inter, sans-serif;
}

/* HEADER */
.map-tools-header{
    padding:10px 18px;
    font-weight:600;
    font-size:14px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    cursor:pointer;
}

/* BODY */
.map-tools-body{
    padding:14px 18px 18px 18px;
    transition:all 0.3s ease;
}

.map-tools-body.collapsed{
    display:none;
}

/* ROW */
.map-row{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:end;
    margin-bottom:10px;
}

/* GROUP */
.map-group{
    display:flex;
    flex-direction:column;
    font-size:11px;
    color:#666;
}

.map-group input,
.map-group select{
    margin-top:4px;
}

/* INPUT */
.map-row input,
.map-row select{
    padding:7px 10px;
    border-radius:8px;
    border:1px solid #e5e7eb;
    font-size:13px;
    background:white;
}

/* CHECKBOX */
.map-row label{
    font-size:13px;
    display:flex;
    align-items:center;
    gap:4px;
    color:#555;
}

/* BUTTON */
.map-row button{
    border:none;
    padding:7px 12px;
    border-radius:8px;
    font-size:13px;
    cursor:pointer;
    transition:0.2s ease;
}

.btn-primary{
    background:#0d6efd;
    color:white;
}

.btn-success{
    background:#198754;
    color:white;
}

.btn-warning{
    background:#ffc107;
}

.btn-soft{
    background:#f1f3f5;
}

.map-row button:hover{
    transform:translateY(-1px);
}
.map-tools-body.collapsed{
    display:none;
}

.map-tools{
    position:absolute;
    top:70px;
    left:50%;
    transform:translateX(-50%);
    width:94%;
    max-width:1250px;

    backdrop-filter:blur(12px);
    background:rgba(255,255,255,0.75);

    border-radius:20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.06);

    z-index:3000;
    font-family:Inter, sans-serif;

    transition:all 0.35s ease;
}

/* Khi mở thì nổi hơn */
.map-tools.active{
    box-shadow:0 18px 45px rgba(0,0,0,0.12);
    background:rgba(255,255,255,0.9);
}

/* HEADER */
.map-tools-header{
    padding:12px 20px;
    font-weight:600;
    font-size:14px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    cursor:pointer;
    transition:all 0.3s ease;
}

/* Header đổi màu nhẹ khi active */
.map-tools.active .map-tools-header{
    background:rgba(13,110,253,0.06);
}

/* Mũi tên */
.arrow{
    transition:transform 0.4s cubic-bezier(.34,1.56,.64,1);
    font-size:14px;
}

/* BODY */
.map-tools-body{
    overflow:hidden;
    padding:16px 20px 20px 20px;

    max-height:600px;
    opacity:1;
    transform:translateY(0);

    transition:
        max-height 0.5s cubic-bezier(.4,0,.2,1),
        opacity 0.3s ease,
        transform 0.4s ease,
        padding 0.3s ease;
}

/* Khi đóng */
.map-tools-body.collapsed{
    max-height:0;
    opacity:0;
    transform:translateY(-15px);
    padding-top:0;
    padding-bottom:0;
}

/* Bounce nhẹ khi mở */
.map-tools-body.opening{
    animation:bounceOpen 0.45s ease;
}

@keyframes bounceOpen{
    0%{ transform:translateY(-20px); }
    60%{ transform:translateY(5px); }
    100%{ transform:translateY(0); }
}
/* MOBILE */
@media(max-width:768px){
    .map-row{
        flex-direction:column;
        align-items:stretch;
    }

    .map-row input,
    .map-row select,
    .map-row button{
        width:100%;
    }
}

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
    /* position: absolute; */
    top: 0;
    width: 100%;
    z-index: 2000;
    background: #fff;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content:space-between;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.logo {
    font-weight: 700;
    font-size: 30px;
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
    padding: 0px 16px;
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
        font-size: 25px;
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
        font-size: 25px;
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
</style>
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
        👤 <a href="{{ route('login') }}">Đăng nhập</a>
    </div>
    <div class="menu-item">
        🧑‍🤝‍🧑 <a href="{{ route('register') }}">Đăng ký</a>
    </div>
@endguest

@auth
    <div class="menu-item">
        👋 Xin chào {{ Auth::user()->name }}
    </div>

    @role('admin')
        <div class="menu-item">
            🛠 <a href="{{ url('/admin/dashboard') }}">
                Trang quản trị
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
                <button type="submit" 
                    style="border:none;background:none;color:#dc3545;width:100%;text-align:left;">
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
<!-- <div class="map-tools">

    <div class="map-tools-header" onclick="toggleMapTools()">
        <span>🗺 Công cụ địa chính & quy hoạch</span>
        <span id="mapArrow">▲</span>
    </div>

    <div class="map-tools-body collapsed" id="mapToolsBody">

        <div class="map-row">

            <div class="map-group">
                <label>ĐC MỚI</label>
                <input type="text" placeholder="Zip/Shp/Json">
            </div>

            <div class="map-group">
                <label>ĐC CŨ</label>
                <input type="text" placeholder="Zip/Shp/Json">
            </div>

            <div class="map-group">
                <label>QUY HOẠCH</label>
                <input type="text" placeholder="Zip/Shp/Json">
            </div>

            <div class="map-group">
                <label>TỈNH/TP</label>
                <select>
                    <option>Bình Thuận + Đắk Lắk + Đắk Nông</option>
                </select>
            </div>

            <button class="btn-primary">Tìm</button>
            <button class="btn-soft">✕</button>

        </div>

   
        <div class="map-row">

            <input type="text" placeholder="Tờ">
            <input type="text" placeholder="Thửa">
            <input type="text" placeholder="Tờ cũ">
            <input type="text" placeholder="Tên chủ">

            <button class="btn-success">Tìm tọa độ</button>

            <label><input type="checkbox" checked> ĐC</label>
            <label><input type="checkbox" checked> ĐC Cũ</label>
            <label><input type="checkbox" checked> QH</label>
            <label><input type="checkbox" checked> Cạnh</label>

            <button class="btn-warning">KC</button>
            <button class="btn-warning">DT</button>
            <button class="btn-soft">Xóa</button>

        </div>

    </div>
</div>

<div class="left-toolbar">
    <button class="circle-btn">+</button>
    <button class="circle-btn">−</button>
    <button class="circle-btn"></button>
    <button class="circle-btn">↻</button>
    <button class="circle-btn text-danger">🗑</button>
</div>


<div class="bottom-filter">
    <button class="btn btn-light shadow">QH 2030</button>
    <button class="btn btn-light shadow">KH 2026</button>
    <button class="btn btn-primary shadow">QH phân khu</button>
    <button class="btn btn-light shadow">QH 2040</button>
    <button class="btn btn-light shadow">QH khác</button>
</div> -->

<script>
function toggleMapTools(){
    const body = document.getElementById("mapToolsBody");
    const wrapper = document.querySelector(".map-tools");
    const arrow = document.getElementById("mapArrow");

    const isCollapsed = body.classList.contains("collapsed");

    body.classList.toggle("collapsed");
    wrapper.classList.toggle("active");

    if(isCollapsed){
        arrow.style.transform = "rotate(180deg)";
        body.classList.add("opening");

        setTimeout(()=>{
            body.classList.remove("opening");
        },450);
    }else{
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

menu.addEventListener("click", function(e){
    if(e.target === menu){
        closeMenu();
    }
});
</script>