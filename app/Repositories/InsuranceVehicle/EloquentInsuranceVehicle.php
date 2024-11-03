<?php

namespace App\Repositories\InsuranceVehicle;

use App\Models\InsuranceVehicle;
use App\Repositories\InsuranceVehicle\InsuranceVehicleRepository;



class EloquentInsuranceVehicle implements InsuranceVehicleRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return InsuranceVehicle::all();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return InsuranceVehicle::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $InsuranceVehicle = InsuranceVehicle::create($data);

        return $InsuranceVehicle;
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
        $query = InsuranceVehicle::query();

        $result = $query->orderBy('id', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
