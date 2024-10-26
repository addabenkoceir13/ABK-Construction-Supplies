<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Repositories\Supplier\SupplierRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    private $supplier;

    public function __construct(SupplierRepository $supplier)
    {
      $this->supplier = $supplier;
    }

    public function index()
    {
        $suppliers = $this->supplier->all();
        return view('content.supplier.index', compact('suppliers'));
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

            $supplier = $this->supplier->create($request->all());

            toastr()->success(__('Delivery driver added successfully'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Delivery driver added successfully'));
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

            $supplier = $this->supplier->update($id, $request->all());

            toastr()->success(__('Delivery driver update successfully'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Delivery driver update successfully'));
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

            $this->supplier->delete($id);

            toastr()->success(__('Delivery driver deleted successfully'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Delivery driver deleted successfully'));
        }
        catch (\Exception $e) {
            DB::rollBack();
            toastr()->error($e->getMessage());
            return redirect()->back();
        }
    }
}
