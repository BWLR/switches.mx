<?php

namespace App\Scopes;

use Statamic\Query\Scopes\Scope;

class Switches extends Scope
{
    /**
     * Apply the scope.
     *
     * @param \Statamic\Query\Builder $query
     * @param array $values
     * @return void
     */
    public function apply($query, $values)
    {
        // Name
        if (request()->get('search')) {
            $query->where('title', 'like', '%' . request()->get('search') . '%');
        }

        // Manufacturer
        if (request()->get('manufacturer')) {
            $query->where('manufacturer', request()->get('manufacturer'));
        }

        // Brand
        if (request()->get('brand')) {
            $query->where('brand', request()->get('brand'));
        }

        // Switch type
        if (request()->get('switch-type')) {
            $query->where('switch_type', request()->get('switch-type'));
        }
    }
}
