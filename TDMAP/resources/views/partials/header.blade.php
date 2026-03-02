<style>
.top-tools-wrapper{
    position:absolute;
    top:70px;
    left:50%;
    transform:translateX(-50%);
    width:95%;
    max-width:1300px;
    background:white;
    border-radius:16px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    z-index:3000;
    overflow:hidden;
    font-family:Segoe UI, sans-serif;
}

/* HEADER */
.top-tools-header{
    padding:12px 18px;
    background:#f8f9fa;
    font-weight:600;
    display:flex;
    justify-content:space-between;
    align-items:center;
    cursor:pointer;
}

.tools-arrow{
    font-size:16px;
    transition:transform 0.3s ease;
}

/* CONTENT */
.top-tools-content{
    padding:14px 18px;
    transition:max-height 0.4s ease, opacity 0.3s ease;
    overflow:hidden;
}

.top-tools-content.collapsed{
    max-height:0;
    padding-top:0;
    padding-bottom:0;
    opacity:0;
}

/* ROW */
.tools-row{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
    margin-bottom:10px;
}

/* INPUT */
.tools-row input,
.tools-row select{
    padding:6px 10px;
    border-radius:8px;
    border:1px solid #e4e6eb;
    background:#f9fafb;
    font-size:13px;
}

/* BUTTON */
.tools-row button{
    border:none;
    padding:6px 12px;
    border-radius:8px;
    font-size:13px;
    cursor:pointer;
}

.btn-search{
    background:#ff7a00;
    color:white;
}

.btn-green{
    background:#198754;
    color:white;
}

.btn-yellow{
    background:#ffc107;
}

.btn-gray{
    background:#e9ecef;
}

/* CHECKBOX */
.tools-row label{
    font-size:13px;
    display:flex;
    align-items:center;
    gap:4px;
}

/* MOBILE */
@media(max-width:768px){
    .tools-row{
        flex-direction:column;
        align-items:stretch;
    }

    .tools-row input,
    .tools-row select,
    .tools-row button{
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
<!-- TOOLBAR WRAPPER -->
<div class="top-tools-wrapper">

    <!-- HEADER LUÔN HIỆN -->
    <div class="top-tools-header" onclick="toggleTools()">
        <span>🗂 Công cụ địa chính & quy hoạch</span>
        <span class="tools-arrow" id="toolsArrow">▲</span>
    </div>

    <!-- CONTENT CÓ THỂ THU GỌN -->
    <div class="top-tools-content" id="toolsContent">

        <!-- DÒNG 1 -->
        <div class="tools-row">
            <input type="text" placeholder="File địa chính...">
            <input type="text" placeholder="File ĐC cũ...">
            <input type="text" placeholder="File quy hoạch...">

            <select>
                <option>Bình Thuận + Đắk Lắk + Đắk Nông</option>
            </select>

            <button class="btn-search">🔎 TÌM</button>
            <button class="btn-gray">✖</button>
        </div>

        <!-- DÒNG 2 -->
        <div class="tools-row">
            <input type="text" placeholder="Tờ">
            <input type="text" placeholder="Thửa">
            <input type="text" placeholder="Tờ cũ">
            <input type="text" placeholder="Tên chủ">

            <button class="btn-green">▶ Tìm tọa độ</button>

            <label><input type="checkbox" checked> Hiện ĐC</label>
            <label><input type="checkbox" checked> Hiện ĐC Cũ</label>
            <label><input type="checkbox" checked> Hiện QH</label>
            <label><input type="checkbox" checked> Hiện Cạnh</label>

            <button class="btn-yellow">📏 Đo KC</button>
            <button class="btn-yellow">📐 Đo DT</button>
            <button class="btn-gray">✖ Xóa</button>
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
function toggleTools(){
    const content = document.getElementById("toolsContent");
    const arrow = document.getElementById("toolsArrow");

    content.classList.toggle("collapsed");

    if(content.classList.contains("collapsed")){
        arrow.style.transform = "rotate(180deg)";
    } else {
        arrow.style.transform = "rotate(0deg)";
    }
}
</script>