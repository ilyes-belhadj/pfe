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
        Schema::create('paies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->string('periode'); // ex: "2025-07" pour juillet 2025
            $table->date('date_paiement');
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('heures_travaillees', 8, 2)->default(0);
            $table->decimal('taux_horaire', 8, 2)->default(0);
            $table->decimal('salaire_brut', 10, 2);
            $table->decimal('primes', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('cotisations_sociales', 10, 2)->default(0);
            $table->decimal('impots', 10, 2)->default(0);
            $table->decimal('salaire_net', 10, 2);
            $table->enum('statut', ['en_attente', 'paye', 'annule'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->string('mode_paiement')->default('virement'); // virement, cheque, especes
            $table->string('numero_cheque')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['employe_id', 'periode']);
            $table->index(['statut', 'date_paiement']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paies');
    }
};
