import React, { useState, useRef, useEffect } from 'react';
import { Card } from './card';
import { Button } from './button';
import { Input } from './input';
import { MessageCircle, X, Send, Mic, MicOff, Sparkles, Bot } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useAuth } from '../../context/AuthContext';

interface Message {
  id: string;
  text: string;
  isBot: boolean;
  timestamp: Date;
}

const intelligentAnswers: Record<string, string[]> = {
  'inscription': [
    'Pour vous inscrire à un cours, rendez-vous dans la section "Cours", sélectionnez le cours qui vous intéresse et cliquez sur "S\'inscrire".',
    'L\'inscription est simple : choisissez votre niveau (Écolier, Collégien, Lycéen, Étudiant) puis explorez les cours adaptés.',
    'Vous pouvez vous inscrire directement depuis la page d\'accueil en cliquant sur "Découvrir nos cours".'
  ],
  'certificat': [
    'Pour obtenir votre certificat, vous devez compléter 100% du cours. Le certificat sera disponible dans votre section "Bibliothèque".',
    'Les certificats sont délivrés automatiquement après validation de tous les modules du cours.',
    'Seuls les abonnés Premium peuvent télécharger leurs certificats au format PDF.'
  ],
  'premium': [
    'Pour passer en Premium, allez dans "Premium" dans le menu et choisissez l\'abonnement qui vous convient.',
    'Premium vous donne accès aux téléchargements, certificats, sessions live et contenus exclusifs.',
    'L\'abonnement Premium coûte 15 000 FCFA/mois et inclut tous les avantages de la plateforme.'
  ],
  
  // Questions pédagogiques
  'svt': [
    'La SVT (Sciences de la Vie et de la Terre) étudie les êtres vivants et notre planète. Elle comprend la biologie, la géologie et l\'écologie.',
    'En SVT, vous apprendrez le fonctionnement du corps humain, l\'évolution des espèces, et les phénomènes géologiques.',
    'Nos cours de SVT couvrent la génétique, l\'écologie, la géologie et la physiologie humaine.'
  ],
  'équation': [
    'Pour résoudre une équation du 2nd degré ax² + bx + c = 0, utilisez la formule : x = (-b ± √(b²-4ac)) / 2a',
    'Les équations du second degré peuvent avoir 0, 1 ou 2 solutions selon le discriminant Δ = b²-4ac.',
    'Je peux vous aider avec des exercices pratiques d\'équations du second degré si vous le souhaitez.'
  ],
  'mathématiques': [
    'Les mathématiques sur DOREMI couvrent l\'algèbre, la géométrie, les statistiques et l\'analyse.',
    'Nous proposons des cours adaptés à tous les niveaux : du primaire aux études supérieures.',
    'Nos professeurs utilisent des méthodes pédagogiques modernes avec de nombreux exercices pratiques.'
  ],
  
  // Questions pour formateurs
  'ajouter cours': [
    'Pour ajouter un cours, allez dans votre tableau de bord formateur et cliquez sur "Créer un cours".',
    'Vous pouvez créer des cours avec vidéos, documents PDF, quiz et sessions live.',
    'Assurez-vous d\'avoir téléchargé votre CV et votre pièce d\'identité pour valider votre profil formateur.'
  ],
  'live': [
    'Pour démarrer une session live, cliquez sur "Nouveau Live" dans votre dashboard formateur.',
    'Les sessions live sont automatiquement notifiées aux étudiants inscrits par email.',
    'Les sessions live sont réservées aux formateurs vérifiés avec un profil complet.'
  ],
  
  // Questions d'orientation
  'orientation': [
    'Pour choisir votre parcours, commencez par définir votre niveau actuel et vos objectifs.',
    'Nous proposons des tests d\'orientation gratuits pour vous aider à trouver votre voie.',
    'Nos conseillers pédagogiques peuvent vous accompagner dans votre choix d\'orientation.'
  ],
  'niveau': [
    'Choisissez votre niveau lors de l\'inscription : Écolier, Collégien, Lycéen ou Étudiant.',
    'Chaque niveau propose des cours adaptés au programme scolaire sénégalais.',
    'Vous pouvez modifier votre niveau à tout moment dans les paramètres de votre profil.'
  ],

  // Littérature sénégalaise
  'littérature': [
    'Découvrez notre collection de classiques sénégalais : "Une si longue lettre", "Sous l\'orage", "L\'aventure ambiguë".',
    'La littérature sénégalaise est riche et variée, explorant les thèmes de l\'identité, de la tradition et de la modernité.',
    'Nos ouvrages incluent des analyses détaillées pour mieux comprendre les œuvres des grands auteurs africains.'
  ]
};

const getRandomResponse = (responses: string[]): string => {
  return responses[Math.floor(Math.random() * responses.length)];
};

export const Chatbot: React.FC = () => {
  const { user } = useAuth();
  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState<Message[]>([
    {
      id: '1',
      text: `Bonjour ${user?.name || 'cher étudiant'} ! Je suis votre assistant DOREMI intelligent. Comment puis-je vous aider aujourd\'hui ?`,
      isBot: true,
      timestamp: new Date()
    }
  ]);
  const [inputValue, setInputValue] = useState('');
  const [isListening, setIsListening] = useState(false);
  const [isTyping, setIsTyping] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const recognitionRef = useRef<SpeechRecognition | null>(null);

  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  useEffect(() => {
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      recognitionRef.current = new SpeechRecognition();
      recognitionRef.current.continuous = false;
      recognitionRef.current.interimResults = false;
      recognitionRef.current.lang = 'fr-FR';

      recognitionRef.current.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        setInputValue(transcript);
        setIsListening(false);
      };

      recognitionRef.current.onerror = () => {
        setIsListening(false);
      };

      recognitionRef.current.onend = () => {
        setIsListening(false);
      };
    }
  }, []);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  const findIntelligentAnswer = (text: string): string => {
    const lowerText = text.toLowerCase();
    
    // Recherche de mots-clés dans le texte
    for (const [key, responses] of Object.entries(intelligentAnswers)) {
      if (lowerText.includes(key) || 
          lowerText.includes(key.replace('é', 'e')) || 
          lowerText.includes(key.replace('è', 'e'))) {
        return getRandomResponse(responses);
      }
    }

    // Réponses contextuelles selon le rôle de l'utilisateur
    if (user?.role === 'instructor') {
      if (lowerText.includes('cours') || lowerText.includes('enseigner')) {
        return 'En tant que formateur, vous pouvez créer des cours interactifs, organiser des sessions live et suivre les progrès de vos étudiants. Voulez-vous que je vous guide ?';
      }
      if (lowerText.includes('étudiant') || lowerText.includes('élève')) {
        return 'Vous pouvez voir tous vos étudiants inscrits dans la section "Mes Étudiants" et suivre leur progression en temps réel.';
      }
    }

    // Réponses par défaut plus intelligentes
    const defaultResponses = [
      'Je ne suis pas sûr de comprendre votre question. Pouvez-vous me donner plus de détails ?',
      'Votre question est intéressante ! Pouvez-vous la reformuler pour que je puisse mieux vous aider ?',
      'Je peux vous aider avec les cours, les inscriptions, les certificats, ou la littérature sénégalaise. Que souhaitez-vous savoir ?',
      'Pour une assistance personnalisée, n\'hésitez pas à contacter notre équipe pédagogique via la section "Messages".'
    ];

    return getRandomResponse(defaultResponses);
  };

  const handleSendMessage = () => {
    if (!inputValue.trim()) return;

    const userMessage: Message = {
      id: Date.now().toString(),
      text: inputValue,
      isBot: false,
      timestamp: new Date()
    };

    setMessages(prev => [...prev, userMessage]);
    setInputValue('');
    setIsTyping(true);

    // Simulation de frappe plus réaliste
    setTimeout(() => {
      const botResponse: Message = {
        id: (Date.now() + 1).toString(),
        text: findIntelligentAnswer(inputValue),
        isBot: true,
        timestamp: new Date()
      };
      setMessages(prev => [...prev, botResponse]);
      setIsTyping(false);
    }, Math.random() * 1500 + 1000); // Entre 1 et 2.5 secondes
  };

  const handleVoiceInput = () => {
    if (recognitionRef.current && !isListening) {
      setIsListening(true);
      recognitionRef.current.start();
    } else if (isListening) {
      setIsListening(false);
      recognitionRef.current?.stop();
    }
  };

  if (!isOpen) {
    return (
      <div className="fixed bottom-6 right-6 z-50">
        <Button
          onClick={() => setIsOpen(true)}
          className="w-16 h-16 rounded-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 shadow-xl animate-pulse relative"
        >
          <Bot className="w-8 h-8" />
          <div className="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full animate-ping"></div>
          <Sparkles className="w-4 h-4 absolute -top-2 -left-2 text-yellow-400 animate-bounce" />
        </Button>
      </div>
    );
  }

  return (
    <div className="fixed bottom-6 right-6 z-50 w-80 h-[500px]">
      <Card className="w-full h-full flex flex-col shadow-2xl border-0 bg-card overflow-hidden">
        {/* Header amélioré */}
        <div className="flex items-center justify-between p-4 border-b bg-gradient-to-r from-blue-600 to-green-600 text-white">
          <div className="flex items-center gap-3">
            <div className="relative">
              <Bot className="w-6 h-6" />
              <div className="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
            </div>
            <div>
              <span className="font-semibold">Assistant DOREMI</span>
              <p className="text-xs opacity-90">IA Éducative Intelligente</p>
            </div>
          </div>
          <Button
            variant="ghost"
            size="sm"
            onClick={() => setIsOpen(false)}
            className="text-white hover:bg-white/20"
          >
            <X className="w-4 h-4" />
          </Button>
        </div>

        {/* Messages */}
        <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
          {messages.map((message) => (
            <div
              key={message.id}
              className={cn(
                "flex",
                message.isBot ? "justify-start" : "justify-end"
              )}
            >
              <div
                className={cn(
                  "max-w-[85%] rounded-2xl px-4 py-3 text-sm shadow-md",
                  message.isBot
                    ? "bg-white dark:bg-gray-700 text-foreground border border-gray-200 dark:border-gray-600"
                    : "bg-gradient-to-r from-blue-600 to-green-600 text-white"
                )}
              >
                {message.text}
                <div className={cn(
                  "text-xs mt-1 opacity-70",
                  message.isBot ? "text-muted-foreground" : "text-white/70"
                )}>
                  {message.timestamp.toLocaleTimeString('fr-FR', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                  })}
                </div>
              </div>
            </div>
          ))}
          
          {isTyping && (
            <div className="flex justify-start">
              <div className="bg-white dark:bg-gray-700 rounded-2xl px-4 py-3 border border-gray-200 dark:border-gray-600 shadow-md">
                <div className="flex space-x-1">
                  <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                  <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '0.1s' }}></div>
                  <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '0.2s' }}></div>
                </div>
              </div>
            </div>
          )}
          
          <div ref={messagesEndRef} />
        </div>

        {/* Input amélioré */}
        <div className="p-4 border-t bg-white dark:bg-gray-800">
          <div className="flex gap-2">
            <Input
              value={inputValue}
              onChange={(e) => setInputValue(e.target.value)}
              onKeyPress={(e) => e.key === 'Enter' && handleSendMessage()}
              placeholder="Posez-moi une question..."
              className="flex-1 rounded-full border-gray-300 focus:border-blue-500"
            />
            <Button
              variant="outline"
              size="sm"
              onClick={handleVoiceInput}
              className={cn(
                "px-3 rounded-full",
                isListening && "bg-red-500 text-white hover:bg-red-600"
              )}
            >
              {isListening ? <MicOff className="w-4 h-4" /> : <Mic className="w-4 h-4" />}
            </Button>
            <Button 
              onClick={handleSendMessage} 
              size="sm" 
              className="rounded-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700"
              disabled={!inputValue.trim()}
            >
              <Send className="w-4 h-4" />
            </Button>
          </div>
          <div className="mt-2 text-xs text-center text-muted-foreground">
            Propulsé par l'IA DOREMI • Disponible 24h/24
          </div>
        </div>
      </Card>
    </div>
  );
};
