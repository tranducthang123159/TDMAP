<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\GeoJsonOptimizeService;
class MapController extends Controller
{
public function upload(Request $request, GeoJsonOptimizeService $service)
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

    $file = $request->file('file');

    $raw = file_get_contents($file->getRealPath());
    $geojson = json_decode($raw, true);

    if (!$geojson || !isset($geojson['features']) || !is_array($geojson['features'])) {
        return response()->json([
            'success' => false,
            'message' => 'File không đúng định dạng GeoJSON'
        ], 422);
    }

    $name = $file->getClientOriginalName();
    $size = $file->getSize();
    $featureCount = count($geojson['features']);
    $bbox = $service->getBBox($geojson);

    $mapFile = MapFile::create([
        'user_id' => $user->id,
        'type' => $type,
        'file_name' => $name,
        'file_path' => '',
        'lite_file_path' => null,
        'ultra_lite_file_path' => null,
        'bbox' => $bbox,
        'feature_count' => $featureCount,
        'file_size' => $size
    ]);

    $dir = "map_files/{$mapFile->id}";
    $fullPath = "{$dir}/full.geojson";
    $litePath = "{$dir}/lite.geojson";
    $ultraLitePath = "{$dir}/ultra_lite.geojson";

    Storage::disk('public')->put(
        $fullPath,
        json_encode($geojson, JSON_UNESCAPED_UNICODE)
    );

    $liteStep = $featureCount > 5000 ? 8 : 4;
    $ultraStep = $featureCount > 5000 ? 18 : 10;

    $liteGeojson = $service->makeLite($geojson, $liteStep);
    $ultraLiteGeojson = $service->makeUltraLite($geojson, $ultraStep);

    Storage::disk('public')->put(
        $litePath,
        json_encode($liteGeojson, JSON_UNESCAPED_UNICODE)
    );

    Storage::disk('public')->put(
        $ultraLitePath,
        json_encode($ultraLiteGeojson, JSON_UNESCAPED_UNICODE)
    );

    $mapFile->update([
        'file_path' => $fullPath,
        'lite_file_path' => $litePath,
        'ultra_lite_file_path' => $ultraLitePath,
    ]);

    return response()->json([
        'success' => true,
        'id' => $mapFile->id,
        'name' => $name,
        'size' => $size,
        'type' => $type,
        'type_label' => $typeLabel,
        'message' => 'Tải file lên thành công'
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

    // Kiểm tra quyền sở hữu
    if ($file->user_id != Auth::id()) {
        abort(403);
    }

    // Đảm bảo trả về đúng URL public
    return response()->json([
        'success' => true,
        'id' => $file->id,
        'type' => $file->type,
        'file_name' => $file->file_name,
        'feature_count' => $file->feature_count,
        'bbox' => $file->bbox,
        // Trả lại đúng URL cho các file lưu trong public/storage
        'full_url' => $file->file_path ? Storage::url($file->file_path) : null,
        'lite_url' => $file->lite_file_path ? Storage::url($file->lite_file_path) : null,
        'ultra_lite_url' => $file->ultra_lite_file_path ? Storage::url($file->ultra_lite_file_path) : null,
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