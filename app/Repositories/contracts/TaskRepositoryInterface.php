<?php

namespace App\Repositories\Contracts;


use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


interface TaskRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function find(int $id): ?Task;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;
}
