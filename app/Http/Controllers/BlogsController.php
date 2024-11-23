<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Http\Requests\Blogrequest;

class BlogsController extends Controller
{
    public function index()
    {
        $blogs = Blog::whereNull('deleted_at')->get();
        return response()->json($blogs);
    }

    public function store(Blogrequest $request)
    {  
       
        $token = $request->query('token');
        $slug = Str::slug($request->name, '-');
        $blog = new Blog();
        $blog->name = $request->name;
        $blog->title = $request->title;
        $blog->slug = $slug;
        $blog->create_by = auth()->user()->id; 
        $blog->save();

        return redirect()->route('blog', [
            'token' => $token,
        ])->with('message', 'Đã thêm ');
    }


    public function show($id)
    {
        $blog = Blog::where('id', $id)->whereNull('deleted_at')->firstOrFail();
        return response()->json($blog);
    }

    public function update(BlogRequest $request, string $id)
    {

        $blog = Blog::where('id', $id)->whereNull('deleted_at')->firstOrFail();
        $slug = Str::slug($request->name, '-');
        $blog = new Blog();
        $blog->name = $request->name;
        $blog->title = $request->title;
        $blog->slug = $slug; 
        $blog->updated_by = auth()->user()->id; 
        $blog->save();

        return response()->json(['message' => 'Blog updated successfully!', 'blog' => $blog], 200);
    }


    public function destroy(Request $request,$id)
    {
        $token = $request->query('token');
        $tab = $request->query('tab');
        $blog = Blog::where('id', $id)->whereNull('deleted_at')->firstOrFail();
        $blog->delete();

        return redirect()->route('blog',['token' => $token,'tab' => $tab])->with('success', 'Blog đã được xóa thành công!');
    }
  

  
}
