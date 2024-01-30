<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Models\Plane;
use App\Services\PlaneRepository;

class EloquentPlaneRepository implements PlaneRepository
{
    public function __construct(
    ) {
    }

    /**
     * @return iterable<Plane>
     */
    public function getAll(): iterable
    {
        return Plane::query()->orderBy('registration')->get();
    }
}
