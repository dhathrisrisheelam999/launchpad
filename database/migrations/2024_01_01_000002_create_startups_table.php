<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Unit VI — Database Migration: startups table
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('startups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('tagline');
            $table->text('description');
            $table->enum('industry', ['SaaS','FinTech','HealthTech','EdTech','E-commerce','AI','DevTools']);
            $table->enum('stage', ['Bootstrapped','Pre-Seed','Seed','Pre-Series A','Series A']);
            $table->unsignedBigInteger('arr')->default(0);           // Annual Revenue in USD
            $table->unsignedBigInteger('asking_price')->default(0);  // Asking price in USD
            $table->string('mrr_growth')->default('+0%');
            $table->string('logo_path')->nullable();
            $table->enum('status', ['pending','active','closed'])->default('pending');
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('startups');
    }
};
