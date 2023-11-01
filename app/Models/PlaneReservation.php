<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

/**
 * @property string $id
 * @property string $user_id
 * @property string $plane_id
 * @property DateTimeInterface $starts_at
 * @property DateTimeInterface $ends_at
 * @property int $time
 * @property DateTimeInterface $confirmed_at
 * @property string $confirmed_by
 */
class PlaneReservation extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    /** @var array<string> */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'plane_id',
        'starts_at',
        'ends_at',
        'time',
        'confirmed_at',
        'confirmed_by',
        'deleted_at',
    ];
}
