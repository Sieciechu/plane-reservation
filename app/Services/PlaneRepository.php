<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plane;

interface PlaneRepository
{
    /**
     * @return iterable<Plane>
     */
    public function getAll(): iterable;
}
