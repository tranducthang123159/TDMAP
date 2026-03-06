<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\MapFile;
use Illuminate\Http\Request;

class MapController extends Controller
{

public function index()
{
$file = MapFile::where('user_id',Auth::id())->latest()->first();
return view('map',compact('file'));
}


public function upload(Request $request)
{
    if ($request->hasFile('dc_moi')) {

        $file = $request->file('dc_moi');

        $path = $file->storeAs(
            'public/maps',
            'dc_moi.geojson'
        );

    }

    return back();
}
}