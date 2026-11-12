@extends('layouts.app')

@section('title', 'Revendeurs — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="showcase-head">
            <h1>Revendeurs</h1>
            <a href="{{ route('admin.resellers.create') }}" class="btn">+ Donner accès à un revendeur</a>
        </div>

        @if($resellers->isEmpty())
            <p class="muted">Aucun revendeur pour le moment.</p>
        @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Revendeur</th>
                        <th>Lien de vente</th>
                        <th>MoMo</th>
                        <th>Statut</th>
                        <th>À reverser</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($resellers as $r)
                    <tr>
                        <td>
                            <strong>{{ $r->user->name }}</strong><br>
                            <span class="muted">{{ $r->user->email }}</span>
                        </td>
                        <td>{{ url('/r/' . $r->code) }}</td>
                        <td>{{ $r->momo_number }}</td>
                        <td>
                            @if($r->isBlocked())
                                <span class="badge badge-red">Bloqué</span>
                                @if($r->blocked_at)<div class="muted small">depuis le {{ $r->blocked_at->format('d/m/Y') }}</div>@endif
                                @if($r->blocked_reason)<div class="muted small">{{ $r->blocked_reason }}</div>@endif
                            @else
                                <span class="badge badge-green">Actif</span>
                            @endif
                        </td>
                        <td>{{ \App\Support\Shop::money($r->balance()) }}</td>
                        <td>
                            @if($r->isBlocked())
                                <form method="POST" action="{{ route('admin.resellers.unblock', $r) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-small">Débloquer</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.resellers.block', $r) }}" class="inline-form"
                                      onsubmit="return confirm('Bloquer {{ $r->user->name }} ? Il n\'aura plus accès à rien.');">
                                    @csrf
                                    <input type="text" name="reason" placeholder="Motif (facultatif)" maxlength="190">
                                    <button type="submit" class="btn btn-small btn-danger">Bloquer</button>
                                </form>
                            @endif
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
