<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Model\Cast;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ImmutableCarbonInstance implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        /** @var string $tz */
        $tz = config('app.timezone');
        return CarbonImmutable::createFromFormat($model->getDateFormat(), $value, $tz);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  array  $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        /** @var string $tz */
        $tz = config('app.timezone');
        /** @var CarbonImmutable $value */
        return $value->setTimezone($tz)->format($model->getDateFormat());
    }
}
