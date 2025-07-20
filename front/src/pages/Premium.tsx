
import React, { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  Crown,
  Check,
  X,
  Download,
  MessageSquare,
  BookOpen,
  Award,
  Zap,
  Star,
  Users,
  Shield,
  Sparkles,
  Infinity,
  TrendingUp,
  Video,
  FileText,
  Globe
} from 'lucide-react';
import { useAuth } from '../context/AuthContext';

const subscriptionPlans = [
  {
    id: 'basic',
    name: 'Basique',
    price: 0,
    period: 'Gratuit',
    description: 'Parfait pour découvrir DOREMI',
    features: [
      'Accès aux cours publics',
      'Tableau de bord personnel',
      'Support communautaire',
      'Progression sauvegardée'
    ],
    limitations: [
      'Pas de téléchargement',
      'Pas de certificats',
      'Messagerie limitée',
      'Pas de contenu premium'
    ],
    buttonText: 'Plan actuel',
    popular: false,
    gradient: 'from-gray-400 to-gray-600'
  },
  {
    id: 'standard',
    name: 'Standard',
    price: 10,
    period: '/mois',
    description: 'Idéal pour les étudiants sérieux',
    features: [
      'Tous les avantages Basique',
      'Accès à tous les cours',
      'Téléchargement PDF illimité',
      'Messagerie étendue',
      'Support prioritaire',
      'Certificats de complétion'
    ],
    limitations: [
      'Pas de téléchargement vidéo',
      'Contenu premium limité'
    ],
    buttonText: 'Choisir Standard',
    popular: false,
    gradient: 'from-blue-500 to-blue-700'
  },
  {
    id: 'premium',
    name: 'Premium',
    price: 20,
    period: '/mois',
    description: 'L\'expérience complète DOREMI',
    features: [
      'Tous les avantages Standard',
      'Téléchargement vidéos HD',
      'Contenu exclusif Premium',
      'Certificats officiels PDF',
      'Support 24/7 prioritaire',
      'Groupes d\'étude privés',
      'Stockage illimité',
      'Accès anticipé aux nouveautés'
    ],
    limitations: [],
    buttonText: 'Choisir Premium',
    popular: true,
    gradient: 'from-yellow-400 via-yellow-500 to-yellow-600'
  }
];

const whyPremiumFeatures = [
  {
    icon: Infinity,
    title: 'Accès Illimité',
    description: 'Tous les cours, toutes les ressources, sans aucune limite'
  },
  {
    icon: Download,
    title: 'Téléchargement',
    description: 'Accédez à vos cours hors ligne, partout et à tout moment'
  },
  {
    icon: Star,
    title: 'Contenu Exclusif',
    description: 'Cours premium, masterclass et contenus réservés aux membres'
  },
  {
    icon: Award,
    title: 'Certificats Officiels',
    description: 'Certificats PDF reconnus pour valoriser votre CV'
  },
  {
    icon: Users,
    title: 'Communauté VIP',
    description: 'Rejoignez des groupes d\'étude privés avec d\'autres Premium'
  },
  {
    icon: Shield,
    title: 'Support Prioritaire',
    description: 'Support 24/7 avec temps de réponse garanti'
  }
];

const comparisonFeatures = [
  { name: 'Accès aux cours publics', basic: true, standard: true, premium: true },
  { name: 'Accès à tous les cours', basic: false, standard: true, premium: true },
  { name: 'Téléchargement PDF', basic: false, standard: true, premium: true },
  { name: 'Téléchargement vidéos', basic: false, standard: false, premium: true },
  { name: 'Certificats de complétion', basic: false, standard: true, premium: true },
  { name: 'Certificats officiels PDF', basic: false, standard: false, premium: true },
  { name: 'Messagerie', basic: 'Limitée', standard: 'Étendue', premium: 'Illimitée' },
  { name: 'Support', basic: 'Communautaire', standard: 'Prioritaire', premium: '24/7 VIP' },
  { name: 'Contenu premium', basic: false, standard: 'Partiel', premium: true },
  { name: 'Groupes d\'étude', basic: false, standard: false, premium: true },
  { name: 'Stockage', basic: 'Limité', standard: '5GB', premium: 'Illimité' }
];

const PricingCard = ({ plan, delay }: { plan: typeof subscriptionPlans[0], delay: number }) => {
  const { user } = useAuth();
  const [isVisible, setIsVisible] = useState(false);
  const [isHovered, setIsHovered] = useState(false);

  useEffect(() => {
    const timer = setTimeout(() => setIsVisible(true), delay);
    return () => clearTimeout(timer);
  }, [delay]);

  const handleSubscribe = () => {
    // Simulation de l'intégration Stripe
    if (plan.id === 'basic') {
      alert('Vous utilisez déjà le plan Basique !');
      return;
    }
    
    // Ici sera l'intégration Stripe réelle
    alert(`Redirection vers Stripe pour l'abonnement ${plan.name} à ${plan.price}€${plan.period}...`);
    console.log('Stripe integration:', {
      planId: plan.id,
      amount: plan.price * 100, // Stripe utilise les centimes
      currency: 'eur',
      userId: user?.id
    });
  };

  return (
    <Card 
      className={`relative transform transition-all duration-700 ${
        isVisible ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'
      } ${isHovered ? 'scale-105' : 'scale-100'} ${
        plan.popular ? 'border-4 border-yellow-400 shadow-2xl' : 'border-2 border-border'
      }`}
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
    >
      {plan.popular && (
        <div className="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
          <Badge className="premium-badge px-6 py-2 text-lg animate-pulse">
            <Sparkles className="w-5 h-5 mr-2" />
            Le plus populaire
          </Badge>
        </div>
      )}
      
      <CardHeader className={`text-center ${plan.popular ? 'pt-8' : 'pt-6'}`}>
        <div className={`w-16 h-16 bg-gradient-to-r ${plan.gradient} rounded-2xl flex items-center justify-center mx-auto mb-4 animate-bounce-gentle`}>
          {plan.id === 'basic' && <Globe className="w-8 h-8 text-white" />}
          {plan.id === 'standard' && <BookOpen className="w-8 h-8 text-white" />}
          {plan.id === 'premium' && <Crown className="w-8 h-8 text-white" />}
        </div>
        
        <CardTitle className={`text-3xl ${plan.popular ? 'bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-transparent' : ''}`}>
          {plan.name}
        </CardTitle>
        
        <CardDescription className="text-lg">{plan.description}</CardDescription>
        
        <div className="text-5xl font-bold mt-4">
          {plan.price === 0 ? (
            <span className="text-green-600">Gratuit</span>
          ) : (
            <>
              <span className={plan.popular ? 'bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-transparent' : ''}>
                {plan.price}€
              </span>
              <span className="text-lg font-normal text-muted-foreground">{plan.period}</span>
            </>
          )}
        </div>
      </CardHeader>
      
      <CardContent className="space-y-6">
        <div className="space-y-4">
          <h4 className="font-semibold text-green-600 flex items-center">
            <Check className="w-5 h-5 mr-2" />
            Inclus :
          </h4>
          <ul className="space-y-3">
            {plan.features.map((feature, index) => (
              <li key={index} className="flex items-center gap-3">
                <div className="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                  <Check className="w-4 h-4 text-white" />
                </div>
                <span className="text-sm">{feature}</span>
              </li>
            ))}
          </ul>
          
          {plan.limitations.length > 0 && (
            <>
              <h4 className="font-semibold text-red-600 flex items-center mt-6">
                <X className="w-5 h-5 mr-2" />
                Non inclus :
              </h4>
              <ul className="space-y-2">
                {plan.limitations.map((limitation, index) => (
                  <li key={index} className="flex items-center gap-3">
                    <X className="w-5 h-5 text-red-500 flex-shrink-0" />
                    <span className="text-sm text-muted-foreground">{limitation}</span>
                  </li>
                ))}
              </ul>
            </>
          )}
        </div>
        
        <Button 
          size="lg"
          className={`w-full ${
            plan.popular 
              ? 'bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 text-black font-bold shadow-lg' 
              : plan.id === 'basic'
              ? 'bg-gray-400 text-gray-700'
              : 'bg-primary hover:bg-primary/90'
          } transform hover:scale-105 transition-all duration-200`}
          onClick={handleSubscribe}
          disabled={plan.id === 'basic'}
        >
          {plan.id === 'premium' && <Zap className="w-5 h-5 mr-2" />}
          {plan.buttonText}
        </Button>
      </CardContent>
    </Card>
  );
};

const FeatureCard = ({ feature, delay }: { feature: typeof whyPremiumFeatures[0], delay: number }) => {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const timer = setTimeout(() => setIsVisible(true), delay);
    return () => clearTimeout(timer);
  }, [delay]);

  return (
    <Card className={`transform transition-all duration-700 ${isVisible ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'} hover:scale-105 hover:shadow-xl`}>
      <CardContent className="p-6 text-center">
        <div className="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce-gentle">
          <feature.icon className="w-8 h-8 text-white" />
        </div>
        <h3 className="text-lg font-semibold mb-2">{feature.title}</h3>
        <p className="text-muted-foreground">{feature.description}</p>
      </CardContent>
    </Card>
  );
};

export const Premium: React.FC = () => {
  const { user } = useAuth();

  return (
    <div className="space-y-16 overflow-hidden">
      {/* Header */}
      <div className="text-center space-y-6 py-16 bg-gradient-to-br from-blue-50 via-purple-50 to-yellow-50 rounded-3xl">
        <div className="relative">
          <div className="w-24 h-24 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-3xl flex items-center justify-center mx-auto animate-bounce">
            <Crown className="w-12 h-12 text-white" />
          </div>
          <div className="absolute -top-2 -right-2 w-8 h-8 bg-yellow-300 rounded-full animate-ping"></div>
          <div className="absolute -bottom-2 -left-2 w-6 h-6 bg-yellow-400 rounded-full animate-pulse"></div>
        </div>
        <h1 className="text-6xl font-bold bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 bg-clip-text text-transparent">
          DOREMI Premium
        </h1>
        <p className="text-xl text-muted-foreground max-w-3xl mx-auto">
          Débloquez le potentiel illimité de votre apprentissage avec nos abonnements adaptés à tous les besoins
        </p>
      </div>

      {/* Pricing Cards */}
      <div className="space-y-8">
        <h2 className="text-4xl font-bold text-center">Choisissez votre plan</h2>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
          {subscriptionPlans.map((plan, index) => (
            <PricingCard key={plan.id} plan={plan} delay={index * 200} />
          ))}
        </div>
      </div>

      {/* Why Premium */}
      <div className="space-y-8">
        <h2 className="text-4xl font-bold text-center">Pourquoi devenir Premium ?</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {whyPremiumFeatures.map((feature, index) => (
            <FeatureCard key={index} feature={feature} delay={index * 150} />
          ))}
        </div>
      </div>

      {/* Comparison Table */}
      <Card className="max-w-6xl mx-auto">
        <CardHeader>
          <CardTitle className="text-3xl text-center">Comparaison détaillée</CardTitle>
          <CardDescription className="text-center text-lg">
            Découvrez en détail ce qui est inclus dans chaque plan
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b-2">
                  <th className="text-left py-4 px-6 font-bold">Fonctionnalité</th>
                  <th className="text-center py-4 px-6 font-bold">Basique</th>
                  <th className="text-center py-4 px-6 font-bold bg-blue-50 rounded-t-lg">Standard</th>
                  <th className="text-center py-4 px-6 font-bold bg-gradient-to-r from-yellow-100 to-orange-100 rounded-t-lg">
                    Premium
                  </th>
                </tr>
              </thead>
              <tbody>
                {comparisonFeatures.map((feature, index) => (
                  <tr key={index} className="border-b hover:bg-muted/30 transition-colors">
                    <td className="py-4 px-6 font-medium">{feature.name}</td>
                    <td className="text-center py-4 px-6">
                      {typeof feature.basic === 'boolean' ? (
                        feature.basic ? (
                          <Check className="w-6 h-6 text-green-500 mx-auto" />
                        ) : (
                          <X className="w-6 h-6 text-red-500 mx-auto" />
                        )
                      ) : (
                        <span className="text-sm text-orange-600 font-medium">{feature.basic}</span>
                      )}
                    </td>
                    <td className="text-center py-4 px-6 bg-blue-50">
                      {typeof feature.standard === 'boolean' ? (
                        feature.standard ? (
                          <Check className="w-6 h-6 text-green-500 mx-auto" />
                        ) : (
                          <X className="w-6 h-6 text-red-500 mx-auto" />
                        )
                      ) : (
                        <span className="text-sm text-blue-600 font-medium">{feature.standard}</span>
                      )}
                    </td>
                    <td className="text-center py-4 px-6 bg-gradient-to-r from-yellow-50 to-orange-50">
                      {typeof feature.premium === 'boolean' ? (
                        feature.premium ? (
                          <Check className="w-6 h-6 text-green-500 mx-auto" />
                        ) : (
                          <X className="w-6 h-6 text-red-500 mx-auto" />
                        )
                      ) : (
                        <span className="text-sm text-yellow-600 font-medium">{feature.premium}</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      {/* CTA Banner */}
      <Card className="max-w-5xl mx-auto bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50 border-2 border-yellow-200">
        <CardContent className="p-16 text-center">
          <div className="animate-bounce mb-8">
            <Crown className="w-20 h-20 text-yellow-500 mx-auto" />
          </div>
          <h3 className="text-4xl font-bold mb-6 bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">
            Prêt à transformer votre apprentissage ?
          </h3>
          <p className="text-xl text-muted-foreground mb-10 max-w-3xl mx-auto">
            Rejoignez des milliers d'étudiants qui ont choisi DOREMI Premium pour accélérer leur apprentissage et atteindre leurs objectifs professionnels
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <Button 
              size="lg" 
              className="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 text-black font-bold px-10 py-4 text-xl shadow-xl transform hover:scale-110 transition-all duration-300"
              onClick={() => alert('Redirection vers l\'abonnement Premium...')}
            >
              <Sparkles className="w-6 h-6 mr-3" />
              Rejoindre Premium maintenant
            </Button>
            <Button 
              size="lg" 
              variant="outline"
              className="px-10 py-4 text-lg border-2 border-blue-500 hover:bg-blue-50"
              onClick={() => alert('Redirection vers l\'abonnement Standard...')}
            >
              <TrendingUp className="w-6 h-6 mr-3" />
              Essayer Standard
            </Button>
          </div>
          <p className="text-sm text-muted-foreground mt-6">
            ✨ 7 jours d'essai gratuit • Annulation à tout moment • Support 24/7
          </p>
        </CardContent>
      </Card>
    </div>
  );
};
