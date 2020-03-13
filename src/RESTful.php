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

class RESTful
{
    private $_resource;

    private $_fieldsResolver;
    private $_withResolver;
    private $_filtersDefaultResolver;
    private $_filtersAdvanceResolver;
    private $_scopesResolver;
    private $_searchResolver;
    private $_sortResovler;
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
        $this->_appendsResolver = new RESTfulAppendsResolver($params);

        if (!empty($params['limit'])) {
            $this->_limit = $params['limit'];
        }

        if (!empty($params['offset'])) {
            $this->_offset = $params['offset'];
        }
    }

    /* -------------------- */

    public function getQuery() {
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

        // dd($this->_resource->toSql());

        return $this->_resource;
    }

    private function _badRequest($e) {
        if (config('app.debug')) {
            throw $e;
        }

        throw new \Exception('Bad request.', 400);
    }

    /* -------------------- */

    public function paginate() {
        try {
            return $this->_appendsResolver->resolve($this->getQuery()->paginate($this->_limit));

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function get() {
        try {
            return $this->_appendsResolver->resolve($this->getQuery()
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

    public function count() {
        try {
            return $this->getQuery()->count();

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function find($primaryKeys) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery()->find($primaryKeys));

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function findOrFail($primaryKeys) {
        try {
            return $this->_appendsResolver->resolve($this->getQuery()->findOrFail($primaryKeys));

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function first() {
        try {
            return $this->_appendsResolver->resolve($this->getQuery()->first());

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function firstOrFail() {
        try {
            return $this->_appendsResolver->resolve($this->getQuery()->firstOrFail());

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function exists() {
        try {
            return $this->getQuery()->exists();

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }

    public function doesntExist() {
        try {
            return $this->getQuery()->doesntExist();

        } catch (QueryException $e) {
            $this->_badRequest($e);
        }
    }
}
