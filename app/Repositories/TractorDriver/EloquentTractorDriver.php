<?php

namespace App\Repositories\TractorDriver;

use App\Models\Supplier;
use App\Models\TractorDriver;
use App\Repositories\Supplier\SupplierRepository;



class EloquentTractorDriver implements TractorDriverRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return TractorDriver::all();
    }

    public function TractorDriverNormal()
    {
        return TractorDriver::whereType('normal')->first();
    }
    public function TractorDriverDeliveryActive()
    {
        return TractorDriver::whereStatus('active')->whereType('delivery')->get();
    }
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return TractorDriver::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $TractorDriver = TractorDriver::create($data);

        return $TractorDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $TractorDriver = $this->find($id);

        $TractorDriver->update($data);

        return $TractorDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $TractorDriver = $this->find($id);

        return $TractorDriver->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = TractorDriver::query();

        $result = $query->orderBy('id', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
