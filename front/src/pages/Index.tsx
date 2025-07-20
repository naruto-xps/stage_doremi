import React from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { HeroBanner } from '@/components/ui/hero-banner';
import { 
  BookOpen, 
  Users, 
  Award, 
  TrendingUp, 
  Star,
  Play,
  Download,
  Clock,
  MessageCircle,
  Bot,
  GraduationCap,
  Library,
  Video,
  FileText,
  Bookmark,
  PenTool
} from 'lucide-react';
import { useAuth } from '../context/AuthContext';
import { Link } from 'react-router-dom';

const coursesAndBooksCarousel = [
  {
    id: 1,
    title: "Découverte de l'assurance vie",
    image: "https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=500&h=300&fit=crop",
    category: "Finance",
    level: "Débutant",
    duration: "2h 30min",
    rating: 4.8,
    students: 1234,
    isPremium: false,
    type: "course"
  },
  {
    id: 2,
    title: "Manuel Complet de Finance Personnelle",
    image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=300&fit=crop",
    category: "Livre",
    level: "Tous niveaux",
    pages: 350,
    rating: 4.9,
    downloads: 2156,
    isPremium: false,
    type: "book"
  },
  {
    id: 3,
    title: "Investir en bourse pour débutants",
    image: "https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=500&h=300&fit=crop",
    category: "Investissement",
    level: "Intermédiaire",
    duration: "3h 15min",
    rating: 4.7,
    students: 987,
    isPremium: true,
    type: "course"
  },
  {
    id: 4,
    title: "Guide de l'Entrepreneur Digital",
    image: "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=500&h=300&fit=crop",
    category: "Entrepreneuriat",
    level: "Avancé",
    pages: 280,
    rating: 4.6,
    downloads: 756,
    isPremium: true,
    type: "book"
  },
  {
    id: 5,
    title: "Cryptomonnaies et Blockchain",
    image: "https://images.unsplash.com/photo-1639762681485-074b7f938ba0?w=500&h=300&fit=crop",
    category: "Technologie",
    level: "Intermédiaire",
    duration: "4h 20min",
    rating: 4.5,
    students: 1567,
    isPremium: true,
    type: "course"
  },
  {
    id: 6,
    title: "Psychologie de l'Investissement",
    image: "https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=500&h=300&fit=crop",
    category: "Psychologie",
    level: "Avancé",
    pages: 420,
    rating: 4.8,
    downloads: 892,
    isPremium: false,
    type: "book"
  }
];

const announcements = [
  "🎓 Nouveau cours : 'Comprendre les cryptomonnaies' maintenant disponible !",
  "📚 Découvrez notre nouvelle bibliothèque digitale avec plus de 500 livres",
  "🏆 Félicitations aux 500 nouveaux certifiés de ce mois !",
  "💡 Webinaire gratuit le 25 octobre : 'Les tendances économiques 2024'",
  "🔥 Offre spéciale : -30% sur tous les cours Premium jusqu'à la fin du mois !",
  "👨‍🏫 Nouveaux formateurs experts rejoignent notre équipe pédagogique"
];

export const Index: React.FC = () => {
  const { user } = useAuth();
  const [currentAnnouncement, setCurrentAnnouncement] = React.useState(0);
  const [showChatbot, setShowChatbot] = React.useState(false);

  React.useEffect(() => {
    const timer = setInterval(() => {
      setCurrentAnnouncement((prev) => (prev + 1) % announcements.length);
    }, 4000);
    return () => clearInterval(timer);
  }, []);

  const handleChatbotClick = () => {
    setShowChatbot(true);
    // Simulation d'ouverture du chatbot
    console.log("Chatbot intelligent activé - Prêt à vous aider !");
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
      {/* Enhanced Announcements Bar */}
      <div className="bg-gradient-to-r from-primary to-blue-600 text-white py-3 px-4 text-center relative overflow-hidden z-30">
        <div className="absolute inset-0 bg-black/10"></div>
        <div className="relative animate-fade-in">
          <div className="flex items-center justify-center gap-2">
            <TrendingUp className="w-4 h-4" />
            {announcements[currentAnnouncement]}
          </div>
        </div>
      </div>

      {/* Hero Banner */}
      <HeroBanner />

      {/* Enhanced Courses and Books Carousel */}
      <section className="py-16 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">Cours & Livres Populaires</h2>
            <p className="text-muted-foreground text-lg">Découvrez nos formations et ouvrages les plus appréciés</p>
          </div>
          
          <Carousel className="w-full max-w-6xl mx-auto">
            <CarouselContent>
              {coursesAndBooksCarousel.map((item) => (
                <CarouselItem key={item.id} className="md:basis-1/2 lg:basis-1/3">
                  <Card className="h-full hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <div className="relative">
                      <img 
                        src={item.image} 
                        alt={item.title}
                        className="w-full h-48 object-cover rounded-t-lg"
                      />
                      {item.isPremium && (
                        <Badge className="absolute top-2 right-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white">
                          Premium
                        </Badge>
                      )}
                      <div className="absolute top-2 left-2">
                        <Badge variant={item.type === 'course' ? 'default' : 'secondary'}>
                          {item.type === 'course' ? (
                            <><Video className="w-3 h-3 mr-1" /> Cours</>
                          ) : (
                            <><FileText className="w-3 h-3 mr-1" /> Livre</>
                          )}
                        </Badge>
                      </div>
                      <div className="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center rounded-t-lg">
                        <Button size="sm" variant="secondary" className="bg-white/90 hover:bg-white">
                          {item.type === 'course' ? (
                            <><Play className="w-4 h-4 mr-2" /> Aperçu</>
                          ) : (
                            <><Bookmark className="w-4 h-4 mr-2" /> Feuilleter</>
                          )}
                        </Button>
                      </div>
                    </div>
                    <CardHeader>
                      <div className="flex justify-between items-start mb-2">
                        <Badge variant="outline">{item.category}</Badge>
                        <div className="flex items-center gap-1">
                          <Star className="w-4 h-4 text-yellow-500 fill-current" />
                          <span className="text-sm font-medium">{item.rating}</span>
                        </div>
                      </div>
                      <CardTitle className="text-lg line-clamp-2">{item.title}</CardTitle>
                      <CardDescription className="flex items-center gap-4 text-sm">
                        {item.type === 'course' ? (
                          <>
                            <span className="flex items-center gap-1">
                              <Clock className="w-4 h-4" />
                              {item.duration}
                            </span>
                            <span className="flex items-center gap-1">
                              <Users className="w-4 h-4" />
                              {item.students} étudiants
                            </span>
                          </>
                        ) : (
                          <>
                            <span className="flex items-center gap-1">
                              <FileText className="w-4 h-4" />
                              {item.pages} pages
                            </span>
                            <span className="flex items-center gap-1">
                              <Download className="w-4 h-4" />
                              {item.downloads} téléchargements
                            </span>
                          </>
                        )}
                      </CardDescription>
                    </CardHeader>
                    <CardContent>
                      <Button className="w-full" variant={item.type === 'course' ? 'default' : 'outline'}>
                        {item.type === 'course' ? 'Commencer le cours' : 'Télécharger le livre'}
                      </Button>
                    </CardContent>
                  </Card>
                </CarouselItem>
              ))}
            </CarouselContent>
            <CarouselPrevious />
            <CarouselNext />
          </Carousel>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="text-4xl font-bold text-primary mb-2">10K+</div>
              <div className="text-muted-foreground">Étudiants actifs</div>
            </div>
            <div className="text-center">
              <div className="text-4xl font-bold text-primary mb-2">500+</div>
              <div className="text-muted-foreground">Cours disponibles</div>
            </div>
            <div className="text-center">
              <div className="text-4xl font-bold text-primary mb-2">200+</div>
              <div className="text-muted-foreground">Formateurs experts</div>
            </div>
            <div className="text-center">
              <div className="text-4xl font-bold text-primary mb-2">95%</div>
              <div className="text-muted-foreground">Satisfaction</div>
            </div>
          </div>
        </div>
      </section>

      {/* Enhanced Intelligent Chatbot */}
      <div className="fixed bottom-6 right-6 z-50">
        <div className="relative">
          {showChatbot && (
            <div className="absolute bottom-16 right-0 w-80 h-96 bg-white rounded-lg shadow-xl border animate-fade-in">
              <div className="p-4 bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-t-lg">
                <h3 className="font-semibold flex items-center gap-2">
                  <Bot className="w-5 h-5" />
                  Assistant DOREMI IA
                </h3>
                <p className="text-sm opacity-90">Comment puis-je vous aider ?</p>
              </div>
              <div className="p-4 space-y-3">
                <div className="bg-gray-100 p-3 rounded-lg">
                  <p className="text-sm">👋 Bonjour ! Je suis votre assistant intelligent. Je peux vous aider à :</p>
                  <ul className="text-xs mt-2 space-y-1">
                    <li>• Trouver des cours adaptés</li>
                    <li>• Répondre à vos questions</li>
                    <li>• Vous guider dans la plateforme</li>
                    <li>• Recommander des livres</li>
                  </ul>
                </div>
                <Button 
                  size="sm" 
                  className="w-full"
                  onClick={() => setShowChatbot(false)}
                >
                  Fermer
                </Button>
              </div>
            </div>
          )}
          <Button 
            size="lg" 
            className="rounded-full w-16 h-16 bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 shadow-lg animate-pulse"
            onClick={handleChatbotClick}
          >
            <Bot className="w-8 h-8" />
          </Button>
          <div className="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full animate-ping"></div>
        </div>
      </div>
    </div>
  );
};
