<?php

namespace App\Packages\v1\RESTful\Resolvers;

use App\Packages\v1\RESTful\Resolvers\IRESTfulResolver;

class RESTfulSortResolver implements IRESTfulResolver
{
    private $_sort;

    public function __construct(Array $params) {
        if (!empty($params['sort'])) {
            $this->_sort = explode(',', $params['sort']);
        }
    }

    /* -------------------- */

    public function resolve($query) {
        return $query->when($this->_sort, function ($query) {
            foreach ($this->_sort as $value) {
                $sortDirection = 'asc';

                if (starts_with($value, '-')) {
                    $sortDirection = 'desc';
                    $value = str_replace('-', '', $value);
                }

                $query = $query->orderBy($value, $sortDirection);
            }

            return $query;
        });
    }
}