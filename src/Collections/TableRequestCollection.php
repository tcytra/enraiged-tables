<?php

namespace Enraiged\Tables\Collections;

use Enraiged\Collections\RequestCollection;
use Enraiged\Collections\RouteCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TableRequestCollection extends RequestCollection
{
    /** @var  Collection  The collection of table filters. */
    protected $filters;

    /**
     *  Determine if the specified item(s) from the collection has/have value.
     *
     *  @param  string|array  $key
     *  @return bool
     */
    public function filled($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $key) {
            $value = $this->get($key);

            if (is_null($value)
            || (is_array($value) && !count($value))
            || (!is_bool($value) && trim((string) $value) === '')) {
                return false;
            }
        }

        return true;
    }

    /**
     *  Return the collection of requested filters.
     *
     *  @return \Illuminate\Support\Collection
     */
    public function filters(): Collection
    {
        return $this->filters;
    }

    /**
     *  Return the item if it exists in the collection.
     *
     *  @param  string  $key
     *  @return mixed|null
     */
    public function getFilter(string $key)
    {
        return $this->hasFilter($key)
            ? $this->filters->get($key)
            : null;
    }

    /**
     *  Determine if an item exists in the collection by key.
     *
     *  @param  string  $key
     *  @return bool
     */
    public function hasFilters(): bool
    {
        return $this->filters->count();
    }

    /**
     *  Determine if an item exists in the collection by key.
     *
     *  @param  string  $key
     *  @return bool
     */
    public function hasFilter(string $key): bool
    {
        return $this->filters->has($key);
    }

    /**
     *  Add a parameter to the route collection.
     *
     *  @param  array   $parameter
     *  @return self
     *
    public function addRouteParameter(array $parameter): self
    {
        $this->items['_route'] = $this->items['_route']->merge($parameter);

        return $this;
    }*/

    /**
     *  Return the RouteCollection.
     *
     *  @return \Enraiged\Collections\RouteCollection
     */
    public function route(): RouteCollection
    {
        return $this->route;
    }

    /**
     *  Create a collection from the provided Request object.
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @return self
     */
    public static function From(Request $request): self
    {
        $called = get_called_class();

        $parameters = collect($request->all())
            ->except('filters')
            ->toArray();

        $filters = $request->has('filters')
            ? $request->get('filters')
            : [];

        $collection = new $called($parameters);
        $collection->filters = collect($filters);
        $collection->route = RouteCollection::from($request->route());

        if (Auth::check()) {
            $collection->user = $request->user();
        }

        return $collection;
    }
}
