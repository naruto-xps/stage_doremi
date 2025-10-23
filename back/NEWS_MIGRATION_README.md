# 🚀 Migration de la Table News - DOREMI

## 📋 Vue d'ensemble

Cette migration enrichit considérablement la table `news` du backend pour supporter toutes les fonctionnalités avancées du frontend, incluant le système CRUD complet pour les administrateurs.

## 🆕 Nouveaux Champs Ajoutés

### **Métadonnées de Base**
- `image` - URL de l'image principale
- `author` - Nom de l'auteur
- `location` - Lieu/zone géographique
- `read_time` - Temps de lecture estimé

### **Gestion des Priorités et Statuts**
- `priority` - Priorité (low/medium/high)
- `status` - Statut (draft/published/archived)
- `is_featured` - Mise en vedette
- `is_urgent` - Actualité urgente

### **Métriques d'Engagement**
- `views_count` - Nombre de vues
- `likes_count` - Nombre de likes
- `comments_count` - Nombre de commentaires
- `shares_count` - Nombre de partages

### **Informations de Publication**
- `published_at` - Date de publication
- `expires_at` - Date d'expiration
- `publish_schedule` - Publication programmée
- `author_id` - Référence vers l'utilisateur auteur

### **Catégorisation Avancée**
- `category` - Catégorie principale
- `subcategory` - Sous-catégorie
- `tags` - Tags multiples (JSON)
- `target_audience` - Public cible (JSON)

### **Contenu Enrichi**
- `excerpt` - Résumé court
- `meta_description` - Description pour SEO
- `meta_keywords` - Mots-clés SEO
- `featured_image` - Image principale
- `gallery` - Galerie d'images (JSON)

## 🔧 Installation

### **1. Exécuter la Migration**
```bash
php artisan migrate
```

### **2. Exécuter le Seeder (Optionnel)**
```bash
php artisan db:seed --class=NewsSeeder
```

## 📊 Structure de la Table

```sql
CREATE TABLE news (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('news', 'scholarship', 'announcement') NOT NULL,
    is_published BOOLEAN DEFAULT TRUE,
    
    -- Nouveaux champs
    image VARCHAR(500) NULL,
    author VARCHAR(255) NULL,
    location VARCHAR(255) NULL,
    read_time VARCHAR(50) NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    is_featured BOOLEAN DEFAULT FALSE,
    is_urgent BOOLEAN DEFAULT FALSE,
    views_count INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    shares_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    publish_schedule TIMESTAMP NULL,
    author_id BIGINT NULL,
    category VARCHAR(100) NULL,
    subcategory VARCHAR(100) NULL,
    tags JSON NULL,
    target_audience JSON NULL,
    excerpt TEXT NULL,
    meta_description TEXT NULL,
    meta_keywords TEXT NULL,
    featured_image VARCHAR(500) NULL,
    gallery JSON NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (author_id) REFERENCES users(id)
);
```

## 🎯 Nouvelles Fonctionnalités

### **Scopes de Requête**
```php
// Actualités publiées et non expirées
News::published()->notExpired()->get();

// Actualités en vedette
News::featured()->get();

// Par priorité
News::byPriority('high')->get();

// Par catégorie
News::byCategory('Formation')->get();

// Actualités urgentes
News::urgent()->get();
```

### **Actions Spéciales**
```php
$news = News::find(1);

// Basculer le statut vedette
$news->toggleFeatured();

// Basculer le statut urgent
$news->toggleUrgent();

// Publier
$news->publish();

// Archiver
$news->archive();

// Incrémenter les compteurs
$news->incrementViews();
$news->incrementLikes();
$news->incrementComments();
$news->incrementShares();
```

### **Accesseurs Automatiques**
```php
$news = News::find(1);

// Formattage automatique
echo $news->formatted_read_time;    // "2 min"
echo $news->formatted_views;        // "3,247"
echo $news->formatted_likes;        // "189"
echo $news->priority_color;         // "red", "yellow", "green"
echo $news->status_color;           // "green", "yellow", "gray"
```

## 🌐 Nouvelles Routes API

### **Routes Publiques**
```
GET /api/news                    - Liste des actualités
GET /api/news/{id}              - Détails d'une actualité
GET /api/news/stats             - Statistiques des actualités
```

### **Routes Protégées (Admin)**
```
POST /api/news                  - Créer une actualité
PUT /api/news/{id}              - Modifier une actualité
DELETE /api/news/{id}           - Supprimer une actualité
PATCH /api/news/{id}/toggle-featured  - Basculer vedette
PATCH /api/news/{id}/toggle-urgent    - Basculer urgent
PATCH /api/news/{id}/publish          - Publier
PATCH /api/news/{id}/archive          - Archiver
```

### **Routes d'Engagement (Utilisateurs)**
```
POST /api/news/{id}/like        - Ajouter un like
POST /api/news/{id}/share       - Compter un partage
```

## 📝 Exemple d'Utilisation

### **Créer une Actualité**
```php
$news = News::create([
    'title' => 'Nouvelle Formation',
    'content' => 'Contenu de la formation...',
    'excerpt' => 'Résumé court...',
    'image' => 'https://example.com/image.jpg',
    'author' => 'Équipe Formation',
    'location' => 'Dakar',
    'read_time' => '3 min',
    'type' => 'announcement',
    'priority' => 'high',
    'category' => 'Formation',
    'tags' => ['formation', 'nouveau', 'dakar'],
    'target_audience' => ['étudiants', 'professionnels'],
    'is_featured' => true,
    'published_at' => now()
]);
```

### **Requête Avancée**
```php
$news = News::query()
    ->published()
    ->notExpired()
    ->featured()
    ->byPriority('high')
    ->byCategory('Formation')
    ->orderBy('published_at', 'desc')
    ->paginate(15);
```

## 🔍 Filtres Disponibles

### **Paramètres de Requête**
```
?category=Formation          - Filtrer par catégorie
?priority=high              - Filtrer par priorité
?featured=true              - Actualités en vedette
?urgent=true                - Actualités urgentes
?search=formation           - Recherche textuelle
?sort_by=views_count        - Trier par champ
?sort_order=desc            - Ordre de tri
?per_page=20                - Pagination
?admin=true                 - Mode admin (voir tous les statuts)
```

## 📊 Statistiques Disponibles

```php
// Route: GET /api/news/stats
{
    "total": 25,
    "published": 20,
    "draft": 3,
    "archived": 2,
    "featured": 5,
    "urgent": 1,
    "total_views": 15420,
    "total_likes": 890,
    "total_comments": 234,
    "total_shares": 156,
    "by_category": [
        {"category": "Formation", "count": 10},
        {"category": "Stage", "count": 8}
    ],
    "by_priority": [
        {"priority": "high", "count": 5},
        {"priority": "medium", "count": 15}
    ]
}
```

## 🚨 Points d'Attention

### **Validation**
- Tous les nouveaux champs sont validés côté serveur
- Les URLs d'images sont validées
- Les dates d'expiration doivent être après la date de publication
- Les tableaux JSON sont automatiquement encodés/décodés

### **Sécurité**
- Seuls les admins peuvent créer/modifier/supprimer
- Les utilisateurs connectés peuvent liker/partager
- Validation stricte des entrées utilisateur

### **Performance**
- Index recommandés sur `category`, `priority`, `status`
- Index sur `published_at`, `expires_at`
- Pagination par défaut (15 éléments)

## 🔄 Rollback

Si vous devez annuler la migration :
```bash
php artisan migrate:rollback --step=1
```

## 📚 Ressources Supplémentaires

- **Modèle** : `app/Models/News.php`
- **Contrôleur** : `app/Http/Controllers/NewsController.php`
- **Migration** : `database/migrations/2025_01_15_000000_enhance_news_table.php`
- **Seeder** : `database/seeders/NewsSeeder.php`

---

**🎉 La table `news` est maintenant prête pour supporter toutes les fonctionnalités avancées du frontend !** 