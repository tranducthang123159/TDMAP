<footer class="guland-footer">
    <div class="footer-container">

        <!-- CỘT 1 -->
        <div class="footer-col">
            <h4>TẢI ỨNG DỤNG TDMAP-PRO</h4>
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://guland.vn" alt="QR Code">
                <div class="store-links">
                    <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play">
                </div>
            </div>
        </div>

        <!-- CỘT 2 -->
        <div class="footer-col">
            <h4>HỖ TRỢ</h4>
            <ul>
                <li>Điều khoản thỏa thuận</li>
                <li>Chính sách bảo mật</li>
                <li>Quy chế hoạt động</li>
                <li>Giải quyết khiếu nại</li>
                <li>Quy định đăng tin</li>
                <li>Hội nhóm nguồn hàng</li>
                <li>Nguồn nhà đất ký gửi</li>
            </ul>
        </div>

        <!-- CỘT 3 -->
        <div class="footer-col">
            <h4>VỀ TDMAP-PRO</h4>
            <ul>
                <li>Quy hoạch</li>
                <li>Mua bán</li>
                <li>Cho thuê</li>
                <li>Giới thiệu thành viên</li>
                <li>Thông tin quy hoạch</li>
                <li>Luật đất đai 2024</li>
                <li>Giá đất 2025</li>
                <li>Đăng dự án</li>
                <li>Bất động sản</li>
                <li>Địa danh hành chính</li>
            </ul>
        </div>

        <!-- CỘT 4 -->
        <div class="footer-col">
            <h4>MẠNG XÃ HỘI</h4>
            <div class="social">
                <div class="fb">f</div>
                <div class="yt">▶</div>
            </div>

            <h4 style="margin-top:20px;">CHỨNG NHẬN</h4>
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/B%C3%B4%CC%89_C%C3%B4ng_Th%C6%B0%C6%A1ng_Logo.svg/2560px-B%C3%B4%CC%89_C%C3%B4ng_Th%C6%B0%C6%A1ng_Logo.svg.png" 
                 alt="Bộ Công Thương"
                 class="cert">
        </div>

    </div>

    <div class="footer-bottom">
        <div class="logo">TDMAP-PRO</div>
        <p>Email:@gmail.com - Hotline: 098.328.FGF</p>
        <small>
            Guland.vn có trách nhiệm chuyển tải thông tin. Mọi thông tin chỉ có giá trị tham khảo.
        </small>
    </div>
</footer>

<style>
.guland-footer{
    background:#f2f2f2;
    padding:50px 20px 30px;
    font-family:'Segoe UI', sans-serif;
}

.footer-container{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:40px;
}

.footer-col h4{
    font-size:16px;
    margin-bottom:15px;
    font-weight:700;
}

.footer-col ul{
    list-style:none;
    padding:0;
    margin:0;
}

.footer-col ul li{
    font-size:14px;
    margin-bottom:8px;
    color:#444;
    cursor:pointer;
}

.footer-col ul li:hover{
    color:#0072ff;
}

.qr-box{
    display:flex;
    gap:15px;
}

.qr-box img{
    width:110px;
}

.store-links img{
    width:130px;
    margin-bottom:10px;
}

.social{
    display:flex;
    gap:10px;
}

.social div{
    width:40px;
    height:40px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-weight:bold;
    cursor:pointer;
}

.fb{background:#1877f2;}
.yt{background:red;}

.cert{
    width:150px;
    margin-top:10px;
}

.footer-bottom{
    text-align:center;
    margin-top:40px;
}

.footer-bottom .logo{
    font-size:36px;
    font-weight:800;
    color:#d4a017;
}

.footer-bottom p{
    margin:10px 0;
}

.footer-bottom small{
    color:#555;
}

/* RESPONSIVE */
@media(max-width:992px){
    .footer-container{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:600px){
    .footer-container{
        grid-template-columns:1fr;
    }

    .qr-box{
        flex-direction:column;
    }
}
</style>