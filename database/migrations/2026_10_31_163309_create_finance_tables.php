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
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('type', 10);
            $table->timestamps();
            $table->unique(['name', 'type']);
        });

        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 10);
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->date('date')->index();
            $table->text('description')->nullable();
            $table->string('proof_file')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['type', 'date']);
        });

        Schema::create('donations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_name')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->unsignedBigInteger('amount');
            $table->string('purpose');
            $table->text('message')->nullable();
            $table->string('payment_method')->default('manual');
            $table->string('status')->default('pending')->index();
            $table->string('payment_reference')->nullable()->unique();
            $table->string('proof_file')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('transaction_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->timestamps();
        });

        Schema::create('mosque_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mosque_settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('categories');
    }
};
