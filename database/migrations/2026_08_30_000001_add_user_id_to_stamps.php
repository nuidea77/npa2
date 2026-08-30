<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Тамгыг тухайн ХЗ-ны бүртгэлтэй хэрэглэгчид олгодог болгов.
 * staff_name / staff_position нь хэвээр үлдэнэ — тамга олгосон үеийн
 * мэдээллийн хувилбар (хэрэглэгчийн албан тушаал хожим өөрчлөгдсөн ч
 * түүх хэвээр байхын тулд).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stamps', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('org_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stamps', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
