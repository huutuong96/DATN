<?php

namespace App\Http\Controllers;
use Cloudinary\Cloudinary;
use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::where('deleted_at', null)->get(); 
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {   
        $token = $request->query('token');
        $post = new Post();
        $post->title = $request->title;
        $post->image =  $this->storeImage($request->image);
        $post->slug = Str::slug($request->title, '-'); 
        $post->create_by = auth()->user()->id; 
        $post->content = $request->content; 
        $post->blog_id = $request->blog_id; 
        $post->save();

        return back()->with('message', 'thêm thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::where('deleted_at', null)->findOrFail($id); 
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id)
    {
        $post = Post::where('deleted_at', null)->findOrFail($id);
        $post->title = $request->title; 
        $post->slug = Str::slug($request->name, '-'); 
        $post->update_by = auth()->user()->id;
        $post->updated_at = now();
        $post->content = $request->content;
        $post->blog_id = $request->blog_id; 
        $post->save();

        return response()->json(['message' => 'Post updated successfully!', 'post' => $post], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {   $token = $request->query('token');
        $tab = $request->query('tab');
        $post = Post::where('deleted_at', null)->findOrFail($id);
        $post->delete(); 
        return redirect()->route('post',['token' => $token,'tab' => $tab])->with('success', 'post đã được xóa thành công!');
    }
    private function storeImage($image)
    {
        $cloudinary = new Cloudinary();
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        return $uploadedImage['secure_url'];
    }
}
