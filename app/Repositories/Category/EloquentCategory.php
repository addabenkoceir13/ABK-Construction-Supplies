<?php

namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\Category\CategoryRepository;



class EloquentCategory implements CategoryRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return Category::all();
    }
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Category::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $Category = Category::create($data);

        return $Category;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $Category = $this->find($id);

        $Category->update($data);

        return $Category;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $Category = $this->find($id);

        return $Category->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = Category::query();

        $result = $query->orderBy('id', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
