
import React, { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
  Search, 
  FileText, 
  Download,
  Eye,
  Crown,
  Filter,
  Calendar,
  User,
  Tag,
  Book,
  Star
} from 'lucide-react';
import { useAuth } from '../context/AuthContext';

interface Document {
  id: string;
  title: string;
  author: string;
  description: string;
  type: 'pdf' | 'doc' | 'ppt' | 'video' | 'audio' | 'book';
  category: string;
  genre: string;
  uploadDate: string;
  size: string;
  isPremium: boolean;
  downloadCount: number;
  tags: string[];
  imageUrl?: string;
  rating?: number;
  country?: string;
}

const senegalBooks: Document[] = [
  {
    id: 'book1',
    title: 'Une si longue lettre',
    author: 'Mariama Bâ',
    description: 'Roman épistolaire qui dénonce la condition féminine au Sénégal et plus largement en Afrique. Un chef-d\'œuvre de la littérature africaine.',
    type: 'book',
    category: 'Littérature Sénégalaise',
    genre: 'Roman',
    uploadDate: '2024-01-15',
    size: '2.1 MB',
    isPremium: false,
    downloadCount: 1250,
    tags: ['féminisme', 'société', 'afrique', 'classique'],
    imageUrl: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=600&fit=crop',
    rating: 4.8,
    country: 'Sénégal'
  },
  {
    id: 'book2',
    title: 'Sous l\'orage',
    author: 'Seydou Badian',
    description: 'Premier roman malien qui explore les conflits entre tradition et modernité à travers l\'histoire de Kany et Samou.',
    type: 'book',
    category: 'Littérature Africaine',
    genre: 'Roman',
    uploadDate: '2024-01-20',
    size: '1.8 MB',
    isPremium: false,
    downloadCount: 980,
    tags: ['tradition', 'modernité', 'amour', 'mali'],
    imageUrl: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=600&fit=crop',
    rating: 4.6,
    country: 'Mali'
  },
  {
    id: 'book3',
    title: 'L\'appel des arènes',
    author: 'Aminata Sow Fall',
    description: 'Roman qui met en scène les valeurs traditionnelles sénégalaises face à la modernité, centré sur la lutte traditionnelle.',
    type: 'book',
    category: 'Littérature Sénégalaise',
    genre: 'Roman',
    uploadDate: '2024-02-01',
    size: '2.3 MB',
    isPremium: true,
    downloadCount: 756,
    tags: ['tradition', 'sport', 'identité', 'culture'],
    imageUrl: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=600&fit=crop',
    rating: 4.7,
    country: 'Sénégal'
  },
  {
    id: 'book4',
    title: 'L\'aventure ambiguë',
    author: 'Cheikh Hamidou Kane',
    description: 'Roman philosophique sur le dilemme entre éducation occidentale et valeurs africaines traditionnelles.',
    type: 'book',
    category: 'Littérature Sénégalaise',
    genre: 'Roman philosophique',
    uploadDate: '2024-02-10',
    size: '2.0 MB',
    isPremium: true,
    downloadCount: 1100,
    tags: ['philosophie', 'éducation', 'spiritualité', 'identité'],
    imageUrl: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=600&fit=crop',
    rating: 4.9,
    country: 'Sénégal'
  },
  {
    id: 'book5',
    title: 'Conte d\'Amadou Koumba',
    author: 'Birago Diop',
    description: 'Recueil de contes traditionnels wolof qui préserve la richesse de l\'oralité africaine.',
    type: 'book',
    category: 'Littérature Sénégalaise',
    genre: 'Contes',
    uploadDate: '2024-02-15',
    size: '1.5 MB',
    isPremium: false,
    downloadCount: 890,
    tags: ['contes', 'tradition', 'oralité', 'culture'],
    imageUrl: 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=400&h=600&fit=crop',
    rating: 4.5,
    country: 'Sénégal'
  },
  {
    id: 'book6',
    title: 'Le docker noir',
    author: 'Ousmane Sembène',
    description: 'Premier roman de Sembène qui dénonce l\'exploitation des travailleurs africains dans les ports de Marseille.',
    type: 'book',
    category: 'Littérature Sénégalaise',
    genre: 'Roman social',
    uploadDate: '2024-02-20',
    size: '2.2 MB',
    isPremium: false,
    downloadCount: 1050,
    tags: ['travail', 'immigration', 'justice', 'société'],
    imageUrl: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=600&fit=crop',
    rating: 4.6,
    country: 'Sénégal'
  }
];

const additionalDocuments: Document[] = [
  {
    id: 'doc1',
    title: 'Guide de mathématiques avancées',
    author: 'Professeur Diagne',
    description: 'Manuel complet pour les étudiants en sciences',
    type: 'pdf',
    category: 'Éducation',
    genre: 'Manuel',
    uploadDate: '2024-03-10',
    size: '5.5 MB',
    isPremium: true,
    downloadCount: 456,
    tags: ['mathématiques', 'sciences', 'manuel'],
    imageUrl: 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=400&h=600&fit=crop',
    rating: 4.4
  },
  {
    id: 'doc2',
    title: 'Histoire du Sénégal moderne',
    author: 'Dr. Fatou Sall',
    description: 'Chronologie détaillée de l\'histoire contemporaine sénégalaise',
    type: 'pdf',
    category: 'Histoire',
    genre: 'Essai',
    uploadDate: '2024-03-05',
    size: '3.8 MB',
    isPremium: false,
    downloadCount: 678,
    tags: ['histoire', 'sénégal', 'politique'],
    imageUrl: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=600&fit=crop',
    rating: 4.7
  }
];

const allDocuments = [...senegalBooks, ...additionalDocuments];

export const Library: React.FC = () => {
  const { user } = useAuth();
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [selectedGenre, setSelectedGenre] = useState('all');

  const categories = ['all', 'Littérature Sénégalaise', 'Littérature Africaine', 'Éducation', 'Histoire'];
  const genres = ['all', 'Roman', 'Roman philosophique', 'Roman social', 'Contes', 'Essai', 'Manuel'];

  const filteredDocuments = allDocuments.filter(doc => {
    const matchesSearch = doc.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         doc.author.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         doc.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         doc.tags.some(tag => tag.toLowerCase().includes(searchTerm.toLowerCase()));
    const matchesCategory = selectedCategory === 'all' || doc.category === selectedCategory;
    const matchesGenre = selectedGenre === 'all' || doc.genre === selectedGenre;
    
    return matchesSearch && matchesCategory && matchesGenre;
  });

  const getFileIcon = (type: string) => {
    const iconClass = "w-8 h-8";
    switch (type) {
      case 'book': return <Book className={`${iconClass} text-amber-600`} />;
      case 'pdf': return <FileText className={`${iconClass} text-red-500`} />;
      case 'doc': return <FileText className={`${iconClass} text-blue-500`} />;
      case 'ppt': return <FileText className={`${iconClass} text-orange-500`} />;
      case 'video': return <FileText className={`${iconClass} text-purple-500`} />;
      case 'audio': return <FileText className={`${iconClass} text-green-500`} />;
      default: return <FileText className={`${iconClass} text-gray-500`} />;
    }
  };

  const getTypeLabel = (type: string) => {
    switch (type) {
      case 'book': return 'Livre';
      case 'pdf': return 'PDF';
      case 'doc': return 'Document';
      case 'ppt': return 'Présentation';
      case 'video': return 'Vidéo';
      case 'audio': return 'Audio';
      default: return type.toUpperCase();
    }
  };

  const canAccess = (document: Document) => {
    return !document.isPremium || user?.isPremium;
  };

  const handleDownload = (document: Document) => {
    if (!canAccess(document)) {
      alert('Ce document nécessite un abonnement Premium');
      return;
    }
    alert(`Téléchargement de "${document.title}" en cours...`);
  };

  const handlePreview = (document: Document) => {
    if (!canAccess(document)) {
      alert('Ce document nécessite un abonnement Premium');
      return;
    }
    alert(`Ouverture de la prévisualisation de "${document.title}"`);
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4">
        <h1 className="text-3xl font-bold">Médiathèque</h1>
        <p className="text-muted-foreground">
          Découvrez notre collection d'ouvrages sénégalais et africains, ainsi que nos ressources pédagogiques
        </p>
      </div>

      {/* Filters */}
      <Card>
        <CardContent className="p-4">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder="Rechercher un livre, auteur ou thème..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div className="flex gap-2">
              <Select value={selectedCategory} onValueChange={setSelectedCategory}>
                <SelectTrigger className="w-[200px]">
                  <SelectValue placeholder="Catégorie" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Toutes catégories</SelectItem>
                  {categories.slice(1).map(category => (
                    <SelectItem key={category} value={category}>{category}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              <Select value={selectedGenre} onValueChange={setSelectedGenre}>
                <SelectTrigger className="w-[150px]">
                  <SelectValue placeholder="Genre" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Tous genres</SelectItem>
                  {genres.slice(1).map(genre => (
                    <SelectItem key={genre} value={genre}>{genre}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Results count */}
      <div className="flex justify-between items-center">
        <p className="text-muted-foreground">
          {filteredDocuments.length} document{filteredDocuments.length > 1 ? 's' : ''} trouvé{filteredDocuments.length > 1 ? 's' : ''}
        </p>
      </div>

      {/* Documents Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        {filteredDocuments.map((document) => (
          <Card key={document.id} className={`hover:shadow-lg transition-all duration-300 hover:scale-105 ${!canAccess(document) ? 'opacity-75' : ''}`}>
            <div className="relative">
              <img 
                src={document.imageUrl || 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=600&fit=crop'} 
                alt={document.title}
                className="w-full h-48 object-cover rounded-t-lg"
              />
              {document.isPremium && (
                <Badge className="absolute top-2 right-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white">
                  <Crown className="w-3 h-3 mr-1" />
                  Premium
                </Badge>
              )}
              <div className="absolute top-2 left-2">
                <Badge variant={document.type === 'book' ? 'default' : 'secondary'}>
                  {getTypeLabel(document.type)}
                </Badge>
              </div>
              {document.country && (
                <div className="absolute bottom-2 left-2">
                  <Badge variant="outline" className="bg-white/90">
                    {document.country}
                  </Badge>
                </div>
              )}
            </div>
            
            <CardHeader className="pb-3">
              <div className="flex items-start justify-between gap-3">
                <div className="flex-1">
                  <CardTitle className="text-lg line-clamp-2 mb-1">{document.title}</CardTitle>
                  <p className="text-sm text-muted-foreground font-medium">{document.author}</p>
                  {document.rating && (
                    <div className="flex items-center gap-1 mt-1">
                      <Star className="w-4 h-4 text-yellow-500 fill-current" />
                      <span className="text-sm font-medium">{document.rating}</span>
                    </div>
                  )}
                </div>
              </div>
            </CardHeader>
            
            <CardContent>
              <div className="space-y-3">
                <CardDescription className="line-clamp-3">
                  {document.description}
                </CardDescription>
                
                <div className="flex flex-wrap gap-1">
                  {document.tags.slice(0, 3).map((tag) => (
                    <Badge key={tag} variant="secondary" className="text-xs">
                      <Tag className="w-3 h-3 mr-1" />
                      {tag}
                    </Badge>
                  ))}
                </div>
                
                <div className="space-y-2 text-sm text-muted-foreground">
                  <div className="flex items-center gap-2">
                    <Calendar className="w-4 h-4" />
                    <span>{new Date(document.uploadDate).toLocaleDateString('fr-FR')}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span>{document.size}</span>
                    <span>{document.downloadCount} téléchargements</span>
                  </div>
                </div>
                
                <div className="flex gap-2 pt-2">
                  <Button 
                    variant="outline" 
                    size="sm" 
                    className="flex-1"
                    onClick={() => handlePreview(document)}
                    disabled={!canAccess(document)}
                  >
                    <Eye className="w-4 h-4 mr-2" />
                    Lire
                  </Button>
                  <Button 
                    size="sm" 
                    className="flex-1"
                    onClick={() => handleDownload(document)}
                    disabled={!canAccess(document)}
                  >
                    <Download className="w-4 h-4 mr-2" />
                    Télécharger
                  </Button>
                </div>
                
                {!canAccess(document) && (
                  <div className="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div className="flex items-center gap-2 text-sm text-yellow-800 dark:text-yellow-200">
                      <Crown className="w-4 h-4" />
                      <span>Accès Premium requis</span>
                    </div>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {filteredDocuments.length === 0 && (
        <div className="text-center py-12">
          <Book className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
          <h3 className="text-lg font-semibold mb-2">Aucun document trouvé</h3>
          <p className="text-muted-foreground">
            Essayez de modifier vos critères de recherche
          </p>
        </div>
      )}
    </div>
  );
};
