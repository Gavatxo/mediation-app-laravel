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
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
             $table->string('reference')->nullable()->index();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable(); 
            $table->string('denomination')->nullable();
            $table->string('avatar')->nullable();
            $table->string('identifiant')->nullable();
            $table->string('mot_passe')->nullable();
            $table->string('siret')->nullable();
            
            // NOUVEAUTÉ : suivi accès unifié
            $table->unsignedBigInteger('acces_id')->nullable();
            $table->timestamp('acces_date')->nullable();
            
            // NOUVEAUTÉ : hiérarchie juridique native
            $table->foreignId('parent_id')->nullable()->constrained('tiers')->cascadeOnDelete();

            $table->timestamps();

            // Index optimisé pour performance
            $table->index(['nom', 'prenom']);
            $table->index('denomination');
            $table->index(['acces_id', 'acces_date']);
            $table->index(['parent_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
