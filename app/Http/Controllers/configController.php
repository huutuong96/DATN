<?php

namespace App\Http\Controllers;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Models\configModel;
use App\Jobs\changeConfig;
class configController extends Controller
{
    public function index()
    {
        $config = configModel::all();
        return response()->json($config, 200);
    }

    public function store(Request $request)
    {
        $image_logo = $request->image_logo;
        $image_footer = $request->image_footer;
        $image_icon = $request->image_icon;
        $thumbnail = $request->thumbnail;
        $main_color = $request->main_color;
        changeConfig::dispatch($image_logo, $image_footer, $image_icon, $thumbnail, $main_color);
        return response()->json($config, 201);
    }

    public function active(Request $request)
    {
        $config = configModel::where('is_active', 1)->get();
        $config->is_active = 0;
        $config->save();
        $config = configModel::find($request->id);
        $config->is_active = 1;
        $config->save();
        return response()->json($config, 200);
    }
    public function update(Request $request)
    {
        $input = $request->all();
        $config = configModel::find($request->id);
        $config->logo_header = $input['logo_header'] ?? $config->logo_header;
        $config->logo_footer = $input['logo_footer'] ?? $config->logo_footer;
        $config->main_color = $input['main_color'] ??  $config->main_color;
        $config->icon = $input['icon'] ?? $config->icon;
        $config->thumbnail = $input['thumbnail'] ?? $config->thumbnail;
        $config->save();
        return response()->json($config, 200);
    }

    public function destroy(Request $request)
    {
        $config = configModel::find($request->id);
        $config->is_active = 2;
        return response()->json(null, 204);
    }

    public function show(Request $request)
    {
        $config = configModel::find($request->id);
        return response()->json($config, 200);
    }

    public function restore(Request $request)
    {
        $config = configModel::find($request->id);
        $config->is_active = 1;
        return response()->json($config, 200);
    }
}
