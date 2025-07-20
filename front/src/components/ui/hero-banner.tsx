
import React, { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight, Play } from 'lucide-react';

interface Slide {
  id: number;
  image: string;
  title: string;
  description: string;
  buttonText: string;
}

const slides: Slide[] = [
  {
    id: 1,
    image: "https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1920&h=1080&fit=crop",
    title: "Sénégal",
    description: "Découvrez les opportunités éducatives et économiques du Sénégal. Apprenez avec des experts locaux et développez vos compétences.",
    buttonText: "Découvrir"
  },
  {
    id: 2,
    image: "https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1920&h=1080&fit=crop",
    title: "Gérer ses finances à 20 ans",
    description: "Maîtrisez les bases de la gestion financière personnelle. Budgets, épargne, investissements : tout pour bien commencer.",
    buttonText: "Découvrir"
  },
  {
    id: 3,
    image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=1920&h=1080&fit=crop",
    title: "Formation Professionnelle",
    description: "Développez vos compétences avec nos formations certifiantes. Plus de 500 cours disponibles dans tous les domaines.",
    buttonText: "Découvrir"
  },
  {
    id: 4,
    image: "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&h=1080&fit=crop",
    title: "Entrepreneuriat Digital",
    description: "Lancez votre business en ligne avec nos guides complets. De l'idée à la réalisation, nous vous accompagnons.",
    buttonText: "Découvrir"
  },
  {
    id: 5,
    image: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1920&h=1080&fit=crop",
    title: "Certification DOREMI",
    description: "Obtenez des certifications reconnues qui valorisent votre profil professionnel et augmentent vos opportunités.",
    buttonText: "Découvrir"
  }
];

export const HeroBanner: React.FC = () => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [isAnimating, setIsAnimating] = useState(false);

  useEffect(() => {
    const timer = setInterval(() => {
      setIsAnimating(true);
      setTimeout(() => {
        setCurrentSlide((prev) => (prev + 1) % slides.length);
        setIsAnimating(false);
      }, 300);
    }, 4000);

    return () => clearInterval(timer);
  }, []);

  const goToSlide = (index: number) => {
    if (index !== currentSlide) {
      setIsAnimating(true);
      setTimeout(() => {
        setCurrentSlide(index);
        setIsAnimating(false);
      }, 300);
    }
  };

  const goToPrevious = () => {
    const prevSlide = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
    goToSlide(prevSlide);
  };

  const goToNext = () => {
    const nextSlide = (currentSlide + 1) % slides.length;
    goToSlide(nextSlide);
  };

  const currentSlideData = slides[currentSlide];

  return (
    <div className="relative w-full h-screen overflow-hidden">
      {/* Background Image */}
      <div className="absolute inset-0">
        <img
          src={currentSlideData.image}
          alt={currentSlideData.title}
          className={`w-full h-full object-cover transition-opacity duration-500 ${
            isAnimating ? 'opacity-0' : 'opacity-100'
          }`}
        />
        <div className="absolute inset-0 bg-black/40"></div>
      </div>

      {/* Content */}
      <div className="relative z-10 flex items-center justify-center h-full px-4">
        <div className="text-center text-white max-w-4xl mx-auto">
          <h1 
            className={`text-5xl md:text-7xl font-bold mb-6 transition-all duration-700 ${
              isAnimating ? 'opacity-0 transform translate-y-8' : 'opacity-100 transform translate-y-0'
            }`}
          >
            {currentSlideData.title}
          </h1>
          
          <p 
            className={`text-xl md:text-2xl mb-8 max-w-2xl mx-auto leading-relaxed transition-all duration-700 delay-200 ${
              isAnimating ? 'opacity-0 transform translate-y-8' : 'opacity-100 transform translate-y-0'
            }`}
          >
            {currentSlideData.description}
          </p>
          
          <div 
            className={`transition-all duration-700 delay-400 ${
              isAnimating ? 'opacity-0 transform translate-y-8' : 'opacity-100 transform translate-y-0'
            }`}
          >
            <Button 
              size="lg" 
              className="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white px-8 py-4 text-lg rounded-full shadow-2xl hover:shadow-blue-500/25 transition-all duration-300 hover:scale-105"
            >
              <Play className="w-5 h-5 mr-2" />
              {currentSlideData.buttonText}
            </Button>
          </div>
        </div>
      </div>

      {/* Navigation Arrows */}
      <button
        onClick={goToPrevious}
        className="absolute left-4 top-1/2 -translate-y-1/2 z-20 p-3 bg-white/20 hover:bg-white/30 rounded-full backdrop-blur-sm transition-all duration-300 hover:scale-110"
      >
        <ChevronLeft className="w-6 h-6 text-white" />
      </button>

      <button
        onClick={goToNext}
        className="absolute right-4 top-1/2 -translate-y-1/2 z-20 p-3 bg-white/20 hover:bg-white/30 rounded-full backdrop-blur-sm transition-all duration-300 hover:scale-110"
      >
        <ChevronRight className="w-6 h-6 text-white" />
      </button>

      {/* Slide Indicators */}
      <div className="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex space-x-3">
        {slides.map((_, index) => (
          <button
            key={index}
            onClick={() => goToSlide(index)}
            className={`w-3 h-3 rounded-full transition-all duration-300 ${
              index === currentSlide
                ? 'bg-white scale-125'
                : 'bg-white/50 hover:bg-white/75'
            }`}
          />
        ))}
      </div>

      {/* Progress Bar */}
      <div className="absolute bottom-0 left-0 w-full h-1 bg-white/20">
        <div 
          className="h-full bg-gradient-to-r from-blue-500 to-green-500 transition-all duration-4000 ease-linear"
          style={{ 
            width: `${((currentSlide + 1) / slides.length) * 100}%` 
          }}
        />
      </div>
    </div>
  );
};
