@php $r = $review ?? null; @endphp

<label>Prénom du client (ajoutez l'initiale du nom si vous voulez, par exemple « Kevin A. »)
    <input type="text" name="author_name" value="{{ old('author_name', $r->author_name ?? '') }}" maxlength="80" required>
</label>

<label>Ville (facultatif)
    <input type="text" name="author_city" value="{{ old('author_city', $r->author_city ?? '') }}" maxlength="80" placeholder="Cotonou">
</label>

<label>Note
    <select name="rating">
        @foreach([5, 4, 3, 2, 1] as $n)
            <option value="{{ $n }}" {{ (int) old('rating', $r->rating ?? 5) === $n ? 'selected' : '' }}>{{ str_repeat('★', $n) }} ({{ $n }}/5)</option>
        @endforeach
    </select>
</label>

<label>Message du client
    <textarea name="body" rows="5" maxlength="1000" required>{{ old('body', $r->body ?? '') }}</textarea>
</label>

<label>Produit concerné (facultatif)
    <select name="product_id">
        <option value="">Aucun produit précis</option>
        @foreach($products as $p)
            <option value="{{ $p->id }}" {{ (int) old('product_id', $r->product_id ?? 0) === $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
        @endforeach
    </select>
    <span class="small">L'avis apparaîtra aussi sur la page de ce produit.</span>
</label>

<label>Date de l'avis (facultatif)
    <input type="date" name="reviewed_at" value="{{ old('reviewed_at', optional($r->reviewed_at ?? null)->format('Y-m-d')) }}">
</label>

<label>Capture du message (facultatif)
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
    <span class="small">JPG, PNG ou WEBP, 5 Mo maximum. Cachez les numéros de téléphone avant de l'envoyer.</span>
</label>

@if($r && $r->image)
    <div>
        <img src="{{ $r->imageUrl() }}" class="review-img" alt="">
        <label class="check">
            <input type="hidden" name="remove_image" value="0">
            <input type="checkbox" name="remove_image" value="1"> Retirer cette image
        </label>
    </div>
@endif

<label class="check">
    <input type="hidden" name="is_published" value="0">
    <input type="checkbox" name="is_published" value="1" {{ (string) old('is_published', ($r->is_published ?? true) ? '1' : '0') === '1' ? 'checked' : '' }}>
    Publier sur le site
</label>
