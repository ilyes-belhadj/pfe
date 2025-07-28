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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('type', ['candidat', 'employe', 'periode_essai', 'annuelle', 'performance'])->default('candidat');
            $table->enum('statut', ['brouillon', 'en_cours', 'terminee', 'validee', 'rejetee'])->default('brouillon');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            
            // Relations polymorphiques pour évaluer différents types d'entités
            $table->morphs('evaluable'); // candidat_id, candidat_type ou employe_id, employe_type
            
            // Évaluateur
            $table->foreignId('evaluateur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('departement_id')->nullable()->constrained('departements')->onDelete('set null');
            
            // Dates
            $table->date('date_evaluation');
            $table->date('date_limite')->nullable();
            $table->date('date_validation')->nullable();
            $table->date('prochaine_evaluation')->nullable();
            
            // Critères d'évaluation (JSON)
            $table->json('criteres_evaluation')->nullable(); // Structure des critères
            $table->json('resultats')->nullable(); // Résultats détaillés par critère
            
            // Notes globales
            $table->decimal('note_globale', 3, 1)->nullable(); // Note sur 10
            $table->decimal('note_competences', 3, 1)->nullable();
            $table->decimal('note_performance', 3, 1)->nullable();
            $table->decimal('note_comportement', 3, 1)->nullable();
            $table->decimal('note_potentiel', 3, 1)->nullable();
            
            // Évaluations détaillées
            $table->text('forces')->nullable(); // Points forts
            $table->text('axes_amelioration')->nullable(); // Axes d'amélioration
            $table->text('objectifs')->nullable(); // Objectifs fixés
            $table->text('recommandations')->nullable(); // Recommandations
            
            // Commentaires
            $table->text('commentaires_evaluateur')->nullable();
            $table->text('commentaires_evalue')->nullable();
            $table->text('commentaires_manager')->nullable();
            $table->text('commentaires_rh')->nullable();
            
            // Validation et approbation
            $table->boolean('validee_par_evalue')->default(false);
            $table->boolean('validee_par_manager')->default(false);
            $table->boolean('validee_par_rh')->default(false);
            $table->date('date_validation_evalue')->nullable();
            $table->date('date_validation_manager')->nullable();
            $table->date('date_validation_rh')->nullable();
            
            // Métadonnées
            $table->string('version_grille')->nullable(); // Version de la grille d'évaluation
            $table->string('reference')->nullable(); // Référence unique
            $table->text('notes_internes')->nullable(); // Notes pour usage interne
            
            // Statut de l'évalué
            $table->enum('recommandation', ['embauche', 'confirmation', 'promotion', 'formation', 'sanction', 'licenciement'])->nullable();
            $table->text('justification_recommandation')->nullable();
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['type', 'statut']);
            $table->index('date_evaluation');
            $table->index('evaluateur_id');
            $table->index('departement_id');
            $table->index('priorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
