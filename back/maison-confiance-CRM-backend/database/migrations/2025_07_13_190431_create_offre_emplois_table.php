<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offre_emplois', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->text('profil_recherche')->nullable();
            $table->text('missions')->nullable();
            $table->text('competences_requises')->nullable();
            $table->text('avantages')->nullable();
            
            // Informations du poste
            $table->string('type_contrat'); // CDI, CDD, Stage, Alternance, Freelance
            $table->string('niveau_experience'); // Débutant, Intermédiaire, Confirmé, Expert
            $table->string('niveau_etude')->nullable(); // Bac, Bac+2, Bac+3, Bac+5, Doctorat
            $table->string('lieu_travail');
            $table->string('mode_travail')->default('presentiel'); // Présentiel, Hybride, Télétravail
            $table->integer('nombre_poste')->default(1);
            
            // Rémunération
            $table->decimal('salaire_min', 10, 2)->nullable();
            $table->decimal('salaire_max', 10, 2)->nullable();
            $table->string('devise_salaire')->default('EUR');
            $table->string('periode_salaire')->default('annuel'); // Horaire, Mensuel, Annuel
            
            // Dates
            $table->date('date_publication');
            $table->date('date_limite_candidature')->nullable();
            $table->date('date_debut_poste')->nullable();
            $table->date('date_fin_publication')->nullable();
            
            // Statut et visibilité
            $table->enum('statut', ['brouillon', 'active', 'en_cours', 'terminee', 'archivee'])->default('brouillon');
            $table->boolean('publiee')->default(false);
            $table->boolean('urgente')->default(false);
            $table->boolean('sponsorisee')->default(false);
            
            // Relations
            $table->foreignId('departement_id')->nullable()->constrained('departements')->onDelete('set null');
            $table->foreignId('recruteur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Métadonnées
            $table->string('reference')->nullable(); // Référence unique de l'offre
            $table->string('source')->nullable(); // Source de l'offre (site web, réseau social, etc.)
            $table->text('notes_internes')->nullable();
            $table->json('tags')->nullable(); // Tags pour catégoriser l'offre
            
            // Statistiques
            $table->integer('nombre_vues')->default(0);
            $table->integer('nombre_candidatures')->default(0);
            $table->integer('nombre_candidatures_acceptees')->default(0);
            $table->integer('nombre_candidatures_rejetees')->default(0);
            
            // SEO et marketing
            $table->string('slug')->nullable(); // URL SEO-friendly
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Configuration
            $table->boolean('auto_archive')->default(true); // Archiver automatiquement après date limite
            $table->boolean('notifications_email')->default(true);
            $table->boolean('notifications_sms')->default(false);
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['statut', 'publiee']);
            $table->index('date_publication');
            $table->index('date_limite_candidature');
            $table->index('departement_id');
            $table->index('recruteur_id');
            $table->index('type_contrat');
            $table->index('niveau_experience');
            $table->index('lieu_travail');
            $table->index('urgente');
            $table->index('reference');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_emplois');
    }
};
