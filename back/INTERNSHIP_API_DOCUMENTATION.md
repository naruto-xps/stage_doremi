# 📋 Documentation API - Stages (Internships)

## 🎯 Vue d'ensemble

L'API des stages permet de gérer les offres de stage disponibles. Elle fournit des endpoints pour lister, créer, modifier et supprimer les stages.

**URL de base** : `http://localhost:8000/api`

---

## 📚 Endpoints

### 1️⃣ Lister tous les stages

**Endpoint** : `GET /internships`

**Description** : Récupère la liste de tous les stages disponibles.

**Authentification** : ❌ Non requise

**Réponse (200 OK)** :
```json
[
  {
    "id": 1,
    "title": "Stage Développement Web Frontend",
    "description": "Rejoignez notre équipe pour développer des interfaces web modernes...",
    "location": "Dakar, Sénégal",
    "company": "TechHub Senegal",
    "created_at": "2025-10-23T05:34:00.000000Z",
    "updated_at": "2025-10-23T05:34:00.000000Z"
  },
  {
    "id": 2,
    "title": "Stage Développement Backend Laravel",
    "description": "Opportunité de stage pour développer des APIs robustes...",
    "location": "Dakar, Sénégal",
    "company": "Digital Solutions Inc",
    "created_at": "2025-10-23T05:34:00.000000Z",
    "updated_at": "2025-10-23T05:34:00.000000Z"
  }
]
```

**Exemple cURL** :
```bash
curl -X GET "http://localhost:8000/api/internships" \
  -H "Accept: application/json"
```

**Exemple JavaScript/Fetch** :
```javascript
fetch('http://localhost:8000/api/internships')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Erreur:', error));
```

**Exemple Axios** :
```javascript
axios.get('http://localhost:8000/api/internships')
  .then(response => console.log(response.data))
  .catch(error => console.error('Erreur:', error));
```

---

### 2️⃣ Afficher un stage spécifique

**Endpoint** : `GET /internships/{id}`

**Description** : Récupère les détails d'un stage spécifique.

**Authentification** : ❌ Non requise

**Paramètres** :
- `id` (integer, required) : L'ID du stage

**Réponse (200 OK)** :
```json
{
  "id": 1,
  "title": "Stage Développement Web Frontend",
  "description": "Rejoignez notre équipe pour développer des interfaces web modernes...",
  "location": "Dakar, Sénégal",
  "company": "TechHub Senegal",
  "created_at": "2025-10-23T05:34:00.000000Z",
  "updated_at": "2025-10-23T05:34:00.000000Z"
}
```

**Erreur (404 Not Found)** :
```json
{
  "message": "No query results for model [App\\Models\\Internship] 999"
}
```

**Exemple cURL** :
```bash
curl -X GET "http://localhost:8000/api/internships/1" \
  -H "Accept: application/json"
```

**Exemple JavaScript** :
```javascript
fetch('http://localhost:8000/api/internships/1')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Erreur:', error));
```

---

### 3️⃣ Créer un nouveau stage

**Endpoint** : `POST /internships`

**Description** : Crée une nouvelle offre de stage.

**Authentification** : ✅ Requise (Bearer Token)

**Rôle requis** : `admin`

**Corps de la requête** :
```json
{
  "title": "Stage Développement Web",
  "description": "Description du stage...",
  "location": "Dakar, Sénégal",
  "company": "Nom de l'entreprise"
}
```

**Réponse (201 Created)** :
```json
{
  "id": 7,
  "title": "Stage Développement Web",
  "description": "Description du stage...",
  "location": "Dakar, Sénégal",
  "company": "Nom de l'entreprise",
  "created_at": "2025-10-23T05:40:00.000000Z",
  "updated_at": "2025-10-23T05:40:00.000000Z"
}
```

**Exemple cURL** :
```bash
curl -X POST "http://localhost:8000/api/internships" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Stage Développement Web",
    "description": "Description du stage...",
    "location": "Dakar, Sénégal",
    "company": "Nom de l'"'"'entreprise"
  }'
```

**Exemple JavaScript/Axios** :
```javascript
axios.post('http://localhost:8000/api/internships', {
  title: 'Stage Développement Web',
  description: 'Description du stage...',
  location: 'Dakar, Sénégal',
  company: 'Nom de l\'entreprise'
}, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
.then(response => console.log(response.data))
.catch(error => console.error('Erreur:', error));
```

---

### 4️⃣ Mettre à jour un stage

**Endpoint** : `PUT /internships/{id}`

**Description** : Met à jour une offre de stage existante.

**Authentification** : ✅ Requise (Bearer Token)

**Rôle requis** : `admin`

**Paramètres** :
- `id` (integer, required) : L'ID du stage

**Corps de la requête** :
```json
{
  "title": "Stage Développement Web Mis à Jour",
  "description": "Description mise à jour...",
  "location": "Dakar, Sénégal",
  "company": "Nouvelle Entreprise"
}
```

**Réponse (200 OK)** :
```json
{
  "id": 1,
  "title": "Stage Développement Web Mis à Jour",
  "description": "Description mise à jour...",
  "location": "Dakar, Sénégal",
  "company": "Nouvelle Entreprise",
  "created_at": "2025-10-23T05:34:00.000000Z",
  "updated_at": "2025-10-23T05:40:00.000000Z"
}
```

**Exemple cURL** :
```bash
curl -X PUT "http://localhost:8000/api/internships/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Stage Développement Web Mis à Jour",
    "description": "Description mise à jour...",
    "location": "Dakar, Sénégal",
    "company": "Nouvelle Entreprise"
  }'
```

**Exemple JavaScript/Axios** :
```javascript
axios.put(`http://localhost:8000/api/internships/1`, {
  title: 'Stage Développement Web Mis à Jour',
  description: 'Description mise à jour...',
  location: 'Dakar, Sénégal',
  company: 'Nouvelle Entreprise'
}, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
.then(response => console.log(response.data))
.catch(error => console.error('Erreur:', error));
```

---

### 5️⃣ Supprimer un stage

**Endpoint** : `DELETE /internships/{id}`

**Description** : Supprime une offre de stage.

**Authentification** : ✅ Requise (Bearer Token)

**Rôle requis** : `admin`

**Paramètres** :
- `id` (integer, required) : L'ID du stage

**Réponse (200 OK)** :
```json
{
  "message": "Internship deleted successfully"
}
```

**Erreur (404 Not Found)** :
```json
{
  "message": "No query results for model [App\\Models\\Internship] 999"
}
```

**Exemple cURL** :
```bash
curl -X DELETE "http://localhost:8000/api/internships/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Exemple JavaScript/Axios** :
```javascript
axios.delete(`http://localhost:8000/api/internships/1`, {
  headers: {
    'Authorization': `Bearer ${token}`
  }
})
.then(response => console.log(response.data))
.catch(error => console.error('Erreur:', error));
```

---

## 🔐 Authentification

Pour les endpoints protégés (POST, PUT, DELETE), vous devez inclure un **Bearer Token** dans l'en-tête `Authorization`.

### Obtenir un token

**Endpoint** : `POST /login`

**Corps de la requête** :
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Réponse** :
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "admin"
  }
}
```

---

## 📊 Structure des données

### Modèle Internship

| Champ | Type | Description |
|-------|------|-------------|
| `id` | integer | Identifiant unique |
| `title` | string | Titre du stage |
| `description` | text | Description détaillée |
| `location` | string | Lieu du stage |
| `company` | string | Nom de l'entreprise |
| `created_at` | timestamp | Date de création |
| `updated_at` | timestamp | Date de dernière modification |

---

## 🧪 Tester l'API

### Avec Swagger UI

Accédez à : `http://localhost:8000/api/documentation`

### Avec Postman

1. Importez les endpoints dans Postman
2. Configurez les variables d'environnement
3. Testez chaque endpoint

### Avec cURL

Utilisez les exemples fournis ci-dessus.

---

## ⚠️ Codes d'erreur

| Code | Description |
|------|-------------|
| `200` | Succès |
| `201` | Créé avec succès |
| `400` | Requête invalide |
| `401` | Non authentifié |
| `403` | Non autorisé |
| `404` | Ressource non trouvée |
| `500` | Erreur serveur |

---

## 🚀 Intégration Frontend

### Exemple React

```javascript
import axios from 'axios';
import { useState, useEffect } from 'react';

function InternshipList() {
  const [internships, setInternships] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    axios.get('http://localhost:8000/api/internships')
      .then(response => {
        setInternships(response.data);
        setLoading(false);
      })
      .catch(error => {
        console.error('Erreur:', error);
        setLoading(false);
      });
  }, []);

  if (loading) return <div>Chargement...</div>;

  return (
    <div>
      <h1>Offres de Stage</h1>
      {internships.map(internship => (
        <div key={internship.id}>
          <h2>{internship.title}</h2>
          <p>{internship.description}</p>
          <p><strong>Entreprise:</strong> {internship.company}</p>
          <p><strong>Lieu:</strong> {internship.location}</p>
        </div>
      ))}
    </div>
  );
}

export default InternshipList;
```

### Exemple Vue.js

```vue
<template>
  <div>
    <h1>Offres de Stage</h1>
    <div v-if="loading">Chargement...</div>
    <div v-else>
      <div v-for="internship in internships" :key="internship.id">
        <h2>{{ internship.title }}</h2>
        <p>{{ internship.description }}</p>
        <p><strong>Entreprise:</strong> {{ internship.company }}</p>
        <p><strong>Lieu:</strong> {{ internship.location }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      internships: [],
      loading: true
    };
  },
  mounted() {
    axios.get('http://localhost:8000/api/internships')
      .then(response => {
        this.internships = response.data;
        this.loading = false;
      })
      .catch(error => {
        console.error('Erreur:', error);
        this.loading = false;
      });
  }
};
</script>
```

---

## 📝 Notes

- L'API retourne toujours du JSON
- Les timestamps sont au format ISO 8601
- Les erreurs incluent un message descriptif
- CORS est activé pour les requêtes cross-origin

---

**Dernière mise à jour** : 23 octobre 2025
