@extends('layouts.app')

@section('title', 'Produits — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.dashboard') }}" class="back">← Admin</a>
        <div class="showcase-head">
            <h1>Produits</h1>
            <a href="{{ route('admin.products.create') }}" class="btn">+ Ajouter un produit</a>
        </div>

        @if($products->isEmpty())
            <p class="muted">Aucun produit pour le moment.</p>
        @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th></th><th>Produit</th><th>Catégorie</th><th>Prix client</th><th>Gain revendeur</th><th>Prix revendeur</th><th>Stock</th><th>Médias</th><th>Statut</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($products as $p)
                    <tr>
                        <td>
                            @if($p->coverUrl())<img src="{{ $p->coverUrl() }}" class="thumb" alt="">@endif
                        </td>
                        <td><strong>{{ $p->name }}</strong><br><span class="muted small">{{ $p->brand }}</span></td>
                        <td>{{ $p->category->name }}</td>
                        <td>{{ \App\Support\Shop::money($p->prix_client) }}</td>
                        <td>{{ \App\Support\Shop::money($p->gain_revendeur) }}</td>
                        <td>{{ \App\Support\Shop::money($p->prixRevendeur()) }}</td>
                        <td>{{ $p->stock }}</td>
                        <td>{{ $p->media->count() }} / {{ \App\Models\Product::MAX_MEDIA }}</td>
                        <td>
                            @if($p->is_active)<span class="badge badge-green">En vente</span>
                            @else<span class="badge badge-red">Masqué</span>@endif
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-small">Modifier</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $p) }}"
                                      onsubmit="return confirm('Supprimer ce produit et ses photos ? Cette action est définitive.');">
                                    @csrf
                                    <button type="submit" class="btn btn-small btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</section>
@endsection
