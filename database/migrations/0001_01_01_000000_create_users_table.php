<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('last_name')->nullable();      // Овог
            $table->string('first_name')->nullable();     // Нэр
            $table->string('name')->nullable();           // Бүтэн нэр (кэш)
            $table->date('birth_date')->nullable();       // Төрсөн огноо
            $table->string('gender')->nullable();         // male | female
            $table->unsignedBigInteger('org_id')->nullable(); // Хамгаалалтын захиргаа
            $table->string('position')->nullable();       // Албан тушаал
            $table->string('phone')->nullable();          // Гар утас
            $table->string('emergency_phone')->nullable();// Яаралтай үед холбоо барих
            $table->string('email')->unique();            // Нэвтрэх нэр = и-мэйл
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();          // Профайл зураг
            $table->string('role')->default('user');      // user | admin | superadmin
            $table->string('status')->default('pending'); // pending | active | rejected
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
