
import React, { createContext, useContext, useState, useEffect } from 'react';
import { User, AuthState } from '../types/auth';
import { EducationLevel } from '../types/course';

interface AuthContextType extends AuthState {
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
  updateProfile: (data: Partial<User>) => void;
  updateProfilePicture: (avatar: string) => void;
  updateEducationLevel: (level: EducationLevel) => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [authState, setAuthState] = useState<AuthState>({
    user: null,
    isAuthenticated: false,
    isLoading: true,
  });

  // Load user from localStorage on mount
  useEffect(() => {
    const savedUser = localStorage.getItem('doremi_user');
    if (savedUser) {
      const user = JSON.parse(savedUser);
      setAuthState({
        user,
        isAuthenticated: true,
        isLoading: false,
      });
    } else {
      setAuthState(prev => ({ ...prev, isLoading: false }));
    }
  }, []);

  const login = async (email: string, password: string) => {
    // Mock login - in real app, this would call an API
    const mockUser: User = {
      id: '1',
      email,
      name: 'John Doe',
      role: 'student',
      isPremium: false,
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + email,
      joinedDate: new Date().toISOString(),
      educationLevel: 'lyceen',
      isVerified: true,
    };

    localStorage.setItem('doremi_user', JSON.stringify(mockUser));
    setAuthState({
      user: mockUser,
      isAuthenticated: true,
      isLoading: false,
    });
  };

  const logout = () => {
    localStorage.removeItem('doremi_user');
    setAuthState({
      user: null,
      isAuthenticated: false,
      isLoading: false,
    });
  };

  const updateProfile = (data: Partial<User>) => {
    if (authState.user) {
      const updatedUser = { ...authState.user, ...data };
      localStorage.setItem('doremi_user', JSON.stringify(updatedUser));
      setAuthState(prev => ({
        ...prev,
        user: updatedUser,
      }));
    }
  };

  const updateProfilePicture = (avatar: string) => {
    updateProfile({ avatar });
  };

  const updateEducationLevel = (educationLevel: EducationLevel) => {
    updateProfile({ educationLevel });
  };

  return (
    <AuthContext.Provider value={{
      ...authState,
      login,
      logout,
      updateProfile,
      updateProfilePicture,
      updateEducationLevel,
    }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
