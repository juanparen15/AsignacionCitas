<?php

// database/migrations/2024_01_01_000001_create_services_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration'); // en minutos
            $table->boolean('is_active')->default(true);
            $table->json('availability_days')->nullable(); // [1,2,3,4,5] para lun-vie
            $table->time('start_time')->default('09:00');
            $table->time('end_time')->default('17:00');
            $table->integer('slot_interval')->default(30); // minutos entre citas
            $table->integer('max_bookings_per_slot')->default(1);
            $table->integer('advance_booking_days')->default(30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};

// database/migrations/2024_01_01_000002_create_appointments_table.php
return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();
            $table->datetime('appointment_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            $table->index(['service_id', 'appointment_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};

// database/migrations/2024_01_01_000003_create_blocked_times_table.php
return new class extends Migration
{
    public function up()
    {
        Schema::create('blocked_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->string('reason')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly'])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocked_times');
    }
};

// database/migrations/2024_01_01_000004_create_appointment_settings_table.php
return new class extends Migration
{
    public function up()
    {
        Schema::create('appointment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointment_settings');
    }
};