# Déploiement de TECHFLEX Boutique sur Render (gratuit)

## URL de production

**https://techflex-boutique.onrender.com**

Le service est déployé depuis le dépôt public :
`https://github.com/juniorhounsou673-sketch/techflex`

## Plan utilisé

- **Render Web Service — plan Free / Hobby**
- Région : **Frankfurt**
- Docker + PHP 8.3 + Node 22 pour la compilation Vite/Tailwind
- Base **SQLite** embarquée dans le conteneur
- `autoDeploy: true` : chaque push sur `main` redéploie automatiquement

Aucune carte bancaire n’est nécessaire. Ne sélectionne surtout pas **Pro (25 $/mois)**.

## Déploiement depuis le blueprint

Le fichier `render.yaml` est à la racine du dépôt. Pour recréer le service :

1. Ouvre https://render.com/deploy?repo=https://github.com/juniorhounsou673-sketch/techflex
2. Vérifie le nom `techflex-boutique` et le plan **Free**.
3. Clique sur **Apply**.

Render lit automatiquement `render.yaml`, construit l’image Docker, lance les
migrations, remplit la base de démonstration et expose le chemin de santé `/up`.

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@techflex.test` | `password` |
| Revendeur 1 | `revendeur1@techflex.test` | `password` |
| Revendeur 2 | `revendeur2@techflex.test` | `password` |

La boutique publique n’a pas besoin de compte : le client saisit son nom et son
numéro au moment de commander.

## Parcours à vérifier

1. **Boutique** : `/` affiche les catégories Accessoires Apple et Montres de luxe.
2. **Lien revendeur** : `/r/REV001` active le revendeur 1 ; `/direct` revient en vente directe.
3. **Commande** : ajoute un produit au panier, remplis le formulaire `/commander`,
   puis choisis le paiement.
4. **Admin** : `/admin` → produits, revendeurs, livraisons.
5. **Revendeur** : `/revendeur` → tableau de bord et solde.

## Variables d’environnement importantes

- `APP_KEY` : déjà fournie dans `render.yaml` au format `base64:` + 32 octets.
  **Ne clique pas sur Generate** dans Render : la valeur générée n’est pas compatible
  avec Laravel et provoque une erreur 500 `Unsupported cipher or incorrect key length`.
- `APP_URL` : posée dans `render.yaml`, puis remplacée automatiquement au démarrage
  par `RENDER_EXTERNAL_URL` si Render la fournit.
- `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` : `database`, donc aucune
  configuration externe requise.

Pour un vrai environnement de production, déplace `APP_KEY` vers une variable
secrète Render et utilise une base PostgreSQL persistante au lieu de SQLite.

## Important à savoir

- La base SQLite vit dans le conteneur : un **redeploy ou un restart remet les
  données de démonstration à zéro**. C’est normal sur le plan gratuit.
- Le plan gratuit met le service en veille après environ 15 minutes d’inactivité.
  La première requête peut prendre 50 secondes.
- En cas d’erreur 500, consulte **Logs** dans le dashboard Render, puis
  **Manual Deploy → Deploy latest commit**.

## Commandes locales utiles

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
npm run build
php artisan test
php artisan serve
```
