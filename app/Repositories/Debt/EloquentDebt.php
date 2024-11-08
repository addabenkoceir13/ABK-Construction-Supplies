<?php

namespace App\Repositories\Debt;

use App\Models\Debt;
use App\Repositories\Debt\DebtRepository;



class EloquentDebt implements DebtRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return Debt::all();
    }

    public function getSupplier()
    {
        return Debt::where('tractor_driver_id', '!=', '1')->orderBy('id', 'DESC')->orderBy('status', 'ASC')->get();
    }

    public function driverDebtPaid()
    {
        return Debt::whereStatus('paid')->where('tractor_driver_id','!=','1')->orderBy('id', 'desc')->get();
    }
    public function driverDebtUnPaid()
    {
        return Debt::whereStatus('unpaid')->where('tractor_driver_id','!=',1)->orderBy('id', 'desc')->get();
    }
    public function debtPaid()
    {
        return Debt::whereStatus('paid')->whereTractorDriverId(1)->orderBy('id', 'desc')->get();
    }
    public function debtUnPaid()
    {
        return Debt::whereStatus('unpaid')->whereTractorDriverId(1)->orderBy('id', 'desc')->get();
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

        $result = $query->where('tractor_driver_id', '=', '1')->orderBy('id', 'desc')->orderBy('status', 'desc')
            ->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
