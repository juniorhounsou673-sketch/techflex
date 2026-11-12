<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'media'])->orderBy('category_id')->orderBy('name')->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create', ['categories' => Category::orderBy('id')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $product = Product::create($this->productData($data));

        $error = $this->storeMedia($product, $request->file('media', []));

        if ($error) {
            return redirect()->route('admin.products.edit', $product)->with('error', $error);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Produit « {$product->name} » créé.");
    }

    public function edit(Product $product)
    {
        $product->load('media');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

        $product->update($this->productData($data));

        $error = $this->storeMedia($product, $request->file('media', []));

        if ($error) {
            return back()->with('error', $error);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Produit « {$product->name} » enregistré.");
    }

    public function destroy(Product $product)
    {
        Storage::disk('public')->deleteDirectory('products/' . $product->id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé.');
    }

    public function mediaDestroy(ProductMedia $media)
    {
        Storage::disk('public')->delete($media->path);
        $media->delete();

        return back()->with('success', 'Média retiré.');
    }

    /** Met ce média en première position (= photo principale). */
    public function mediaCover(ProductMedia $media)
    {
        $others = ProductMedia::where('product_id', $media->product_id)
            ->where('id', '!=', $media->id)
            ->orderBy('position')->orderBy('id')->get();

        $position = 1;
        $media->update(['position' => $position++]);
        foreach ($others as $other) {
            $other->update(['position' => $position++]);
        }

        return back()->with('success', 'Photo principale mise à jour.');
    }

    // ---------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'brand' => 'nullable|string|max:80',
            'description' => 'nullable|string|max:5000',
            'prix_client' => 'required|integer|min:0',
            'gain_revendeur' => 'required|integer|min:0|lte:prix_client',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'media' => 'nullable|array|max:' . Product::MAX_MEDIA,
            'media.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,webm,mov|max:51200',
        ], [
            'gain_revendeur.lte' => 'Le gain du revendeur ne peut pas dépasser le prix client.',
            'media.max' => 'Maximum ' . Product::MAX_MEDIA . ' photos ou vidéos à la fois.',
            'media.*.mimes' => 'Formats acceptés : JPG, PNG, WEBP (photos) et MP4, WEBM, MOV (vidéos).',
            'media.*.max' => 'Un fichier dépasse 50 Mo.',
            'media.*.uploaded' => 'Un fichier est trop lourd pour le serveur (voir le réglage php.ini).',
        ]);
    }

    private function productData(array $data): array
    {
        return [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'brand' => $data['brand'] ?? null,
            'description' => $data['description'] ?? null,
            'prix_client' => $data['prix_client'],
            'gain_revendeur' => $data['gain_revendeur'],
            'stock' => $data['stock'],
            'is_active' => (bool) $data['is_active'],
        ];
    }

    /** Enregistre les fichiers envoyés (8 médias maximum par produit). Retourne un message d'erreur ou null. */
    private function storeMedia(Product $product, array $files): ?string
    {
        $files = array_values(array_filter($files));

        if (empty($files)) {
            return null;
        }

        $count = $product->media()->count();

        if ($count + count($files) > Product::MAX_MEDIA) {
            return 'Maximum ' . Product::MAX_MEDIA . ' photos ou vidéos par produit. Il reste '
                . max(0, Product::MAX_MEDIA - $count) . ' place(s) : retirez des médias ou envoyez-en moins.';
        }

        $position = (int) $product->media()->max('position');

        foreach ($files as $file) {
            $type = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';
            $path = $file->store('products/' . $product->id, 'public');

            $product->media()->create([
                'type' => $type,
                'path' => $path,
                'position' => ++$position,
            ]);
        }

        return null;
    }
}
