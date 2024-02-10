<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $user_id
 * @property string|null $user2_id
 * @property string $plane_id
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property int $time
 * @property CarbonImmutable|null $confirmed_at
 * @property string $confirmed_by
 * @property string $comment
 * @property User $user
 */
class PlaneReservation extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    /** @var array<string> */
    protected $dates = ['deleted_at'];
    protected $casts = [
        'starts_at' => 'immutable_datetime',
        'ends_at' => 'immutable_datetime',
        'confirmed_at' => 'immutable_datetime',
        'deleted_at' => 'immutable_datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'user2_id',
        'plane_id',
        'starts_at',
        'ends_at',
        'time',
        'confirmed_at',
        'confirmed_by',
        'deleted_at',
        'comment',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function user2(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user2_id');
    }
}
