# Déploiement de TECHFLEX Boutique sur Render

## URL de production

**https://techflex-boutique.onrender.com**

Le service est déployé depuis le dépôt :
`https://github.com/juniorhounsou673-sketch/techflex`

## Architecture retenue

- **Render Web Service — plan Free** (Docker, PHP 8.3), région Frankfurt.
- **Render PostgreSQL — plan Free** : base persistante, hors du conteneur.
- `autoDeploy: true` : chaque push sur `main` redéploie le site.
- HTTPS obligatoire, `APP_DEBUG=false`, locale `fr`, fuseau `Africa/Porto-Novo`.
- Aucune carte bancaire n'est nécessaire sur le plan Free.

> ⚠️ L'offre PostgreSQL gratuite de Render expire après environ **30 jours**.
> Pour une mise en ligne définitive, prévoir un plan payant ou une base externe.

> ⚠️ **Configuration transitoire en cours** : `APP_KEY` est encore dans le dépôt
> et le compte administrateur de démonstration (`admin@techflex.test` /
> `password`) reste actif tant que les secrets Render ne sont pas posés.
> Les photos et le catalogue sont déjà ceux du site localhost.

## Première configuration

1. Ouvre https://render.com/deploy?repo=https://github.com/juniorhounsou673-sketch/techflex
2. Vérifie le nom `techflex-boutique`, le plan **Free** et la base `techflex-db`.
3. Renseigne les variables secrètes demandées (`sync: false`) :
   - `APP_KEY` : clé Laravel au format `base64:` (générée avec `php artisan key:generate`).
   - `TECHFLEX_ADMIN_EMAIL` : email de votre compte administrateur.
   - `TECHFLEX_ADMIN_PASSWORD` : mot de passe solide, jamais `password`.
4. Clique sur **Apply**.

Render construit l'image Docker, crée la base PostgreSQL, lance les migrations,
puis le seeder idempotent. Le seeder restaure **le catalogue et les photos
d'origine de TECHFLEX** (fichiers de `storage/app/public/products`, versionnés
dans Git) et **uniquement le compte administrateur** : aucun revendeur de test
n'est créé en production (`TECHFLEX_SEED_DEMO=false`). Aucune image Unsplash
n'est utilisée.

## Ajouter vos revendeurs

1. Ouvre https://techflex-boutique.onrender.com/connexion
2. Connecte-toi avec `TECHFLEX_ADMIN_EMAIL` / `TECHFLEX_ADMIN_PASSWORD`.
3. Va dans **Revendeurs → Donner accès à un revendeur**.
4. Chaque revendeur reçoit son lien `/r/CODE` et peut se connecter avec l'email
   et le mot de passe définis à la création.

Les données saisies restent dans PostgreSQL : elles survivent aux redéploiements.
Le catalogue de départ n'est jamais réinitialisé (`firstOrCreate` dans le seeder).

## Variables d'environnement importantes

- `APP_KEY` : secret Render, ne jamais le committer. Ne pas utiliser le bouton
  « Generate » de Render (valeur incompatible avec Laravel).
- `APP_URL` : posée dans `render.yaml`, remplacée automatiquement par
  `RENDER_EXTERNAL_URL` au démarrage.
- `DATABASE_URL` : fournie automatiquement par `techflex-db` via `render.yaml`.
- `TECHFLEX_DELIVERY_PHONE` : numéro WhatsApp unique qui reçoit les demandes de
  livraison. Remplace `22900000000` par votre vrai numéro avec indicatif.
- `TECHFLEX_MOMO_SIMULATION=true` : aucun paiement réel n'est effectué tant que
  l'API MoMo n'est pas branchée. Le mode simulé n'est jamais proposé en ligne.
- `SESSION_SECURE_COOKIE=true` : cookie transmis uniquement en HTTPS.

## Bon à savoir

- Le plan gratuit met le service en veille après ~15 min d'inactivité : la
  première requête peut prendre 50 secondes.
- En cas d'erreur 500, consulte **Logs** dans le dashboard Render, puis
  **Manual Deploy → Deploy latest commit**.
- Le dépôt GitHub doit rester **privé** : il contient la configuration du site
  et les données des revendeurs ne doivent jamais y être commitées.

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
