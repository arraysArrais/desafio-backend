<?php

use App\Models\User;
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
        Schema::create('transactions', function (Blueprint $table) {
            
            $table->uuid('id')->primary();
            $table->decimal('value', 65, 2)->default(0.00);
            $table->foreignUuid('sender_id')->constrained('users')->onDelete('cascade')->nullable(false);
            $table->foreignUuid('receiver_id')->constrained('users')->onDelete('cascade')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function(Blueprint $table){
            $table->dropForeignIdFor(User::class);
        });

        Schema::dropIfExists('transactions');
    }
};
