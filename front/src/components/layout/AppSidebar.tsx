
import React from 'react';
import { NavLink, useLocation } from 'react-router-dom';
import {
  Home,
  BookOpen,
  MessageSquare,
  FileText,
  Briefcase,
  Newspaper,
  Building,
  Crown,
  Settings,
  Users,
  BarChart3,
  LogOut,
  GraduationCap
} from 'lucide-react';
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarTrigger,
  useSidebar,
} from '@/components/ui/sidebar';
import { useAuth } from '../../context/AuthContext';

const studentMenuItems = [
  { title: 'Accueil', url: '/', icon: Home },
  { title: 'Mes Cours', url: '/courses', icon: BookOpen },
  { title: 'Messagerie', url: '/messages', icon: MessageSquare },
  { title: 'Médiathèque', url: '/library', icon: FileText },
  { title: 'Stages', url: '/internships', icon: Briefcase },
  { title: 'Actualités', url: '/news', icon: Newspaper },
  { title: 'Établissements', url: '/institutions', icon: Building },
  { title: 'Premium', url: '/premium', icon: Crown },
  { title: 'Paramètres', url: '/settings', icon: Settings },
];

const instructorMenuItems = [
  { title: 'Accueil', url: '/', icon: Home },
  { title: 'Mes Cours', url: '/courses', icon: BookOpen },
  { title: 'Messagerie', url: '/messages', icon: MessageSquare },
  { title: 'Étudiants', url: '/students', icon: Users },
  { title: 'Médiathèque', url: '/library', icon: FileText },
  { title: 'Actualités', url: '/news', icon: Newspaper },
  { title: 'Paramètres', url: '/settings', icon: Settings },
];

const adminMenuItems = [
  { title: 'Dashboard', url: '/', icon: BarChart3 },
  { title: 'Utilisateurs', url: '/admin/users', icon: Users },
  { title: 'Cours', url: '/admin/courses', icon: BookOpen },
  { title: 'Documents', url: '/admin/documents', icon: FileText },
  { title: 'Actualités', url: '/admin/news', icon: Newspaper },
  { title: 'Statistiques', url: '/admin/stats', icon: BarChart3 },
  { title: 'Paramètres', url: '/admin/settings', icon: Settings },
];

export function AppSidebar() {
  const { state } = useSidebar();
  const location = useLocation();
  const { user, logout } = useAuth();
  
  if (!user) return null;

  const currentPath = location.pathname;
  const isCollapsed = state === 'collapsed';
  
  const getMenuItems = () => {
    switch (user.role) {
      case 'student':
        return studentMenuItems;
      case 'instructor':
        return instructorMenuItems;
      case 'admin':
        return adminMenuItems;
      default:
        return studentMenuItems;
    }
  };

  const menuItems = getMenuItems();
  const isActive = (path: string) => currentPath === path;
  const getNavCls = (active: boolean) =>
    active ? "bg-primary text-white" : "hover:bg-muted/50";

  return (
    <Sidebar className={isCollapsed ? "w-16" : "w-64"} collapsible="icon">
      <div className="p-4 border-b">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
            <GraduationCap className="w-5 h-5 text-white" />
          </div>
          {!isCollapsed && (
            <div>
              <h2 className="font-bold text-lg text-primary">DOREMI</h2>
              <p className="text-xs text-muted-foreground">Plateforme Éducative</p>
            </div>
          )}
        </div>
      </div>

      <SidebarContent className="px-2">
        <SidebarGroup>
          {!isCollapsed && (
            <SidebarGroupLabel className="px-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
              Navigation
            </SidebarGroupLabel>
          )}
          
          <SidebarGroupContent>
            <SidebarMenu>
              {menuItems.map((item) => (
                <SidebarMenuItem key={item.title}>
                  <SidebarMenuButton asChild>
                    <NavLink 
                      to={item.url} 
                      className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-colors ${getNavCls(isActive(item.url))}`}
                    >
                      <item.icon className="w-5 h-5" />
                      {!isCollapsed && <span className="font-medium">{item.title}</span>}
                    </NavLink>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              ))}
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>

        {/* User Profile Section */}
        <div className="mt-auto border-t pt-4">
          <div className="px-3 py-2">
            {!isCollapsed && (
              <div className="mb-2">
                <p className="font-medium text-sm">{user.name}</p>
                <p className="text-xs text-muted-foreground">{user.email}</p>
                {user.isPremium && (
                  <span className="premium-badge mt-1 inline-block">Premium</span>
                )}
              </div>
            )}
            <button
              onClick={logout}
              className="flex items-center gap-2 w-full px-2 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            >
              <LogOut className="w-4 h-4" />
              {!isCollapsed && <span>Déconnexion</span>}
            </button>
          </div>
        </div>
      </SidebarContent>
    </Sidebar>
  );
}
