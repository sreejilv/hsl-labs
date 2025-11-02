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
        Schema::create('recurring_orders', function (Blueprint $table) {
            $table->id();
            $table->string('recurring_order_number')->unique();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->enum('frequency', ['monthly'])->default('monthly'); // Can extend to weekly, quarterly later
            $table->integer('duration_months'); // 4 or 12 months
            $table->integer('remaining_months'); // How many months left
            $table->date('start_date');
            $table->date('next_due_date');
            $table->integer('day_of_month')->default(1); // Which day of month to process (1-28)
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->decimal('total_amount', 10, 2); // Total amount for each recurring order
            $table->text('notes')->nullable();
            $table->timestamp('last_processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'next_due_date']);
            $table->index(['doctor_id', 'status']);
            $table->index(['staff_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_orders');
    }
};
