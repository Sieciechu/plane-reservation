<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plane extends Model
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
        'name',
        'registration',
    ];

    public function toArray(): array
    {
        $arr = parent::toArray();
        $arr['deleted_at'] = $this->deleted_at;
        return $arr;
    }
}
