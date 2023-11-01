<?php
declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use \DateTimeInterface;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property UserRole $role
 * @property string $remember_token
 * @property DateTimeInterface $email_verified_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids;


    /** @var array<string> */
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role'=> UserRole::class,
    ];

    // write toArray method here
    public function toArray(): array
    {
        $arr = parent::toArray();
        unset($arr['password']);
        $arr['deleted_at'] = $this->deleted_at;
        $arr['email_verified_at'] = $this->email_verified_at;
        return $arr;
    }
}
