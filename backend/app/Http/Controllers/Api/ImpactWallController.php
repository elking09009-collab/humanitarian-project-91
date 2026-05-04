<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImpactStory;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ImpactWallController extends Controller
{
    public function stories()
    {
        return response()->json(ImpactStory::where('is_published', true)->latest()->paginate(12));
    }

    public function likeStory($id)
    {
        $story = ImpactStory::findOrFail($id);
        $story->increment('likes_count');
        return response()->json(['likes' => $story->likes_count]);
    }

    public function forumPosts()
    {
        return response()->json(ForumPost::latest()->paginate(15));
    }

    public function createForumPost(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'content'     => 'required|string',
            'city'        => 'nullable|string|max:100',
            'category'    => 'nullable|in:logistics,medical,legal,experience,general',
            'author_name' => 'nullable|string|max:100',
        ]);
        if (auth('sanctum')->check()) {
            $data['user_id']     = auth('sanctum')->id();
            $data['author_name'] = auth('sanctum')->user()->name;
        }
        $post = ForumPost::create($data);
        return response()->json(['message' => 'تم نشر المنشور', 'post' => $post], 201);
    }
}
