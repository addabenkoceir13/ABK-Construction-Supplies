<?php

namespace App\Repositories\SubCategories;


use App\Models\SubCategory;

class EloquentSubCategory implements SubCategoryRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return SubCategory::all();
    }
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return SubCategory::find($id);
    }

    public function get($id)
    {
        return SubCategory::where('category_id', $id)->get();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $SubCategory = SubCategory::create($data);

        return $SubCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $SubCategory = $this->find($id);

        $SubCategory->update($data);

        return $SubCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $SubCategory = $this->find($id);

        return $SubCategory->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = SubCategory::query();

        $result = $query->orderBy('id', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
