<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plane;
use App\Models\User;
use App\Services\PlaneReservationCheck\Checker;
use Carbon\CarbonImmutable;
use Exception;

class PlaneReservationChecker
{
    public function __construct(
        private Checker $checker,
    ) {
    }
    /**
     * @param $params array {
     *     plane_registration: string,
     *     user_id: string,
     *     user2_id: string,
     *     starts_at: string,
     *     ends_at: string,
     *     time: int,
     * }
     * @throws Exception
     */
    public function checkAll(array $params): void
    {
        /** @var User $user */
        $user = User::findOrFail($params['user_id']);
        /** @var Plane $plane */
        $plane = Plane::findOrFail($params['plane_id']);
        
        $startDate = CarbonImmutable::parse($params['starts_at']);
        $endDate = CarbonImmutable::parse($params['ends_at']);
        
        /** @var User|null $user2 */
        $user2 = isset($params['user2_id']) ? User::find($params['user2_id']) : null;

        $this->checker->check($startDate, $endDate, $user, $plane->id, $user2);
    }
}
