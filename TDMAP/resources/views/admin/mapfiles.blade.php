@extends('admin.layout.header')

@section('title')
    Danh sách file map người dùng
@endsection

@section('content')

    <div class="container-fluid mt-3">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Danh sách file map người dùng</h5>
            </div>

            <div class="card-body p-0">

                <table class="table table-bordered table-hover mb-0 text-center">

                    <thead class="thead-dark">

                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Tên file</th>
                            <th>Loại</th>
                            <th>Dung lượng</th>
                            <th>Download</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($files as $file)

                            <tr>

                                <td>{{ $file->id }}</td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ $file->user->name }}
                                    </span>
                                </td>

                                <td>{{ $file->file_name }}</td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $file->type }}
                                    </span>
                                </td>

                                <td>

                                    @php

                                        $size = $file->file_size;

                                        if ($size > 1048576) {
                                            echo round($size / 1048576, 2) . ' MB';
                                        } else {
                                            echo round($size / 1024, 2) . ' KB';
                                        }

                                    @endphp

                                </td>

                                <td>

                                    <a href="{{ route('admin.mapfiles.download', $file->id) }}" class="btn btn-success btn-sm">

                                        <i class="fa fa-download"></i>
                                        Download

                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection