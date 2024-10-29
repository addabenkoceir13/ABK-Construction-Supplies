<?php

namespace App\Http\Controllers\Debt;

use App\Http\Controllers\Controller;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Debt\DebtRepository;
use App\Repositories\DebtProduct\DebtProductRepository;
use App\Repositories\Supplier\SupplierRepository;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DebtWithSupplierController extends Controller
{

  private $debt;
  private $debtProduct;
  private $category;
  private $supplier;

  public function __construct(DebtRepository $debt, DebtProductRepository $debtProduct, CategoryRepository $category, SupplierRepository $supplier)
  {
      $this->debt = $debt;
      $this->debtProduct = $debtProduct;
      $this->category = $category;
      $this->supplier = $supplier;
  }

  public function index()
  {
      $date = now();
      $dateToday = $date->format('Y-m-d');

      $debts = $this->debt->driverDebtUnPaid();
      // dd($debts);
      $categories = $this->category->all();
      $suppliers = $this->supplier->SupplierActive();

      return view('content.DebtWithSupplier.index', compact('debts', 'categories' , 'suppliers', 'dateToday'));
  }

  public function indexPaid()
  {
      $date = now();
      $dateToday = $date->format('Y-m-d');

      $debts = $this->debt->driverDebtPaid();
      // dd($debts);
      $categories = $this->category->all();
      $suppliers = $this->supplier->SupplierActive();

      return view('content.DebtWithSupplier.indexPaid', compact('debts', 'categories' , 'suppliers', 'dateToday'));
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

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request): RedirectResponse
  {
    // dd($request->all());
      $validator = Validator::make($request->all(), [
          'supplier_id'  => ['required','exists:suppliers,id'],
          'fullname'  => ['required','string','max:255'],
          'phone'     => ['required','numeric'],
          'date_debut_debt' => ['required','date'],
      ]);
      if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();
      }

      try {
          DB::beginTransaction();

          $products   = $request->input('name_product');
          $quantities = $request->input('quantity');
          $amounts    = $request->input('amount_due');
          $dateDebts  = $request->input('date_debt');
          $subcategoryIds  = $request->input('subcategory_ids');
          $total      = 0;

          $dataDebt = array_replace( [
              'user_id'       => Auth::user()->id,
              'supplier_id'   => $request->supplier_id,
              'fullname'      => $request->fullname,
              'phone'         => $request->phone,
              'date_debut_debt' => $request->input('date_debut_debt'),
              'note'      => $request->note,
              'status'    => config('constant.DEBTS_STATUS.UNPAID'),
          ]);


          $debt = $this->debt->create($dataDebt);

          foreach ($products as $index => $product) {
            // Process each product, quantity, and amount
              $subcategory_id  = $subcategoryIds[$index];
              $quantity  = $quantities[$index];
              $amount    = $amounts[$index];
              $dateDebt  = $dateDebts[$index];
              $total    += $amount;

              $dataDebtProduct = array_replace( [
                  'debt_id'   => $debt->id,
                  'subcategory_id'   => $subcategory_id,
                  'name_category'      => $products[$index],
                  'quantity'  => $quantity,
                  'amount'    => $amount,
                  'date_debt' => $dateDebt,
              ]);

              $this->debtProduct->create($dataDebtProduct);
          }

          $dataDebtTotal = array_replace( [
              'total_debt_amount' => $total,
              'rest_debt_amount' => $total,
          ]);

          $this->debt->update($debt->id, $dataDebtTotal);

          toastr()->success(__('Debt added successfully'));

          DB::commit();
          return redirect()->back()->withSuccess(__('Debt added successfully'));
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
      $debt = $this->debt->find($id);

      return view('content.DebtWithSupplier.view', compact('debt'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      $date = now();
      $dateToday = $date->format('Y-m-d');
      $debt = $this->debt->find($id);
      $categories = $this->category->all();

      return view('content.DebtWithSupplier.edit', compact('debt', 'categories', 'dateToday'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      // dd($request->all());
      // dd($request->all());
      $validator = Validator::make($request->all(), [
          'fullname'  => ['required','string','max:255'],
          'phone'     => ['required','numeric'],
          'date_debut_debt' => ['required','date'],
      ]);
      if ($validator->fails()){
          toastr()->error($validator->errors()->first());
          return redirect()->back()->withErrors($validator)->withInput();
      }

      try {
          DB::beginTransaction();

          $products   = $request->input('name_product');
          $quantities = $request->input('quantity');
          $amounts    = $request->input('amount_due');
          $dateDebts  = $request->input('date_debt');
          $subcategoryIds  = $request->input('subcategory_ids');
          $Ids        = $request->input('id');
          $total      = 0;

          $dataDebt = array_replace( [
              'fullname'      => $request->fullname,
              'phone'         => $request->phone,
              'date_debut_debt' => $request->input('date_debut_debt'),
              'note'      => $request->note,
              'status'    => config('constant.DEBTS_STATUS.UNPAID'),
          ]);

          $debt = $this->debt->update($id, $dataDebt);

          for ($index=0; $index < count($products); $index++) {
              $subcategory_id  = $subcategoryIds[$index];
              $quantity        = $quantities[$index];
              $amount          = $amounts[$index];
              $dateDebt        = $dateDebts[$index];
              $idOld           = $Ids[$index];

              $total += $amount;

              $dataDebtProduct = [
                'subcategory_id' => $subcategory_id,
                'name_category'  => $products[$index],
                'quantity'       => $quantities[$index],
                'amount'         => $amounts[$index],
                'date_debt'      => $dateDebts[$index],
            ];

              if ($idOld == 0) {
                $dataDebtProduct['debt_id'] = $id;
                $this->debtProduct->create( $dataDebtProduct);
            }
            else {
                $dataDebtProduct = array_replace( [
                  'subcategory_id'   => $subcategory_id,
                  'name_category'    => $products[$index],
                  'quantity'  => $quantities[$index],
                  'amount'    => $amounts[$index],
                  'date_debt' => $dateDebts[$index],
                ]);
                $this->debtProduct->update($idOld,$dataDebtProduct);
            }

          }

          $dataDebtTotal = array_replace( [
              'total_debt_amount' => $total,
              'rest_debt_amount' => $total,
          ]);

          $this->debt->update($id, $dataDebtTotal);

          toastr()->success(__('Debt updated successfully'));

          DB::commit();
          return redirect()->route('debt-supplier.index')->withSuccess(__('Debt updated successfully'));
      }
      catch (\Exception $e) {
          DB::rollBack();
          dd($e->getMessage());
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
          $this->debt->delete($id);
          toastr()->success(__('Debt deleted successfully'));
          return redirect()->route('debt.index');
      }
      catch (\Exception $e) {
        DB::rollBack();
        toastr()->error($e->getMessage());
        return redirect()->back();
    }
  }

  public function payDebt(Request $request,$id)
  {
    try {
      DB::beginTransaction();
      $debt = $this->debt->find($id);

      $DebtPaid = $request->debt_paid;
      $idsDebtProsucts = $request->id_debt_product;

      foreach ($idsDebtProsucts as $idDebtProduct) {
        $data = array_replace(['status' => 1]);
        $debtProduct = $this->debtProduct->update($idDebtProduct, $data);
      }

      $restDebtAmount =  $debt->rest_debt_amount;


      if ($DebtPaid == $debt->total_debt_amount) {
        $dataDebt = array_replace([
          'status'            => config('constant.DEBTS_STATUS.PAID'),
          'debt_paid'         => $DebtPaid,
          'rest_debt_amount'  => $debt->total_debt_amount - $DebtPaid,
          'date_end_debt'     => now()->format('Y-m-d'),
        ]);
        $this->debt->update($id, $dataDebt);
      }
      elseif(($debt->debt_paid + $DebtPaid) == $debt->total_debt_amount) {
          $dataDebt = array_replace([
            'status'            => config('constant.DEBTS_STATUS.PAID'),
            'debt_paid'         => $debt->debt_paid + $DebtPaid,
            'rest_debt_amount'  => $debt->rest_debt_amount - $DebtPaid,
            'date_end_debt'     => now()->format('Y-m-d'),
          ]);
          $this->debt->update($id, $dataDebt);
      }
      elseif(($debt->debt_paid + $DebtPaid) < $debt->total_debt_amount) {
          $dataDebt = array_replace([
            'debt_paid'         => $debt->debt_paid + $DebtPaid,
            'rest_debt_amount'  => $debt->rest_debt_amount - $DebtPaid,
            'date_end_debt'     => now()->format('Y-m-d'),
          ]);
          $this->debt->update($id, $dataDebt);
      }
      elseif(($debt->debt_paid + $DebtPaid) > $debt->total_debt_amount){
          toastr()->error(__('The amount paid exceeds the amount owed.'));
          return redirect()->route('debt.index');
      }


      toastr()->success(__('Debt paid successfully'));
      DB::commit();
      return redirect()->back();
    }
    catch (\Exception $e) {
      DB::rollBack();
      toastr()->error($e->getMessage());
      return redirect()->back();
  }
    // $debts = $this->debt->paginate(10);

    // return view('content.debt.pay', compact('debts'));
  }
}
