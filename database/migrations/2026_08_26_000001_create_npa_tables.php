<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Хамгаалалтын захиргаад (ХЗ)
        Schema::create('orgs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region')->nullable();      // Баруун, Хангай, Төв, Зүүн, Говь
            $table->json('aimags')->nullable();        // харьяалагдах аймгууд
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('logo')->nullable();
            $table->text('intro')->nullable();
            $table->json('volunteer_durations')->nullable(); // сайн дурын ажлын боломжит хугацаанууд
            $table->boolean('accepts_volunteers')->default(true);
            $table->timestamps();
        });

        // Тусгай хамгаалалттай газрууд (ТХГ)
        Schema::create('parks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('name');
            $table->string('aimag')->nullable();
            $table->string('type')->nullable(); // Дархан цаазат газар, Байгалийн цогцолборт газар, Байгалийн нөөц газар, Байгалийн дурсгалт газар
            $table->string('image')->nullable();
            $table->text('intro')->nullable();          // Танилцуулга
            $table->text('highlights')->nullable();     // Онцолж буй байгалийн тогтоц газар
            $table->text('animals')->nullable();        // Амьтад
            $table->text('geography')->nullable();      // Газар зүйн онцлог
            $table->text('locals')->nullable();         // Нутгийн зон олон
            $table->text('get_there')->nullable();      // Хэрхэн хүрч очих вэ?
            $table->text('travel')->nullable();         // Хэрхэн аялах вэ?
            $table->text('services')->nullable();       // Аялал жуулчлалын үйлчилгээ
            $table->text('warnings')->nullable();       // Анхааруулга, уриалга
            $table->text('admin_info')->nullable();     // Хамгаалалтын захиргаа
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->boolean('featured')->default(false); // жишээ дэлгэрэнгүй мэдээлэлтэй эсэх
            $table->timestamps();
        });

        // NPA тамга
        Schema::create('stamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->constrained('orgs')->cascadeOnDelete();
            $table->integer('year');
            $table->string('name');                    // Тамга авсан нэр (жишээ: Бүсийн сургалтанд хамрагдав)
            $table->string('staff_name')->nullable();  // Тамгыг авсан ажилтны нэр
            $table->string('staff_position')->nullable();
            $table->date('stamp_date');
            $table->text('note')->nullable();
            $table->string('status')->default('active'); // active | edited | deleted
            $table->text('delete_reason')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stamp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stamp_id')->constrained('stamps')->cascadeOnDelete();
            $table->string('action');       // created | edited | deleted
            $table->string('admin_name');   // үйлдэл хийсэн админ
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Жил бүрийн тамганы тохиргоо (reset хугацаа, загвар зураг)
        Schema::create('stamp_years', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('design_image')->nullable();
            $table->timestamps();
        });

        // Мэдээ
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('image')->nullable();
            $table->text('body')->nullable();
            $table->text('body_en')->nullable();
            $table->string('type')->default('internal'); // internal | external
            $table->string('external_url')->nullable();
            $table->date('published_at');
            $table->string('status')->default('active'); // active | hidden
            $table->timestamps();
        });

        // ТХГ-уудын нээлттэй ажлын байр
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('park_name')->nullable();   // ТХГ-ын нэр
            $table->string('position');                // Албан тушаал
            $table->string('contract_type');           // Гэрээт | Үндсэн | Дадлагажигч оюутан
            $table->string('status')->default('open'); // open | closed
            $table->date('open_date')->nullable();
            $table->date('close_date')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->text('materials')->nullable();     // Бүрдүүлэх материал
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Сургалт, арга хэмжээ (бүртгэлтэй асуулга)
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('program');    // khuraldai | junior_ranger | regional
            $table->integer('year');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('reg_start')->nullable();
            $table->date('reg_end')->nullable();
            $table->string('status')->default('auto'); // auto | open | closed | soon
            $table->boolean('login_required')->default(true);
            $table->json('questions')->nullable();     // асуулгын бүтэц
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('data')->nullable();   // хариултууд
            $table->json('files')->nullable();  // хавсаргасан файлууд
            $table->timestamps();
        });

        // Сайн дурын ажлын хүсэлт
        Schema::create('volunteer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('duration')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->text('intro')->nullable();   // Өөрийн товч танилцуулга
            $table->text('reason')->nullable();  // Яагаад сайн дурын ажил хиймээр байгаа
            $table->string('status')->default('new'); // new | replied
            $table->timestamps();
        });

        // Санал хүсэлт
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('type');              // Санал, хүсэлт | Гомдол | Талархал
            $table->string('subtype')->nullable();
            $table->text('message');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('unanswered'); // unanswered | answered
            $table->text('reply')->nullable();
            $table->timestamps();
        });

        // Түгээмэл асуулт
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->text('question_en')->nullable();
            $table->text('answer_en')->nullable();
            $table->integer('ord')->default(0);
            $table->timestamps();
        });

        // Сургалтын материал (dashboard)
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');              // youtube | pdf | image
            $table->string('url')->nullable();   // youtube линк эсвэл файлын зам
            $table->string('cover')->nullable();
            $table->text('summary')->nullable();
            $table->date('published_at');
            $table->json('positions')->nullable(); // харах эрхтэй албан тушаалууд (null = бүгд)
            $table->json('regions')->nullable();   // харах эрхтэй бүсүүд (null = бүгд)
            $table->timestamps();
        });

        // Админд ирэх мэдэгдлүүд
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('kind');        // user_register | volunteer | feedback | event_reg
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->text('text');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });

        // Ерөнхий тохиргоо (цэсний харагдац, санал хүсэлтийн төрлүүд г.м)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        // И-мэйл илгээлтийн бүртгэл (SMTP тохируулаагүй үед лог болно)
        Schema::create('mail_outbox', function (Blueprint $table) {
            $table->id();
            $table->string('to');
            $table->string('subject');
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['mail_outbox', 'settings', 'admin_notifications', 'trainings', 'faqs', 'feedbacks',
                  'volunteer_requests', 'event_registrations', 'events', 'job_postings', 'news',
                  'stamp_years', 'stamp_logs', 'stamps', 'parks', 'orgs'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
