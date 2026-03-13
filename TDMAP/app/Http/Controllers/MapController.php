<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MapFile;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{

public function upload(Request $request)
{

$request->validate([
'file' => 'required|file',
'type' => 'required'
]);

$file = $request->file('file');

/* tên file */

$name = $file->getClientOriginalName();

/* size file */

$size = $file->getSize();

/* lưu file */

$path = $file->store('geojson','public');

/* lưu database */

MapFile::create([

'user_id' => Auth::id(),
'type' => $request->type,
'file_name' => $name,
'file_path' => $path,
'file_size' => $size

]);

return response()->json([
'success'=>true
]);

}


public function myFiles()
{

$user = Auth::user();

$files = MapFile::where('user_id',$user->id)->get();

return view('map.myfiles',compact('user','files'));

}

public function download($id)
{

$file = MapFile::findOrFail($id);

/* kiểm tra quyền */

if($file->user_id != Auth::id()){
abort(403);
}

/* đường dẫn file */

$path = storage_path('app/public/'.$file->file_path);

/* tải file */

return response()->download($path,$file->file_name);

}

}