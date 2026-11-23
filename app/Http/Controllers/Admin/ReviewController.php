<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with('product')->latest('id')->get();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        return view('admin.reviews.create', ['products' => Product::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('reviews', 'public');
        }

        unset($data['remove_image']);

        Review::create($data);

        return redirect()->route('admin.reviews.index')->with('success', 'Avis ajouté.');
    }

    public function edit(Review $review)
    {
        return view('admin.reviews.edit', [
            'review' => $review,
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($review->image) {
                Storage::disk('public')->delete($review->image);
            }
            $data['image'] = $request->file('image')->store('reviews', 'public');
        } elseif ($request->boolean('remove_image') && $review->image) {
            Storage::disk('public')->delete($review->image);
            $data['image'] = null;
        }

        unset($data['remove_image']);

        $review->update($data);

        return redirect()->route('admin.reviews.index')->with('success', 'Avis enregistré.');
    }

    public function toggle(Review $review)
    {
        $review->update(['is_published' => ! $review->is_published]);

        return back()->with('success', $review->is_published ? 'Avis publié sur le site.' : 'Avis masqué du site.');
    }

    public function destroy(Review $review)
    {
        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return back()->with('success', 'Avis supprimé.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'author_name' => 'required|string|max:80',
            'author_city' => 'nullable|string|max:80',
            'rating' => 'required|integer|between:1,5',
            'body' => 'required|string|max:1000',
            'product_id' => 'nullable|exists:products,id',
            'reviewed_at' => 'nullable|date',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published' => 'required|boolean',
            'remove_image' => 'nullable|boolean',
        ], [
            'image.mimes' => 'Image acceptée : JPG, PNG ou WEBP (pas HEIC).',
            'image.max' => "L'image dépasse 5 Mo.",
            'image.uploaded' => "L'image est trop lourde pour le serveur (voir le réglage php.ini).",
        ]);
    }
}
