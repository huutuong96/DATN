<?php

namespace App\Http\Controllers;
use App\Models\web_app;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class webAppController extends Controller
{
    public function index()
    {
        $apps = web_app::all();
        return view('webapp.web_app', ['apps' => $apps]);
    }

    public function create(Request $request)
    {
        $token = $request->query('token');
        if ($request->icon_app) {
            $urlIcon = $this->storeImage($request->icon_app);
        }
        $app = new web_app();
        $app->name = $request->name_app ?? 'Không tên';
        $app->icon = $urlIcon ?? null;
        $app->url = $request->url_app ?? null;
        $app->save();
        return redirect()->route('list_app', [
            'token' => $token,
        ])->with('message', 'Thêm web app thành công');
    }

    public function delete_app(Request $request)
    {
        $token = $request->query('token');
        $id = $request->query('id');
        web_app::where('id', $id)->delete();
        return redirect()->route('list_app', [
            'token' => $token,
        ])->with('message', 'Xóa web app thành công');
    }

    private function storeImage($image)
    {
        $cloudinary = new Cloudinary();
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        return $uploadedImage['secure_url'];
    }
   
}
