<?php

declare(strict_types=1);

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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade')->comment('Foreign key to tenant');
            $table->foreignUuid('parent_id')->nullable()->constrained('organizations')->onDelete('set null')->comment('Parent organization for hierarchy');
            $table->string('name')->comment('Organization name');
            $table->string('code')->unique()->comment('Unique organization code');
            $table->text('description')->nullable()->comment('Organization description');
            $table->enum('type', ['headquarters', 'branch', 'subsidiary', 'division'])->default('branch')->comment('Organization type');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Organization status');
            $table->string('email')->nullable()->comment('Primary email');
            $table->string('phone')->nullable()->comment('Primary phone');
            $table->string('address')->nullable()->comment('Full address');
            $table->string('city')->nullable()->comment('City');
            $table->string('state')->nullable()->comment('State/Province');
            $table->string('country', 2)->nullable()->comment('Country (ISO 3166-1 alpha-2)');
            $table->string('postal_code')->nullable()->comment('Postal/ZIP code');
            $table->string('currency', 3)->default('USD')->comment('Primary currency (ISO 4217)');
            $table->string('timezone')->default('UTC')->comment('Timezone (IANA format)');
            $table->string('locale', 5)->default('en')->comment('Locale (ISO 639-1)');
            $table->json('settings')->nullable()->comment('Custom organization settings');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('tenant_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('type');
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
