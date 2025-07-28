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
        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->default('France');
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('nationalite')->nullable();
            $table->enum('civilite', ['M', 'Mme', 'Mlle'])->default('M');
            $table->string('linkedin')->nullable();
            $table->string('site_web')->nullable();
            $table->text('bio')->nullable(); // Biographie courte
            $table->text('competences')->nullable(); // Compétences principales
            $table->text('experiences')->nullable(); // Expériences résumées
            $table->text('formation')->nullable(); // Formation/études
            $table->string('disponibilite')->nullable(); // Immédiat, 1 mois, etc.
            $table->decimal('pretention_salaire', 10, 2)->nullable(); // Prétentions salariales
            $table->string('mobilite')->nullable(); // Mobilité géographique
            $table->enum('statut', ['actif', 'inactif', 'blacklist'])->default('actif');
            $table->text('notes')->nullable(); // Notes internes
            $table->string('source_recrutement')->nullable(); // LinkedIn, Indeed, etc.
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['nom', 'prenom']);
            $table->index('email');
            $table->index('statut');
            $table->index('source_recrutement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidats');
    }
};
