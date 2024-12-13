<?php

namespace App\Http\Controllers\FuelStation;

use App\Http\Controllers\Controller;
use App\Repositories\FuelStation\FuelStationRepository;
use App\Repositories\Vehicle\VehicleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FulstationController extends Controller
{
    private $fuelStation;
    private $vehicle;

    public function __construct(FuelStationRepository $fuelStation, VehicleRepository $vehicle)
    {
        $this->fuelStation = $fuelStation;
        $this->vehicle = $vehicle;
    }

    public function index(Request $request)
    {
        $fuelStations = $this->fuelStation->paginate(10, $serche = null, $request->start_date, $request->end_date);
        $vehicles = $this->vehicle->paginate(10);
        return view('content.Fuelstation.index', compact('fuelStations', 'vehicles'));
    }
    public function indexPaid(Request $request)
    {
        $fuelStations = $this->fuelStation->paginatePaid(10, $serche = null, $request->start_date, $request->end_date);
        $vehicles = $this->vehicle->paginate(10);
        return view('content.Fuelstation.index', compact('fuelStations', 'vehicles'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id'        => 'required|exists:vehicles,id',
            'name_owner'        => 'required|string|max:255',
            'name_driver'       => 'required|string|max:255',
            'name_distributor'  => 'required|string|max:255',
            'filing_datetime'   => 'required|date',
            'liter'             => 'required|numeric',
            'amount'            => 'required|numeric',
            'type_fuel'         => 'required',
        ]);

        if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
          DB::beginTransaction();

          $fuelStation = $this->fuelStation->create($request->all());

          DB::commit();
          toastr()->success(__('Added fuel receipt successfully'));
          return redirect()->back();
        }
        catch (\Exception $e) {
          DB::rollBack();
          toastr()->error($e->getMessage());
          return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
          'vehicle_id'        => 'required|exists:vehicles,id',
          'name_owner'        => 'required|string|max:255',
          'name_driver'       => 'required|string|max:255',
          'name_distributor'  => 'required|string|max:255',
          'filing_datetime'   => 'required|date',
          'liter'             => 'required|numeric',
          'amount'            => 'required|numeric',
          'type_fuel'         => 'required',
        ]);

        if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
          DB::beginTransaction();

          $fuelStation = $this->fuelStation->update($id,$request->all());

          DB::commit();
          toastr()->success(__('Updated fuel receipt successfully'));
          return redirect()->back();
        }
        catch (\Exception $e) {
          DB::rollBack();
          toastr()->error($e->getMessage());
          return redirect()->back();
        }
    }

    public function destroy($id)
    {
        try {
          DB::beginTransaction();

          $fuelStation = $this->fuelStation->delete($id);

          DB::commit();
          toastr()->success(__('Deleted fuel receipt successfully'));
          return redirect()->back();
        }
        catch (\Exception $e) {
          DB::rollBack();
          toastr()->error($e->getMessage());
          return redirect()->back();
        }
    }

    public function status(Request $request, $id)
    {
      try {
        DB::beginTransaction();

        $fuelStation = $this->fuelStation->find($id);
        $fuelStation->status = $request->status;
        $fuelStation->save();

        DB::commit();
        toastr()->success(__('Status updated successfully'));
        return redirect()->back();
      } 
      catch (\Exception $e) {
        DB::rollBack();
        toastr()->error($e->getMessage());
        return redirect()->back();
      }
    }
}
