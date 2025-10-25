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
        Schema::create('messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('chat_session_id')->constrained()->cascadeOnDelete();
            $t->enum('role', ['user','assistant','system'])->index();
            $t->longText('content');
            $t->timestamps();
            $t->index(['chat_session_id','created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('messages'); }

};
