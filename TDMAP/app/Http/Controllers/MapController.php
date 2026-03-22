<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MapController extends Controller
{
    public function upload(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để tải file'
            ], 401);
        }

        $request->validate([
            'file' => 'required|file',
            'type' => 'required|in:dcmoi,dccu,quyhoach'
        ]);

        $user = Auth::user();
        $type = $request->type;

        $typeLabel = match ($type) {
            'dcmoi' => 'Địa chính mới',
            'dccu' => 'Địa chính cũ',
            'quyhoach' => 'Quy hoạch',
            default => $type,
        };

        if (!$user->canUploadType($type)) {
            $limit = $user->getUploadLimitByType($type);

            return response()->json([
                'success'   => false,
                'message'   => "{$user->getCurrentVipName()} chỉ được tải tối đa {$limit} file cho mục {$typeLabel}.",
                'vip_level' => $user->getCurrentVipLevel(),
                'vip_name'  => $user->getCurrentVipName(),
                'type'      => $type,
                'type_label'=> $typeLabel,
                'limit'     => $limit,
                'used'      => $user->uploadedCountByType($type),
                'remaining' => $user->remainingUploadByType($type),
            ], 403);
        }

        $file = $request->file('file');

        $name = $file->getClientOriginalName();
        $size = $file->getSize();
        $path = $file->store('geojson', 'public');

        MapFile::create([
            'user_id'   => $user->id,
            'type'      => $type,
            'file_name' => $name,
            'file_path' => $path,
            'file_size' => $size
        ]);

        return response()->json([
            'success'   => true,
            'url'       => asset('storage/' . $path),
            'name'      => $name,
            'size'      => $size,
            'vip_level' => $user->getCurrentVipLevel(),
            'vip_name'  => $user->getCurrentVipName(),
            'type'      => $type,
            'type_label'=> $typeLabel,
            'used'      => $user->uploadedCountByType($type),
            'remaining' => $user->remainingUploadByType($type),
            'message'   => 'Tải file lên thành công'
        ]);
    }

    public function myFiles()
    {
        $user = Auth::user();
        $files = MapFile::where('user_id', $user->id)->latest()->get();

        return view('map.myfiles', compact('user', 'files'));
    }

    public function myFilesJson()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập'
            ], 401);
        }

        $user = Auth::user();

        $files = MapFile::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'type', 'file_name', 'file_size', 'created_at']);

        return response()->json([
            'success'   => true,
            'vip_level' => $user->getCurrentVipLevel(),
            'vip_name'  => $user->getCurrentVipName(),
            'limits' => [
                'dcmoi'    => $user->getUploadLimitByType('dcmoi'),
                'dccu'     => $user->getUploadLimitByType('dccu'),
                'quyhoach' => $user->getUploadLimitByType('quyhoach'),
            ],
            'used' => [
                'dcmoi'    => $user->uploadedCountByType('dcmoi'),
                'dccu'     => $user->uploadedCountByType('dccu'),
                'quyhoach' => $user->uploadedCountByType('quyhoach'),
            ],
            'remaining' => [
                'dcmoi'    => $user->remainingUploadByType('dcmoi'),
                'dccu'     => $user->remainingUploadByType('dccu'),
                'quyhoach' => $user->remainingUploadByType('quyhoach'),
            ],
            'files' => $files
        ]);
    }

    public function getGeoJson($id)
    {
        $file = MapFile::findOrFail($id);

        if ($file->user_id != Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File không tồn tại'
            ], 404);
        }

        $content = Storage::disk('public')->get($file->file_path);
        $json = json_decode($content, true);

        if (!$json) {
            return response()->json([
                'success' => false,
                'message' => 'File không đúng định dạng GeoJSON'
            ], 422);
        }

        return response()->json([
            'success'   => true,
            'id'        => $file->id,
            'type'      => $file->type,
            'file_name' => $file->file_name,
            'geojson'   => $json
        ]);
    }

    public function download($id)
    {
        $file = MapFile::findOrFail($id);

        if ($file->user_id != Auth::id()) {
            abort(403);
        }

        $path = storage_path('app/public/' . $file->file_path);

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File không tồn tại'
            ], 404);
        }

        return response()->download($path, $file->file_name);
    }
}