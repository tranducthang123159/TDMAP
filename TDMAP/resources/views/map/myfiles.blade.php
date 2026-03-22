<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý file bản đồ</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        html, body{
            width:100%;
            min-height:100vh;
            overflow-x:hidden !important;
            overflow-y:auto !important;
        }

        body{
            font-family:'Segoe UI', sans-serif;
            background:
                linear-gradient(rgba(8, 20, 40, 0.78), rgba(8, 20, 40, 0.82)),
                url('https://cdn.thuvienphapluat.vn/uploads/tintuc/2023/04/04/ban-do-dia-chinh.jpg');
            background-size:cover;
            background-position:center;
            background-attachment:scroll;
            color:#1f2937;
        }

        body::before{
            content:"";
            position:fixed;
            inset:0;
            background:rgba(0,0,0,.18);
            pointer-events:none;
            z-index:0;
        }

        .page-content{
            position:relative;
            z-index:1;
            padding:36px 0 52px;
        }

        .glass-card{
            background:rgba(255,255,255,0.94);
            backdrop-filter:blur(12px);
            -webkit-backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,0.24);
            border-radius:28px;
            box-shadow:0 18px 50px rgba(0,0,0,0.18);
        }

        .section-space{
            margin-bottom:24px;
        }

        /* USER CARD */
        .user-card{
            padding:30px;
        }

        .user-top{
            display:flex;
            align-items:center;
            gap:18px;
            flex-wrap:wrap;
        }

        .avatar{
            width:74px;
            height:74px;
            border-radius:50%;
            background:linear-gradient(135deg,#1d4ed8,#38bdf8);
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            font-size:30px;
            flex-shrink:0;
            box-shadow:0 12px 26px rgba(29,78,216,.28);
        }

        .welcome-title{
            margin:0;
            font-size:34px;
            font-weight:800;
            line-height:1.1;
            color:#0f172a;
        }

        .welcome-title span{
            color:#2563eb;
        }

        .user-email{
            margin-top:6px;
            color:#6b7280;
            font-size:15px;
        }

        .user-divider{
            border:0;
            height:1px;
            background:linear-gradient(to right, transparent, #dbe2ea, transparent);
            margin:24px 0;
        }

        .stat-box{
            background:#f8fbff;
            border:1px solid #e7eef8;
            border-radius:20px;
            padding:20px 20px 18px;
            height:100%;
        }

        .stat-label{
            font-size:14px;
            font-weight:700;
            color:#6b7280;
            margin-bottom:10px;
        }

        .stat-value{
            font-size:42px;
            line-height:1;
            font-weight:800;
            color:#0f172a;
            margin:0;
        }

        .custom-progress{
            height:12px;
            background:#e5e7eb;
            border-radius:999px;
            overflow:hidden;
            margin-top:14px;
        }

        .custom-progress .progress-bar{
            height:100%;
            border-radius:999px;
            background:linear-gradient(90deg,#16a34a,#22c55e);
        }

        /* FILE CARD */
        .file-card{
            padding:24px;
        }

        .file-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:14px;
            margin-bottom:18px;
            flex-wrap:wrap;
        }

        .file-title{
            margin:0;
            font-size:26px;
            font-weight:800;
            color:#0f172a;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .file-subtitle{
            margin:5px 0 0;
            color:#6b7280;
            font-size:15px;
        }

        .file-count{
            background:#eef4ff;
            color:#2563eb;
            padding:10px 16px;
            border-radius:999px;
            font-size:14px;
            font-weight:800;
            white-space:nowrap;
        }

        /* TABLE */
        .table-wrap{
            overflow:auto;
            max-height:640px;
            border-radius:20px;
            border:1px solid #e8edf3;
            background:#fff;
        }

        .table{
            margin-bottom:0;
            min-width:980px;
        }

        .table thead th{
            position:sticky;
            top:0;
            z-index:3;
            background:#f8fafc !important;
            color:#111827;
            font-size:14px;
            font-weight:800;
            text-transform:uppercase;
            border-bottom:1px solid #e5e7eb;
            padding:16px 14px;
            white-space:nowrap;
        }

        .table tbody td{
            vertical-align:middle;
            padding:16px 14px;
            font-size:15px;
            color:#374151;
            border-color:#eef2f7;
            white-space:nowrap;
        }

        .table tbody tr{
            transition:.2s ease;
        }

        .table tbody tr:hover{
            background:#f8fbff;
        }

        .file-id{
            font-weight:800;
            color:#374151;
        }

        .file-name{
            display:flex;
            align-items:center;
            gap:12px;
            min-width:280px;
        }

        .file-icon{
            width:38px;
            height:38px;
            border-radius:12px;
            background:#eef4ff;
            color:#2563eb;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
            font-size:17px;
        }

        .file-name-text{
            font-weight:700;
            color:#111827;
            line-height:1.35;
        }

        .badge-type{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:8px 14px;
            border-radius:999px;
            background:#eaf4fb;
            color:#0284c7;
            font-size:12px;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.35px;
            min-width:96px;
        }

        .size-text{
            font-weight:700;
            color:#374151;
        }

        .btn-download{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            min-width:122px;
            padding:10px 16px;
            border:none;
            border-radius:14px;
            background:linear-gradient(135deg,#22c55e,#16a34a);
            color:#fff;
            font-size:14px;
            font-weight:800;
            text-decoration:none;
            box-shadow:0 10px 22px rgba(34,197,94,.22);
            transition:.2s ease;
        }

        .btn-download:hover{
            transform:translateY(-1px);
            color:#fff;
            box-shadow:0 14px 26px rgba(34,197,94,.30);
        }

        /* SCROLLBAR */
        .table-wrap::-webkit-scrollbar{
            width:10px;
            height:10px;
        }

        .table-wrap::-webkit-scrollbar-track{
            background:#edf2f7;
            border-radius:20px;
        }

        .table-wrap::-webkit-scrollbar-thumb{
            background:#cbd5e1;
            border-radius:20px;
        }

        .table-wrap::-webkit-scrollbar-thumb:hover{
            background:#94a3b8;
        }

        .guland-footer{
            position:relative;
            z-index:1;
            margin-top:16px;
        }

        @media (max-width: 1200px){
            .welcome-title{
                font-size:30px;
            }
        }

        @media (max-width: 992px){
            .user-card,
            .file-card{
                padding:20px;
            }

            .welcome-title{
                font-size:26px;
            }

            .file-title{
                font-size:22px;
            }

            .table-wrap{
                max-height:560px;
            }
        }

        @media (max-width: 768px){
            .page-content{
                padding:22px 0 40px;
            }

            .avatar{
                width:62px;
                height:62px;
                font-size:26px;
            }

            .welcome-title{
                font-size:22px;
            }

            .stat-value{
                font-size:34px;
            }

            .file-subtitle{
                font-size:14px;
            }

            .table-wrap{
                max-height:500px;
            }
        }
    </style>
</head>

<body>
    @include('components.header')

    <div class="container-fluid px-3 px-lg-4 px-xl-5 page-content">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                <!-- USER CARD -->
                <div class="glass-card user-card section-space">
                    <div class="user-top">
                        <div class="avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div>
                            <h4 class="welcome-title">
                                Xin chào, <span>{{ $user->name }}</span>
                            </h4>
                            <div class="user-phone">
                                <i class="bi bi-envelope me-1"></i>
                                {{ $user->email }}
                            </div>
                               <div class="user-email">
                                <i class="bi bi-phone me-1"></i>
                                {{ $user->phone }}
                            </div>
                        </div>
                    </div>

                    <hr class="user-divider">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="stat-box">
                                <div class="stat-label">
                                    <i class="bi bi-folder2-open me-1"></i>
                                    Tổng số file bản đồ
                                </div>
                                <p class="stat-value">{{ count($files) }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="stat-box">
                                <div class="stat-label">
                                    <i class="bi bi-hdd-stack me-1"></i>
                                    Dung lượng sử dụng
                                </div>

                                <div class="custom-progress">
                                    <div class="progress-bar" style="width:60%"></div>
                                </div>

                                <div class="mt-2 text-muted" style="font-size:13px;">
                                    Hệ thống đang hoạt động ổn định
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FILE CARD -->
                <div class="glass-card file-card">
                    <div class="file-head">
                        <div>
                            <h5 class="file-title">
                                <i class="bi bi-map"></i>
                                Danh sách file bản đồ
                            </h5>
                            <p class="file-subtitle">
                                Quản lý, theo dõi và tải xuống các file bản đồ của bạn
                            </p>
                        </div>

                        <div class="file-count">
                            {{ count($files) }} file
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên file</th>
                                    <th>Loại</th>
                                    <th>Kích thước</th>
                                    <th>Tải xuống</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($files as $file)
                                    <tr>
                                        <td>
                                            <span class="file-id">{{ $file->id }}</span>
                                        </td>

                                        <td>
                                            <div class="file-name">
                                                <div class="file-icon">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </div>
                                                <div class="file-name-text">
                                                    {{ $file->file_name }}
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="badge-type">
                                                {{ $file->type }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="size-text">
                                                <i class="bi bi-hdd me-1"></i>
                                                {{ round($file->file_size / 1024 / 1024, 2) }} MB
                                            </span>
                                        </td>

                                        <td>
                                            <a href="/download-map/{{ $file->id }}" class="btn-download">
                                                <i class="bi bi-download"></i>
                                                Tải file
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('components.footer')
</body>
</html>