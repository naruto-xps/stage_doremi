<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API DoReMi - Plateforme scolaire numérique",
 *     version="1.0.0",
 *     description="Bienvenue dans la documentation de l'API **DoReMi**, une plateforme complète de gestion scolaire hybride.\n\n
        ## Modules couverts :\n
        - Gestion des écoles et utilisateurs (élèves, enseignants, parents)\n
        - Cours en ligne et en présentiel\n
        - Suivi des notes et bulletins\n
        - Gestion des documents et devoirs\n
        - Actualités, emploi du temps, messagerie\n
        - Gestion des stages : demandes & offres\n\n
        Cette documentation est à jour pour l'environnement de développement.",
 *     termsOfService="https://doremi-app.test/conditions",
 *     @OA\Contact(
 *         name="Support Technique DoReMi",
 *         email="support@doremi-app.test"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
* @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"name", "surname", "email", "password", "role"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Jean"),
 *     @OA\Property(property="surname", type="string", example="Dupont"),
 *     @OA\Property(property="email", type="string", format="email", example="jean@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="12345678"),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         enum={"admin", "teacher", "student","user"},
 *         example="etudiant"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *  @OA\Schema(
 *     schema="Progression",
 *     type="object",
 *     title="Progression",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="course_id", type="integer", example=10),
 *     @OA\Property(property="progress", type="number", format="float", example=65.5),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-31T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-31T12:15:00Z")
 * )
 *@OA\Schema(
 *     schema="School",
 *     type="object",
 *     title="School",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Lycée Blaise Diagne"),
 *     @OA\Property(property="address", type="string", example="Dakar, Sénégal"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-30T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-30T10:00:00Z")
 * )
 *  @OA\Schema(
 *     schema="Message",
 *     type="object",
 *     title="Message",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="sender_id", type="integer", example=3),
 *     @OA\Property(property="receiver_id", type="integer", example=5),
 *     @OA\Property(property="content", type="string", example="Bonjour, comment ça va ?"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Serveur local de développement"
 * )
 */
class SwaggerController extends Controller
{
    // Aucun code ici, uniquement les annotations Swagger
}
