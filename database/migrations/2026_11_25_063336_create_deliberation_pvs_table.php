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
        Schema::create('deliberation_pvs', function (Blueprint $table) {
            $table->id();

$table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
$table->foreignId('program_id')->constrained()->restrictOnDelete();
$table->foreignId('level_id')->constrained()->restrictOnDelete();
$table->foreignId('semester_id')->constrained()->restrictOnDelete();

$table->string('class_name');              // ex. « L3 IG-ARI A »
$table->string('specialty')->nullable();   // 'ARI' | 'AIP' | null
$table->date('deliberation_date');

$table->string('file_path');
$table->string('file_name');
$table->string('file_type');
$table->unsignedBigInteger('file_size');

$table->string('status')->default('soumis');
$table->foreignId('created_by')->constrained('users')->restrictOnDelete();
$table->timestamp('archived_at')->nullable();

$table->timestamps();

$table->unique(
    ['academic_year_id', 'program_id', 'level_id', 'semester_id', 'class_name'],
    'pvs_class_unique'
);
$table->index(['program_id', 'status']);
$table->index('academic_year_id');
$table->index('class_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliberation_pvs');
    }
};
