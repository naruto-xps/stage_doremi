<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = config('database.default');
        
        // Supprimer les anciens champs s'ils existent (sauf pour SQLite)
        if ($driver !== 'sqlite') {
            if (Schema::hasColumn('documents', 'theme')) {
                Schema::table('documents', function (Blueprint $table) {
                    $table->dropColumn('theme');
                });
            }
            if (Schema::hasColumn('documents', 'school')) {
                Schema::table('documents', function (Blueprint $table) {
                    $table->dropColumn('school');
                });
            }
            if (Schema::hasColumn('documents', 'file_path')) {
                Schema::table('documents', function (Blueprint $table) {
                    $table->dropColumn('file_path');
                });
            }
        }
        
        // Ajouter les nouveaux champs pour la bibliothèque
        if (!Schema::hasColumn('documents', 'author')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('author')->after('title');
            });
        }
        if (!Schema::hasColumn('documents', 'description')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->text('description')->after('author');
            });
        }
        
        // Gérer le changement de type de la colonne type
        if ($driver === 'pgsql') {
            // Pour PostgreSQL, on doit gérer le changement de type manuellement
            DB::statement('ALTER TABLE documents DROP CONSTRAINT IF EXISTS documents_type_check');
            DB::statement("ALTER TABLE documents ALTER COLUMN type TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE documents ADD CONSTRAINT documents_type_check CHECK (type IN ('pdf', 'doc', 'ppt', 'video', 'audio', 'book'))");
        } elseif ($driver === 'mysql') {
            // Pour MySQL, utiliser la méthode change()
            if (Schema::hasColumn('documents', 'type')) {
                Schema::table('documents', function (Blueprint $table) {
                    $table->enum('type', ['pdf', 'doc', 'ppt', 'video', 'audio', 'book'])->change();
                });
            }
        }
        // Pour SQLite, on ne change pas le type
        
        // Ajouter les autres colonnes une par une
        if (!Schema::hasColumn('documents', 'category')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('category')->after('type');
            });
        }
        if (!Schema::hasColumn('documents', 'genre')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('genre')->after('category');
            });
        }
        if (!Schema::hasColumn('documents', 'upload_date')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->date('upload_date')->after('genre');
            });
        }
        if (!Schema::hasColumn('documents', 'size')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->bigInteger('size')->nullable()->after('upload_date');
            });
        }
        if (!Schema::hasColumn('documents', 'file_url')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('file_url')->nullable()->after('size');
            });
        }
        if (!Schema::hasColumn('documents', 'file_name')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('file_name')->nullable()->after('file_url');
            });
        }
        if (!Schema::hasColumn('documents', 'is_premium')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->boolean('is_premium')->default(false)->after('file_name');
            });
        }
        if (!Schema::hasColumn('documents', 'download_count')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->integer('download_count')->default(0)->after('is_premium');
            });
        }
        if (!Schema::hasColumn('documents', 'tags')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->json('tags')->nullable()->after('download_count');
            });
        }
        if (!Schema::hasColumn('documents', 'image_url')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('tags');
            });
        }
        if (!Schema::hasColumn('documents', 'rating')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->decimal('rating', 2, 1)->nullable()->after('image_url');
            });
        }
        if (!Schema::hasColumn('documents', 'country')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('country')->nullable()->after('rating');
            });
        }
        if (!Schema::hasColumn('documents', 'instructor_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->foreignId('instructor_id')->nullable()->after('country')->constrained('users')->onDelete('set null');
            });
        }
        if (!Schema::hasColumn('documents', 'status')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('published')->after('instructor_id');
            });
        }
        if (!Schema::hasColumn('documents', 'views')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->integer('views')->default(0)->after('status');
            });
        }
        if (!Schema::hasColumn('documents', 'duration')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->integer('duration')->nullable()->after('views');
            });
        }
        if (!Schema::hasColumn('documents', 'thumbnail')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('thumbnail')->nullable()->after('duration');
            });
        }
        
        // Ajouter les index s'ils n'existent pas déjà
        $this->addIndexesIfNotExist();
    }
    
    /**
     * Ajouter les index s'ils n'existent pas déjà
     */
    private function addIndexesIfNotExist()
    {
        // Vérifier et ajouter les index un par un
        $indexes = [
            ['type', 'status'],
            ['category', 'genre'],
            ['instructor_id', 'status'],
            ['is_premium', 'status'],
            ['upload_date', 'status'],
            ['views'],
            ['download_count'],
            ['rating']
        ];
        
        foreach ($indexes as $index) {
            $indexName = 'documents_' . implode('_', $index) . '_index';
            if (!Schema::hasIndex('documents', $indexName)) {
                Schema::table('documents', function (Blueprint $table) use ($index) {
                    if (count($index) === 1) {
                        $table->index($index[0]);
                    } else {
                        $table->index($index);
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = config('database.default');
        
        // Pour SQLite, on ne peut pas supprimer les colonnes facilement
        // On va juste laisser les colonnes existantes
        if ($driver === 'sqlite') {
            return;
        }
        
        // Supprimer les nouveaux champs s'ils existent
        $columnsToDrop = [
            'author', 'description', 'category', 'genre', 'upload_date', 'size',
            'file_url', 'file_name', 'is_premium', 'download_count', 'tags',
            'image_url', 'rating', 'country', 'instructor_id', 'status',
            'views', 'duration', 'thumbnail'
        ];
        
        foreach ($columnsToDrop as $column) {
            if (Schema::hasColumn('documents', $column)) {
                Schema::table('documents', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
        
        // Restaurer les anciens champs s'ils n'existent pas
        if (!Schema::hasColumn('documents', 'theme')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('theme')->after('title');
            });
        }
        if (!Schema::hasColumn('documents', 'school')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('school')->after('theme');
            });
        }
        if (!Schema::hasColumn('documents', 'file_path')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('file_path')->after('school');
            });
        }
        
        // Supprimer les index s'ils existent
        $this->removeIndexesIfExist();
    }
    
    /**
     * Supprimer les index s'ils existent
     */
    private function removeIndexesIfExist()
    {
        $indexes = [
            ['type', 'status'],
            ['category', 'genre'],
            ['instructor_id', 'status'],
            ['is_premium', 'status'],
            ['upload_date', 'status'],
            ['views'],
            ['download_count'],
            ['rating']
        ];
        
        foreach ($indexes as $index) {
            $indexName = 'documents_' . implode('_', $index) . '_index';
            if (Schema::hasIndex('documents', $indexName)) {
                Schema::table('documents', function (Blueprint $table) use ($index) {
                    if (count($index) === 1) {
                        $table->dropIndex([$index[0]]);
                    } else {
                        $table->dropIndex($index);
                    }
                });
            }
        }
    }
};
