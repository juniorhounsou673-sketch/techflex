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

> ⚠️ **Avant le prochain déploiement** : renseigner les secrets `APP_KEY`,
> `TECHFLEX_ADMIN_PASSWORD` et `MAIL_PASSWORD` dans le dashboard Render. Le
> blueprint `render.yaml` ne contient plus l'ancienne clé Laravel ni l'ancien
> mot de passe admin. Les photos et le catalogue sont déjà ceux du site localhost.

## Première configuration

1. Ouvre https://render.com/deploy?repo=https://github.com/juniorhounsou673-sketch/techflex
2. Vérifie le nom `techflex-boutique`, le plan **Free** et la base `techflex-db`.
3. Renseigne les variables secrètes demandées (`sync: false`) :
   - `APP_KEY` : **nouvelle** clé Laravel au format `base64:` (générée avec
     `php artisan key:generate` ; ne jamais réutiliser celle qui était dans Git).
   - `TECHFLEX_ADMIN_PASSWORD` : mot de passe solide, jamais `password`.
     L'email admin est déjà posé : `techflex79@gmail.com`.
   - `MAIL_PASSWORD` : clé SMTP Brevo actuelle (`xsmtpsib-...`), inchangée.
4. Clique sur **Apply**. La base PostgreSQL `techflex-db` est créée par le
   blueprint et ses données survivent aux redéploiements.

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
4. Renseigne le nom, l'email, le téléphone, le numéro MoMo et éventuellement le code.
   **Vous ne définissez aucun mot de passe**.
5. Un email d'invitation est envoyé au revendeur. Le message contient un bouton
   **Activer mon compte** : le revendeur arrive sur une page d'accueil, clique sur
   **Activer mon compte**, choisit **lui-même** son mot de passe, puis entre
   directement dans son espace `/revendeur` avec le message
   **BIENVENUE DANS L'EQUIPE DE TECHFLEX !**.
6. Le message de confirmation affiché à l'admin contient toujours le **lien
   d'activation** : copiez-le et transmettez-le au revendeur (WhatsApp, SMS...)
   si son email n'arrive pas. Le lien `/r/CODE` de la liste des revendeurs est
   son lien de vente client. Si nécessaire, cliquez sur **Renvoyer
   l'invitation** pour générer un lien neuf.

Le lien d'invitation expire après 7 jours et devient invalide dès que le mot de
passe est défini. Tant que l'invitation n'est pas acceptée, le revendeur ne peut
pas se connecter.

## Emails automatiques

- **Blocage / déblocage** : l'admin et le revendeur concerné reçoivent un email
  dès qu'un compte est bloqué ou réactivé (motif et date inclus).
- **Tops produits et revendeurs** : un email est envoyé chaque **lundi à 08:00**
  (semaine précédente) et le **1er de chaque mois à 09:00** (mois précédent),
  fuseau **Africa/Porto-Novo**, à l'admin et aux revendeurs actifs.
- Commandes utiles :

```bash
php artisan techflex:send-top weekly
php artisan techflex:send-top monthly
```

Le planificateur Laravel (`schedule:work`) est démarré par le `Dockerfile`, comme
le worker de file d'attente. Sur le plan gratuit Render, le service se met en
veille après une période d'inactivité : les envois planifiés ne sont donc pas
garantis à l'heure. Utilisez les commandes ci-dessus depuis le Shell Render si
besoin, ou prévoyez un Cron Render / un service toujours actif pour un envoi
strictement ponctuel.

⚠️ Ces emails passent par Brevo. Tant que le support Brevo n'a pas activé
l'envoi transactionnel, les messages restent au statut **Queued** et ne sont
pas livrés, même si le code et la file d'attente fonctionnent.

Les données saisies restent dans PostgreSQL : elles survivent aux redéploiements.
Le catalogue de départ n'est jamais réinitialisé (`firstOrCreate` dans le seeder).

## Variables d'environnement importantes

- `APP_KEY` : secret Render, ne jamais le committer. Ne pas utiliser le bouton
  « Generate » de Render (valeur incompatible avec Laravel).
- `APP_URL` : posée dans `render.yaml`, remplacée automatiquement par
  `RENDER_EXTERNAL_URL` au démarrage.
- `DB_URL` : chaîne de connexion fournie automatiquement par `techflex-db` via
  `render.yaml` ; `DB_CONNECTION=pgsql` et `DB_SSLMODE=require` sont posés en
  conséquence.
- `TECHFLEX_DELIVERY_PHONE` : numéro WhatsApp unique qui reçoit les demandes de
  livraison. Remplace `22900000000` par votre vrai numéro avec indicatif.
- `TECHFLEX_MOMO_SIMULATION=true` : aucun paiement réel n'est effectué tant que
  l'API MoMo n'est pas branchée. Le mode simulé n'est jamais proposé en ligne.
- `SESSION_SECURE_COOKIE=true` : cookie transmis uniquement en HTTPS.
- **Emails d'invitation** : la production utilise **Brevo SMTP** :
  `MAIL_MAILER=smtp`, `MAIL_HOST=smtp-relay.brevo.com`, `MAIL_PORT=587`,
  `MAIL_SCHEME=smtp`, `MAIL_USERNAME=c4cd69001@smtp-brevo.com`,
  `MAIL_FROM_ADDRESS=techflex79@gmail.com`, `MAIL_FROM_NAME=TECHFLEX`.
  `MAIL_PASSWORD` est la clé SMTP Brevo (`xsmtpsib-...`) stockée comme secret
  Render, jamais dans Git. L'adresse d'expéditeur doit être vérifiée dans
  Brevo → Senders.
  Avec `log`, les emails ne sont qu'écrits dans les logs et ne parviennent jamais
  au revendeur.

## Bon à savoir

- Le plan gratuit met le service en veille après ~15 min d'inactivité : la
  première requête peut prendre 50 secondes.
- L'email d'invitation est **mis en file d'attente** (`QUEUE_CONNECTION=database`)
  et envoyé par un worker `php artisan queue:work` démarré par le `Dockerfile`.
  Ainsi, créer un revendeur ne bloque jamais sur le serveur SMTP et ne déclenche
  pas de 502. En cas de problème d'envoi, la tâche est retentée 3 fois, puis
  visible dans la table `jobs` / les logs Render.
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
