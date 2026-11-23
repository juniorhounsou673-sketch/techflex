# TECHFLEX Boutique

Boutique en ligne Laravel pour la vente d’accessoires Apple et de montres de luxe,
avec espace administrateur, espace revendeur et suivi des commandes.

**En ligne :** https://techflex-boutique.onrender.com  
**Guide de déploiement :** [DEPLOIEMENT-RENDER.md](DEPLOIEMENT-RENDER.md)

## Fonctionnalités

### Boutique publique
- Catalogue par catégorie et fiche produit.
- Panier avec quantités et calcul du total en FCFA.
- Commande sans compte : nom, téléphone et mode de paiement.
- Paiement Mobile Money simulé, référence de paiement, puis livraison.
- Lien de vente personnel pour chaque revendeur : `/r/REV001`.
- Retour à la vente directe TECHFLEX : `/direct`.

### Espace administrateur `/admin`
- Tableau de bord : revendeurs actifs, revendeurs bloqués, soldes, commandes.
- Gestion des produits et de leurs médias (les catégories sont fournies dans le catalogue de démonstration).
- Création et blocage/déblocage des revendeurs.
- Suivi des livraisons et validation du paiement des commandes.

### Espace revendeur `/revendeur`
- Tableau de bord avec ventes réalisées via son lien et solde à recevoir.
- Gain configuré par produit dans l’administration.

## Stack technique

- Laravel 13 / PHP 8.3
- Blade + Tailwind CSS 4 + Vite 8
- SQLite (sessions, cache, files d’attente et données de la boutique)
- Docker multi-étapes, déployé sur Render plan Free

## Installation locale

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Ouvre ensuite http://localhost:8000.

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| Admin | `admin@techflex.test` | `password` |
| Revendeur 1 | `revendeur1@techflex.test` | `password` |
| Revendeur 2 | `revendeur2@techflex.test` | `password` |

Les comptes et le catalogue sont créés par `TechflexSeeder`, appelé par
`DatabaseSeeder`.

## Tests

```bash
php artisan test
```

La suite couvre l’accueil, la protection des espaces admin/revendeur, le lien
revendeur et la création d’une commande avec réservation du stock.

## Dépôt

https://github.com/juniorhounsou673-sketch/techflex
