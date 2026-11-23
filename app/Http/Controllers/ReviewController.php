<?php

namespace App\Http\Controllers;

use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::published()->with('product')->latest('id')->get();

        return view('reviews.index', [
            'reviews' => $reviews,
            'average' => $reviews->isEmpty() ? null : round($reviews->avg('rating'), 1),
        ]);
    }
}
