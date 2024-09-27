<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Http\Requests\Blogrequest;

class BlogsController extends Controller
{

    public function index()
    {
        $blogs = Blog::where('is_deleted', false)->get();
        return response()->json($blogs);
    }
    public function store(BlogRequest $request)
    {  
          
       
        $slug = Str::slug($request->title, '-');
        $blog = new Blog();
        $blog->post_id = $request->post_id;
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->content = $request->content;
        $blog->slug = $slug;
        $blog->create_by = auth()->id(); 
        $blog->save();
    
        return response()->json(['message' => 'Post created successfully!', 'blog' => $blog], 201);
    }

    // Get single blog by ID
    public function show($id)
    {
        $blog = Blog::where('id', $id)->where('is_deleted', false)->firstOrFail();
        return response()->json($blog);
    }

   
    public function update(BlogRequest $request, string $id)
    {

    //    ;  dd($request->all())

        
        // Tiếp tục xử lý nếu không có lỗi xác thực
        // $validatedData = $validator->validated();
        // dd('ok');
        $blog = Blog::where('is_deleted', false)->findOrFail($id);
        $blog->post_id = $request->post_id;
        $blog->title = $request->title;
        $slug = Str::slug($request->title, '-');
        $blog->description = $request->description;
        $blog->content = $request->content;
        $blog->slug = $slug;
        $blog->updated_by = auth()->id(); 
        $blog->save();
    
        return response()->json(['message' => 'Blog updated successfully!', 'Blog' => $blog], 200);
    }

    // Soft delete a blog
    public function destroy($id)
    {
        $blog = Blog::where('is_deleted', false)->findOrFail($id);
        $blog->is_deleted = true;
        $blog->updated_by = auth()->id();
        $blog->updated_at = now();
        $blog->save();
        return response()->json(['message' => 'Blog deleted successfully!'], 200);
    }
}