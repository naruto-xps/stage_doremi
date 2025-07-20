
import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
} from '@/components/ui/navigation-menu';
import { 
  GraduationCap, 
  BookOpen, 
  MessageSquare, 
  FileText, 
  Crown, 
  User,
  Settings,
  LogOut
} from 'lucide-react';
import { useAuth } from '../../context/AuthContext';

export const MainNavigation: React.FC = () => {
  const { user, logout } = useAuth();
  const location = useLocation();

  const isActive = (path: string) => location.pathname === path;

  return (
    <header className="bg-white/95 backdrop-blur-sm border-b sticky top-0 z-40">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-3">
            <div className="w-10 h-10 bg-gradient-to-r from-blue-600 to-green-600 rounded-lg flex items-center justify-center">
              <GraduationCap className="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 className="font-bold text-xl bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">
                DOREMI
              </h1>
              <p className="text-xs text-muted-foreground">Plateforme Éducative</p>
            </div>
          </Link>

          {/* Navigation Menu */}
          {user && (
            <NavigationMenu>
              <NavigationMenuList>
                <NavigationMenuItem>
                  <Link 
                    to="/courses"
                    className={`px-4 py-2 rounded-md transition-colors ${
                      isActive('/courses') ? 'bg-primary text-white' : 'hover:bg-muted'
                    }`}
                  >
                    <BookOpen className="w-4 h-4 inline mr-2" />
                    Cours
                  </Link>
                </NavigationMenuItem>
                
                <NavigationMenuItem>
                  <Link 
                    to="/messages"
                    className={`px-4 py-2 rounded-md transition-colors ${
                      isActive('/messages') ? 'bg-primary text-white' : 'hover:bg-muted'
                    }`}
                  >
                    <MessageSquare className="w-4 h-4 inline mr-2" />
                    Messages
                  </Link>
                </NavigationMenuItem>
                
                <NavigationMenuItem>
                  <Link 
                    to="/library"
                    className={`px-4 py-2 rounded-md transition-colors ${
                      isActive('/library') ? 'bg-primary text-white' : 'hover:bg-muted'
                    }`}
                  >
                    <FileText className="w-4 h-4 inline mr-2" />
                    Médiathèque
                  </Link>
                </NavigationMenuItem>
                
                <NavigationMenuItem>
                  <Link 
                    to="/premium"
                    className={`px-4 py-2 rounded-md transition-colors ${
                      isActive('/premium') ? 'bg-primary text-white' : 'hover:bg-muted'
                    }`}
                  >
                    <Crown className="w-4 h-4 inline mr-2" />
                    Premium
                  </Link>
                </NavigationMenuItem>
              </NavigationMenuList>
            </NavigationMenu>
          )}

          {/* User Menu or Auth Buttons */}
          <div className="flex items-center gap-4">
            {user ? (
              <NavigationMenu>
                <NavigationMenuList>
                  <NavigationMenuItem>
                    <NavigationMenuTrigger className="flex items-center gap-2">
                      <div className="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                        <User className="w-4 h-4 text-white" />
                      </div>
                      <span className="font-medium">{user.name}</span>
                      {user.isPremium && (
                        <Crown className="w-4 h-4 text-yellow-500" />
                      )}
                    </NavigationMenuTrigger>
                    <NavigationMenuContent>
                      <div className="w-64 p-4">
                        <div className="space-y-2">
                          <NavigationMenuLink asChild>
                            <Link 
                              to="/settings" 
                              className="flex items-center gap-2 w-full px-3 py-2 hover:bg-muted rounded-md transition-colors"
                            >
                              <Settings className="w-4 h-4" />
                              Paramètres
                            </Link>
                          </NavigationMenuLink>
                          
                          <button
                            onClick={logout}
                            className="flex items-center gap-2 w-full px-3 py-2 text-red-600 hover:bg-red-50 rounded-md transition-colors"
                          >
                            <LogOut className="w-4 h-4" />
                            Déconnexion
                          </button>
                        </div>
                      </div>
                    </NavigationMenuContent>
                  </NavigationMenuItem>
                </NavigationMenuList>
              </NavigationMenu>
            ) : (
              <div className="flex gap-2">
                <Button variant="outline" asChild>
                  <Link to="/login">Connexion</Link>
                </Button>
                <Button asChild>
                  <Link to="/register">Inscription</Link>
                </Button>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};
