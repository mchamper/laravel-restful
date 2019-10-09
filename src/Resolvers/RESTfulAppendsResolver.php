<?php

namespace App\Packages\v1\RESTful\Resolvers;

use App\Packages\v1\RESTful\Resolvers\IRESTfulResolver;

class RESTfulAppendsResolver implements IRESTfulResolver
{
    private $_appends;

    public function __construct(Array $params, $tableName = null) {
        if (!empty($params['appends'])) {
            $this->_appends = explode(',', $params['appends']);
        }
    }

    /* -------------------- */

    public function resolve($res) {
        if ($this->_appends) {
            if (method_exists($res, 'setAppends')) {
                return $res->setAppends($this->_appends);
            }

            $res->data = $res->each(function ($item, $key) {
                return $item->setAppends($this->_appends);
            });
        }

        return $res;
    }
}
