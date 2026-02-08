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
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('domain')->unique()->comment('Primary domain for tenant identification');
            $table->string('subdomain')->unique()->nullable()->comment('Subdomain for tenant identification');
            $table->string('company_name')->comment('Company/Tenant name');
            $table->string('database_name')->unique()->comment('Name of tenant dedicated database');
            $table->string('database_host')->default('localhost')->comment('Database host');
            $table->integer('database_port')->default(5432)->comment('Database port');
            $table->enum('status', ['active', 'suspended', 'trial', 'expired'])->default('trial')->comment('Tenant status');
            $table->enum('plan', ['basic', 'professional', 'enterprise'])->default('basic')->comment('Subscription plan');
            $table->integer('max_users')->default(10)->comment('Maximum allowed users');
            $table->integer('max_organizations')->default(1)->comment('Maximum allowed organizations');
            $table->timestamp('subscription_start')->nullable()->comment('Subscription start date');
            $table->timestamp('subscription_end')->nullable()->comment('Subscription end date');
            $table->string('billing_email')->comment('Billing contact email');
            $table->json('custom_settings')->nullable()->comment('Custom tenant settings');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('plan');
            $table->index(['subscription_start', 'subscription_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
