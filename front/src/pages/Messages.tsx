
import React, { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { 
  Send, 
  Search, 
  Plus,
  MessageCircle,
  Clock,
  User,
  Crown
} from 'lucide-react';
import { useAuth } from '../context/AuthContext';

interface Message {
  id: string;
  senderId: string;
  senderName: string;
  senderRole: 'student' | 'instructor' | 'admin';
  content: string;
  timestamp: string;
  read: boolean;
}

interface Conversation {
  id: string;
  participantId: string;
  participantName: string;
  participantRole: 'student' | 'instructor' | 'admin';
  lastMessage: string;
  lastMessageTime: string;
  unreadCount: number;
  messages: Message[];
}

const mockConversations: Conversation[] = [
  {
    id: '1',
    participantId: '2',
    participantName: 'Marie Dubois',
    participantRole: 'instructor',
    lastMessage: 'Excellente question sur le chapitre 3 !',
    lastMessageTime: '10:30',
    unreadCount: 2,
    messages: [
      {
        id: '1',
        senderId: '1',
        senderName: 'Vous',
        senderRole: 'student',
        content: 'Bonjour, j\'ai une question sur le chapitre 3 du cours d\'assurance vie.',
        timestamp: '09:15',
        read: true
      },
      {
        id: '2',
        senderId: '2',
        senderName: 'Marie Dubois',
        senderRole: 'instructor',
        content: 'Bonjour ! Je serais ravie de vous aider. Quelle est votre question spécifique ?',
        timestamp: '09:45',
        read: true
      },
      {
        id: '3',
        senderId: '1',
        senderName: 'Vous',
        senderRole: 'student',
        content: 'Je ne comprends pas bien la différence entre assurance vie temporaire et permanente.',
        timestamp: '10:00',
        read: true
      },
      {
        id: '4',
        senderId: '2',
        senderName: 'Marie Dubois',
        senderRole: 'instructor',
        content: 'Excellente question sur le chapitre 3 ! L\'assurance vie temporaire couvre une période déterminée, tandis que l\'assurance permanente couvre toute la vie. Je vais préparer un complément d\'explication pour demain.',
        timestamp: '10:30',
        read: false
      }
    ]
  },
  {
    id: '2',
    participantId: '3',
    participantName: 'Support DOREMI',
    participantRole: 'admin',
    lastMessage: 'Votre abonnement Premium a été activé',
    lastMessageTime: 'Hier',
    unreadCount: 0,
    messages: [
      {
        id: '5',
        senderId: '3',
        senderName: 'Support DOREMI',
        senderRole: 'admin',
        content: 'Bonjour ! Votre abonnement Premium a été activé avec succès. Vous avez maintenant accès à tous les cours Premium et aux fonctionnalités avancées.',
        timestamp: 'Hier 16:20',
        read: true
      }
    ]
  }
];

export const Messages: React.FC = () => {
  const { user } = useAuth();
  const [selectedConversation, setSelectedConversation] = useState<Conversation | null>(mockConversations[0]);
  const [newMessage, setNewMessage] = useState('');
  const [searchTerm, setSearchTerm] = useState('');

  const filteredConversations = mockConversations.filter(conv =>
    conv.participantName.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleSendMessage = () => {
    if (!newMessage.trim() || !selectedConversation) return;

    const message: Message = {
      id: Date.now().toString(),
      senderId: user?.id || '1',
      senderName: 'Vous',
      senderRole: user?.role || 'student',
      content: newMessage,
      timestamp: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
      read: true
    };

    selectedConversation.messages.push(message);
    setNewMessage('');
  };

  const getRoleColor = (role: string) => {
    switch (role) {
      case 'instructor': return 'bg-blue-100 text-blue-800';
      case 'admin': return 'bg-purple-100 text-purple-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getRoleText = (role: string) => {
    switch (role) {
      case 'instructor': return 'Formateur';
      case 'admin': return 'Admin';
      case 'student': return 'Étudiant';
      default: return role;
    }
  };

  return (
    <div className="h-[calc(100vh-8rem)] flex gap-4">
      {/* Conversations List */}
      <Card className="w-80 flex flex-col">
        <CardHeader className="pb-3">
          <div className="flex items-center justify-between">
            <CardTitle className="text-lg">Messages</CardTitle>
            <Button size="sm" variant="outline">
              <Plus className="w-4 h-4 mr-2" />
              Nouveau
            </Button>
          </div>
          <div className="relative">
            <Search className="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground" />
            <Input
              placeholder="Rechercher..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-10"
            />
          </div>
        </CardHeader>
        
        <CardContent className="flex-1 overflow-y-auto p-0">
          <div className="space-y-1">
            {filteredConversations.map((conversation) => (
              <div
                key={conversation.id}
                onClick={() => setSelectedConversation(conversation)}
                className={`p-4 cursor-pointer hover:bg-muted/50 transition-colors border-b ${
                  selectedConversation?.id === conversation.id ? 'bg-muted' : ''
                }`}
              >
                <div className="flex items-start gap-3">
                  <Avatar className="w-10 h-10">
                    <AvatarFallback>
                      {conversation.participantName.split(' ').map(n => n[0]).join('')}
                    </AvatarFallback>
                  </Avatar>
                  
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                      <h4 className="font-medium text-sm truncate">
                        {conversation.participantName}
                      </h4>
                      <Badge className={`text-xs ${getRoleColor(conversation.participantRole)}`}>
                        {getRoleText(conversation.participantRole)}
                      </Badge>
                    </div>
                    
                    <p className="text-sm text-muted-foreground truncate mb-1">
                      {conversation.lastMessage}
                    </p>
                    
                    <div className="flex items-center justify-between">
                      <span className="text-xs text-muted-foreground">
                        {conversation.lastMessageTime}
                      </span>
                      {conversation.unreadCount > 0 && (
                        <Badge variant="destructive" className="text-xs">
                          {conversation.unreadCount}
                        </Badge>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Chat Area */}
      {selectedConversation ? (
        <Card className="flex-1 flex flex-col">
          <CardHeader className="border-b">
            <div className="flex items-center gap-3">
              <Avatar>
                <AvatarFallback>
                  {selectedConversation.participantName.split(' ').map(n => n[0]).join('')}
                </AvatarFallback>
              </Avatar>
              <div>
                <CardTitle className="text-lg">{selectedConversation.participantName}</CardTitle>
                <div className="flex items-center gap-2">
                  <Badge className={`text-xs ${getRoleColor(selectedConversation.participantRole)}`}>
                    {getRoleText(selectedConversation.participantRole)}
                  </Badge>
                  <span className="text-sm text-green-600">● En ligne</span>
                </div>
              </div>
            </div>
          </CardHeader>
          
          <CardContent className="flex-1 overflow-y-auto p-4">
            <div className="space-y-4">
              {selectedConversation.messages.map((message) => (
                <div
                  key={message.id}
                  className={`flex ${message.senderId === user?.id ? 'justify-end' : 'justify-start'}`}
                >
                  <div className={`max-w-[70%] ${
                    message.senderId === user?.id 
                      ? 'bg-primary text-white' 
                      : 'bg-muted'
                  } rounded-lg p-3`}>
                    <div className="flex items-center gap-2 mb-1">
                      <span className="text-sm font-medium">
                        {message.senderName}
                      </span>
                      <span className={`text-xs ${
                        message.senderId === user?.id ? 'text-blue-100' : 'text-muted-foreground'
                      }`}>
                        {message.timestamp}
                      </span>
                    </div>
                    <p className="text-sm">{message.content}</p>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
          
          <div className="border-t p-4">
            <div className="flex gap-2">
              <Textarea
                placeholder="Tapez votre message..."
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                className="flex-1 min-h-[40px] max-h-32"
                onKeyPress={(e) => {
                  if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    handleSendMessage();
                  }
                }}
              />
              <Button onClick={handleSendMessage} className="self-end">
                <Send className="w-4 h-4" />
              </Button>
            </div>
            {!user?.isPremium && (
              <div className="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div className="flex items-center gap-2 text-sm text-yellow-800">
                  <Crown className="w-4 h-4" />
                  <span>Passez au Premium pour des conversations illimitées</span>
                </div>
              </div>
            )}
          </div>
        </Card>
      ) : (
        <Card className="flex-1 flex items-center justify-center">
          <div className="text-center">
            <MessageCircle className="w-16 h-16 text-muted-foreground mx-auto mb-4" />
            <h3 className="text-lg font-semibold mb-2">Sélectionnez une conversation</h3>
            <p className="text-muted-foreground">
              Choisissez une conversation pour commencer à échanger
            </p>
          </div>
        </Card>
      )}
    </div>
  );
};
