<?php

namespace Mchamper\LaravelRestful;

use Illuminate\Database\QueryException;
use Mchamper\LaravelRestful\Resolvers\RESTfulFieldsResolver;
use Mchamper\LaravelRestful\Resolvers\RESTfulWithResolver;
use Mchamper\LaravelRestful\Resolvers\RESTfulWithCountResolver;
use Mchamper\LaravelRestful\Resolvers\Filters\RESTfulFiltersDefaultResolver;
use Mchamper\LaravelRestful\Resolvers\Filters\RESTfulFiltersAdvanceResolver;
use Mchamper\LaravelRestful\Resolvers\RESTfulScopesResolver;
use Mchamper\LaravelRestful\Resolvers\RESTfulSearchResolver;
use Mchamper\LaravelRestful\Resolvers\RESTfulSortResolver;
use Mchamper\LaravelRestful\Resolvers\RESTfulAppendsResolver;
use Mchamper\LaravelRestful\Resolvers\RESTfulGroupByResolver;

class RESTful
{
    private $_resource;

    private $_fieldsResolver;
    private $_withResolver;
    private $_withCountResolver;
    private $_filtersDefaultResolver;
    private $_filtersAdvanceResolver;
    private $_scopesResolver;
    private $_searchResolver;
    private $_sortResovler;
    private $_groupByResovler;
    private $_appendsResolver;

    private $_limit;
    private $_offset;

    public function __construct($resource, array $params, $tableName = null) {
        $this->_resource = $resource;

        if (!$tableName) {
            if (method_exists($this->_resource, 'getTable')) {
                $tableName = $this->_resource->getTable();
            }
        }

        $this->_fieldsResolver = new RESTfulFieldsResolver($params, $tableName);
        $this->_withResolver = new RESTfulWithResolver($params);
        $this->_withCountResolver = new RESTfulWithCountResolver($params);
        $this->_filtersDefaultResolver = new RESTfulFiltersDefaultResolver($params, $tableName);
        $this->_filtersAdvanceResolver = new RESTfulFiltersAdvanceResolver($params, $tableName);
        $this->_scopesResolver = new RESTfulScopesResolver($params, $tableName);
        $this->_searchResolver = new RESTfulSearchResolver($params);
        $this->_sortResovler = new RESTfulSortResolver($params);
        $this->_groupByResovler = new RESTfulGroupByResolver($params);
        $this->_appendsResolver = new RESTfulAppendsResolver($params);

        if (!empty($params['limit'])) {
            $this->_limit = $params['limit'];
        }

        if (!empty($params['offset'])) {
            $this->_offset = $params['offset'];
        }

        $this->_resource = $this->_fieldsResolver->resolve($this->_resource);
        $this->_resource = $this->_withResolver->resolve($this->_resource);
        $this->_resource = $this->_withResolver->resolveRelation($this->_resource);
        $this->_resource = $this->_withCountResolver->resolve($this->_resource);
        $this->_resource = $this->_withCountResolver->resolveRelation($this->_resource);
        $this->_resource = $this->_filtersDefaultResolver->resolve($this->_resource);
        $this->_resource = $this->_filtersAdvanceResolver->resolve($this->_resource);
        $this->_resource = $this->_scopesResolver->resolve($this->_resource);
        $this->_resource = $this->_searchResolver->resolve($this->_resource);
        $this->_resource = $this->_sortResovler->resolve($this->_resource);
        $this->_resource = $this->_groupByResovler->resolve($this->_resource);
    }

    /* -------------------- */

    public function getQuery(bool $mustClone = false) {
        // dd($this->_resource->toSql());

        if ($mustClone) {
            return clone $this->_resource;
        }

        return $this->_resource;
    }

    private function _badRequest($e) {
        throw $e;

        // if (config('app.debug')) {
        //     throw $e;
        // }

        // throw new \Exception('Bad request.', 400);
    }

    /* -------------------- */

    public function paginate(bool $mustClone = false) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery($mustClone)->paginate($this->_limit));

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function get(bool $mustClone = false) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery($mustClone)
                ->when($this->_limit, function ($query) {
                    return $query->limit($this->_limit);
                })

                ->when($this->_offset, function ($query) {
                    return $query->offset($this->_offset);
                })

                ->get());

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function count(bool $mustClone = false) {
        try {
            return $this->getQuery($mustClone)->count();

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function find($primaryKeys, bool $mustClone = false) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery($mustClone)->find($primaryKeys));

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function findOrFail($primaryKeys, bool $mustClone = false) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery($mustClone)->findOrFail($primaryKeys));

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function first(bool $mustClone = false) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery($mustClone)->first());

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function firstOrFail(bool $mustClone = false) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery($mustClone)->firstOrFail());

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function exists(bool $mustClone = false) {
        try {
            return $this->getQuery($mustClone)->exists();

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function doesntExist(bool $mustClone = false) {
        try {
            return $this->getQuery($mustClone)->doesntExist();

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }
}
