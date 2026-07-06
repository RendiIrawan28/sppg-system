<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_reports', function (Blueprint $table) {
            $table->id();

            $table->string('category', 80)->index();
            $table->date('report_date')->index();

            $table->json('payload');

            $table->string('photo_path')->nullable();
            $table->string('photo_url')->nullable();

            $table->string('source', 50)->nullable()->default('android');
            $table->string('submitted_by')->nullable();

            $table->timestamps();

            $table->index(['category', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_reports');
    }
};