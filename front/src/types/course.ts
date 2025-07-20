
export type EducationLevel = 'ecolier' | 'collegien' | 'lyceen' | 'etudiant';

export interface Course {
  id: string;
  title: string;
  description: string;
  instructor: string;
  instructorId: string;
  thumbnail: string;
  category: string;
  level: 'beginner' | 'intermediate' | 'advanced';
  educationLevel: EducationLevel;
  duration: string;
  chaptersCount: number;
  isPremium: boolean;
  price?: number;
  rating: number;
  studentsCount: number;
  progress?: number;
  createdAt: string;
}

export interface Chapter {
  id: string;
  courseId: string;
  title: string;
  description: string;
  videoUrl?: string;
  pdfUrl?: string;
  audioUrl?: string;
  duration: string;
  order: number;
  isCompleted: boolean;
}

export const EDUCATION_CATEGORIES: Record<EducationLevel, string[]> = {
  ecolier: ['IST', 'Mathématiques', 'Vocabulaire', 'Orthographe', 'Histoire', 'Géographie'],
  collegien: ['Mathématiques', 'Français', 'SVT', 'Histoire-Géographie', 'Physique-Chimie', 'Anglais'],
  lyceen: ['Physique-Chimie', 'SVT', 'Français', 'Philosophie', 'Mathématiques', 'Anglais'],
  etudiant: ['Informatique', 'Mécanique', 'Droit', 'Lettres Modernes', 'Anglais']
};

export const EDUCATION_LEVEL_LABELS: Record<EducationLevel, string> = {
  ecolier: 'Écolier',
  collegien: 'Collégien',
  lyceen: 'Lycéen',
  etudiant: 'Étudiant'
};
