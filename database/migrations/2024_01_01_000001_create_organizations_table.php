<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['sd', 'smp', 'sma', 'smk', 'others'])->default('smp');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add columns to users table only if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }
            if (!Schema::hasColumn('users', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('walas')->after('organization_id');
            }
            // Note: phone might already exist, so we skip it
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['google_id', 'avatar', 'organization_id', 'role', 'is_active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    if ($column === 'organization_id') {
                        $table->dropForeign(['organization_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('organizations');
    }
};
