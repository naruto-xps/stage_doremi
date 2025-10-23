<?php

// Déclaration de variables
$nom = "Tapha";
$age = 25;

// Création d'un tableau
$fruits = ["mangue", "orange", "banane", "pomme"];

// Définition d'une fonction
function saluer($nom) {
    return "Bonjour " . $nom . "!";
}

// Utilisation d'une condition
if ($age >= 18) {
    echo saluer($nom) . " Vous êtes majeur.<br>";
} else {
    echo saluer($nom) . " Vous êtes mineur.<br>";
}

// Utilisation d'une boucle pour afficher les fruits
echo "<h3>Liste des fruits :</h3>";
echo "<ul>";
foreach ($fruits as $fruit) {
    echo "<li>" . $fruit . "</li>";
}
echo "</ul>";

// Afficher les informations des variables
echo "<h3>Informations des variables :</h3>";
echo "Nom : " . $nom . "<br>";
echo "Age : " . $age . " ans<br>";

?>