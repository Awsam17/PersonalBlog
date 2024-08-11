<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ResponseTrait;
    public function show()
    {
        $posts = Post::with('category','tags')->orderBy('created_at','desc');
        return $this->apiResponse($posts,'Data returned successfully',200);
    }

    public function create()
    {

    }
}
