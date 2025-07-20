
import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { GraduationCap } from 'lucide-react';
import { EducationLevel, EDUCATION_LEVEL_LABELS } from '../../types/course';

interface EducationLevelSelectorProps {
  value?: EducationLevel;
  onChange: (level: EducationLevel) => void;
}

export const EducationLevelSelector: React.FC<EducationLevelSelectorProps> = ({ 
  value, 
  onChange 
}) => {
  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <GraduationCap className="w-5 h-5" />
          Niveau d'étude
        </CardTitle>
        <CardDescription>
          Sélectionnez votre niveau d'étude pour personnaliser vos cours
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div className="space-y-2">
          <Label htmlFor="education-level">Votre niveau actuel</Label>
          <Select 
            value={value} 
            onValueChange={(value: EducationLevel) => onChange(value)}
          >
            <SelectTrigger>
              <SelectValue placeholder="Choisissez votre niveau" />
            </SelectTrigger>
            <SelectContent>
              {Object.entries(EDUCATION_LEVEL_LABELS).map(([key, label]) => (
                <SelectItem key={key} value={key}>
                  {label}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
      </CardContent>
    </Card>
  );
};
