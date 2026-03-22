<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Thanh toán VIP</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
        }

        html, body{
            width:100%;
            min-height:100vh;
            overflow-x:hidden !important;
            overflow-y:auto !important;
        }

        body{
            font-family:Arial, sans-serif;
            color:#1f2937;
            background:
                radial-gradient(circle at top left, rgba(245,158,11,0.12), transparent 320px),
                radial-gradient(circle at top right, rgba(59,130,246,0.10), transparent 360px),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }

        .vip-page{
            min-height:100vh;
            padding:40px 20px 60px;
        }

        .container{
            width:100%;
            max-width:1250px;
            margin:0 auto;
        }

        .hero{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:30px;
            flex-wrap:wrap;
            margin-bottom:32px;
            padding:36px;
            border-radius:28px;
            background:rgba(255,255,255,0.82);
            backdrop-filter:blur(10px);
            border:1px solid rgba(255,255,255,0.7);
            box-shadow:0 18px 50px rgba(15,23,42,0.08);
        }

        .hero-left{
            flex:1;
            min-width:300px;
        }

        .vip-badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 14px;
            border-radius:999px;
            background:#fff7ed;
            border:1px solid #fed7aa;
            color:#c2410c;
            font-size:13px;
            font-weight:800;
            margin-bottom:16px;
        }

        .page-title{
            font-size:42px;
            font-weight:900;
            line-height:1.15;
            color:#0f172a;
            margin-bottom:12px;
        }

        .page-sub{
            font-size:17px;
            color:#64748b;
            line-height:1.7;
            max-width:720px;
        }

        .hero-right{
            display:flex;
            gap:14px;
            flex-wrap:wrap;
        }

        .hero-mini{
            min-width:150px;
            background:#ffffff;
            border:1px solid #e5e7eb;
            border-radius:20px;
            padding:18px 16px;
            box-shadow:0 12px 25px rgba(15,23,42,0.05);
        }

        .hero-mini strong{
            display:block;
            font-size:22px;
            color:#0f172a;
            margin-bottom:6px;
        }

        .hero-mini span{
            font-size:14px;
            color:#64748b;
        }

        .section-title{
            font-size:24px;
            font-weight:900;
            color:#0f172a;
            margin-bottom:18px;
        }

        .row{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
            gap:24px;
        }

        .card{
            position:relative;
            background:rgba(255,255,255,0.95);
            border-radius:26px;
            border:1px solid #e5e7eb;
            box-shadow:0 16px 35px rgba(15,23,42,0.07);
            overflow:hidden;
            transition:.25s ease;
        }

        .card:hover{
            transform:translateY(-6px);
            box-shadow:0 22px 45px rgba(15,23,42,0.12);
        }

        .card::before{
            content:"";
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:6px;
            background:linear-gradient(90deg, #f59e0b, #f97316);
        }

        .card-body{
            padding:28px;
        }

        .plan-chip{
            display:inline-block;
            padding:7px 12px;
            border-radius:999px;
            background:#eff6ff;
            color:#1d4ed8;
            font-size:12px;
            font-weight:800;
            margin-bottom:14px;
        }

        .card-title{
            margin:0 0 10px;
            font-size:28px;
            font-weight:900;
            color:#0f172a;
        }

        .card-price{
            font-size:46px;
            font-weight:900;
            color:#b45309;
            margin-bottom:18px;
            letter-spacing:-1px;
        }

        .card-price small{
            font-size:18px;
            color:#64748b;
            font-weight:700;
        }

        .card-meta{
            color:#475569;
            line-height:1.85;
            margin-bottom:22px;
            font-size:15px;
        }

        .card-meta .meta-line{
            padding:10px 0;
            border-bottom:1px dashed #e5e7eb;
        }

        .card-meta .meta-line:last-child{
            border-bottom:none;
        }

        .btn{
            width:100%;
            border:none;
            border-radius:16px;
            padding:15px 18px;
            font-size:16px;
            font-weight:800;
            cursor:pointer;
            background:linear-gradient(135deg, #f59e0b, #f97316);
            color:#fff;
            box-shadow:0 10px 24px rgba(249,115,22,0.28);
            transition:.2s ease;
        }

        .btn:hover{
            transform:translateY(-2px);
            opacity:.98;
        }

        .payment-box{
            display:none;
            margin-top:34px;
            background:rgba(255,255,255,0.96);
            border-radius:30px;
            box-shadow:0 18px 50px rgba(15,23,42,0.10);
            border:1px solid #e5e7eb;
            padding:32px;
            overflow:hidden;
        }

        .payment-box.active{
            display:block;
            animation:fadeUp .35s ease;
        }

        @keyframes fadeUp{
            from{
                opacity:0;
                transform:translateY(16px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        .payment-head{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            flex-wrap:wrap;
            margin-bottom:26px;
        }

        .payment-head-left h2{
            font-size:30px;
            font-weight:900;
            color:#0f172a;
            margin-bottom:6px;
        }

        .payment-head-left p{
            color:#64748b;
            line-height:1.6;
        }

        .safe-badge{
            padding:10px 16px;
            border-radius:999px;
            background:#ecfdf5;
            color:#166534;
            border:1px solid #bbf7d0;
            font-size:14px;
            font-weight:800;
        }

        .payment-grid{
            display:grid;
            grid-template-columns:360px 1fr;
            gap:30px;
            align-items:start;
        }

        .qr-card{
            background:linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border:1px solid #e5e7eb;
            border-radius:24px;
            padding:22px;
            text-align:center;
            box-shadow:0 10px 25px rgba(15,23,42,0.05);
        }

        .qr-card img{
            width:100%;
            max-width:280px;
            margin:0 auto 16px;
            display:block;
            border-radius:18px;
            border:1px solid #e5e7eb;
            background:#fff;
            padding:8px;
        }

        .qr-note{
            font-size:14px;
            color:#64748b;
            line-height:1.7;
        }

        .payment-info{
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:24px;
            padding:24px;
        }

        .payment-title{
            font-size:28px;
            font-weight:900;
            margin-bottom:18px;
            color:#0f172a;
        }

        .info-list{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:14px;
            margin-bottom:18px;
        }

        .info-item{
            background:#f8fafc;
            border:1px solid #e5e7eb;
            border-radius:18px;
            padding:16px;
        }

        .info-label{
            font-size:13px;
            font-weight:700;
            color:#64748b;
            margin-bottom:6px;
        }

        .info-value{
            font-size:17px;
            font-weight:800;
            color:#0f172a;
            word-break:break-word;
        }

        .transfer-box{
            margin-top:8px;
            background:#fff7ed;
            border:1px solid #fed7aa;
            border-radius:20px;
            padding:18px;
        }

        .transfer-box-title{
            font-size:15px;
            font-weight:800;
            color:#9a3412;
            margin-bottom:10px;
        }

        .content-code{
            display:inline-block;
            margin-top:4px;
            background:#ffffff;
            color:#1d4ed8;
            padding:12px 16px;
            border-radius:14px;
            font-weight:900;
            border:1px dashed #93c5fd;
            font-size:16px;
            letter-spacing:.4px;
        }

        .notice{
            margin-top:18px;
            background:#fffbeb;
            border:1px solid #fde68a;
            color:#92400e;
            padding:16px 18px;
            border-radius:18px;
            line-height:1.7;
            font-size:15px;
        }

        .confirm-btn{
            margin-top:18px;
            width:100%;
            background:linear-gradient(135deg, #16a34a, #22c55e);
            color:#fff;
            border:none;
            border-radius:16px;
            padding:16px 18px;
            font-size:16px;
            font-weight:900;
            cursor:pointer;
            box-shadow:0 10px 24px rgba(34,197,94,0.26);
            transition:.2s ease;
        }

        .confirm-btn:hover{
            transform:translateY(-2px);
        }

        .confirm-btn:disabled{
            opacity:.75;
            cursor:not-allowed;
            transform:none;
        }

        .status{
            display:none;
            margin-top:18px;
            padding:16px 18px;
            border-radius:18px;
            line-height:1.7;
            font-size:15px;
        }

        .status.active{
            display:block;
        }

        .status.waiting{
            background:#eff6ff;
            border:1px solid #bfdbfe;
            color:#1d4ed8;
        }

        .status.success{
            background:#ecfdf5;
            border:1px solid #bbf7d0;
            color:#166534;
        }

        .status.error{
            background:#fef2f2;
            border:1px solid #fecaca;
            color:#b91c1c;
        }

        @media(max-width: 992px){
            .payment-grid{
                grid-template-columns:1fr;
            }

            .info-list{
                grid-template-columns:1fr;
            }
        }

        @media(max-width: 768px){
            .vip-page{
                padding:24px 14px 40px;
            }

            .hero{
                padding:24px 18px;
                border-radius:22px;
            }

            .page-title{
                font-size:30px;
            }

            .page-sub{
                font-size:15px;
            }

            .card-title{
                font-size:24px;
            }

            .card-price{
                font-size:36px;
            }

            .payment-box{
                padding:20px;
                border-radius:24px;
            }

            .payment-head-left h2{
                font-size:24px;
            }

            .payment-title{
                font-size:24px;
            }
        }
    </style>
</head>
<body>
    @include('components.header')

    <div class="vip-page">
        <div class="container">

            <div class="hero">
                <div class="hero-left">
                    <div class="vip-badge">⭐ Nâng cấp tài khoản VIP</div>
                    <div class="page-title">Chọn gói VIP phù hợp với nhu cầu của bạn</div>
                    <div class="page-sub">
                        Mở rộng lượt tải dữ liệu, tăng giới hạn sử dụng và trải nghiệm các tính năng mạnh hơn
                        với giao diện thanh toán nhanh, rõ ràng và dễ dùng.
                    </div>
                </div>

                <div class="hero-right">
                    <div class="hero-mini">
                        <strong>3 Gói</strong>
                        <span>Linh hoạt theo nhu cầu</span>
                    </div>
                    <div class="hero-mini">
                        <strong>Nhanh</strong>
                        <span>Thanh toán quét QR</span>
                    </div>
                    <div class="hero-mini">
                        <strong>An toàn</strong>
                        <span>Xác nhận rõ nội dung CK</span>
                    </div>
                </div>
            </div>

            <div class="section-title">Danh sách gói VIP</div>

            <div class="row">
                @foreach($packages as $package)
                    <div class="card">
                        <div class="card-body">
                            <div class="plan-chip">GÓI NÂNG CẤP</div>
                            <h3 class="card-title">{{ $package['name'] }}</h3>
                            <div class="card-price">
                                {{ number_format($package['price'], 0, ',', '.') }}đ
                            </div>

                            <div class="card-meta">
                                <div class="meta-line">
                                    Thời hạn: <b>{{ $package['duration'] }}</b>
                                </div>
                                <div class="meta-line">
                                    Quyền lợi: <b>{{ $package['limit_text'] }}</b>
                                </div>
                            </div>

                            <button class="btn" onclick="createOrder('{{ $package['code'] }}')">
                                Chọn {{ strtoupper($package['code']) }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="paymentBox" class="payment-box">
                <div class="payment-head">
                    <div class="payment-head-left">
                        <h2>Thông tin thanh toán</h2>
                        <p>Quét mã QR hoặc chuyển khoản đúng nội dung để hệ thống/admin xác nhận nhanh hơn.</p>
                    </div>
                    <div class="safe-badge">Bảo mật • Rõ ràng • Dễ kiểm tra</div>
                </div>

                <div class="payment-grid">
                    <div class="qr-card">
                        <img id="qrCode" src="" alt="QR Code">
                        <div class="qr-note">
                            Quét mã bằng ứng dụng ngân hàng để thanh toán nhanh chóng và chính xác.
                        </div>
                    </div>

                    <div class="payment-info">
                        <div class="payment-title" id="paymentTitle">Thanh toán VIP</div>

                        <div class="info-list">
                            <div class="info-item">
                                <div class="info-label">Ngân hàng</div>
                                <div class="info-value" id="bankName"></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Số tài khoản</div>
                                <div class="info-value" id="accountNo"></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Chủ tài khoản</div>
                                <div class="info-value" id="accountName"></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Số tiền</div>
                                <div class="info-value" id="amountText"></div>
                            </div>
                        </div>

                        <div class="transfer-box">
                            <div class="transfer-box-title">Nội dung chuyển khoản</div>
                            <span id="transactionCode" class="content-code"></span>
                        </div>

                        <div class="notice">
                            Vui lòng <b>giữ đúng nội dung chuyển khoản</b> để admin xác nhận giao dịch chính xác.
                            Nếu nhập sai nội dung, thời gian xử lý có thể chậm hơn.
                        </div>

                        <button id="confirmBtn" class="confirm-btn" onclick="confirmPayment()">
                            Tôi đã thanh toán
                        </button>

                        <div id="paymentStatus" class="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')

    <script>
        let currentTransactionId = null;
        let statusCheckInterval = null;

        function formatMoney(amount) {
            return Number(amount).toLocaleString('vi-VN') + 'đ';
        }

        function setStatus(type, html) {
            const statusBox = document.getElementById('paymentStatus');
            statusBox.className = 'status active ' + type;
            statusBox.innerHTML = html;
        }

        function stopStatusChecking() {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
            }
        }

        function createOrder(vip) {
            stopStatusChecking();

            axios.post('{{ route('vip.payment.order') }}', { vip: vip }, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                const data = response.data;

                if (data.success) {
                    currentTransactionId = data.transaction_id;

                    document.getElementById('paymentBox').classList.add('active');
                    document.getElementById('paymentTitle').innerText = data.vip.toUpperCase();
                    document.getElementById('transactionCode').innerText = data.content;
                    document.getElementById('amountText').innerText = formatMoney(data.amount);
                    document.getElementById('qrCode').src = data.qr_url;
                    document.getElementById('bankName').innerText = data.bank_name;
                    document.getElementById('accountNo').innerText = data.account_no;
                    document.getElementById('accountName').innerText = data.account_name;

                    const confirmBtn = document.getElementById('confirmBtn');
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = 'Tôi đã thanh toán';

                    document.getElementById('paymentStatus').className = 'status';
                    document.getElementById('paymentStatus').innerHTML = '';

                    document.getElementById('paymentBox').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(error => {
                console.error(error);
                alert('Không tạo được đơn thanh toán');
            });
        }

        function confirmPayment() {
            if (!currentTransactionId) {
                alert('Chưa có giao dịch để xác nhận');
                return;
            }

            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerText = 'Đang gửi xác nhận...';

            axios.post('/vip/payment/confirmed/' + currentTransactionId, {}, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                confirmBtn.innerText = 'Đang kiểm tra...';

                setStatus(
                    'waiting',
                    '<b>Đã ghi nhận yêu cầu của bạn.</b><br>Đơn hàng đang được xử lý, vui lòng chờ admin kiểm tra và xác nhận thanh toán.'
                );

                checkPaymentStatus();

                stopStatusChecking();
                statusCheckInterval = setInterval(() => {
                    checkPaymentStatus();
                }, 6000);
            })
            .catch(error => {
                console.error(error);

                confirmBtn.disabled = true;
                confirmBtn.innerText = 'Đang kiểm tra...';

                setStatus(
                    'waiting',
                    '<b>Yêu cầu của bạn đã được ghi nhận.</b><br>Hệ thống đang kiểm tra giao dịch, vui lòng chờ admin xác nhận thanh toán.'
                );

                checkPaymentStatus();

                stopStatusChecking();
                statusCheckInterval = setInterval(() => {
                    checkPaymentStatus();
                }, 6000);
            });
        }

        function checkPaymentStatus() {
            if (!currentTransactionId) return;

            axios.get('/vip/payment/status/' + currentTransactionId)
                .then(response => {
                    const data = response.data;

                    if (!data.success) {
                        setStatus(
                            'waiting',
                            '<b>Đơn hàng đang được xử lý.</b><br>Vui lòng chờ thêm trong giây lát để hệ thống cập nhật trạng thái.'
                        );
                        return;
                    }

                    if (data.status === 'completed') {
                        stopStatusChecking();

                        const confirmBtn = document.getElementById('confirmBtn');
                        confirmBtn.disabled = true;
                        confirmBtn.innerText = 'Đã xác nhận';

                        setStatus(
                            'success',
                            '<b>Thanh toán thành công.</b><br>Admin đã xác nhận giao dịch và gói VIP đã được bật cho tài khoản của bạn.'
                        );
                    } else if (data.status === 'cancelled') {
                        stopStatusChecking();

                        const confirmBtn = document.getElementById('confirmBtn');
                        confirmBtn.disabled = false;
                        confirmBtn.innerText = 'Tôi đã thanh toán';

                        setStatus(
                            'error',
                            '<b>Giao dịch đã bị hủy.</b><br>Vui lòng liên hệ admin để được hỗ trợ.'
                        );
                    } else {
                        setStatus(
                            'waiting',
                            '<b>Bạn vui lòng chờ trong giây lát.</b><br>Giao dịch đang được xử lý, gói VIP sắp được bật.'
                        );
                    }
                })
                .catch(error => {
                    console.error(error);
                    setStatus(
                        'waiting',
                        '<b>Đơn hàng đang được xử lý.</b><br>Hệ thống sẽ tiếp tục cập nhật trạng thái trong ít giây nữa.'
                    );
                });
        }
    </script>
</body>
</html>