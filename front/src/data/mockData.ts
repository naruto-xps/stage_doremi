import { Course } from '../types/course';

export const mockCourses: Course[] = [
  {
    id: '1',
    title: 'Découverte de l\'assurance vie',
    description: 'Comprendre les bases de l\'assurance vie et ses avantages pour votre avenir financier',
    instructor: 'Marie Dubois',
    instructorId: '2',
    thumbnail: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
    category: 'Finance',
    level: 'beginner',
    educationLevel: 'etudiant',
    duration: '2h 30min',
    chaptersCount: 8,
    isPremium: false,
    rating: 4.5,
    studentsCount: 234,
    progress: 65,
    createdAt: '2024-01-15'
  },
  {
    id: '2',
    title: 'Gérer ses finances à 20 ans',
    description: 'Les bases de la gestion financière pour les jeunes adultes',
    instructor: 'Pierre Martin',
    instructorId: '3',
    thumbnail: 'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=800',
    category: 'Finance Personnelle',
    level: 'beginner',
    educationLevel: 'etudiant',
    duration: '1h 45min',
    chaptersCount: 6,
    isPremium: true,
    price: 49.99,
    rating: 4.8,
    studentsCount: 189,
    progress: 0,
    createdAt: '2024-02-01'
  },
  {
    id: '3',
    title: 'Introduction à la comptabilité',
    description: 'Maîtrisez les principes fondamentaux de la comptabilité générale',
    instructor: 'Sophie Bernard',
    instructorId: '4',
    thumbnail: 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800',
    category: 'Comptabilité',
    level: 'intermediate',
    educationLevel: 'etudiant',
    duration: '4h 15min',
    chaptersCount: 12,
    isPremium: true,
    price: 79.99,
    rating: 4.6,
    studentsCount: 156,
    progress: 25,
    createdAt: '2024-01-20'
  },
  {
    id: '4',
    title: 'Mathématiques CM2',
    description: 'Révisions et exercices pour les élèves de CM2 - Fractions, géométrie et calculs',
    instructor: 'Fatou Diallo',
    instructorId: '5',
    thumbnail: 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=800',
    category: 'Mathématiques',
    level: 'beginner',
    educationLevel: 'ecolier',
    duration: '3h 20min',
    chaptersCount: 10,
    isPremium: false,
    rating: 4.3,
    studentsCount: 312,
    progress: 0,
    createdAt: '2024-02-10'
  },
  {
    id: '5',
    title: 'SVT - Sciences de la Vie et de la Terre',
    description: 'Comprendre le corps humain, la reproduction et l\'environnement pour les collégiens',
    instructor: 'Dr. Amadou Kane',
    instructorId: '6',
    thumbnail: 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800',
    category: 'SVT',
    level: 'intermediate',
    educationLevel: 'collegien',
    duration: '5h 30min',
    chaptersCount: 15,
    isPremium: true,
    price: 39.99,
    rating: 4.9,
    studentsCount: 198,
    progress: 10,
    createdAt: '2024-01-25'
  },
  {
    id: '6',
    title: 'Philosophie Terminale',
    description: 'Préparation au BAC - Les grands thèmes philosophiques et méthodologie',
    instructor: 'Professeur Ndiaye',
    instructorId: '7',
    thumbnail: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800',
    category: 'Philosophie',
    level: 'advanced',
    educationLevel: 'lyceen',
    duration: '6h 45min',
    chaptersCount: 18,
    isPremium: true,
    price: 59.99,
    rating: 4.7,
    studentsCount: 145,
    progress: 0,
    createdAt: '2024-02-05'
  },
  {
    id: '7',
    title: 'Vocabulaire et Orthographe CE2',
    description: 'Améliorer son français écrit - Dictées, règles d\'orthographe et enrichissement du vocabulaire',
    instructor: 'Mme Sall',
    instructorId: '8',
    thumbnail: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800',
    category: 'Vocabulaire',
    level: 'beginner',
    educationLevel: 'ecolier',
    duration: '2h 15min',
    chaptersCount: 8,
    isPremium: false,
    rating: 4.6,
    studentsCount: 267,
    progress: 0,
    createdAt: '2024-02-12'
  },
  {
    id: '8',
    title: 'Physique-Chimie 3ème',
    description: 'Préparation au BFEM - Électricité, chimie et mécanique pour les collégiens',
    instructor: 'M. Sarr',
    instructorId: '9',
    thumbnail: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800',
    category: 'Physique-Chimie',
    level: 'intermediate',
    educationLevel: 'collegien',
    duration: '4h 30min',
    chaptersCount: 12,
    isPremium: true,
    price: 45.99,
    rating: 4.4,
    studentsCount: 189,
    progress: 0,
    createdAt: '2024-02-08'
  }
];

export const mockNews = [
  {
    id: '1',
    title: 'Nouvelles bourses d\'études disponibles',
    excerpt: 'Découvrez les dernières opportunités de financement pour vos études supérieures',
    content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
    category: 'Bourses',
    publishedAt: '2024-03-15',
    author: 'DOREMI Team'
  },
  {
    id: '2',
    title: 'Partenariat avec HEC Paris',
    excerpt: 'DOREMI s\'associe avec HEC Paris pour proposer de nouveaux cours',
    content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
    category: 'Partenariats',
    publishedAt: '2024-03-10',
    author: 'DOREMI Team'
  }
];

export const mockInternships = [
  {
    id: '1',
    title: 'Stage en Finance d\'Entreprise',
    company: 'BNP Paribas',
    location: 'Paris, France',
    duration: '6 mois',
    description: 'Rejoignez notre équipe finance pour une expérience enrichissante',
    requirements: ['Bac+3/4 en Finance', 'Maîtrise d\'Excel', 'Anglais courant'],
    applicationDeadline: '2024-04-30'
  },
  {
    id: '2',
    title: 'Stage Marketing Digital',
    company: 'L\'Oréal',
    location: 'Levallois-Perret, France',
    duration: '4-6 mois',
    description: 'Participez au développement de nos campagnes digitales',
    requirements: ['Bac+3/4 en Marketing', 'Créativité', 'Réseaux sociaux'],
    applicationDeadline: '2024-05-15'
  }
];

export const mockInstitutions = [
  {
    id: '1',
    name: 'HEC Paris',
    type: 'École de Commerce',
    location: 'Jouy-en-Josas, France',
    language: 'Français/Anglais',
    description: 'Grande école de commerce reconnue mondialement',
    website: 'https://www.hec.edu'
  },
  {
    id: '2',
    name: 'Sorbonne Université',
    type: 'Université',
    location: 'Paris, France',
    language: 'Français',
    description: 'Université pluridisciplinaire de renommée internationale',
    website: 'https://www.sorbonne-universite.fr'
  }
];
