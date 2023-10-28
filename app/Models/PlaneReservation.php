<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaneReservation extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<string> */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plane_id',
        'starts_at_date',
        'ends_at_date',
        'starts_at_time',
        'ends_at_time',
        'confirmed_at',
        'confirmed_by',
    ];
}
