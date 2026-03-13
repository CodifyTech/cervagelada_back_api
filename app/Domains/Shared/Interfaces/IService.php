<?php

namespace App\Domains\Shared\Interfaces;

interface IService
{
    /**
     * @param  array  $options  (path, query, fragment, pageName)
     */
    public function index(array $options = [], ?\Closure $builderCallback = null);

    /**
     * Display the specified resource.
     */
    public function show(string $id);

    /**
     * Show the form for editing the specified resource.
     */
    public function store(array $data);

    /**
     * Update the specified resource in storage.
     */
    public function update(array $data, string $id);

    /**
     * Find
     */
    public function findById(string $id);

    /**
     * Search
     */
    public function search(
        array $options = [],
        ?\Closure $builderCallback = null
    );

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id);
}
