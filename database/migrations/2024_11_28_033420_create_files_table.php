<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('type_id'); // Asumsi ini mengacu ke tabel lain //GANTI JADI 'STRING'
            $table->string('product_name');
            $table->string('contact_person');
            $table->string('vendor');
            $table->string('material_position');
            $table->text('material_description')->nullable();
            $table->string('website')->nullable(); // Nullable
            $table->string('image')->nullable(); // Nullable
            $table->unsignedBigInteger('folder_id'); // Relasi ke folder
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
