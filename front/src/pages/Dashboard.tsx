
import React from 'react';
import { useAuth } from '../context/AuthContext';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { Badge } from '@/components/ui/badge';
import {
  BookOpen,
  Users,
  TrendingUp,
  Star,
  Clock,
  Award,
  MessageSquare,
  Calendar,
  Play,
  Download
} from 'lucide-react';
import { mockCourses } from '../data/mockData';

const StudentDashboard = () => {
  const { user } = useAuth();
  const userCourses = mockCourses.filter(course => course.progress && course.progress > 0);
  const recommendedCourses = mockCourses.filter(course => !course.progress || course.progress === 0).slice(0, 3);

  return (
    <div className="space-y-6">
      {/* Welcome Section */}
      <div className="gradient-bg rounded-2xl p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold mb-2">Bonjour, {user?.name} !</h1>
            <p className="text-blue-100">Continuez votre apprentissage avec DOREMI</p>
          </div>
          <div className="text-right">
            <div className="text-3xl font-bold">{userCourses.length}</div>
            <div className="text-blue-100">Cours en cours</div>
          </div>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Cours terminés</p>
                <p className="text-2xl font-bold">3</p>
              </div>
              <Award className="w-8 h-8 text-green-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Heures d'étude</p>
                <p className="text-2xl font-bold">24h</p>
              </div>
              <Clock className="w-8 h-8 text-blue-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Certificats</p>
                <p className="text-2xl font-bold">2</p>
              </div>
              <Star className="w-8 h-8 text-yellow-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Messages</p>
                <p className="text-2xl font-bold">5</p>
              </div>
              <MessageSquare className="w-8 h-8 text-purple-500" />
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Continue Learning */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <BookOpen className="w-5 h-5" />
              Continuer l'apprentissage
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {userCourses.map((course) => (
              <div key={course.id} className="flex items-center gap-4 p-3 rounded-lg border hover:bg-muted/50 transition-colors">
                <img src={course.thumbnail} alt={course.title} className="w-12 h-12 rounded-lg object-cover" />
                <div className="flex-1">
                  <h4 className="font-medium text-sm">{course.title}</h4>
                  <div className="flex items-center gap-2 mt-1">
                    <Progress value={course.progress} className="flex-1 h-2" />
                    <span className="text-xs text-muted-foreground">{course.progress}%</span>
                  </div>
                </div>
                <Button size="sm" variant="ghost">
                  <Play className="w-4 h-4" />
                </Button>
              </div>
            ))}
          </CardContent>
        </Card>

        {/* Recommended Courses */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <TrendingUp className="w-5 h-5" />
              Cours recommandés
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {recommendedCourses.map((course) => (
              <div key={course.id} className="flex items-center gap-4 p-3 rounded-lg border hover:bg-muted/50 transition-colors">
                <img src={course.thumbnail} alt={course.title} className="w-12 h-12 rounded-lg object-cover" />
                <div className="flex-1">
                  <h4 className="font-medium text-sm">{course.title}</h4>
                  <div className="flex items-center gap-2 mt-1">
                    <Badge variant="secondary" className="text-xs">{course.level}</Badge>
                    {course.isPremium && <Badge className="premium-badge text-xs">Premium</Badge>}
                  </div>
                </div>
                <Button size="sm">Commencer</Button>
              </div>
            ))}
          </CardContent>
        </Card>
      </div>

      {/* Recent Activity */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Calendar className="w-5 h-5" />
            Activité récente
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            <div className="flex items-center gap-3 text-sm">
              <div className="w-2 h-2 bg-green-500 rounded-full"></div>
              <span>Cours "Découverte de l'assurance vie" terminé</span>
              <span className="text-muted-foreground ml-auto">Il y a 2 jours</span>
            </div>
            <div className="flex items-center gap-3 text-sm">
              <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
              <span>Nouveau message de Marie Dubois</span>
              <span className="text-muted-foreground ml-auto">Il y a 3 jours</span>
            </div>
            <div className="flex items-center gap-3 text-sm">
              <div className="w-2 h-2 bg-yellow-500 rounded-full"></div>
              <span>Certificat téléchargé</span>
              <span className="text-muted-foreground ml-auto">Il y a 1 semaine</span>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

const InstructorDashboard = () => {
  const { user } = useAuth();
  
  return (
    <div className="space-y-6">
      <div className="gradient-bg rounded-2xl p-6 text-white">
        <h1 className="text-2xl font-bold mb-2">Tableau de bord formateur</h1>
        <p className="text-blue-100">Gérez vos cours et suivez vos étudiants</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Mes cours</p>
                <p className="text-2xl font-bold">8</p>
              </div>
              <BookOpen className="w-8 h-8 text-blue-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Étudiants actifs</p>
                <p className="text-2xl font-bold">156</p>
              </div>
              <Users className="w-8 h-8 text-green-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Messages</p>
                <p className="text-2xl font-bold">12</p>
              </div>
              <MessageSquare className="w-8 h-8 text-purple-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Note moyenne</p>
                <p className="text-2xl font-bold">4.6</p>
              </div>
              <Star className="w-8 h-8 text-yellow-500" />
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Mes cours populaires</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {mockCourses.slice(0, 3).map((course) => (
              <div key={course.id} className="flex items-center gap-4 p-3 rounded-lg border">
                <img src={course.thumbnail} alt={course.title} className="w-12 h-12 rounded-lg object-cover" />
                <div className="flex-1">
                  <h4 className="font-medium text-sm">{course.title}</h4>
                  <p className="text-xs text-muted-foreground">{course.studentsCount} étudiants</p>
                </div>
                <div className="text-right">
                  <div className="flex items-center gap-1">
                    <Star className="w-4 h-4 text-yellow-500" />
                    <span className="text-sm">{course.rating}</span>
                  </div>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Messages récents</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-3">
              <div className="flex items-start gap-3 p-3 rounded-lg border">
                <div className="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm">
                  A
                </div>
                <div className="flex-1">
                  <p className="font-medium text-sm">Alice Martin</p>
                  <p className="text-xs text-muted-foreground">Question sur le chapitre 3...</p>
                </div>
                <span className="text-xs text-muted-foreground">2h</span>
              </div>
              <div className="flex items-start gap-3 p-3 rounded-lg border">
                <div className="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">
                  B
                </div>
                <div className="flex-1">
                  <p className="font-medium text-sm">Bob Dupont</p>
                  <p className="text-xs text-muted-foreground">Merci pour le cours!</p>
                </div>
                <span className="text-xs text-muted-foreground">5h</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

const AdminDashboard = () => {
  return (
    <div className="space-y-6">
      <div className="gradient-bg rounded-2xl p-6 text-white">
        <h1 className="text-2xl font-bold mb-2">Dashboard Administrateur</h1>
        <p className="text-blue-100">Vue d'ensemble de la plateforme DOREMI</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Utilisateurs totaux</p>
                <p className="text-2xl font-bold">1,234</p>
              </div>
              <Users className="w-8 h-8 text-blue-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Cours disponibles</p>
                <p className="text-2xl font-bold">86</p>
              </div>
              <BookOpen className="w-8 h-8 text-green-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Abonnés Premium</p>
                <p className="text-2xl font-bold">456</p>
              </div>
              <Star className="w-8 h-8 text-yellow-500" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-muted-foreground">Revenus mensuel</p>
                <p className="text-2xl font-bold">€12,450</p>
              </div>
              <TrendingUp className="w-8 h-8 text-purple-500" />
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Cours les plus populaires</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {mockCourses.slice(0, 4).map((course) => (
              <div key={course.id} className="flex items-center justify-between p-3 rounded-lg border">
                <div className="flex items-center gap-3">
                  <img src={course.thumbnail} alt={course.title} className="w-10 h-10 rounded object-cover" />
                  <div>
                    <h4 className="font-medium text-sm">{course.title}</h4>
                    <p className="text-xs text-muted-foreground">{course.studentsCount} inscrits</p>
                  </div>
                </div>
                <div className="flex items-center gap-1">
                  <Star className="w-4 h-4 text-yellow-500" />
                  <span className="text-sm">{course.rating}</span>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Statistiques récentes</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-3">
              <div className="flex justify-between items-center">
                <span className="text-sm">Nouveaux utilisateurs (7j)</span>
                <span className="font-semibold text-green-600">+45</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm">Cours créés (30j)</span>
                <span className="font-semibold text-blue-600">12</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm">Taux de completion</span>
                <span className="font-semibold">73%</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm">Satisfaction moyenne</span>
                <span className="font-semibold text-yellow-600">4.6/5</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export const Dashboard: React.FC = () => {
  const { user } = useAuth();

  if (!user) return null;

  switch (user.role) {
    case 'student':
      return <StudentDashboard />;
    case 'instructor':
      return <InstructorDashboard />;
    case 'admin':
      return <AdminDashboard />;
    default:
      return <StudentDashboard />;
  }
};
