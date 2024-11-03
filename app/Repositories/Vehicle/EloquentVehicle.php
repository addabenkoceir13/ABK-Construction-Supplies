<?php

namespace App\Repositories\Vehicle;

use App\Models\Vehicle;
use App\Repositories\Vehicle\VehicleRepository;



class EloquentVehicle implements VehicleRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return Vehicle::all();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Vehicle::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $Vehicle = Vehicle::create($data);

        return $Vehicle;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $Vehicle = $this->find($id);

        $Vehicle->update($data);

        return $Vehicle;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $Vehicle = $this->find($id);

        return $Vehicle->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = Vehicle::query();

        $result = $query->orderBy('id', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
