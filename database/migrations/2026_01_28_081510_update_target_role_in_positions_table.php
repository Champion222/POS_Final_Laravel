<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('positions', function (Blueprint $table) {
        // This changes the column to a String (VARCHAR 255)
        // It removes the strict ENUM restrictions or length limits
        $table->string('target_role', 50)->change(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            //
        });
    }
};
