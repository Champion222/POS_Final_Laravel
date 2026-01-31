<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- MAKE SURE THIS IS IMPORTED

return new class extends Migration
{
    public function up()
    {
        // FIX: Clear old broken data first to prevent Foreign Key Error
        DB::table('attendances')->truncate();

        Schema::table('attendances', function (Blueprint $table) {
            
            // 1. Add user_id if missing
            if (!Schema::hasColumn('attendances', 'user_id')) {
                // 'nullable()' allows saving initially without crashing, but we want it strict usually.
                // Since we truncated above, we can enforce strictness.
                $table->foreignId('user_id')->after('id')->constrained('users')->onDelete('cascade');
            }

            // 2. Add Date
            if (!Schema::hasColumn('attendances', 'date')) {
                $table->date('date')->after('user_id')->nullable();
            }

            // 3. Add Checkin Time
            if (!Schema::hasColumn('attendances', 'checkin_time')) {
                $table->timestamp('checkin_time')->nullable()->after('date');
            }

            // 4. Add Checkout Time
            if (!Schema::hasColumn('attendances', 'checkout_time')) {
                $table->timestamp('checkout_time')->nullable()->after('checkin_time');
            }

            // 5. Add Status
            if (!Schema::hasColumn('attendances', 'status')) {
                $table->string('status')->default('present')->after('checkout_time'); 
            }
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'date', 'checkin_time', 'checkout_time', 'status']);
        });
    }
};