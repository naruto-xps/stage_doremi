
import React, { useRef } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { User, Camera, Upload } from 'lucide-react';
import { useAuth } from '../../context/AuthContext';

export const ProfilePictureUpload: React.FC = () => {
  const { user, updateProfilePicture } = useAuth();
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleFileSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file && file.type.startsWith('image/')) {
      if (file.size > 5 * 1024 * 1024) { // 5MB limit
        alert('Le fichier est trop volumineux. Taille maximale : 5MB');
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        const result = e.target?.result as string;
        updateProfilePicture(result);
      };
      reader.readAsDataURL(file);
    } else {
      alert('Veuillez sélectionner un fichier image valide (JPG, PNG)');
    }
  };

  const handleUploadClick = () => {
    fileInputRef.current?.click();
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <User className="w-5 h-5" />
          Photo de profil
        </CardTitle>
        <CardDescription>
          Changez votre photo de profil visible par les autres utilisateurs
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div className="flex items-center gap-6">
          <div className="relative cursor-pointer" onClick={handleUploadClick}>
            <div className="w-24 h-24 rounded-full overflow-hidden bg-primary flex items-center justify-center">
              {user?.avatar ? (
                <img
                  src={user.avatar}
                  alt="Photo de profil"
                  className="w-full h-full object-cover"
                />
              ) : (
                <User className="w-12 h-12 text-white" />
              )}
            </div>
            <button className="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
              <Camera className="w-4 h-4" />
            </button>
          </div>
          <div className="space-y-2">
            <Button variant="outline" onClick={handleUploadClick}>
              <Upload className="w-4 h-4 mr-2" />
              Télécharger une photo
            </Button>
            <p className="text-sm text-muted-foreground">
              Formats acceptés : JPG, PNG (max. 5MB)
            </p>
          </div>
          <input
            ref={fileInputRef}
            type="file"
            accept="image/*"
            onChange={handleFileSelect}
            className="hidden"
          />
        </div>
      </CardContent>
    </Card>
  );
};
