<?php

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            
            // 🛡️ সোশ্যাল লগইনের জন্য পাসওয়ার্ড Nullable করা হলো
            $table->string('password')->nullable(); 

            // 🛡️ গুগল লগইনের সময় ফোন নম্বর ও ব্লাড গ্রুপ থাকে না, তাই এগুলো Nullable করা হলো
            $table->string('phone', 15)->unique()->nullable();
            $table->string('role')->default(UserRole::DONOR->value);
            $table->string('blood_group')->nullable(); 
            
            // ❌ পুরনো district এবং upazila স্ট্রিং কলামগুলো মুছে ফেলা হয়েছে (যেহেতু আমরা এখন রিলেশনাল আইডি ব্যবহার করছি)

            $table->text('address')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('edu_email')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // ডোনার স্ট্যাটাস
            $table->boolean('is_available')->default(true);
            $table->boolean('is_ready_now')->default(false);
            $table->boolean('hide_phone')->default(false);
            $table->timestamp('cooldown_until')->nullable();
            $table->integer('total_donations')->default(0);

            // গেমিফিকেশন
            $table->integer('points')->default(0);

            // ভেরিফিকেশন
            $table->boolean('verified_badge')->default(false);
            $table->string('nid_image')->nullable();
            $table->string('nid_status')->default('none'); // none, pending, approved, rejected

            // ফ্রেশনেস ট্র্যাকিং
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('welcome_back_checked')->default(false);

            $table->rememberToken();
            $table->timestamps();

            // ইনডেক্স
            $table->index('blood_group');
            // ❌ district ইনডেক্স মুছে ফেলা হয়েছে কারণ কলামটি আর নেই
            $table->index('is_available');
            $table->index('role');
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
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};