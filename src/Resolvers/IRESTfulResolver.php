<?php

namespace App\Packages\v1\RESTful\Resolvers;

interface IRESTfulResolver
{
    public function resolve($query);
}