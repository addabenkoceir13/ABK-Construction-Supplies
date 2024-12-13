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
    public function paginate($perPage, $search = null, $start_date = null, $end_date = null)
    {
      $query = FuelStation::query();

      if ($start_date && $end_date) {
          $query->whereBetween('filing_datetime', [$start_date, $end_date]);
      }

      // Use paginate() instead of get()
      $result = $query->orderBy('id', 'desc')->whereStatus('unpaid')
                      ->paginate($perPage)
                      ->appends([
                          'start_date' => $start_date,
                          'end_date' => $end_date,
                      ]);

      return $result;
    }
    public function paginatePaid($perPage, $search = null, $start_date = null, $end_date = null)
    {
      $query = FuelStation::query();

      if ($start_date && $end_date) {
          $query->whereBetween('filing_datetime', [$start_date, $end_date]);
      }

      // Use paginate() instead of get()
      $result = $query->orderBy('id', 'desc')->whereStatus('paid')
                      ->paginate($perPage)
                      ->appends([
                          'start_date' => $start_date,
                          'end_date' => $end_date,
                      ]);

      return $result;
    }
}
