<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\InsuranceVehicle;
use App\Repositories\InsuranceVehicle\InsuranceVehicleRepository;
use App\Repositories\Vehicle\VehicleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    protected $vehicle;
    protected $insuranceVehicle;

    public function __construct(VehicleRepository $vehicle, InsuranceVehicleRepository $insuranceVehicle)
    {
        $this->vehicle = $vehicle;
        $this->insuranceVehicle = $insuranceVehicle;
    }
    public function index()
    {
        $vehicles = $this->vehicle->paginate(10);

        return view('content.Vehicle.index', compact('vehicles'));
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
            'name'              => 'required|string',
            'type'              => 'required',
            'wilaya_license'    => 'required|numeric',
            'year_license'      => 'required|numeric',
            'license'           => 'required|numeric',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date',
        ]);
        if ($validator->fails()){
            toastr()->error($validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            DB::beginTransaction();
            $license_plate = $request->license . ' - ' . $request->year_license . ' - ' . $request->wilaya_license;

            $data = array_replace([
                'name'              => $request->name,
                'type'              => $request->type,
                'license_plate'    => $license_plate,
            ]);
            $vehicle = $this->vehicle->create($data);

            $dataIns = array_replace([
                'vehicle_id'     => $vehicle->id,
                'start_date'     => $request->start_date,
                'end_date'       => $request->end_date,
            ]);
            $insuranceVehicle = $this->insuranceVehicle->create($dataIns);

            DB::commit();
            toastr()->success('Vehicle added successfully');
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
          'name'              => 'required|string',
          'type'              => 'required',
          'wilaya_license'    => 'required|numeric',
          'year_license'      => 'required|numeric',
          'license'           => 'required|numeric',
          'start_date'        => 'required|date',
          'end_date'          => 'required|date',
        ]);
        if ($validator->fails()){
            toastr()->error($validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            DB::beginTransaction();
            $license_plate = $request->license . ' - ' . $request->year_license . ' - ' . $request->wilaya_license;

            $data = array_replace([
                'name'              => $request->name,
                'type'              => $request->type,
                'license_plate'    => $license_plate,
            ]);
            $vehicle = $this->vehicle->update($id,$data);

            $dataIns = array_replace([
                'start_date'     => $request->start_date,
                'end_date'       => $request->end_date,
            ]);
            $insuranceVehicle = $this->insuranceVehicle->update($request->insurance_id,$dataIns);

            DB::commit();
            toastr()->success('Vehicle updated successfully');
            return redirect()->back();
        }
        catch (\Exception $e) {
            DB::rollBack();
            toastr()->error($e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      try {
        $this->vehicle->delete($id);
        toastr()->success(__('Vehicle deleted successfully'));
        return redirect()->back();
      }
      catch (\Exception $e) {
        DB::rollBack();
        toastr()->error($e->getMessage());
        return redirect()->back();
    }
    }
}
