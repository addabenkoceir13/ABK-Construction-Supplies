<?php

namespace App\Http\Controllers\FuelStation;

use App\Http\Controllers\Controller;
use App\Models\FuelStation;
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
    $perPage  = request('per_page', 10);
    $search   = request('search');
    $startDate = request('start_date');
    $endDate   = request('end_date');

    $fuelStations = $this->fuelStation->paginate($perPage, $search, $startDate, $endDate);
    $vehicles = $this->vehicle->all();

    if ($request->ajax()) {
      $total = $fuelStations->sum('amount');

      return response()->json([
        'content' => view('content.Fuelstation.pagination-data', compact('fuelStations', 'vehicles'))->render(),
        'pagination' => $fuelStations->links('vendor.pagination.custom')->render(),
        'total' => number_format($total, 2, ',', ''), // Format the total
      ]);
      // return view('content.fuelstation.pagination-data', compact('fuelStations', 'vehicles'))->render();
    }
    return view('content.fuelstation.index', compact('fuelStations', 'vehicles'));
  }

  public function indexA(Request $request)
  {
    if ($request->ajax()) {
      $query = FuelStation::query();

      // Apply search filter
      if (!empty($request->search['value'])) {
        $searchValue = $request->search['value'];
        $query->where('name_owner', 'like', "%{$searchValue}%")
          ->orWhere('name_driver', 'like', "%{$searchValue}%")
          ->orWhere('name_distributor', 'like', "%{$searchValue}%");
      }

      // Apply date filter
      if ($request->start_date && $request->end_date) {
        $query->whereBetween('filing_datetime', [$request->start_date, $request->end_date]);
      }

      // Pagination
      $totalRecords = $query->count();
      $data = $query->offset($request->start)
        ->limit($request->length)
        ->get();

      // Prepare data for DataTables
      $result = [
        "draw" => $request->draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data->map(function ($fuelStation, $index) use ($request) {
          return [
            '#' => $request->start + $index + 1,
            'Vehicle' => $fuelStation->vehicle->name,
            'Name Owner' => $fuelStation->name_owner,
            'Name Driver' => $fuelStation->name_driver,
            'Name Distributor' => $fuelStation->name_distributor,
            'Filing Datetime' => $fuelStation->filing_datetime,
            'Liter' => $fuelStation->liter,
            'Amount' => $fuelStation->amount,
            'Created At' => $fuelStation->created_at->format('Y-m-d'),
            'Status' => $fuelStation->status === 'paid' ? __('Paid') : __('Unpaid'),
            'Action' => view('content.Fuelstation.partials.actions', compact('fuelStation'))->render(),
          ];
        }),
      ];

      return response()->json($result);
    }
    $vehicles = $this->vehicle->paginate(10);

    return view('content.fuelstation.index', compact('vehicles'));
  }

  public function indexPaid(Request $request)
  {
    $fuelStations = $this->fuelStation->paginatePaid(10, $search = null, $request->start_date, $request->end_date);
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
      'liter'             => 'sometimes|numeric',
      'amount'            => 'required|numeric',
      'type_fuel'         => 'required',
    ]);

    if ($validator->fails()) {
      toastr()->error($validator->errors()->first());
      return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
      DB::beginTransaction();

      $fuelStation = $this->fuelStation->create($request->all());

      DB::commit();
      toastr()->success(__('Added fuel receipt successfully'));
      return redirect()->back();
    } catch (\Exception $e) {
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
      'liter'             => 'sometimes|numeric',
      'amount'            => 'required|numeric',
      'type_fuel'         => 'required',
    ]);

    if ($validator->fails()) {
      toastr()->error($validator->errors()->first());
      return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
      DB::beginTransaction();

      $fuelStation = $this->fuelStation->update($id, $request->all());

      DB::commit();
      toastr()->success(__('Updated fuel receipt successfully'));
      return redirect()->back();
    } catch (\Exception $e) {
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
    } catch (\Exception $e) {
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
    } catch (\Exception $e) {
      DB::rollBack();
      toastr()->error($e->getMessage());
      return redirect()->back();
    }
  }

  public function updateStatus(Request $request)
  {
    try {
      DB::beginTransaction();
        $ids = $request->ids;
        $fuelStation = $this->fuelStation->updateStatus($ids, 'paid');

      DB::commit();
      toastr()->success(__('Status updated successfully'));
      return redirect()->back();
    } catch (\Exception $e) {
      DB::rollBack();
      toastr()->error($e->getMessage());
      return redirect()->back();
    }
  }
}
