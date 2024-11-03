<?php

namespace App\Repositories\FuelStation;

use App\Models\FuelStation;
use App\Repositories\FuelStation\FuelStationRepository;



class EloquentFuelStation implements FuelStationRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return FuelStation::all();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return FuelStation::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $FuelStation = FuelStation::create($data);

        return $FuelStation;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $FuelStation = $this->find($id);

        $FuelStation->update($data);

        return $FuelStation;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $FuelStation = $this->find($id);

        return $FuelStation->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = FuelStation::query();

        $result = $query->orderBy('id', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
