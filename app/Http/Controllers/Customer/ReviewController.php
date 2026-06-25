<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;

class ReviewController extends Controller

{
    // List reviews for a product (JSON)
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)->with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($reviews);
    }

    // Store a new review
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $sessionUser = session('user');
        if (! $sessionUser) {
            return response()->json(['error' => 'Silakan login terlebih dahulu.'], 401);
        }

        $user = User::where('name', $sessionUser['username'])->orWhere('email', $sessionUser['username'])->first();
        if (! $user) {
            return response()->json(['error' => 'User tidak ditemukan di database.'], 404);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json(['success' => true, 'review' => $review->load('user')]);
    }

    // Update review (owner only)
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review = Review::findOrFail($id);

        $sessionUser = session('user');
        $user = User::where('name', $sessionUser['username'])->orWhere('email', $sessionUser['username'])->first();
        if (! $user || $review->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->rating = $data['rating'];
        $review->comment = $data['comment'] ?? $review->comment;
        $review->save();

        return response()->json(['success' => true, 'review' => $review]);
    }

    // Delete review (owner only)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $sessionUser = session('user');
        $user = User::where('name', $sessionUser['username'])->orWhere('email', $sessionUser['username'])->first();
        if (! $user || $review->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();
        return response()->json(['success' => true]);
    }
}
