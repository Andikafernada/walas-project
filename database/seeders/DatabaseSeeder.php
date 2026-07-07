<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\CashBook;
use App\Models\ClassModel;
use App\Models\Journal;
use App\Models\Organization;
use App\Models\OrganizationStructure;
use App\Models\Schedule;
use App\Models\SeatingChart;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Walas Platform organization
        $walasOrg = Organization::firstOrCreate(
            ['slug' => 'walas-platform'],
            [
                'name' => 'Walas Platform',
                'type' => 'others',
                'city' => 'Jakarta',
                'email' => 'admin@walas.my.id',
                'status' => 'active',
            ]
        );

        // Create Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@walas.my.id'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@walas.my.id',
                'password' => Hash::make('password'),
                'organization_id' => $walasOrg->id,
                'role' => User::ROLE_SUPER_ADMIN,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $this->command->info('Super Admin created: superadmin@walas.my.id / password');

        // Create demo user with organization
        $demoOrg = Organization::firstOrCreate(
            ['slug' => 'smp-negeri-1-jakarta'],
            [
                'name' => 'SMP Negeri 1 Jakarta',
                'type' => 'smp',
                'city' => 'Jakarta Pusat',
                'email' => 'info@smpn1jkt.sch.id',
                'status' => 'active',
            ]
        );

        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@sekolah.sch.id',
            'phone' => '6281234567890',
            'organization_id' => $demoOrg->id,
            'role' => User::ROLE_WALAS,
            'tier' => 'pro',
            'is_active' => true,
        ]);

        $this->command->info('Demo user created: budi@sekolah.sch.id / password');

        // Create sample classes
        $classes = [
            [
                'name' => 'X IPA 1',
                'jurusan' => 'IPA',
                'tingkat' => 'X',
                'alias' => 'XIPA1',
            ],
            [
                'name' => 'X IPS 1',
                'jurusan' => 'IPS',
                'tingkat' => 'X',
                'alias' => 'XIPS1',
            ],
            [
                'name' => 'XI IPA 2',
                'jurusan' => 'IPA',
                'tingkat' => 'XI',
                'alias' => 'XIIPA2',
            ],
        ];

        foreach ($classes as $classData) {
            $class = ClassModel::factory()->forUser($user)->create([
                ...$classData,
                'school_year_start' => 2024,
                'school_year_end' => 2025,
            ]);

            $this->seedClassData($class);
        }

        $this->command->info('Sample data seeded successfully!');
    }

    /**
     * Seed data for a class.
     */
    protected function seedClassData(ClassModel $class): void
    {
        // Create students
        $students = Student::factory()->count(32)->forClass($class)->create();

        // Create organization structure
        $positions = [
            'ketua_kelas',
            'wakil_ketua',
            'sekretaris',
            'bendahara',
            'seksi_kehadiran',
        ];

        $studentIndex = 0;
        foreach ($positions as $position) {
            if (isset($students[$studentIndex])) {
                OrganizationStructure::create([
                    'class_id' => $class->id,
                    'student_id' => $students[$studentIndex]->id,
                    'position' => $position,
                    'academic_year' => '2024-2025',
                    'is_active' => true,
                ]);
                $studentIndex++;
            }
        }

        // Create schedules
        $subjects = [
            'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris',
            'Fisika', 'Kimia', 'Biologi', 'Ekonomi',
            'Sejarah', 'Geografi', 'Sosiologi', 'Agama',
            'PKN', 'Olahraga', 'Seni Budaya',
        ];

        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $hours = ['07:00', '08:30', '10:00', '11:30', '13:00', '14:30'];

        foreach ($days as $dayIndex => $day) {
            $scheduleCount = rand(4, 6);
            for ($i = 0; $i < $scheduleCount; $i++) {
                $startTime = $hours[$i];
                $endHour = (int) substr($hours[$i], 0, 2) + 1;
                $endTime = sprintf('%02d', $endHour) . ':30';

                Schedule::create([
                    'class_id' => $class->id,
                    'subject' => $subjects[array_rand($subjects)],
                    'teacher_name' => fake()->name(),
                    'day' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_active' => true,
                ]);
            }
        }

        // Create attendance sessions for the last 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i);
            $session = AttendanceSession::factory()->forClass($class)->create([
                'date' => $date->toDateString(),
                'status' => $i === 0 ? 'active' : 'used',
                'submitted_at' => $i === 0 ? null : $date->copy()->setTime(12, 0),
            ]);

            // Create attendances for each student
            foreach ($students as $student) {
                $status = fake()->randomElement([
                    'hadir', 'hadir', 'hadir', 'hadir', 'hadir',
                    'terlambat', 'sakit', 'izin', 'alpa'
                ]);

                Attendance::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $student->id,
                    'user_id' => $class->user_id,
                    'class_id' => $class->id,
                    'date' => $date->toDateString(),
                    'status' => $status,
                    'minutes_late' => $status === 'terlambat' ? fake()->numberBetween(5, 30) : null,
                ]);
            }
        }

        // Create some violations
        foreach ($students->random(5) as $student) {
            Violation::create([
                'student_id' => $student->id,
                'user_id' => $class->user_id,
                'class_id' => $class->id,
                'category' => fake()->randomElement(['terlambat', 'bolos', 'hp_di_kelas', 'tidak_mengerjakan_tugas']),
                'description' => fake()->sentence(),
                'severity' => fake()->randomElement(['ringan', 'sedang', 'berat']),
                'date' => fake()->dateTimeBetween('-30 days'),
                'poin_reduced' => fake()->randomElement([5, 10, 15]),
                'poin_before' => 100,
                'poin_after' => fake()->randomElement([95, 90, 85]),
                'status' => 'approved',
            ]);

            $student->decrement('poin', fake()->randomElement([5, 10, 15]));
        }

        // Create cash book transactions
        $categories = [
            'iuran_bulanan' => 50000,
            'kegiatan' => 100000,
            'ujian' => 75000,
            'snack' => 25000,
            'atk' => 15000,
        ];

        foreach (range(1, 10) as $i) {
            $type = fake()->randomElement(['income', 'expense']);
            $category = array_rand($categories);

            CashBook::create([
                'class_id' => $class->id,
                'user_id' => $class->user_id,
                'type' => $type,
                'category' => $category,
                'description' => fake()->sentence(),
                'amount' => $type === 'income' ? $categories[$category] : fake()->numberBetween(10000, 100000),
                'date' => fake()->dateTimeBetween('-30 days'),
                'created_by_name' => $class->user->name,
            ]);
        }

        // Create seating chart
        SeatingChart::create([
            'class_id' => $class->id,
            'name' => 'Denah Semester Ganjil 2024',
            'layout' => ['rows' => 6, 'cols' => 6],
            'effective_date' => now()->startOfMonth(),
            'is_active' => true,
        ]);

        // Create some journals
        Journal::create([
            'user_id' => $class->user_id,
            'class_id' => $class->id,
            'student_id' => $students->random()->id,
            'category' => 'konseling',
            'subject' => 'Konseling Individual',
            'content' => 'Diskusi tentang perkembangan akademik dan tantangan belajar.',
            'date' => fake()->dateTimeBetween('-7 days'),
            'outcome' => 'Siswa tampak lebih termotivasi',
            'follow_up' => 'Follow up minggu depan',
        ]);

        $this->command->info("  - Created class: {$class->name} with {$students->count()} students");
    }
}
