<?php

namespace App\Repositories\Supplier;

use App\Models\Supplier;
use App\Repositories\Supplier\SupplierRepository;



class EloquentSupplier implements SupplierRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return Supplier::all();
    }

    public function SelectSupplier()
    {
        return Supplier::whereId(1)->first();
    }
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Supplier::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $Supplier = Supplier::create($data);

        return $Supplier;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $Supplier = $this->find($id);

        $Supplier->update($data);

        return $Supplier;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $Supplier = $this->find($id);

        return $Supplier->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = Supplier::query();

        $result = $query->orderBy('id', 'desc')
            ->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
