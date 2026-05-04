<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','cargo'))       $table->string('cargo')->nullable()->after('name');
            if (!Schema::hasColumn('users','dependencia')) $table->string('dependencia')->nullable()->after('cargo');
            if (!Schema::hasColumn('users','telefono'))    $table->string('telefono',20)->nullable()->after('dependencia');
            if (!Schema::hasColumn('users','active'))      $table->boolean('active')->default(true)->after('telefono');
            if (!Schema::hasColumn('users','avatar'))      $table->string('avatar')->nullable()->after('active');
        });
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->integer('duration_hours')->default(0);
            $table->enum('status',['draft','published','archived'])->default('draft');
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->enum('type',['text','video','pdf','link'])->default('text');
            $table->integer('duration_minutes')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('status',['active','completed','dropped'])->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->unique(['user_id','course_id']);
            $table->timestamps();
        });
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->boolean('completed')->default(false);
            $table->timestamp('viewed_at')->nullable();
            $table->unique(['user_id','lesson_id']);
            $table->timestamps();
        });
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70);
            $table->integer('max_attempts')->default(3);
            $table->timestamps();
        });
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->integer('order')->default(1);
            $table->timestamps();
        });
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_code',30)->unique();
            $table->timestamp('issued_at');
            $table->unique(['user_id','course_id']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('certificates'); Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('question_options'); Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes'); Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('enrollments'); Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules'); Schema::dropIfExists('courses');
    }
};
