<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Models\Post;
use App\Models\Tag;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
{
    use ResponseTrait;

    public function create(CreatePostRequest $request)
    {
        $tags = explode(',',$request->tags);

        //image ...

        $user = JWTAuth::parseToken()->authenticate();
        $post = $user->posts()->create([
            'title' => $request->title,
            'post' => $request->post,
            'category_id' => $request->category_id
        ]);

        foreach ($tags as $tagName){
            $tag = Tag::firstOrCreate(['tag'=>$tagName]);
            $post->tags()->attach($tag);
        }

        return $this->apiResponse($post,'Post created successfully !',200);
    }

    public function show (){
        $posts = Post::with('category','tags')->orderBy('created_at','DESC')->get();
        if (!$posts)
            return $this->apiResponse(null,'No posts found !',404);
        return $this->apiResponse($posts,'Posts returned successfully !',200);
    }

    public function edit(EditPostRequest $request){
        $post = Post::findOrfail($request->post_id)->first();

        $tags = explode(',',$request->tags);

        // image update

        $post->update([
            'title' => $request->title,
            'post' => $request->post,
            'category_id' => $request->category_id
        ]);

        $newTags = [];
        foreach ($tags as $tagName){
            $tag = Tag::firstOrCreate(['tag'=>$tagName]);
            $newTags[] = $tag->id;
        }
        $post->tags()->sync($newTags);
        $data = [
            $post,
            'category' => $post->category,
            'tags' => $post->tags
        ];
        return $this->apiResponse($post,'Post updated successfully !',200);
    }

    public function delete (Request $request){
        // image delete
        $id = $_GET['post_id'];
        $post = Post::findOrFail($id)->first();
        $post->tags()->detach();
        $post->delete();

        return $this->apiResponse(null,'Post deleted successfully !',200);


    }
}
