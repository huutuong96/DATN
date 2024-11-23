<?php

namespace App\Http\Controllers;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Models\ConfigModel;
use App\Models\voucherToMain;
use App\Http\Requests\VoucherRequest;
// use App\Jobs\changeConfig;
class configController extends Controller
{
    public function index()
{

    $configs = ConfigModel::all();
    // dd($configs);
    $information = json_decode($configs[0]->thumbnail);
    // dd($configs["information"]->name);
    return view('config.config_list', compact('configs', 'information'));
}

    public function is_active()
    {
        $active = ConfigModel::where('is_active', 1)->first();
        return $active;
    }
    public function store(Request $request)
    {
        // dd($request);

        $inserData = [
            'logo_header' => $this->storeImage($request->logo_header),
            // dd($image_logo);
            'logo_footer' => $this->storeImage($request->image_footer),
            'icon' => $this->storeImage($request->image_icon),
            'thumbnail' => json_encode([
                'name' =>  $request->name,
                'phone' =>  $request->phone,
                'mail' =>  $request->mail,
            ]),
            'main_color' => $request->main_color ?? '',
            'is_active' => $request->is_active ?? 0,
        ];
        // dd($inserData);
        $config = ConfigModel::create($inserData);
        return response()->json($config, 200);
    }

    // public function active(Request $request)
    // {
    //     $config = ConfigModel::where('is_active', 1)->get();
    //     $config->is_active = 0;
    //     $config->save();
    //     $config = ConfigModel::find($request->id);
    //     $config->is_active = 1;
    //     $config->save();
    //     return response()->json($config, 200);
    // }
    // public function update(Request $request)
    // {
    //     $input = $request->all();
    //     $config = ConfigModel::find($request->id);
    //     $config->logo_header = $input['logo_header'] ?? $config->logo_header;
    //     $config->logo_footer = $input['logo_footer'] ?? $config->logo_footer;
    //     $config->main_color = $input['main_color'] ??  $config->main_color;
    //     $config->icon = $input['icon'] ?? $config->icon;
    //     $config->thumbnail = $input['thumbnail'] ?? $config->thumbnail;
    //     $config->save();
    //     return response()->json($config, 200);
    // }
    public function update(request $request, string $id)
    {
        $config = ConfigModel::findOrFail($id);
        $config->main_color = $request->main_color ?? $config->main_color;
        $config->is_active = $request->has('is_active') ? 1 : 0;
        if ($request->hasFile('logo_header')) {
            $config->logo_header = $this->storeImage($request->logo_header);
        }
        if ($request->hasFile('logo_footer')) {
            $config->logo_footer = $this->storeImage($request->logo_footer);
        }
        if ($request->hasFile('icon')) {
            $config->icon = $this->storeImage($request->icon);
        }
        $config->thumbnail = $request->thumbnail ?? $config->thumbnail;
        $config->save();
    
        return back()->with('message', 'Đã cập nhật thành công');
    }
    
 


    // public function destroy(Request $request)
    // {
    //     $config = ConfigModel::find($request->id);
    //     $config->is_active = 2;
    //     return response()->json(null, 204);
    // }

    // public function show(Request $request)
    // {
    //     $config = ConfigModel::find($request->id);
    //     return response()->json($config, 200);
    // }

    // public function restore(Request $request)
    // {
    //     $config = ConfigModel::find($request->id);
    //     $config->is_active = 1;
    //     return response()->json($config, 200);
    // }
    private function storeImage($image)
    {
        $cloudinary = new Cloudinary();
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        return $uploadedImage['secure_url'];
    }
}
