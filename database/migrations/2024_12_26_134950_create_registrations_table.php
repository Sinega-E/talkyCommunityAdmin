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
    Schema::create('registrations', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->string('email')->unique();
        $table->string('phone');
        $table->date('dob');
        $table->enum('gender', ['male', 'female', 'other', 'prefer'])->nullable();
        $table->string('course');
        $table->dateTime('start_date');
        $table->text('profile_headline')->nullable();
        $table->string('photo')->nullable();
        $table->string('audio_note')->nullable();
        $table->enum('work_type', ['Full-Time', 'Part-Time'])->nullable();
        $table->enum('teaching_mode', ['Online', 'In-Person', 'Hybrid'])->nullable();
        $table->text('availability')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::dropIfExists('registrations');
    // }
};
