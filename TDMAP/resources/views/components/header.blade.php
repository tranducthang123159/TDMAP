<link rel="stylesheet" href="{{ asset('css/loginhear.css') }}">


<!-- MENU SLIDE -->
<div id="categoryMenu" class="menu-overlay">
    <div class="menu-box">
        <div class="text-center mb-3">
            <h2 class="menu-logo logo">TDMAP-PRO</h2>
               <img src="{{ asset('images/logo.png') }}" alt="Tài Đỗ Map" class="site-logo">
            <p class="" style="text-align: center;">THÔNG TIN THẬT - GIÁ TRỊ THẬT</p>
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
      
        <span class="menu-trigger" onclick="openMenu()">☰ Danh mục</span>
    </div>
</div>




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