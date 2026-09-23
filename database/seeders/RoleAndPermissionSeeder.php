<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Sumber: permissions.md §3 (Role) dan §4 (Daftar Permission per Role).
     * super_admin sengaja tidak didaftar eksplisit di sini karena memegang
     * SELURUH permission (permissions.md §4: "seluruh permission di bawah ini"),
     * ditambah permission khususnya sendiri (lihat $superAdminOnly).
     */
    private array $permissionsByRole = [
        'admin_tu' => [
            'academic_years.view', 'academic_years.create', 'academic_years.update',
            'students.view', 'students.create', 'students.update', 'students.change_status',
            'students.import', 'students.export',
            'guardians.view', 'guardians.create', 'guardians.update',
            'employees.view', 'employees.create', 'employees.update', 'employees.import', 'employees.export',
            'classrooms.view', 'classrooms.create', 'classrooms.update',
            'class_enrollments.view', 'class_enrollments.create', 'class_enrollments.update',
            'subjects.view', 'subjects.create', 'subjects.update',
            'teaching_assignments.view', 'teaching_assignments.create', 'teaching_assignments.update',
            'admission_waves.view',
            'applicants.view', 'applicants.export', 'applicants.convert_to_student',
            'public_messages.view', 'public_messages.reply', 'public_messages.update_status',
            'dashboard.view',
        ],
        'operator_konten' => [
            'posts.view', 'posts.create', 'posts.update', 'posts.publish', 'posts.delete',
            'pages.view', 'pages.create', 'pages.update', 'pages.publish', 'pages.delete',
            'events.view', 'events.create', 'events.update', 'events.publish', 'events.delete',
            'galleries.view', 'galleries.create', 'galleries.update', 'galleries.delete',
            'banners.view', 'banners.create', 'banners.update', 'banners.delete',
            'site_settings.view', 'site_settings.update',
        ],
        'panitia_ppdb' => [
            'admission_waves.view', 'admission_waves.create', 'admission_waves.update',
            'admission_waves.open', 'admission_waves.close',
            'applicants.view', 'applicants.export',
            'applicants.verify',
            'applicants.decide',
            'applicants.record_reenrollment',
            'admission_results.publish',
            'messages.send_individual',
            'messages.send_broadcast',
            'messages.view_threads',
            'public_messages.view', 'public_messages.reply', 'public_messages.update_status',
            'applicant_accounts.issue_reset_link',
            'dashboard.view',
        ],
        'guru' => [
            'students.view',
            'classrooms.view',
            'teaching_assignments.view',
            'employees.view',
        ],
        'kepala_madrasah' => [
            'students.view',
            'employees.view',
            'classrooms.view',
            'academic_years.view',
            'admission_waves.view',
            'applicants.view',
            'posts.view',
            'dashboard.view',
        ],
        'calon_siswa' => [
            'my_applications.create',
            'my_applications.check_nisn',
            'my_applications.view',
            'my_applications.update',
            'my_applications.submit',
            'my_applications.print_receipt',
            'my_messages.view',
            'my_messages.send_reply',
            'my_result.view',
        ],
    ];

    /** Permission khusus super_admin di luar yang sudah dimiliki role lain. */
    private array $superAdminOnly = [
        'users.view', 'users.create', 'users.update', 'users.deactivate',
        'roles.assign',
        'users.issue_reset_link',
        'audit_logs.view',
    ];

    private array $roleLabels = [
        'super_admin' => 'Super Admin',
        'admin_tu' => 'Admin Tata Usaha',
        'operator_konten' => 'Operator Konten',
        'panitia_ppdb' => 'Panitia PPDB',
        'guru' => 'Guru',
        'kepala_madrasah' => 'Kepala Madrasah',
        'calon_siswa' => 'Calon Siswa (Orang Tua/Wali)',
    ];

    public function run(): void
    {
        $allPermissionNames = collect($this->permissionsByRole)
            ->flatten()
            ->merge($this->superAdminOnly)
            ->unique()
            ->values();

        // Insert massal sekali jalan, bukan satu-satu (hindari N+1).
        Permission::insertOrIgnore(
            $allPermissionNames->map(fn (string $name) => ['name' => $name])->all()
        );
        $permissions = Permission::whereIn('name', $allPermissionNames)
            ->get()
            ->keyBy('name');

        Role::insertOrIgnore(
            collect($this->roleLabels)
                ->map(fn (string $label, string $name) => ['name' => $name, 'label' => $label])
                ->values()
                ->all()
        );
        $roles = Role::whereIn('name', array_keys($this->roleLabels))
            ->get()
            ->keyBy('name');

        // super_admin: seluruh permission yang ada (permissions.md §4)
        $roles['super_admin']->permissions()->sync($permissions->pluck('id'));

        foreach ($this->permissionsByRole as $roleName => $permissionNames) {
            $roles[$roleName]->permissions()->sync(
                $permissions
                    ->filter(fn (Permission $permission) => in_array($permission->name, $permissionNames))
                    ->pluck('id')
            );
        }
    }
}
