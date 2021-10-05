<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('category', 'user')->latest()->get();
        
        return view('post.index', compact('posts'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('post.create', compact('categories'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        // $imageName = $request->image->store('posts');

        $imageName =$request->image->store(config('images.path'), 'public');

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imageName
        ]);

        return redirect()->route('dashboard')->with('success', 'Votre post a été créé');
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return  view('post.show', compact('post'));
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if (! Gate::allows('update-post', $post)) {
            abort(403);
        }

        $categories = Category::all();
        return view('post.edit', compact('post', 'categories'));
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $arrayUpdate=[
            'title' => $request->title,
            'content' => $request->content
        ];

        if($request->image !== null){

            $imageName =$request->image->store(config('images.path'), 'public');

            $arrayUpdate=array_merge($arrayUpdate, [
                'image' => $imageName,
            ]);
            
        }

        $post->update($arrayUpdate);

        return redirect()->route('dashboard')->with('success', 'Votre post a été modiié');

        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (! Gate::allows('destroy-post', $post)) {
            abort(403);
        }

        $post->delete();
        return redirect()->route('dashboard')->with('success', 'Votre post a été supprimé');

        //
    }
}
