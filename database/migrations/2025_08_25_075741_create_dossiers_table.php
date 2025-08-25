<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            
            // Champs principaux
            $table->integer('type');
            $table->string('reference')->unique();
            $table->string('titre');
            $table->text('descriptif')->nullable();
            $table->integer('statut')->default(1);
            
            // Relations vers Tiers unifiés
            $table->foreignId('mediateur_id')->constrained('tiers');
            $table->foreignId('comediateur_id')->nullable()->constrained('tiers');
            $table->foreignId('centre_id')->nullable()->constrained('tiers');
            $table->foreignId('assistante_id')->nullable()->constrained('tiers');
            
            // NOUVEAUTÉ : suivi d'accès unifié (comme Tiers)
            $table->unsignedBigInteger('acces_id')->nullable();
            $table->timestamp('acces_date')->nullable();
            
            // Dates métier
            $table->timestamp('saisine')->nullable();
            $table->timestamp('cloture')->nullable();
            
            $table->timestamps();
            
            // Index optimisés pour performance
            $table->index(['mediateur_id', 'statut']);
            $table->index(['type', 'statut']);
            $table->index(['acces_id', 'acces_date']);
            $table->index('reference');
            $table->index(['saisine', 'cloture']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};