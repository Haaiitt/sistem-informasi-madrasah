<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'password', 'phone', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }


    /** Set role pengguna, isi created_at manual karena role_user tidak punya updated_at. */
   public function syncRoles(array $roleIds): void
    {
        DB::transaction(function () use ($roleIds) {
            DB::table('role_user')->where('user_id', $this->id)->delete();

            if (empty($roleIds)) {
                return;
            }

            DB::table('role_user')->insert(
                collect($roleIds)->map(fn (int $roleId) => [
                    'user_id' => $this->id,
                    'role_id' => $roleId,
                    'created_at' => now(),
                ])->all()
            );
        });
    }

    /** DB-01: permission pengguna = gabungan permission semua rolenya. */
    public function hasPermission(string $permissionName): bool
    {
        return $this->roles
            ->flatMap(fn (Role $role) => $role->permissions)
            ->contains('name', $permissionName);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    public function applicants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    public function conversations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function announcements(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_recipients')->withPivot('read_at');
    }

    // FR-MSG-08
    public function unreadMessageCount(): int
    {
        $unreadConversations = Message::whereIn('conversation_id', $this->conversations()->pluck('id'))
            ->where('sender_side', 'admin')->whereNull('read_at')->count();
        $unreadAnnouncements = $this->announcements()->wherePivotNull('read_at')->count();

        return $unreadConversations + $unreadAnnouncements;
    }
}
