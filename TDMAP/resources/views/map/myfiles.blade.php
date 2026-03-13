<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">
    <title>Quản lý file bản đồ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {

            background:
                linear-gradient(rgba(10, 25, 47, 0.85), rgba(10, 25, 47, 0.85)),
                url('https://cdn.thuvienphapluat.vn/uploads/tintuc/2023/04/04/ban-do-dia-chinh.jpg');
            background-size: cover;

            background-position: center;

            background-attachment: fixed;

            font-family: 'Segoe UI', sans-serif;

        }

        /* lớp phủ tối để dễ đọc */

        body::before {

            content: "";

            position: fixed;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.45);

            z-index: -1;

        }

        /* card kính */

        .glass {

            background: rgba(255, 255, 255, 0.92);

            backdrop-filter: blur(10px);

            border-radius: 15px;

            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);

        }

        /* avatar */

        .avatar {

            width: 60px;
            height: 60px;

            border-radius: 50%;

            background: linear-gradient(45deg, #007bff, #00c6ff);

            display: flex;

            align-items: center;

            justify-content: center;

            color: white;

            font-size: 26px;

        }

        /* table hover */

        .table tbody tr {

            transition: 0.25s;

        }

        .table tbody tr:hover {

            background: #f4f8ff;

            transform: scale(1.01);

        }

        /* button */

        .btn-download {

            background: linear-gradient(45deg, #00c853, #64dd17);

            border: none;

            color: white;

        }

        .btn-download:hover {

            transform: scale(1.05);

        }
    </style>

</head>

<body>
    @include('components.header')
    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <!-- USER CARD -->
                <div class="glass p-4 mb-4">

                    <div class="d-flex align-items-center">

                        <div class="avatar me-3">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div>
                            <h4 class="mb-1">
                                Xin chào,
                                <span class="text-primary fw-bold">
                                    {{ $user->name }}
                                </span>
                            </h4>

                            <div class="text-muted">
                                <i class="bi bi-envelope"></i>
                                {{ $user->email }}
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        <div class="col-md-6">

                            <p class="mb-1">
                                <i class="bi bi-folder2-open"></i>
                                Tổng số file
                            </p>

                            <h5>{{ count($files) }}</h5>

                        </div>

                        <div class="col-md-6">

                            <p class="mb-1">
                                <i class="bi bi-hdd"></i>
                                Dung lượng sử dụng
                            </p>

                            <div class="progress">
                                <div class="progress-bar bg-success" style="width:60%">
                                </div>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- FILE LIST -->
                <div class="glass p-3">

                    <h5 class="mb-3">
                        <i class="bi bi-map"></i>
                        Danh sách file bản đồ
                    </h5>

                    <table class="table align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Tên file</th>
                                <th>Loại</th>
                                <th>Kích thước</th>
                                <th>Tải</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($files as $file)

                                <tr>

                                    <td>{{ $file->id }}</td>

                                    <td>

                                        <i class="bi bi-file-earmark-text text-primary"></i>

                                        <b>{{ $file->file_name }}</b>

                                    </td>

                                    <td>

                                        <span class="badge badge-type">
                                            {{ $file->type }}
                                        </span>

                                    </td>

                                    <td>

                                        <i class="bi bi-hdd"></i>

                                        {{ round($file->file_size / 1024 / 1024, 2) }} MB

                                    </td>

                                    <td>

                                        <a href="/download-map/{{ $file->id }}" class="btn btn-download btn-sm">

                                            <i class="bi bi-download"></i>
                                            Tải

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
    @include('components.footer')
</body>

</html>