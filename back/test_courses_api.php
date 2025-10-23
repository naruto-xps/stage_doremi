<?php
/**
 * Test simple de l'API des cours
 * Usage: php test_courses_api.php
 */

// Configuration
$baseUrl = 'http://localhost:8000/api';

echo "🧪 Test de l'API des cours\n";
echo "==========================\n\n";

// Test 1: Récupérer tous les cours
echo "1. Récupération de tous les cours...\n";
$courses = file_get_contents($baseUrl . '/courses');
if ($courses !== false) {
    $coursesData = json_decode($courses, true);
    echo "✅ " . count($coursesData) . " cours récupérés\n";
    
    if (!empty($coursesData)) {
        $firstCourse = $coursesData[0];
        echo "   Premier cours: {$firstCourse['title']}\n";
        echo "   Thumbnail: " . ($firstCourse['thumbnail'] ?? 'Non défini') . "\n";
        echo "   Durée: " . ($firstCourse['duration'] ?? 'Non définie') . "\n";
        echo "   Note: " . ($firstCourse['rating'] ?? 'Non définie') . "\n";
        echo "   Prix: " . ($firstCourse['price'] ?? 'Gratuit') . "€\n";
        echo "   Niveau de difficulté: " . ($firstCourse['difficulty_level'] ?? 'Non défini') . "\n";
        echo "   Catégorie: " . ($firstCourse['category'] ?? 'Non définie') . "\n";
        echo "   Niveau d'éducation: " . ($firstCourse['education_level'] ?? 'Non défini') . "\n";
    }
} else {
    echo "❌ Erreur lors de la récupération des cours\n";
}

echo "\n";

// Test 2: Filtrage par catégorie
echo "2. Test du filtre par catégorie (Mathématiques)...\n";
$mathCourses = file_get_contents($baseUrl . '/courses?category=Mathématiques');
if ($mathCourses !== false) {
    $mathData = json_decode($mathCourses, true);
    echo "✅ " . count($mathData) . " cours de mathématiques trouvés\n";
} else {
    echo "❌ Erreur lors du filtrage par catégorie\n";
}

echo "\n";

// Test 3: Filtrage par niveau de difficulté
echo "3. Test du filtre par niveau de difficulté (beginner)...\n";
$beginnerCourses = file_get_contents($baseUrl . '/courses?difficulty_level=beginner');
if ($beginnerCourses !== false) {
    $beginnerData = json_decode($beginnerCourses, true);
    echo "✅ " . count($beginnerData) . " cours débutant trouvés\n";
} else {
    echo "❌ Erreur lors du filtrage par niveau de difficulté\n";
}

echo "\n";

// Test 4: Tri par note
echo "4. Test du tri par note (décroissant)...\n";
$sortedCourses = file_get_contents($baseUrl . '/courses?sort_by=rating&sort_order=desc');
if ($sortedCourses !== false) {
    $sortedData = json_decode($sortedCourses, true);
    echo "✅ " . count($sortedData) . " cours triés par note\n";
    
    if (!empty($sortedData)) {
        echo "   Meilleure note: " . $sortedData[0]['rating'] . "\n";
        echo "   Cours: " . $sortedData[0]['title'] . "\n";
    }
} else {
    echo "❌ Erreur lors du tri par note\n";
}

echo "\n";
echo "🎯 Tests terminés !\n";
echo "Pour exécuter les migrations: php artisan migrate\n";
echo "Pour exécuter les seeders: php artisan db:seed\n"; 