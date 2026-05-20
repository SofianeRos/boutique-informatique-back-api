# 🛍️ Boutique Informatique - API Backend

API REST complète pour la gestion d'une boutique informatique en ligne. Construite avec **Symfony 7.4**, **API Platform**, et **Docker**.

---

## 📋 Table des matières

- [Caractéristiques](#-caractéristiques)
- [Stack technique](#-stack-technique)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [API Endpoints](#-api-endpoints)
- [Authentification JWT](#-authentification-jwt)
- [Intégration Stripe](#-intégration-stripe)
- [Base de données](#-base-de-données)
- [Développement](#-développement)
- [Déploiement](#-déploiement)
- [Support](#-support)

---

## ✨ Caractéristiques

✅ **Gestion complète de boutique** - Produits, commandes, utilisateurs
✅ **Authentification JWT** - Sécurisée et stateless
✅ **API REST RESTful** - Avec API Platform et standards OpenAPI
✅ **Paiement Stripe** - Intégration complète de paiement
✅ **Autorisation ROLE** - Système de rôles (USER, ADMIN)
✅ **CORS** - Support cross-origin configuré
✅ **Docker** - Environnement conteneurisé
✅ **Migrations DB** - Gestion des versions de schéma
✅ **Documentation auto** - Swagger/OpenAPI généré automatiquement

---

## 🛠️ Stack technique

| Composant        | Version   | Description             |
| ---------------- | --------- | ----------------------- |
| **PHP**          | 8.2+      | Langage principal       |
| **Symfony**      | 7.4       | Framework web           |
| **API Platform** | 4.3       | Générateur API REST     |
| **Doctrine ORM** | 3.6       | ORM database            |
| **MariaDB**      | 10.11     | Base de données         |
| **JWT**          | Lexik 3.2 | Authentification tokens |
| **Stripe**       | 20.1      | Traitement paiements    |
| **Docker**       | Latest    | Conteneurisation        |
| **Composer**     | Latest    | Package manager PHP     |

---

## 📦 Prérequis

Avant de commencer, assurez-vous d'avoir :

- **PHP 8.2 ou supérieur**
- **Composer** (dernière version)
- **Docker & Docker Compose** (optionnel mais recommandé)
- **Git**
- **Clé API Stripe** (pour les paiements)

### Vérifier les versions

```bash
php --version
composer --version
docker --version
docker-compose --version
```

---

## 🚀 Installation

### 1. Cloner le repository

```bash
git clone https://github.com/yourusername/boutique-informatique-back-api.git
cd boutique-informatique-back-api
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer les variables d'environnement

Créer un fichier `.env.local` à la racine du projet :

```bash
cp .env .env.local
```

Modifier `.env.local` avec vos paramètres :

```env
# Database
DATABASE_URL="mysql://app:admin@127.0.0.1:3306/boutique_informatique"

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_jwt_passphrase_here

# Stripe
STRIPE_PUBLIC_KEY=pk_test_your_key_here
STRIPE_SECRET_KEY=sk_test_your_key_here

# App
APP_ENV=dev
APP_DEBUG=true
```

### 4. Lancer la base de données avec Docker

```bash
docker-compose up -d
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Charger les données de test (optionnel)

```bash
php bin/console doctrine:fixtures:load
```

### 7. Générer les clés JWT

```bash
mkdir -p config/jwt
openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_key_size:4096
openssl pkey -in config/jwt/private.pem -pubout -out config/jwt/public.pem
```

### 8. Lancer le serveur de développement

```bash
symfony serve
```

Le serveur démarre sur `http://127.0.0.1:8000`

---

## ⚙️ Configuration

### Variables d'environnement essentielles

| Variable            | Description                     | Exemple                                       |
| ------------------- | ------------------------------- | --------------------------------------------- |
| `APP_ENV`           | Environnement (dev, prod, test) | `dev`                                         |
| `DATABASE_URL`      | URL de connexion DB             | `mysql://user:pass@host:3306/db`              |
| `JWT_SECRET_KEY`    | Chemin clé privée JWT           | `%kernel.project_dir%/config/jwt/private.pem` |
| `JWT_PUBLIC_KEY`    | Chemin clé publique JWT         | `%kernel.project_dir%/config/jwt/public.pem`  |
| `JWT_PASSPHRASE`    | Passphrase JWT                  | `your_passphrase`                             |
| `STRIPE_SECRET_KEY` | Clé secrète Stripe              | `sk_test_...`                                 |

### Configuration API Platform

voir : [config/packages/api_platform.yaml](config/packages/api_platform.yaml)

- Prefix API : `/api`
- Version : 1.0
- Formats : JSON, JSON-LD
- Pagination : activée (30 items par défaut)

### Configuration CORS

voir : [config/packages/nelmio_cors.yaml](config/packages/nelmio_cors.yaml)

- Allows origins : `*` (développement)
- Allows methods : GET, POST, PUT, PATCH, DELETE, OPTIONS
- Allows headers : Content-Type, Authorization

### Configuration Sécurité

voir : [config/packages/security.yaml](config/packages/security.yaml)

- Authenticator : JWT (LexikJWTAuthenticationBundle)
- Hachage mot de passe : Argon2
- Firewall API : `/api/*`

---

## 📁 Structure du projet

```
.
├── bin/
│   └── console              # Commandes Symfony CLI
├── config/
│   ├── bundles.php          # Bundles chargés
│   ├── services.yaml        # Services configurés
│   ├── routes.yaml          # Routing principal
│   ├── jwt/                 # Clés JWT (git ignored)
│   ├── packages/            # Configuration bundles
│   │   ├── api_platform.yaml
│   │   ├── security.yaml
│   │   ├── doctrine.yaml
│   │   └── stripe.yaml
│   └── routes/              # Routing par domaine
├── migrations/              # Migrations Doctrine
├── public/
│   └── index.php            # Point d'entrée HTTP
├── src/
│   ├── Entity/              # Entités Doctrine
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderDetail.php
│   │   ├── Event.php
│   │   └── SAV.php
│   ├── Repository/          # Repositories Doctrine
│   ├── Controller/          # Contrôleurs personnalisés
│   ├── State/               # State Processors API Platform
│   ├── DataFixtures/        # Données de test
│   └── Kernel.php           # Noyau Symfony
├── templates/               # Templates Twig
├── var/
│   ├── cache/               # Cache application
│   └── log/                 # Logs
├── vendor/                  # Dépendances Composer (git ignored)
├── docker-compose.yaml      # Configuration Docker
├── composer.json            # Dépendances PHP
└── README.md                # Ce fichier
```

---

## 🔌 API Endpoints

### Documentation interactive

Accédez à la documentation API Swagger complète :

```
http://127.0.0.1:8000/api/docs
```

Schéma OpenAPI JSON :

```
http://127.0.0.1:8000/api/openapi.json
```

### Endpoints principaux

#### 👤 Utilisateurs (Users)

```bash
# Récupérer tous les utilisateurs (admin)
GET /api/users

# Créer un nouvel utilisateur
POST /api/users
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "SecurePass123!",
  "pseudo": "johndoe",
  "firstName": "John",
  "lastName": "Doe",
  "adresse": "123 Rue de la Paix, 75000 Paris"
}

# Récupérer un utilisateur spécifique
GET /api/users/{id}

# Mettre à jour un utilisateur
PUT /api/users/{id}
PATCH /api/users/{id}

# Supprimer un utilisateur
DELETE /api/users/{id}
```

#### 📦 Produits (Products)

```bash
# Récupérer tous les produits (public)
GET /api/products

# Créer un produit (admin only)
POST /api/products
Content-Type: application/json
Authorization: Bearer <token>

{
  "nom": "RTX 4090 Supreme",
  "prix": "1999.99",
  "description": "Carte graphique haute performance",
  "stock_quantite": 50,
  "is_active": true
}

# Récupérer un produit spécifique
GET /api/products/{id}

# Mettre à jour un produit (admin only)
PUT /api/products/{id}
PATCH /api/products/{id}

# Supprimer un produit (admin only)
DELETE /api/products/{id}
```

#### 🛒 Commandes (Orders)

```bash
# Récupérer toutes les commandes (admin)
GET /api/orders

# Créer une commande
POST /api/orders
Content-Type: application/json
Authorization: Bearer <token>

{
  "reference": "CMD-2026-001",
  "statut": "en attente",
  "total": "4999.99",
  "user_id": 1
}

# Récupérer une commande spécifique
GET /api/orders/{id}

# Mettre à jour une commande
PUT /api/orders/{id}
PATCH /api/orders/{id}

# Supprimer une commande
DELETE /api/orders/{id}
```

#### 📝 Détails de Commande (Order Details)

```bash
# Récupérer les détails d'une commande
GET /api/order_details

# Créer un détail de commande
POST /api/order_details
Content-Type: application/json

{
  "order_id": 1,
  "product_id": 2,
  "quantite": 2,
  "prix_unitaire": "1999.99"
}
```

#### 🎉 Événements (Events)

```bash
# Récupérer tous les événements
GET /api/events

# Créer un événement (admin)
POST /api/events
Authorization: Bearer <token>

{
  "nom": "Black Friday 2026",
  "description": "Vente exceptionnelle",
  "date_debut": "2026-11-28T00:00:00Z",
  "date_fin": "2026-12-01T23:59:59Z"
}
```

#### 🔧 Support (SAV)

```bash
# Récupérer tous les tickets support
GET /api/savs

# Créer un ticket support
POST /api/savs
Authorization: Bearer <token>

{
  "titre": "Produit défectueux",
  "description": "Mon RTX ne s'allume pas",
  "statut": "ouvert",
  "user_id": 1
}
```

---

## 🔐 Authentification JWT

### Flux d'authentification

**1. Création de compte**

```bash
curl -X POST http://127.0.0.1:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "email": "newuser@example.com",
    "password": "MySecurePass123!",
    "pseudo": "newuser"
  }'
```

**2. Connexion / Obtenir un token**

L'API Platform génère automatiquement un endpoint de login JWT :

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "newuser@example.com",
    "password": "MySecurePass123!"
  }'
```

**Réponse:**

```json
{
    "token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2Nzc1NDA3OTAsImV4cCI6MTY3NzU0NDM5MCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoibmV3dXNlckBleGFtcGxlLmNvbSJ9.signature..."
}
```

**3. Utiliser le token**

```bash
curl -X GET http://127.0.0.1:8000/api/users/me \
  -H "Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9..."
```

### Gestion des tokens

- **Durée de vie** : Configurable dans `config/packages/lexik_jwt_authentication.yaml`
- **Algorithme** : RS256 (RSA Signature with SHA-256)
- **Rafraîchissement** : À implémenter dans le contrôleur personnalisé
- **Revocation** : Stockage en blacklist (optionnel)

### Rôles et permissions

```yaml
ROLE_USER
- Accès aux endpoints /api/users/me
- Créer/consulter ses propres commandes
- Consulter le catalogue produits

ROLE_ADMIN
- Accès complet /api/admin/*
- Créer/modifier/supprimer les produits
- Gérer tous les utilisateurs
- Consulter tous les rapports
```

---

## 💳 Intégration Stripe

### Configuration

1. **Obtenir vos clés** sur [Stripe Dashboard](https://dashboard.stripe.com)
2. **Configurer les variables** dans `.env.local` :

```env
STRIPE_PUBLIC_KEY=pk_test_XXX
STRIPE_SECRET_KEY=sk_test_XXX
```

### Utilisation dans le code

```php
use Stripe\Stripe;
use Stripe\Charge;

Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

// Créer un paiement
$charge = Charge::create([
    'amount' => 5000,           // Montant en centimes (50€)
    'currency' => 'eur',
    'source' => $tokenSource,   // Token du client
    'description' => 'Commande #CMD-2026-001',
]);
```

### Endpoints de paiement

```bash
# Créer un paiement (checkout)
POST /api/checkout
Authorization: Bearer <token>

{
  "amount": 9999,
  "currency": "eur",
  "token": "tok_visa",
  "order_id": 1
}

# Vérifier le statut d'une transaction
GET /api/checkout/{id}
```

### Webhooks Stripe

Configuration dans `config/packages/stripe.yaml` :

```bash
# Enregistrer l'URL webhook
https://yourdomain.com/api/stripe/webhook

# Events gérés :
- payment_intent.succeeded
- payment_intent.payment_failed
- charge.refunded
```

---

## 🗄️ Base de données

### Architecture

**Moteur** : MariaDB 10.11
**Port** : 3306
**Charset** : UTF-8 MB4

### Entités principales

#### User

- Identifiant unique pour chaque utilisateur
- Email unique avec contrainte d'unicité
- Mot de passe hashé (Argon2)
- Support des rôles (JSON)
- Relation ManyToMany avec Event

#### Product

- Catalogue de produits disponibles
- Prix et stock gérés
- Activation/désactivation de produits
- Relation OneToMany avec OrderDetail

#### Order

- Commandes des clients
- Statuts : en attente, confirmée, expédiée, livrée, annulée
- Relation ManyToOne avec User
- Relation OneToMany avec OrderDetail

#### OrderDetail

- Détails des items dans chaque commande
- Traçabilité du prix unitaire à la date de commande
- Relation ManyToOne avec Product et Order

#### Event

- Événements/promotions
- Relation ManyToMany avec User
- Dates début/fin

#### SAV

- Tickets de support client
- Relation ManyToOne avec User

### Migrations

```bash
# Voir le statut des migrations
php bin/console doctrine:migrations:status

# Créer une nouvelle migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Annuler la dernière migration
php bin/console doctrine:migrations:migrate prev

# Générer un SQL de migration
php bin/console doctrine:migrations:migrate --dump-sql
```

### Sauvegarde/Restauration

```bash
# Exporter la base de données
mysqldump -h 127.0.0.1 -u app -padmin boutique_informatique > backup.sql

# Importer une sauvegarde
mysql -h 127.0.0.1 -u app -padmin boutique_informatique < backup.sql
```

---

## 💻 Développement

### Commandes utiles

```bash
# Afficher toutes les routes
php bin/console debug:router

# Vider le cache
php bin/console cache:clear

# Vider et réchauffer le cache
php bin/console cache:warmup

# Afficher l'environnement
php bin/console about

# Lancer les tests
php bin/console test

# Validation du code
php bin/console lint:yaml config/
php bin/console lint:twig templates/

# Maker bundle - Génération automatique
php bin/console make:entity
php bin/console make:controller
php bin/console make:migration
php bin/console make:fixture
```

### Structure des contrôleurs

Contrôleurs personnalisés dans `src/Controller/` :

- `CheckoutController.php` - Gestion des paiements Stripe
- `UserController.php` - Endpoints utilisateur additionnels

### State Processors

Processeurs personnalisés API Platform dans `src/State/` :

- `UserPasswordHasherProcessor.php` - Hachage automatique des mots de passe

### DataFixtures

Charger des données de test :

```bash
php bin/console doctrine:fixtures:load
```

Voir `src/DataFixtures/AppFixtures.php` pour ajouter vos fixtures.

### Tests

```bash
# Créer une classe de test
php bin/console make:test TestClassName

# Lancer tous les tests
php bin/console test

# Lancer avec coverage
php bin/console test --coverage
```

---

## 📦 Déploiement

### Environnement de production

**1. Cloner en production**

```bash
git clone https://github.com/yourusername/boutique-informatique-back-api.git /var/www/api
cd /var/www/api
```

**2. Installer les dépendances (sans dev)**

```bash
composer install --no-dev --optimize-autoloader
```

**3. Configurer `.env.local`**

```env
APP_ENV=prod
APP_DEBUG=false
DATABASE_URL=mysql://user:pass@prod-db:3306/boutique_informatique
```

**4. Générer les clés JWT en production**

```bash
# Placer les clés JWT en dehors du root public
mkdir -p /var/lib/jwt
openssl genpkey -algorithm RSA -out /var/lib/jwt/private.pem -pkeyopt rsa_key_size:4096
openssl pkey -in /var/lib/jwt/private.pem -pubout -out /var/lib/jwt/public.pem
chmod 600 /var/lib/jwt/private.pem
```

**5. Déployer les migrations**

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

**6. Chauffer le cache**

```bash
php bin/console cache:warmup
```

**7. Configurer le serveur web** (Nginx/Apache)

**Nginx** :

```nginx
server {
    listen 443 ssl http2;
    server_name api.example.com;

    ssl_certificate /etc/ssl/certs/api.example.com.crt;
    ssl_certificate_key /etc/ssl/private/api.example.com.key;

    root /var/www/api/public;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

**8. Permissions des répertoires**

```bash
chown -R www-data:www-data /var/www/api
chmod -R 755 /var/www/api
chmod -R 775 /var/www/api/var
```

### Docker en production

```bash
# Build l'image
docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml build

# Lancer les services
docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml up -d

# Exécuter les migrations dans Docker
docker-compose exec php php bin/console doctrine:migrations:migrate
```

---

## 📞 Support

### Ressources

- 📖 [Documentation Symfony 7.4](https://symfony.com/doc/7.4)
- 📖 [Documentation API Platform 4.3](https://api-platform.com/docs)
- 📖 [Documentation JWT](https://lexik-jwt-authentication-bundle.readthedocs.io/)
- 📖 [Documentation Stripe](https://stripe.com/docs)
- 📖 [Documentation Docker](https://docs.docker.com)

### Rapporter un bug

1. Créer un issue sur le repository GitHub
2. Inclure : version PHP, version Symfony, étapes pour reproduire
3. Joindre les logs pertinents de `var/log/`

### Questions & Discussions

Utiliser les "Discussions" du repository pour poser des questions sur l'utilisation et l'architecture.

---

## 📄 Licence

Ce projet est propriétaire. Tous les droits réservés.

---

## 👥 Contributeurs

- **Développeur Principal** - Spécification et implémentation API

---

## 📅 Version

**Version actuelle** : 1.0.0  
**Dernière mise à jour** : Mai 2026  
**Statut** : En développement actif ✅

---

**Bonne codification ! 🚀**
