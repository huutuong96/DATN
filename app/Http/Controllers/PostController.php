<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::where('is_deleted', false)->get(); 
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
          // Tạo slug từ name
        $slug = Str::slug($validatedData['name'], '-');
        $post = new Post();
        $post->name = $validatedData['name'];
        $post->slug = $slug;
        $post->create_by = auth()->id(); 
        $post->save();
    
        return response()->json(['message' => 'Post created successfully!', 'post' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::where('is_deleted', false)->findOrFail($id); 
        return response()->json($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $post = Post::where('is_deleted', false)->findOrFail($id);
        $post->name = $validatedData['name'];
        $post->slug = Str::slug($validatedData['name'], '-');
        $post->updated_by = auth()->id();
        $post->updated_at = now();
        $post->save();
    
        return response()->json(['message' => 'Post updated successfully!', 'post' => $post], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::where('is_deleted', false)->findOrFail($id);
        $post->is_deleted = true;
        $post->updated_by = auth()->id();
        $post->updated_at = now();
        $post->save();
        return response()->json(['message' => 'Post deleted successfully!'], 200);
    }
}
