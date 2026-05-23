<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        
      
        $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
        $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
        $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
        
        $table->date('appointment_date');
        $table->string('status')->default('pending'); // Options: pending, confirmed, cancelled, completed
        $table->text('complaint');
        $table->timestamps();
    });
}

};
