<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UserRepository extends BaseRepository implements UserInterface
{
    public function model(): Model|Builder
    {
        return new User();
    }

    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return $this->findWhere('email', $email)->first();
    }

    public function search(Request $request): Collection
    {

        $query = $this->model()->newQuery();

        // only designers who have designs
        if ($request->has_designs) {
            $query->has('designs');
        }

        // check for available to hire
        if ($request->available_to_hire) {
            $query->where('available_to_hire', true);
        }

        // Geographic search

        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        if ($lat && $lng) {
            $point = new Point($lat, $lng);
            $unit === 'km' ? $dist *= 1000 : $dist *= 1609.34;

            $query->distanceSphereExcludingSelf('location', $point, $dist);
        }

        if ($request->orderByLatest) {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->get();
    }

    /**
     * @param  array<mixed>  $criteria
     */
    public function withCriteria(...$criteria): self
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }
}
