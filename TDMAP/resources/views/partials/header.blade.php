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
</style>
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

        <!-- DÒNG 1 -->
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

        <!-- DÒNG 2 -->
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
<script>
function toggleMapTools(){
    const body = document.getElementById("mapToolsBody");
    const arrow = document.getElementById("mapArrow");

    body.classList.toggle("collapsed");
    arrow.innerHTML = body.classList.contains("collapsed") ? "▼" : "▲";
}
</script>
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