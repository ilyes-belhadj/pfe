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
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('candidats')->onDelete('cascade');
            $table->foreignId('departement_id')->nullable()->constrained('departements')->onDelete('set null');
            $table->string('poste_souhaite');
            $table->text('lettre_motivation')->nullable();
            $table->string('cv_path')->nullable(); // Chemin vers le fichier CV
            $table->string('cv_filename')->nullable(); // Nom original du fichier
            $table->string('cv_mime_type')->nullable(); // Type MIME du fichier
            $table->integer('cv_size')->nullable(); // Taille du fichier en bytes
            $table->string('lettre_motivation_path')->nullable(); // Chemin vers la lettre de motivation
            $table->string('lettre_motivation_filename')->nullable();
            $table->string('lettre_motivation_mime_type')->nullable();
            $table->integer('lettre_motivation_size')->nullable();
            $table->enum('statut', [
                'nouvelle',
                'en_cours',
                'entretien_telephone',
                'entretien_rh',
                'entretien_technique',
                'entretien_final',
                'test_technique',
                'reference_check',
                'offre_envoyee',
                'offre_acceptee',
                'embauche',
                'refusee',
                'annulee'
            ])->default('nouvelle');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->date('date_candidature');
            $table->date('date_derniere_action')->nullable();
            $table->date('date_entretien')->nullable();
            $table->time('heure_entretien')->nullable();
            $table->string('lieu_entretien')->nullable();
            $table->text('notes_entretien')->nullable();
            $table->text('evaluation')->nullable(); // Évaluation du candidat
            $table->decimal('note_globale', 3, 1)->nullable(); // Note sur 10
            $table->text('commentaires_rh')->nullable();
            $table->text('commentaires_technique')->nullable();
            $table->text('commentaires_manager')->nullable();
            $table->string('source_candidature')->nullable(); // Site web, recommandation, etc.
            $table->string('campagne_recrutement')->nullable(); // Campagne marketing associée
            $table->boolean('candidature_spontanee')->default(false);
            $table->string('offre_reference')->nullable(); // Référence de l'offre d'emploi
            $table->decimal('salaire_propose', 10, 2)->nullable();
            $table->date('date_debut_souhaite')->nullable();
            $table->text('motif_refus')->nullable();
            $table->foreignId('recruteur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['candidat_id', 'statut']);
            $table->index(['departement_id', 'statut']);
            $table->index('date_candidature');
            $table->index('statut');
            $table->index('priorite');
            $table->index('recruteur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
