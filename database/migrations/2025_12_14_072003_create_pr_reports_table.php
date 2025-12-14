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
        Schema::create('pr_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('developers')->onDelete('cascade');

            // PR Identification
            $table->string('repository');
            $table->string('pr_number', 50);
            $table->string('pr_link', 500)->nullable();
            $table->string('title', 500)->nullable();

            // Extracted Metrics
            $table->integer('business_value_score')->nullable()->default(0);
            $table->integer('solid_compliance_score')->nullable()->default(0);
            $table->decimal('tone_score', 5, 2)->nullable()->default(0);
            $table->string('health_status', 50)->nullable();
            $table->string('risk_level', 50)->nullable();
            $table->string('change_type', 100)->nullable();

            // Raw Data
            $table->json('raw_analysis');

            $table->timestamps();

            // Indexes
            $table->index('developer_id');
            $table->index(['repository', 'pr_number']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_reports');
    }
};
