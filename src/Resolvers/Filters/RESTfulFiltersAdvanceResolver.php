<?php

namespace App\Packages\v1\RESTful\Resolvers\Filters;

use App\Packages\v1\RESTful\Resolvers\Filters\RESTfulFiltersResolver;

class RESTfulFiltersAdvanceResolver extends RESTfulFiltersResolver
{
    private $_advanceFilters;

    public function __construct(Array $params, $tableName = null) {
        parent::__construct($tableName);

        if (!empty($params['advFilters'])) {
            $this->_advanceFilters = $params['advFilters'];
        }
    }

    /* -------------------- */

    public function resolve($query) {
        return $query->when($this->_advanceFilters, function ($query) {
            return $this->_getGroup($query, $this->_advanceFilters);
        });
    }

    private function _getGroup($query, $filters, $groupName = '') {
        foreach ($filters as $key => $value) {
            if (starts_with($key, '_')) {
                $groupMethod = starts_with($key, '_orGroup') ? 'orWhere' : 'where';

                $query = $query->$groupMethod(function ($query) use ($key, $value) {
                    return $this->_getGroup($query, $value, $key);
                });
            } else {
                $query = $this->_getFilters($query, $filters, 'where', $groupName);
                break;
            }
        }

        return $query;
    }

    private function _getFilters($query, $filters, $groupMethod, $groupName = '') {
        $query = $query->$groupMethod(function ($query) use ($filters, $groupName) {
            foreach ($filters as $field => $value) {
                if (starts_with($field, '_')) {
                    continue;
                }

                $isOr = ends_with($groupName, ':or') ? true : false;
                $query = $this->_createFilter($query, $field, $value, $isOr);
            }

            return $query;
        });

        return $query;
    }
}
