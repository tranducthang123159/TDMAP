<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MapFile;
use Illuminate\Support\Facades\Auth;

class MapAdminController extends Controller
{

/* danh sách file */

public function index()
{

$files = MapFile::with('user')->get();

return view('admin.mapfiles',compact('files'));

}


/* admin download */

public function download($id)
{

$file = MapFile::findOrFail($id);

$path = storage_path('app/public/'.$file->file_path);

return response()->download($path,$file->file_name);

}

}