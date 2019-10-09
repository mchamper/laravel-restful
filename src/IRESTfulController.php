<?php

namespace App\Packages\v1\RESTful;

interface IRESTfulController
{
    /**
     * Get List
     */
    public function index();

    /**
     * Get One
     */
    public function show($id);

    /**
     * Create
     */
    public function store();

    /**
     * Update
     */
    public function update($id);

    /**
     * Delete
     */
    public function delete($id);
}
