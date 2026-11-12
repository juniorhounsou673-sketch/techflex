@extends('layouts.app')

@section('title', 'Modifier ' . $product->name . ' — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.products.index') }}" class="back">← Produits</a>

        <div class="panel panel-medium">
            <h1>{{ $product->name }}</h1>

            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="form">
                @csrf
                @include('admin.products._fields', ['product' => $product])
                <button type="submit" class="btn">Enregistrer</button>
            </form>
        </div>

        <div class="panel">
            <h2 class="panel-title">Photos et vidéos ({{ $product->media->count() }} / {{ \App\Models\Product::MAX_MEDIA }})</h2>

            @if($product->media->isEmpty())
                <p class="muted">Aucun média pour le moment. Ajoutez-en avec le formulaire ci-dessus.</p>
            @else
                <div class="media-grid">
                    @foreach($product->media as $m)
                        <div class="media-item">
                            <div class="media-preview">
                                @if($m->isVideo())
                                    <video src="{{ $m->url() }}" muted preload="metadata" controls></video>
                                @else
                                    <img src="{{ $m->url() }}" alt="">
                                @endif
                            </div>
                            @if($loop->first)<span class="badge badge-gold">Principale</span>@endif
                            <div class="media-actions">
                                @unless($loop->first)
                                    <form method="POST" action="{{ route('admin.media.cover', $m) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-small btn-outline">Mettre en premier</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.media.destroy', $m) }}"
                                      onsubmit="return confirm('Retirer ce média ?');">
                                    @csrf
                                    <button type="submit" class="link-btn">Retirer</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="panel">
            <h2 class="panel-title">Supprimer ce produit</h2>
            <p class="muted small">Pour seulement le cacher du site, décochez « Produit en vente » ci-dessus.</p>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                  onsubmit="return confirm('Supprimer définitivement ce produit et ses médias ?');">
                @csrf
                <button type="submit" class="btn btn-small btn-danger">Supprimer définitivement</button>
            </form>
        </div>
    </div>
</section>
@endsection
