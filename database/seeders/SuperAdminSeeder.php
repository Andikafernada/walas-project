<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Walas Platform organization (the company behind Walas)
        $walasOrg = Organization::firstOrCreate(
            ['slug' => 'walas-platform'],
            [
                'name' => 'Walas Platform',
                'type' => 'others',
                'city' => 'Jakarta',
                'email' => 'admin@walas.my.id',
                'status' => 'active',
                'notes' => 'Platform administrator organization',
            ]
        );

        // Create Super Admin user
        User::firstOrCreate(
            ['email' => 'superadmin@walas.my.id'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@walas.my.id',
                'password' => Hash::make('password'),
                'google_id' => null,
                'organization_id' => $walasOrg->id,
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Create demo walas organization
        $demoOrg = Organization::firstOrCreate(
            ['slug' => 'smp-negeri-1-demo'],
            [
                'name' => 'SMP Negeri 1 Demo',
                'type' => 'smp',
                'city' => 'Jakarta Selatan',
                'email' => 'info@smpn1demo.sch.id',
                'status' => 'active',
            ]
        );

        // Create demo walas user
        User::firstOrCreate(
            ['email' => 'wali.kelas@smpn1demo.sch.id'],
            [
                'name' => 'Budi Santoso',
                'email' => 'wali.kelas@smpn1demo.sch.id',
                'password' => Hash::make('password'),
                'organization_id' => $demoOrg->id,
                'role' => User::ROLE_WALAS,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $this->command->info('Super Admin created: superadmin@walas.my.id / password');
        $this->command->info('Demo Walas created: wali.kelas@smpn1demo.sch.id / password');
    }
}
