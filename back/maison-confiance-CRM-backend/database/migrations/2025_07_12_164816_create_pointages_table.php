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
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date_pointage');
            $table->time('heure_entree')->nullable();
            $table->time('heure_sortie')->nullable();
            $table->time('heure_pause_debut')->nullable();
            $table->time('heure_pause_fin')->nullable();
            $table->decimal('heures_travaillees', 5, 2)->default(0); // 5 chiffres, 2 décimales
            $table->decimal('heures_pause', 4, 2)->default(0); // 4 chiffres, 2 décimales
            $table->decimal('heures_net', 5, 2)->default(0); // heures travaillées - heures pause
            $table->enum('statut', ['present', 'absent', 'en_pause', 'retard', 'depart_anticipé'])->default('present');
            $table->text('commentaire')->nullable();
            $table->string('lieu_pointage')->nullable(); // bureau, télétravail, déplacement, etc.
            $table->string('methode_pointage')->default('manuel'); // manuel, badge, application, etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('valide')->default(true);
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['employe_id', 'date_pointage']);
            $table->index(['date_pointage', 'statut']);
            $table->index(['employe_id', 'statut']);
            $table->index(['valide', 'date_pointage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
