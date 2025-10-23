# 🎨 Guide d'Intégration Frontend - API Stages

## 📌 Configuration de base

### 1. URL de l'API

```javascript
const API_URL = 'http://localhost:8000/api';
```

### 2. Service API (Axios)

Créez un fichier `services/api.js` :

```javascript
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Intercepteur pour ajouter le token d'authentification
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
```

---

## 🔧 Fonctions principales

### Récupérer tous les stages

```javascript
import api from './services/api';

export const getInternships = async () => {
  try {
    const response = await api.get('/internships');
    return response.data;
  } catch (error) {
    console.error('Erreur lors de la récupération des stages:', error);
    throw error;
  }
};
```

### Récupérer un stage spécifique

```javascript
export const getInternship = async (id) => {
  try {
    const response = await api.get(`/internships/${id}`);
    return response.data;
  } catch (error) {
    console.error(`Erreur lors de la récupération du stage ${id}:`, error);
    throw error;
  }
};
```

### Créer un nouveau stage

```javascript
export const createInternship = async (internshipData) => {
  try {
    const response = await api.post('/internships', internshipData);
    return response.data;
  } catch (error) {
    console.error('Erreur lors de la création du stage:', error);
    throw error;
  }
};
```

### Mettre à jour un stage

```javascript
export const updateInternship = async (id, internshipData) => {
  try {
    const response = await api.put(`/internships/${id}`, internshipData);
    return response.data;
  } catch (error) {
    console.error(`Erreur lors de la mise à jour du stage ${id}:`, error);
    throw error;
  }
};
```

### Supprimer un stage

```javascript
export const deleteInternship = async (id) => {
  try {
    const response = await api.delete(`/internships/${id}`);
    return response.data;
  } catch (error) {
    console.error(`Erreur lors de la suppression du stage ${id}:`, error);
    throw error;
  }
};
```

---

## ⚛️ Exemple React

### Component - InternshipList.jsx

```javascript
import React, { useState, useEffect } from 'react';
import { getInternships } from '../services/internshipService';

function InternshipList() {
  const [internships, setInternships] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchInternships();
  }, []);

  const fetchInternships = async () => {
    try {
      setLoading(true);
      const data = await getInternships();
      setInternships(data);
      setError(null);
    } catch (err) {
      setError('Impossible de charger les stages');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div className="loading">Chargement...</div>;
  if (error) return <div className="error">{error}</div>;

  return (
    <div className="internship-list">
      <h1>Offres de Stage</h1>
      <div className="internships-grid">
        {internships.map((internship) => (
          <div key={internship.id} className="internship-card">
            <h2>{internship.title}</h2>
            <p className="company">{internship.company}</p>
            <p className="location">📍 {internship.location}</p>
            <p className="description">{internship.description}</p>
            <div className="card-footer">
              <small>Créé le: {new Date(internship.created_at).toLocaleDateString('fr-FR')}</small>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

export default InternshipList;
```

### Component - InternshipDetail.jsx

```javascript
import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { getInternship } from '../services/internshipService';

function InternshipDetail() {
  const { id } = useParams();
  const [internship, setInternship] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchInternship();
  }, [id]);

  const fetchInternship = async () => {
    try {
      setLoading(true);
      const data = await getInternship(id);
      setInternship(data);
      setError(null);
    } catch (err) {
      setError('Impossible de charger le stage');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div className="loading">Chargement...</div>;
  if (error) return <div className="error">{error}</div>;
  if (!internship) return <div className="error">Stage non trouvé</div>;

  return (
    <div className="internship-detail">
      <h1>{internship.title}</h1>
      <div className="detail-header">
        <p className="company">Entreprise: <strong>{internship.company}</strong></p>
        <p className="location">Lieu: <strong>{internship.location}</strong></p>
      </div>
      <div className="detail-body">
        <h2>Description</h2>
        <p>{internship.description}</p>
      </div>
      <div className="detail-footer">
        <small>Créé le: {new Date(internship.created_at).toLocaleDateString('fr-FR')}</small>
        <small>Modifié le: {new Date(internship.updated_at).toLocaleDateString('fr-FR')}</small>
      </div>
    </div>
  );
}

export default InternshipDetail;
```

### Component - InternshipForm.jsx (Admin)

```javascript
import React, { useState } from 'react';
import { createInternship, updateInternship } from '../services/internshipService';

function InternshipForm({ internship = null, onSuccess }) {
  const [formData, setFormData] = useState(
    internship || {
      title: '',
      description: '',
      location: '',
      company: '',
    }
  );
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      setLoading(true);
      setError(null);

      if (internship?.id) {
        await updateInternship(internship.id, formData);
      } else {
        await createInternship(formData);
      }

      if (onSuccess) onSuccess();
    } catch (err) {
      setError('Erreur lors de l\'enregistrement');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="internship-form">
      <h2>{internship ? 'Modifier le stage' : 'Créer un nouveau stage'}</h2>

      {error && <div className="error">{error}</div>}

      <div className="form-group">
        <label htmlFor="title">Titre du stage *</label>
        <input
          type="text"
          id="title"
          name="title"
          value={formData.title}
          onChange={handleChange}
          required
          placeholder="Ex: Stage Développement Web"
        />
      </div>

      <div className="form-group">
        <label htmlFor="company">Entreprise *</label>
        <input
          type="text"
          id="company"
          name="company"
          value={formData.company}
          onChange={handleChange}
          required
          placeholder="Ex: TechHub Senegal"
        />
      </div>

      <div className="form-group">
        <label htmlFor="location">Lieu *</label>
        <input
          type="text"
          id="location"
          name="location"
          value={formData.location}
          onChange={handleChange}
          required
          placeholder="Ex: Dakar, Sénégal"
        />
      </div>

      <div className="form-group">
        <label htmlFor="description">Description *</label>
        <textarea
          id="description"
          name="description"
          value={formData.description}
          onChange={handleChange}
          required
          rows="5"
          placeholder="Décrivez le stage en détail..."
        />
      </div>

      <button type="submit" disabled={loading}>
        {loading ? 'Enregistrement...' : internship ? 'Mettre à jour' : 'Créer'}
      </button>
    </form>
  );
}

export default InternshipForm;
```

---

## 🎨 Styles CSS

```css
/* Styles pour la liste des stages */
.internship-list {
  padding: 20px;
}

.internships-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.internship-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.internship-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.internship-card h2 {
  margin: 0 0 10px 0;
  color: #333;
}

.internship-card .company {
  font-weight: bold;
  color: #007bff;
  margin: 5px 0;
}

.internship-card .location {
  color: #666;
  margin: 5px 0;
}

.internship-card .description {
  color: #555;
  line-height: 1.5;
  margin: 10px 0;
}

.internship-card .card-footer {
  border-top: 1px solid #eee;
  padding-top: 10px;
  margin-top: 10px;
  color: #999;
}

/* Styles pour le formulaire */
.internship-form {
  max-width: 600px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: white;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #333;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
}

.internship-form button {
  width: 100%;
  padding: 10px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.internship-form button:hover:not(:disabled) {
  background: #0056b3;
}

.internship-form button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.error {
  padding: 10px;
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  margin-bottom: 15px;
}

.loading {
  text-align: center;
  padding: 20px;
  color: #666;
}
```

---

## 🔑 Authentification

Pour les opérations protégées (créer, modifier, supprimer), vous devez d'abord vous authentifier :

```javascript
export const login = async (email, password) => {
  try {
    const response = await api.post('/login', { email, password });
    const { token } = response.data;
    localStorage.setItem('token', token);
    return response.data;
  } catch (error) {
    console.error('Erreur de connexion:', error);
    throw error;
  }
};

export const logout = async () => {
  try {
    await api.post('/logout');
    localStorage.removeItem('token');
  } catch (error) {
    console.error('Erreur de déconnexion:', error);
    throw error;
  }
};
```

---

## 🧪 Test avec Postman

1. **Importer la collection** :
   - Créez une nouvelle collection
   - Ajoutez les endpoints de l'API

2. **Configurer les variables** :
   - `base_url`: `http://localhost:8000/api`
   - `token`: Votre token d'authentification

3. **Tester les endpoints** :
   - GET /internships
   - GET /internships/1
   - POST /internships (avec authentification)
   - PUT /internships/1 (avec authentification)
   - DELETE /internships/1 (avec authentification)

---

## 📱 Responsive Design

Assurez-vous que votre frontend est responsive :

```css
@media (max-width: 768px) {
  .internships-grid {
    grid-template-columns: 1fr;
  }

  .internship-form {
    padding: 15px;
  }
}
```

---

## ✅ Checklist d'intégration

- [ ] Configurer l'URL de base de l'API
- [ ] Créer le service API avec Axios
- [ ] Implémenter les fonctions CRUD
- [ ] Créer les composants React/Vue
- [ ] Ajouter les styles CSS
- [ ] Tester avec Postman
- [ ] Implémenter l'authentification
- [ ] Tester sur mobile
- [ ] Gérer les erreurs
- [ ] Ajouter les loading states

---

**Dernière mise à jour** : 23 octobre 2025
