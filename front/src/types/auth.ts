
export type EducationLevel = 'ecolier' | 'collegien' | 'lyceen' | 'etudiant';

export interface User {
  id: string;
  email: string;
  name: string;
  role: 'student' | 'instructor' | 'admin';
  isPremium: boolean;
  avatar?: string;
  joinedDate: string;
  educationLevel?: EducationLevel;
  isVerified?: boolean;
  cv?: string;
}

export interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
}
