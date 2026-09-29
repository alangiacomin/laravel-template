<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create(
            'testate',
            function (Blueprint $table): void {
                $table->id();
                $table->string('titolo');
                $table->timestamps();
                $table->softDeletes();
                $table->unique('titolo');
            }
        );

        Schema::create(
            'albi',
            function (Blueprint $table): void {
                $table->id();
                $table->string('titolo');
                $table->foreignId('testata_id')->references('id')->on('testate')->restrictOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['titolo', 'testata_id']);
            }
        );

        Schema::create(
            'serie',
            function (Blueprint $table): void {
                $table->id();
                $table->string('titolo');
                $table->timestamps();
                $table->softDeletes();
                $table->unique('titolo');
            }
        );

        Schema::create(
            'pubblicazioni',
            function (Blueprint $table): void {
                $table->id();
                $table->foreignId('albo_id')->references('id')->on('albi')->restrictOnDelete();
                $table->foreignId('serie_id')->references('id')->on('serie')->restrictOnDelete();
                $table->unsignedInteger('numero');
                $table->unsignedInteger('numero_gruppo')->nullable();
                $table->date('data_pubblicazione')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['albo_id', 'serie_id']);
                $table->unique(['serie_id', 'numero']);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('pubblicazioni');
        Schema::dropIfExists('albi');
        Schema::dropIfExists('testate');
        Schema::dropIfExists('serie');
    }
};
