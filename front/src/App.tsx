
import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route, useLocation } from "react-router-dom";
import { SidebarProvider } from "@/components/ui/sidebar";
import { AuthProvider, useAuth } from "./context/AuthContext";
import { LoginForm } from "./components/auth/LoginForm";
import { AppSidebar } from "./components/layout/AppSidebar";
import { MainNavigation } from "./components/layout/MainNavigation";
import { Header } from "./components/layout/Header";
import { Chatbot } from "./components/ui/chatbot";
import { Index } from "./pages/Index";
import { Dashboard } from "./pages/Dashboard";
import { Courses } from "./pages/Courses";
import { Messages } from "./pages/Messages";
import { Library } from "./pages/Library";
import { Premium } from "./pages/Premium";
import { Settings } from "./pages/Settings";
import NotFound from "./pages/NotFound";

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: false,
      refetchOnWindowFocus: false,
    },
  },
});

const AppContent = () => {
  const { isAuthenticated, isLoading } = useAuth();
  const location = useLocation();

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-primary"></div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return <LoginForm />;
  }

  // Use different layouts for homepage vs other pages
  const isHomePage = location.pathname === '/';

  if (isHomePage) {
    return (
      <div className="min-h-screen">
        <MainNavigation />
        <Routes>
          <Route path="/" element={<Index />} />
          <Route path="/courses" element={<Courses />} />
          <Route path="/messages" element={<Messages />} />
          <Route path="/library" element={<Library />} />
          <Route path="/premium" element={<Premium />} />
          <Route path="/settings" element={<Settings />} />
          <Route path="/internships" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Module Stages</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
          <Route path="/news" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Actualités</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
          <Route path="/students" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Mes Étudiants</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
          <Route path="/admin/*" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Administration</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
          <Route path="*" element={<NotFound />} />
        </Routes>
        <Chatbot />
      </div>
    );
  }

  // Use sidebar layout for non-homepage routes
  return (
    <SidebarProvider>
      <div className="min-h-screen flex w-full bg-gray-50 dark:bg-gray-900">
        <AppSidebar />
        <div className="flex-1 flex flex-col">
          <Header />
          <main className="flex-1 p-6 overflow-auto">
            <Routes>
              <Route path="/" element={<Dashboard />} />
              <Route path="/courses" element={<Courses />} />
              <Route path="/messages" element={<Messages />} />
              <Route path="/library" element={<Library />} />
              <Route path="/premium" element={<Premium />} />
              <Route path="/settings" element={<Settings />} />
              <Route path="/internships" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Module Stages</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
              <Route path="/news" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Actualités</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
              <Route path="/students" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Mes Étudiants</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
              <Route path="/admin/*" element={<div className="text-center py-12"><h2 className="text-2xl font-bold mb-4">Administration</h2><p className="text-muted-foreground">En cours de développement...</p></div>} />
              <Route path="*" element={<NotFound />} />
            </Routes>
          </main>
        </div>
        <Chatbot />
      </div>
    </SidebarProvider>
  );
};

const App = () => (
  <QueryClientProvider client={queryClient}>
    <BrowserRouter>
      <AuthProvider>
        <AppContent />
        <Toaster />
        <Sonner />
      </AuthProvider>
    </BrowserRouter>
  </QueryClientProvider>
);

export default App;
