<?php

namespace App\Repositories\DebtHistory;


use App\Models\DebtHistory;
use App\Repositories\DebtHistory\DebtHistoryRepository;



class EloquentDebtHistory implements DebtHistoryRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return DebtHistory::all();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return DebtHistory::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $DebtHistory = DebtHistory::create($data);

        return $DebtHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $DebtHistory = $this->find($id);

        $DebtHistory->update($data);

        return $DebtHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $DebtHistory = $this->find($id);

        return $DebtHistory->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = DebtHistory::query();

        $result = $query->orderBy('id', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
