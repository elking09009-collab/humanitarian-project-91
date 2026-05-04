<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Need;
use App\Models\NeedComment;
use Illuminate\Http\Request;

class NeedCommentController extends Controller
{
    /** GET /api/needs/{need}/comments */
    public function index(Need $need)
    {
        $comments = $need->comments()
            ->with('user:id,name,role')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    /** POST /api/needs/{need}/comments */
    public function store(Request $request, Need $need)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = NeedComment::create([
            'need_id' => $need->id,
            'user_id' => $request->user()->id,
            'body'    => $request->body,
        ]);

        $comment->load('user:id,name,role');

        return response()->json($comment, 201);
    }

    /** DELETE /api/comments/{comment} */
    public function destroy(Request $request, NeedComment $comment)
    {
        abort_unless(
            $request->user()->id === $comment->user_id || $request->user()->role === 'admin',
            403,
            'غير مصرح'
        );

        $comment->delete();

        return response()->json(['message' => 'تم حذف التعليق']);
    }
}
