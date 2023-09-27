<?php

namespace App\Repositories;

class BaseRepository
{
    public mixed $model;

    public function setModel(string $model)
    {
        $this->model = "App\\Models\\$model";
    }

    public function getModel(): mixed
    {
        return $this->model;
    }

    public function paginationLimit()
    {
        return request('limit') ?? 10;
    }

    public function getById($id, $trashed = false)
    {
        if ($trashed) {
            return $this->getModel()::withTrashed()->findOrFail($id);
        }
        return $this->getModel()::findOrFail($id);
    }

    public function getAll()
    {
        return $this->getModel()::all();
    }

    public function paginate()
    {
        $limit = request('limit') ?? 10;
        return $this->getModel()::simplePaginate($limit);
    }

    public function create($data)
    {
        return $this->getModel()::create($data);
    }

    public function update($id, $data)
    {
        $obj = $this->getModel()::findOrFail($id);
        $obj->update($data);
        return $obj;
    }

    public function delete($id)
    {
        $obj = $this->getModel()::findOrFail($id);
        $obj->delete();
    }
}
