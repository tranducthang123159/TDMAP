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
        $request->validate([
            'file' => 'required|file',
            'type' => 'required'
        ]);

        $file = $request->file('file');

        $name = $file->getClientOriginalName();
        $size = $file->getSize();
        $path = $file->store('geojson', 'public');

        MapFile::create([
            'user_id'   => Auth::id(),
            'type'      => $request->type,
            'file_name' => $name,
            'file_path' => $path,
            'file_size' => $size
        ]);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
            'name'    => $name,
            'size'    => $size
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
        $files = MapFile::where('user_id', Auth::id())
            ->latest()
            ->get(['id', 'type', 'file_name', 'file_size', 'created_at']);

        return response()->json([
            'success' => true,
            'files'   => $files
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

        return response()->download($path, $file->file_name);
    }
}