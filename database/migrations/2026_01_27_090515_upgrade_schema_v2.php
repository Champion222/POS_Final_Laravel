<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. UPDATE EMPLOYEES (Link to User 1:1)
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');
        });

        // 2. CREATE PROMOTIONS
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount_value', 10, 2);
            $table->enum('type', ['percent', 'fixed']);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. PRODUCT_PROMOTIONS (M:N Relationship)
        Schema::create('product_promotion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            // Unique Compound Index to prevent duplicates
            $table->unique(['product_id', 'promotion_id']);
        });

        // 4. ATTENDANCES (1:M Employee)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'leave']);
            $table->timestamps();
            
            // Unique Compound Index (One attendance record per employee per day)
            $table->unique(['employee_id', 'date']); 
        });

        // 5. ADD INDEXES (Performance Optimization)
        Schema::table('products', function (Blueprint $table) {
            // Check if index exists before adding to avoid errors in repeated runs
            // Note: Laravel schema builder doesn't easily check indexes, 
            // so we assume this is a fresh run or you handle errors.
            $table->index('category_id');
            $table->index('supplier_id');
            $table->index('qty');
            // Fulltext search index
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['name', 'barcode']); 
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('user_id');
            $table->index('payment_type');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('type');
            $table->index('date');
        });
    }

    public function down(): void
    {
        // Reverse operations if needed
    }
};
