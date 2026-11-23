@extends('layouts.app')

@section('title', 'Avis clients — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.dashboard') }}" class="back">← Admin</a>
        <div class="showcase-head">
            <h1>Avis clients</h1>
            <a href="{{ route('admin.reviews.create') }}" class="btn">+ Ajouter un avis</a>
        </div>

        @if($reviews->isEmpty())
            <p class="muted">Aucun avis pour le moment. Ajoutez les retours de vos clients.</p>
        @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Client</th><th>Note</th><th>Message</th><th>Produit</th><th>Statut</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($reviews as $r)
                    <tr>
                        <td>
                            <strong>{{ $r->author_name }}</strong>
                            @if($r->author_city)<div class="muted small">{{ $r->author_city }}</div>@endif
                            @if($r->image)<div class="muted small">📎 capture jointe</div>@endif
                        </td>
                        <td><span class="stars">{{ str_repeat('★', $r->rating) }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($r->body, 110) }}</td>
                        <td>{{ $r->product?->name ?? '—' }}</td>
                        <td>
                            @if($r->is_published)<span class="badge badge-green">Publié</span>
                            @else<span class="badge badge-red">Masqué</span>@endif
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('admin.reviews.edit', $r) }}" class="btn btn-small">Modifier</a>
                                <form method="POST" action="{{ route('admin.reviews.toggle', $r) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-small btn-outline">{{ $r->is_published ? 'Masquer' : 'Publier' }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}"
                                      onsubmit="return confirm('Supprimer cet avis définitivement ?');">
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
