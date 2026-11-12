@php $p = $product ?? null; @endphp

<label>Catégorie
    <select name="category_id" required>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ (int) old('category_id', $p->category_id ?? 0) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
</label>

<label>Nom du produit
    <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}" required>
</label>

<label>Marque
    <input type="text" name="brand" value="{{ old('brand', $p->brand ?? '') }}" placeholder="Apple">
</label>

<label>Description
    <textarea name="description" rows="4">{{ old('description', $p->description ?? '') }}</textarea>
</label>

<label>Prix client (FCFA) : le prix payé par tous les clients
    <input type="number" name="prix_client" value="{{ old('prix_client', $p->prix_client ?? '') }}" min="0" step="1" required>
</label>

<label>Gain du revendeur par vente (FCFA)
    <input type="number" name="gain_revendeur" value="{{ old('gain_revendeur', $p->gain_revendeur ?? 0) }}" min="0" step="1" required>
</label>

<div class="notice">
    Prix revendeur affiché aux revendeurs : <strong id="prix-revendeur">—</strong>
    <div class="small">Le client ne voit jamais ce prix ni le gain.</div>
</div>

<label>Stock
    <input type="number" name="stock" value="{{ old('stock', $p->stock ?? 0) }}" min="0" step="1" required>
</label>

<label class="check">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" {{ (string) old('is_active', ($p->is_active ?? true) ? '1' : '0') === '1' ? 'checked' : '' }}>
    Produit en vente (décochez pour le masquer)
</label>

<label>Photos et vidéos (8 au total par produit)
    <input type="file" name="media[]" multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime">
    <span class="small">Photos : JPG, PNG, WEBP · Vidéos : MP4, WEBM, MOV · 50 Mo maximum par fichier.
        La première photo est la photo principale.</span>
</label>

<script>
(function () {
    var prix = document.querySelector('[name=prix_client]');
    var gain = document.querySelector('[name=gain_revendeur]');
    var out = document.getElementById('prix-revendeur');
    function update() {
        var v = (parseInt(prix.value, 10) || 0) - (parseInt(gain.value, 10) || 0);
        out.textContent = (v < 0 ? 0 : v).toLocaleString('fr-FR') + ' FCFA';
    }
    prix.addEventListener('input', update);
    gain.addEventListener('input', update);
    update();
})();
</script>
