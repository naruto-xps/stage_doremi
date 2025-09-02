# 📚 API des Cours - Documentation

## 🆕 Nouveaux champs ajoutés

### Champs ajoutés à la table `courses`

| Champ | Type | Description | Exemple |
|-------|------|-------------|---------|
| `thumbnail` | string (nullable) | URL de l'image de couverture | `https://example.com/image.jpg` |
| `duration` | string (nullable) | Durée du cours | `"3h 20min"` |
| `chapters_count` | integer | Nombre de chapitres | `10` |
| `rating` | decimal(3,2) | Note sur 5 | `4.50` |
| `students_count` | integer | Nombre d'étudiants inscrits | `156` |
| `price` | decimal(8,2) | Prix du cours (null = gratuit) | `39.99` |
| `difficulty_level` | string | Niveau de difficulté | `"beginner"`, `"intermediate"`, `"advanced"` |
| `category` | string (nullable) | Catégorie du cours | `"Mathématiques"`, `"SVT"` |
| `education_level` | string (nullable) | Niveau d'éducation | `"ecolier"`, `"collegien"`, `"lyceen"`, `"etudiant"` |

## 🔧 Installation et configuration

### 1. Exécuter la migration
```bash
php artisan migrate
```

### 2. Exécuter les seeders
```bash
php artisan db:seed
```

### 3. Vérifier l'installation
```bash
php test_courses_api.php
```

## 📡 Endpoints disponibles

### GET `/api/courses` - Liste des cours

#### Filtres disponibles
- `theme` : Filtrer par thème
- `level` : Filtrer par niveau d'études
- `school` : Filtrer par école
- `category` : Filtrer par catégorie
- `difficulty_level` : Filtrer par niveau de difficulté
- `education_level` : Filtrer par niveau d'éducation
- `is_premium` : Filtrer les cours premium
- `price_min` : Prix minimum
- `price_max` : Prix maximum
- `rating_min` : Note minimum

#### Tri disponible
- `sort_by` : Champ de tri (`title`, `rating`, `students_count`, `chapters_count`, `price`, `created_at`)
- `sort_order` : Ordre de tri (`asc`, `desc`)

#### Exemples d'utilisation
```bash
# Tous les cours de mathématiques
GET /api/courses?category=Mathématiques

# Cours débutant gratuits
GET /api/courses?difficulty_level=beginner&is_premium=false

# Cours premium triés par note
GET /api/courses?is_premium=true&sort_by=rating&sort_order=desc

# Cours pour collégiens avec prix max 50€
GET /api/courses?education_level=collegien&price_max=50
```

### GET `/api/courses/{id}` - Détails d'un cours
Retourne un cours avec ses chapitres associés.

### POST `/api/courses` - Créer un cours
**Authentification requise** - Rôle `teacher` uniquement

#### Exemple de création
```json
{
  "title": "Nouveau cours de mathématiques",
  "description": "Description du cours",
  "thumbnail": "https://example.com/image.jpg",
  "duration": "2h 30min",
  "chapters_count": 8,
  "rating": 0.00,
  "students_count": 0,
  "price": 29.99,
  "theme": "Mathématiques",
  "level": "6ème",
  "difficulty_level": "beginner",
  "category": "Mathématiques",
  "education_level": "collegien",
  "is_premium": true,
  "school": "Collège DoReMi"
}
```

### PUT `/api/courses/{id}` - Modifier un cours
**Authentification requise** - Rôle `teacher` uniquement (propriétaire du cours)

### DELETE `/api/courses/{id}` - Supprimer un cours
**Authentification requise** - Rôle `teacher` uniquement (propriétaire du cours)

## 🗄️ Structure de la base de données

### Table `courses` mise à jour
```sql
CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    thumbnail VARCHAR(255) NULL,
    duration VARCHAR(100) NULL,
    chapters_count INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    students_count INT DEFAULT 0,
    price DECIMAL(8,2) NULL,
    theme VARCHAR(255) NOT NULL,
    level VARCHAR(255) NOT NULL,
    difficulty_level VARCHAR(255) DEFAULT 'beginner',
    category VARCHAR(255) NULL,
    education_level VARCHAR(255) NULL,
    is_premium BOOLEAN DEFAULT FALSE,
    teacher_id BIGINT UNSIGNED NOT NULL,
    school VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🔐 Sécurité et permissions

- **Lecture publique** : Liste des cours et filtrage
- **Lecture détaillée** : Authentification requise
- **Création/Modification/Suppression** : Formateurs uniquement
- **Middleware** : `auth:api` + `role:teacher` pour les opérations d'écriture
- **Propriété** : Un formateur ne peut modifier que ses propres cours

## 📊 Données d'exemple

Le seeder crée automatiquement 4 cours d'exemple :
1. **Mathématiques CM2** - Cours gratuit pour écoliers
2. **SVT 4ème** - Cours premium pour collégiens
3. **Philosophie Terminale** - Cours premium pour lycéens
4. **Comptabilité Licence** - Cours premium pour étudiants

## 🧪 Tests

### Fichier de test
Le fichier `test_courses_api.php` permet de tester rapidement l'API :

```bash
cd back
php test_courses_api.php
```

### Tests automatisés
```bash
php artisan test
```

## 🔄 Compatibilité Frontend

Cette API est maintenant **100% compatible** avec le frontend React :

- ✅ Tous les champs du frontend sont présents
- ✅ Filtres alignés (`category`, `difficulty_level`, `education_level`)
- ✅ Types de données compatibles
- ✅ Gestion des images et métadonnées
- ✅ Système de notation et évaluation
- ✅ Gestion des prix et cours premium

## 🚀 Prochaines étapes

1. **Tester l'API** avec le fichier de test
2. **Mettre à jour le frontend** pour utiliser l'API réelle
3. **Ajouter la gestion des images** (upload de thumbnails)
4. **Implémenter le système de notation** des étudiants
5. **Ajouter la gestion des inscriptions** aux cours 