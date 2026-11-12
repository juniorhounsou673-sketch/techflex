@extends('layouts.app')

@section('title', 'Panier — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="showcase-head"><h1>Votre panier</h1></div>

        @if($lines->isEmpty())
            <p class="muted">Votre panier est vide.</p>
            <p><a href="{{ route('home') }}" class="btn">Voir le catalogue</a></p>
        @else
            <div class="table-wrap">
            <table class="cart-table">
                <thead>
                    <tr><th>Produit</th><th>Prix</th><th>Qté</th><th>Sous-total</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($lines as $line)
                    <tr>
                        <td><a href="{{ route('product', $line['product']) }}">{{ $line['product']->name }}</a></td>
                        <td>{{ \App\Support\Shop::money($line['price']) }}</td>
                        <td>{{ $line['qty'] }}</td>
                        <td>{{ \App\Support\Shop::money($line['subtotal']) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.remove', $line['product']) }}">
                                @csrf
                                <button type="submit" class="link-btn">Retirer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

            <div class="cart-total">Total : <strong>{{ \App\Support\Shop::money($total) }}</strong></div>

            <a href="{{ route('checkout') }}" class="btn">Commander</a>
        @endif
    </div>
</section>
@endsection
