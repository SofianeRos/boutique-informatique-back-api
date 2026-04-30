# 📊 Documentation Base de Données - Boutique Informatique

## 🔌 Informations de Connexion

| Propriété         | Valeur                     |
| ----------------- | -------------------------- |
| **Type**          | MySQL / MariaDB            |
| **Host**          | `127.0.0.1` ou `localhost` |
| **Port**          | `3306`                     |
| **Database**      | `boutique_informatique`    |
| **User**          | `app`                      |
| **Password**      | `admin`                    |
| **Root Password** | `root`                     |
| **Version**       | MariaDB 10.11              |
| **Charset**       | UTF-8 MB4                  |

### Commande de connexion

```bash
mysql -h 127.0.0.1 -u app -padmin boutique_informatique
```

---

## 📋 Structure des Tables

### 1️⃣ Table `user` (Utilisateurs)

Stocke les informations des utilisateurs/clients de la boutique.

| Champ       | Type         | Contrainte                  | Description                   |
| ----------- | ------------ | --------------------------- | ----------------------------- |
| `id`        | INT          | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique            |
| `email`     | VARCHAR(180) | UNIQUE, NOT NULL            | Adresse email unique          |
| `password`  | VARCHAR(255) | NOT NULL                    | Mot de passe hashé (Argon2)   |
| `pseudo`    | VARCHAR(255) | NOT NULL                    | Pseudo d'affichage            |
| `firstName` | VARCHAR(255) | NULLABLE                    | Prénom                        |
| `lastName`  | VARCHAR(255) | NULLABLE                    | Nom                           |
| `adresse`   | VARCHAR(255) | NULLABLE                    | Adresse postale               |
| `roles`     | JSON         | NOT NULL                    | Rôles (ROLE_USER, ROLE_ADMIN) |
| `createdAt` | DATETIME     | NOT NULL                    | Date de création              |
| `isActive`  | BOOLEAN      | NOT NULL                    | Compte actif ou non           |

**Restrictions Univoques** : `email` doit être unique

**Valeurs par défaut** :

- `createdAt` : Date actuelle
- `isActive` : `true`
- `roles` : `['ROLE_USER']`

---

### 2️⃣ Table `product` (Produits)

Catalogue des produits disponibles à la vente.

| Champ            | Type          | Contrainte                  | Description            |
| ---------------- | ------------- | --------------------------- | ---------------------- |
| `id`             | INT           | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique     |
| `nom`            | VARCHAR(255)  | NOT NULL                    | Nom du produit         |
| `prix`           | DECIMAL(10,2) | NOT NULL                    | Prix unitaire en euros |
| `description`    | TEXT          | NOT NULL                    | Description détaillée  |
| `stock_quantite` | INT           | NOT NULL                    | Quantité en stock      |
| `is_active`      | BOOLEAN       | NOT NULL                    | Produit actif ou non   |

**Contraintes** :

- `prix` : Nombre décimal à 2 chiffres après la virgule
- Les produits inactifs ne s'affichent pas en front

---

### 3️⃣ Table `order` (Commandes)

Enregistre les commandes des clients.

| Champ           | Type          | Contrainte                  | Description                 |
| --------------- | ------------- | --------------------------- | --------------------------- |
| `id`            | INT           | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique          |
| `reference`     | VARCHAR(255)  | NOT NULL                    | Numéro de commande unique   |
| `statut`        | VARCHAR(255)  | NOT NULL                    | État de la commande         |
| `date_commande` | DATETIME      | NOT NULL                    | Date/heure de la commande   |
| `total`         | DECIMAL(10,2) | NOT NULL                    | Montant total TTC           |
| `user_id`       | INT           | FOREIGN KEY → user(id)      | Propriétaire de la commande |

**Statuts possibles** : `en attente`, `confirmée`, `expédiée`, `livrée`, `annulée`

**Relations** :

- 1 Commande → 1 Utilisateur (Many-to-One)
- 1 Commande → Plusieurs OrderDetails (One-to-Many)

---

### 4️⃣ Table `order_detail` (Détails de Commande)

Détail des articles dans chaque commande.

| Champ           | Type          | Contrainte                  | Description               |
| --------------- | ------------- | --------------------------- | ------------------------- |
| `id`            | INT           | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique        |
| `quantite`      | INT           | NOT NULL                    | Nombre d'articles         |
| `prix_unitaire` | DECIMAL(10,2) | NOT NULL                    | Prix au moment de l'achat |
| `commande_id`   | INT           | FOREIGN KEY → order(id)     | Référence à la commande   |
| `product_id`    | INT           | FOREIGN KEY → product(id)   | Référence au produit      |

**Relations** :

- Many-to-One vers `order`
- Many-to-One vers `product`

**Note** : `prix_unitaire` capture le prix au moment de l'achat (peut différer du prix actuel)

---

### 5️⃣ Table `event` (Événements)

Gestion des événements/promotions.

| Champ          | Type         | Contrainte                  | Description                |
| -------------- | ------------ | --------------------------- | -------------------------- |
| `id`           | INT          | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique         |
| `title`        | VARCHAR(255) | NOT NULL                    | Titre de l'événement       |
| `description`  | TEXT         | NOT NULL                    | Description détaillée      |
| `deadlineJoin` | DATETIME     | NOT NULL                    | Date limite pour rejoindre |
| `deadline`     | DATETIME     | NOT NULL                    | Date limite de l'événement |

**Relations** :

- Many-to-Many vers `user` via `event_user`

---

### 6️⃣ Table `sav` (Service Après-Vente)

Gestion des demandes de support client.

| Champ               | Type         | Contrainte                  | Description                   |
| ------------------- | ------------ | --------------------------- | ----------------------------- |
| `id`                | INT          | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique            |
| `materiel_nom`      | VARCHAR(255) | NOT NULL                    | Nom du produit concerné       |
| `description_panne` | TEXT         | NOT NULL                    | Description du problème       |
| `statut`            | VARCHAR(255) | NOT NULL                    | État de la demande            |
| `user_id`           | INT          | FOREIGN KEY → user(id)      | Client qui demande le support |

**Statuts possibles** : `en attente`, `en cours`, `résolu`, `fermé`

**Relations** :

- Many-to-One vers `user`

---

### 7️⃣ Table `event_user` (Relation Many-to-Many)

Table de jointure pour la relation entre utilisateurs et événements.

| Champ      | Type | Contrainte              | Description               |
| ---------- | ---- | ----------------------- | ------------------------- |
| `event_id` | INT  | FOREIGN KEY → event(id) | Référence à l'événement   |
| `user_id`  | INT  | FOREIGN KEY → user(id)  | Référence à l'utilisateur |

---

## 🔗 Diagramme des Relations

```
┌─────────────────┐
│      USER       │
├─────────────────┤
│ id (PK)         │
│ email (UQ)      │
│ password        │
│ pseudo          │
│ firstName       │
│ lastName        │
│ adresse         │
│ roles           │
│ createdAt       │
│ isActive        │
└─────────────────┘
       ▲ │
   1:N │ │ N:1
       │ └──────────────────┬──────────────────┐
       │                    │                  │
       │              ┌──────────────┐   ┌──────────────┐
       │              │    ORDER     │   │     SAV      │
       │              ├──────────────┤   ├──────────────┤
       │              │ id (PK)      │   │ id (PK)      │
       │              │ reference    │   │ materiel_nom │
       │              │ statut       │   │ descr_panne  │
       │              │ date_command │   │ statut       │
       │              │ total        │   │ user_id (FK) │
       │              │ user_id (FK) │   └──────────────┘
       │              └──────────────┘
       │                    │
       │              1:N   │
       │                    ▼
       │             ┌──────────────────┐
       │             │  ORDER_DETAIL    │
       │             ├──────────────────┤
       │             │ id (PK)          │
       │             │ quantite         │
       │             │ prix_unitaire    │
       │             │ commande_id (FK) │
       │             │ product_id (FK)  │
       │             └──────────────────┘
       │                    │
       │              N:1   │
       │                    ▼
       │             ┌──────────────┐
       │             │   PRODUCT    │
       │             ├──────────────┤
       │             │ id (PK)      │
       │             │ nom          │
       │             │ prix         │
       │             │ description  │
       │             │ stock_quantit│
       │             │ is_active    │
       │             └──────────────┘
       │
   N:M │
       │
   ┌───┴─────────────────────┐
   │    EVENT_USER (JT)      │
   ├─────────────────────────┤
   │ event_id (FK)           │
   │ user_id (FK)            │
   └─────────────────────────┘
       ▲               │
       │               │
    N:M│               │N:M
       │               ▼
       │         ┌──────────────┐
       └─────────┤    EVENT     │
                 ├──────────────┤
                 │ id (PK)      │
                 │ title        │
                 │ description  │
                 │ deadlineJoin │
                 │ deadline     │
                 └──────────────┘
```

---

## 🔐 Sécurité & Permissions

### Authentification

- **Système** : JWT (JSON Web Token) avec Lexik JWT Authentication
- **Hashage** : Argon2 (algorithme de hachage sécurisé)
- **Clés** : Situées dans `config/jwt/` (private.pem, public.pem)

### Rôles et Permissions

#### ROLE_USER

- ✅ Lire les produits
- ✅ Créer une commande
- ✅ Voir ses propres commandes
- ✅ Voir ses demandes SAV
- ✅ Rejoindre les événements

#### ROLE_ADMIN

- ✅ Tous les droits ROLE_USER
- ✅ Créer/Modifier/Supprimer des produits
- ✅ Gérer toutes les commandes
- ✅ Gérer les demandes SAV
- ✅ Créer/Modifier les événements

### Ressources API Protégées

| Ressource        | Lecture    | Création | Modification | Suppression |
| ---------------- | ---------- | -------- | ------------ | ----------- |
| **Users**        | Admin      | -        | Admin        | Admin       |
| **Products**     | Public     | Admin    | Admin        | Admin       |
| **Orders**       | User/Admin | User     | Admin        | Admin       |
| **OrderDetails** | User/Admin | User     | Admin        | Admin       |
| **Events**       | Public     | Admin    | Admin        | Admin       |
| **SAV**          | User/Admin | User     | Admin        | Admin       |

---

## 📡 Points de Terminaison API (Endpoints)

### Base URL

```
http://localhost:8000/api/
```

### Ressources Disponibles

#### Users

- `GET /api/users` - Lister les utilisateurs (Admin)
- `GET /api/users/{id}` - Récupérer un utilisateur
- `POST /api/users` - Créer un utilisateur (Register)
- `PUT /api/users/{id}` - Modifier un utilisateur
- `DELETE /api/users/{id}` - Supprimer un utilisateur (Admin)

#### Products

- `GET /api/products` - Lister les produits
- `GET /api/products/{id}` - Détails d'un produit
- `POST /api/products` - Créer un produit (Admin)
- `PUT /api/products/{id}` - Modifier un produit (Admin)
- `DELETE /api/products/{id}` - Supprimer un produit (Admin)

#### Orders

- `GET /api/orders` - Mes commandes
- `GET /api/orders/{id}` - Détail d'une commande
- `POST /api/orders` - Créer une commande
- `PUT /api/orders/{id}` - Modifier une commande (Admin)
- `DELETE /api/orders/{id}` - Annuler une commande

#### OrderDetails

- `GET /api/order_details` - Lister tous les détails
- `GET /api/order_details/{id}` - Détail d'un article
- `POST /api/order_details` - Créer un détail de commande

#### Events

- `GET /api/events` - Lister les événements
- `GET /api/events/{id}` - Détail d'un événement
- `POST /api/events` - Créer un événement (Admin)
- `PUT /api/events/{id}` - Modifier un événement (Admin)

#### SAV

- `GET /api/savs` - Mes demandes SAV
- `GET /api/savs/{id}` - Détail d'une demande
- `POST /api/savs` - Créer une demande SAV
- `PUT /api/savs/{id}` - Mettre à jour une demande (Admin)

---

## 📝 Migrations & Versioning

Les migrations Doctrine sont versionnées et localisées dans `/migrations/` :

```
Version20250314150337.php  - Création tables initiales
Version20250317113944.php  - Ajout champs utilisateur
Version20250317121333.php  - Relation Event-User
Version20250317122828.php  - Table SAV
Version20250317165034.php  - Index optimisations
Version20250322155506.php  - Modifications contraintes
Version20250428140240.php  - Dernière mise à jour
```

---

## 🔄 Types de Données Utilisés

| Type Doctrine      | Type SQL      | Exemple            | Usage                   |
| ------------------ | ------------- | ------------------ | ----------------------- |
| `int`              | INT           | `42`               | Identifiants, quantités |
| `string`           | VARCHAR(255)  | `"produit"`        | Texte court             |
| `text`             | TEXT          | `"description..."` | Texte long              |
| `decimal`          | DECIMAL(10,2) | `99.99`            | Monnaies, prix          |
| `boolean`          | BOOLEAN       | `true/false`       | Drapeaux                |
| `datetime_mutable` | DATETIME      | `2026-04-29 14:30` | Timestamps              |
| `json`             | JSON          | `["ROLE_USER"]`    | Données structurées     |

---

## 💾 Contraintes & Validations

### Contraintes au Niveau Base de Données

```sql
-- User
UNIQUE(email)

-- Order
FOREIGN KEY(user_id) REFERENCES user(id)
FOREIGN KEY(product_id) REFERENCES product(id)

-- OrderDetail
FOREIGN KEY(commande_id) REFERENCES order(id) ON DELETE CASCADE
FOREIGN KEY(product_id) REFERENCES product(id)

-- SAV
FOREIGN KEY(user_id) REFERENCES user(id)

-- Event_User (Many-to-Many)
PRIMARY KEY(event_id, user_id)
FOREIGN KEY(event_id) REFERENCES event(id)
FOREIGN KEY(user_id) REFERENCES user(id)
```

### Cas Spéciaux

- **Suppression de commande** : Les `OrderDetail` associés sont aussi supprimés (orphanRemoval)
- **Stock négatif** : Validation en application (controller)
- **Prix** : Toujours 2 décimales
- **Dates** : Format ISO 8601

---

## 🚀 Exemples de Requêtes

### Créer un utilisateur

```json
POST /api/users
{
  "email": "user@example.com",
  "pseudo": "john_doe",
  "firstName": "John",
  "lastName": "Doe",
  "adresse": "123 rue de Paris",
  "password": "SecurePassword123!"
}
```

### Créer une commande

```json
POST /api/orders
{
  "reference": "CMD-2026-001",
  "statut": "en attente",
  "date_commande": "2026-04-29T14:30:00Z",
  "total": "149.99",
  "user": "/api/users/1"
}
```

### Ajouter un article à une commande

```json
POST /api/order_details
{
  "quantite": 2,
  "prix_unitaire": "74.99",
  "commande": "/api/orders/1",
  "product": "/api/products/5"
}
```

### Créer une demande SAV

```json
POST /api/savs
{
  "materiel_nom": "Ordinateur Portable",
  "description_panne": "L'écran s'éteint aléatoirement",
  "statut": "en attente",
  "user": "/api/users/1"
}
```

---

## 📊 Statistiques & Performance

- **Nombre de tables** : 7
- **Nombre de colonnes** : ~45
- **Nombre de relations** : 6
- **Index recommandés** : email (user), user_id (order), product_id (order_detail)

---

## 🔧 Outils & Configuration

- **Framework** : Symfony 6+
- **ORM** : Doctrine
- **API** : API Platform
- **Validation** : Symfony Validator
- **Serialization** : Serializer avec groupes
- **CORS** : Nelmio CORS Bundle

---

## 📞 Support & Troubleshooting

### Erreur de connexion BD

```bash
# Vérifier le conteneur Docker
docker compose ps

# Relancer la BD
docker compose up -d database
```

### Réinitialiser la BD

```bash
# Supprimer les données
php bin/console doctrine:database:drop --force

# Recréer la BD
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### Charger les fixtures

```bash
php bin/console doctrine:fixtures:load
```

---

**Dernière mise à jour** : 29 Avril 2026  
**Version BD** : 1.0
