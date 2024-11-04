<?php

namespace App\Http\Controllers\TractorDriver;

use App\Http\Controllers\Controller;
use App\Repositories\Supplier\SupplierRepository;
use App\Repositories\TractorDriver\TractorDriverRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TractorDriverController extends Controller
{
  private $tractorDriver;

  public function __construct(TractorDriverRepository $tractorDriver)
  {
    $this->tractorDriver = $tractorDriver;
  }

  public function index()
  {
      $tractorDrivers = $this->tractorDriver->all();
      return view('content.TractorDriver.index', compact('tractorDrivers'));
  }

  public function create()
  {
      //
  }

  public function store(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'fullname'  => ['required','string','max:255'],
          'phone'     => ['required','numeric'],
      ]);
      if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();
      }

      try {
          DB::beginTransaction();

          $supplier = $this->tractorDriver->create($request->all());

          toastr()->success(__('Tractor driver added successfully'));
          DB::commit();
          return redirect()->back()->withSuccess(__('Tractor driver added successfully'));
      }
      catch (\Exception $e) {
          DB::rollBack();
          toastr()->error($e->getMessage());
          return redirect()->back();
      }
  }


  public function show($id)
  {
      //
  }

  public function edit($id)
  {
      //
  }


  public function update(Request $request, $id)
  {
      $validator = Validator::make($request->all(), [
          'fullname'  => ['required','string','max:255'],
          'phone'     => ['required','numeric'],
      ]);
      if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();
      }

      try {
          DB::beginTransaction();

          $supplier = $this->tractorDriver->update($id, $request->all());

          toastr()->success(__('Tractor driver update successfully'));
          DB::commit();
          return redirect()->back()->withSuccess(__('Tractor driver update successfully'));
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

          $this->tractorDriver->delete($id);

          toastr()->success(__('Tractor driver deleted successfully'));
          DB::commit();
          return redirect()->back()->withSuccess(__('Tractor driver deleted successfully'));
      }
      catch (\Exception $e) {
          DB::rollBack();
          toastr()->error($e->getMessage());
          return redirect()->back();
      }
  }
}
