
import React, { useState, useMemo } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  Search, 
  Star, 
  Clock, 
  Users,
  PlayCircle,
  Download,
  BookOpen,
  Crown,
  GraduationCap
} from 'lucide-react';
import { mockCourses } from '../data/mockData';
import { useAuth } from '../context/AuthContext';
import { Progress } from '@/components/ui/progress';
import { EDUCATION_CATEGORIES, EDUCATION_LEVEL_LABELS, EducationLevel } from '../types/course';

export const Courses: React.FC = () => {
  const { user } = useAuth();
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [selectedLevel, setSelectedLevel] = useState('all');

  // Get available categories based on user's education level
  const availableCategories = useMemo(() => {
    if (!user?.educationLevel) return [];
    return EDUCATION_CATEGORIES[user.educationLevel];
  }, [user?.educationLevel]);

  const levels = ['all', 'beginner', 'intermediate', 'advanced'];

  const filteredCourses = mockCourses.filter(course => {
    const matchesSearch = course.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         course.description.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || course.category === selectedCategory;
    const matchesLevel = selectedLevel === 'all' || course.level === selectedLevel;
    
    // Filter by education level if user has one set
    const matchesEducationLevel = !user?.educationLevel || 
                                 availableCategories.includes(course.category);
    
    return matchesSearch && matchesCategory && matchesLevel && matchesEducationLevel;
  });

  const getLevelBadgeColor = (level: string) => {
    switch (level) {
      case 'beginner': return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
      case 'intermediate': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300';
      case 'advanced': return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
  };

  const getLevelText = (level: string) => {
    switch (level) {
      case 'beginner': return 'Débutant';
      case 'intermediate': return 'Intermédiaire';
      case 'advanced': return 'Avancé';
      default: return level;
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4">
        <h1 className="text-3xl font-bold">Catalogue de Cours</h1>
        <p className="text-muted-foreground">
          Découvrez nos cours de qualité professionnelle pour développer vos compétences
        </p>
        
        {/* Education Level Indicator */}
        {user?.educationLevel && (
          <div className="flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <GraduationCap className="w-5 h-5 text-blue-600" />
            <span className="text-blue-700 dark:text-blue-300">
              Cours pour le niveau : <strong>{EDUCATION_LEVEL_LABELS[user.educationLevel]}</strong>
            </span>
          </div>
        )}
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="p-4">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder="Rechercher des cours..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div className="flex gap-2">
              <Select value={selectedCategory} onValueChange={setSelectedCategory}>
                <SelectTrigger className="w-[180px]">
                  <SelectValue placeholder="Catégorie" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Toutes catégories</SelectItem>
                  {availableCategories.map(category => (
                    <SelectItem key={category} value={category}>{category}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              <Select value={selectedLevel} onValueChange={setSelectedLevel}>
                <SelectTrigger className="w-[150px]">
                  <SelectValue placeholder="Niveau" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Tous niveaux</SelectItem>
                  <SelectItem value="beginner">Débutant</SelectItem>
                  <SelectItem value="intermediate">Intermédiaire</SelectItem>
                  <SelectItem value="advanced">Avancé</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Results count */}
      <div className="flex justify-between items-center">
        <p className="text-muted-foreground">
          {filteredCourses.length} cours trouvé{filteredCourses.length > 1 ? 's' : ''}
        </p>
      </div>

      {/* Courses Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredCourses.map((course) => (
          <Card key={course.id} className="card-hover group">
            <div className="relative">
              <img 
                src={course.thumbnail} 
                alt={course.title}
                className="w-full h-48 object-cover rounded-t-lg"
              />
              {course.isPremium && (
                <div className="absolute top-3 right-3">
                  <Badge className="premium-badge">
                    <Crown className="w-3 h-3 mr-1" />
                    Premium
                  </Badge>
                </div>
              )}
              {course.progress !== undefined && course.progress > 0 && (
                <div className="absolute bottom-3 left-3 right-3">
                  <div className="bg-black/50 rounded-lg p-2">
                    <div className="flex justify-between items-center mb-1">
                      <span className="text-white text-xs">Progression</span>
                      <span className="text-white text-xs">{course.progress}%</span>
                    </div>
                    <Progress value={course.progress} className="h-1" />
                  </div>
                </div>
              )}
            </div>
            
            <CardHeader className="pb-2">
              <div className="flex justify-between items-start gap-2">
                <CardTitle className="text-lg line-clamp-2">{course.title}</CardTitle>
                <div className="flex items-center gap-1 shrink-0">
                  <Star className="w-4 h-4 text-yellow-500 fill-current" />
                  <span className="text-sm font-medium">{course.rating}</span>
                </div>
              </div>
              <CardDescription className="line-clamp-2">
                {course.description}
              </CardDescription>
            </CardHeader>
            
            <CardContent>
              <div className="space-y-3">
                <div className="flex items-center justify-between">
                  <Badge className={getLevelBadgeColor(course.level)}>
                    {getLevelText(course.level)}
                  </Badge>
                  <span className="text-sm text-muted-foreground">{course.category}</span>
                </div>
                
                <div className="flex items-center gap-4 text-sm text-muted-foreground">
                  <div className="flex items-center gap-1">
                    <Clock className="w-4 h-4" />
                    {course.duration}
                  </div>
                  <div className="flex items-center gap-1">
                    <BookOpen className="w-4 h-4" />
                    {course.chaptersCount} chapitres
                  </div>
                  <div className="flex items-center gap-1">
                    <Users className="w-4 h-4" />
                    {course.studentsCount}
                  </div>
                </div>
                
                <div className="flex flex-col gap-2">
                  <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">Par {course.instructor}</span>
                    {course.isPremium && course.price && (
                      <span className="font-semibold text-primary">{course.price}€</span>
                    )}
                  </div>
                  
                  <div className="flex gap-2">
                    <Button className="flex-1" size="sm">
                      <PlayCircle className="w-4 h-4 mr-2" />
                      {course.progress && course.progress > 0 ? 'Continuer' : 'Commencer'}
                    </Button>
                    {user?.isPremium && (
                      <Button variant="outline" size="sm">
                        <Download className="w-4 h-4" />
                      </Button>
                    )}
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {filteredCourses.length === 0 && (
        <div className="text-center py-12">
          <BookOpen className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
          <h3 className="text-lg font-semibold mb-2">Aucun cours trouvé</h3>
          <p className="text-muted-foreground">
            Essayez de modifier vos critères de recherche ou votre niveau d'étude dans les paramètres
          </p>
        </div>
      )}
    </div>
  );
};
