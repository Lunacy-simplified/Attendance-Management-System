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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->date('date');
            $table->string('status');
            $table->decimal('ot_hours', 5, 2)->default(0);

            $table->decimal('salary_effective_rate', 10, 2); // snapshot of daily_rate
            $table->decimal('ot_effective_rate', 10, 2);    // snapshot of ot_rate

            $table->timestamps();

            $table->unique(['worker_id', 'project_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
