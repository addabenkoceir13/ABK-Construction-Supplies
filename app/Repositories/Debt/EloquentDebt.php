<?php

namespace App\Repositories\Debt;

use App\Models\Debt;
use App\Repositories\DebtProduct\DebtProductRepository;



class EloquentDebt implements DebtProductRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return Debt::all();
    }
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Debt::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $Debt = Debt::create($data);

        return $Debt;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $Debt = $this->find($id);

        $Debt->update($data);

        return $Debt;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $Debt = $this->find($id);

        return $Debt->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = Debt::query();

        $result = $query->orderBy('id', 'desc')
            ->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
