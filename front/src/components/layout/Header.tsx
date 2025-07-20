
import React from 'react';
import { Bell, Search, User } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAuth } from '../../context/AuthContext';

export const Header: React.FC = () => {
  const { user } = useAuth();

  return (
    <header className="h-16 border-b bg-white/80 backdrop-blur-sm sticky top-0 z-40">
      <div className="flex items-center justify-between px-4 h-full">
        <div className="flex items-center gap-4">
          <SidebarTrigger />
          
          <div className="hidden md:flex items-center gap-2 max-w-md">
            <div className="relative flex-1">
              <Search className="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground" />
              <Input 
                placeholder="Rechercher des cours, documents..." 
                className="pl-10 bg-muted/50"
              />
            </div>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <Button variant="ghost" size="sm" className="relative">
            <Bell className="w-5 h-5" />
            <span className="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
          </Button>
          
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
              <User className="w-4 h-4 text-white" />
            </div>
            {user && (
              <div className="hidden md:block">
                <p className="text-sm font-medium">{user.name}</p>
                <p className="text-xs text-muted-foreground capitalize">{user.role}</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};
