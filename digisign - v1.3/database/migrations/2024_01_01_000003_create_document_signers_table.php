<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('signer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'signed'])->default('pending');
            $table->timestamps();

            $table->unique(['document_id', 'signer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signers');
    }
};
