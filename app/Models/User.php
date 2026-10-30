<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static updateOrCreate(array $attributes, array $values = [])
 * @method static where(string $string, string $string1)
 * @method static create(array $array)
 * @method static count()
 * @method static register(array $array)
 * @method static findOrFail(int $userId)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Check if the user is banned.
     *
     * This method is required by Gates.
     */
    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    public function ban(): void
    {
        $this->forceFill(['banned_at' => now()])->save();
    }

    public function unban(): void
    {
        $this->forceFill(['banned_at' => null])->save();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
