<?php

namespace App\Http\Controllers\Debt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Debt\SearchDebtRequest;
use App\Http\Requests\Debt\StoreDebtSupplierRequest;
use App\Http\Requests\Debt\UpdateDebtSupplierRequest;
use App\Queries\DebtSearchQuery;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Debt\DebtRepository;
use App\Repositories\DebtHistory\DebtHistoryRepository;
use App\Repositories\DebtProduct\DebtProductRepository;
use App\Repositories\TractorDriver\TractorDriverRepository;
use App\Services\Debt\DebtPaymentCalculator;
use App\Services\Debt\DebtService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DebtWithSupplierController extends Controller
{

  private $debt;
  private $debtHistory;
  private $debtProduct;
  private $category;
  private $tractorDriver;
  private $debtService;
  private $paymentCalculator;
  private $debtSearch;

  public function __construct(DebtRepository $debt, DebtHistoryRepository $debtHistory, DebtProductRepository $debtProduct, CategoryRepository $category, TractorDriverRepository $tractorDriver, DebtService $debtService, DebtPaymentCalculator $paymentCalculator, DebtSearchQuery $debtSearch)
  {
    $this->debt = $debt;
    $this->debtHistory = $debtHistory;
    $this->debtProduct = $debtProduct;
    $this->category = $category;
    $this->tractorDriver = $tractorDriver;
    $this->debtService = $debtService;
    $this->paymentCalculator = $paymentCalculator;
    $this->debtSearch = $debtSearch;
  }

  public function index(SearchDebtRequest $request)
  {
    $date = now();
    $dateToday = $date->format('Y-m-d');

    $debts = $this->debtSearch->paginate($request->validated('name'), $request->validated('phone'), unpaid: true, forSupplier: true);
    $categories = $this->category->all();
    $suppliers = $this->tractorDriver->TractorDriverDeliveryActive();

    return view('content.DebtWithSupplier.index', compact('debts', 'categories', 'suppliers', 'dateToday'));
  }

  public function indexPaid(SearchDebtRequest $request)
  {
    $date = now();
    $dateToday = $date->format('Y-m-d');

    $debts = $this->debtSearch->paginate($request->validated('name'), $request->validated('phone'), unpaid: false, forSupplier: true);
    $categories = $this->category->all();
    $suppliers = $this->tractorDriver->TractorDriverDeliveryActive();

    return view('content.DebtWithSupplier.indexPaid', compact('debts', 'categories', 'suppliers', 'dateToday'));
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
  public function store(StoreDebtSupplierRequest $request): RedirectResponse
  {
    try {
      DB::beginTransaction();

      $dataDebt = array_replace([
        'user_id'       => Auth::user()->id,
        'tractor_driver_id'   => $request->tractor_driver_id,
        'fullname'      => $request->fullname,
        'phone'         => $request->phone,
        'date_debut_debt' => $request->input('date_debut_debt'),
        'note'      => $request->note,
        'status'    => config('constant.DEBTS_STATUS.UNPAID'),
      ]);

      $this->debtService->createWithProducts(
        $dataDebt,
        $request->input('name_product'),
        $request->input('quantity'),
        $request->input('amount_due'),
        $request->input('date_debt'),
        $request->input('subcategory_ids')
      );

      toastr()->success(__('Debt added successfully'));

      DB::commit();
      return redirect()->back()->withSuccess(__('Debt added successfully'));
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
    $debt = $this->debt->find($id);
    $debt->loadMissing(['getDebtProduct.getSubcategory', 'tractorDriver']);

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
    $debt->loadMissing('getDebtProduct');
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
  public function update(UpdateDebtSupplierRequest $request, $id)
  {
    try {
      DB::beginTransaction();

      $dataDebt = array_replace([
        'fullname'      => $request->fullname,
        'phone'         => $request->phone,
        'date_debut_debt' => $request->input('date_debut_debt'),
        'note'      => $request->note,
        'status'    => config('constant.DEBTS_STATUS.UNPAID'),
      ]);

      $this->debtService->updateWithProducts(
        $id,
        $dataDebt,
        $request->input('name_product'),
        $request->input('quantity'),
        $request->input('amount_due'),
        $request->input('date_debt'),
        $request->input('subcategory_ids'),
        $request->input('id')
      );

      toastr()->success(__('Debt updated successfully'));

      DB::commit();
      return redirect()->route('debt-supplier.index')->withSuccess(__('Debt updated successfully'));
    } catch (\Exception $e) {
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
      return redirect()->route('debt-supplier.index');
    } catch (\Exception $e) {
      DB::rollBack();
      toastr()->error($e->getMessage());
      return redirect()->back();
    }
  }

  public function payDebt(Request $request, $id)
  {
    try {
      DB::beginTransaction();
      $debt = $this->debt->find($id);

      $DebtPaid = $request->debt_paid;
      $DatePaid = $request->date_payment;
      $idsDebtProsucts = $request->id_debt_product;
      if (!is_null($idsDebtProsucts)) {
        foreach ($idsDebtProsucts as $idDebtProduct) {
          $data = array_replace(['status' => 1]);
          $debtProduct = $this->debtProduct->update($idDebtProduct, $data);
        }
      }

      $dataDebt = $this->paymentCalculator->calculate(
        $debt->total_debt_amount,
        $debt->debt_paid,
        $debt->rest_debt_amount,
        $DebtPaid
      );

      if ($dataDebt === false) {
        toastr()->error(__('The amount paid exceeds the amount owed.'));
        return redirect()->route('debt.index');
      }

      $this->debt->update($id, $dataDebt);

      $debtHistoryData = array_replace([
        'debt_id' => $id,
        'amount'  => $DebtPaid,
        'date'    => $DatePaid,
      ]);

      $this->debtHistory->create($debtHistoryData);


      toastr()->success(__('Debt paid successfully'));
      DB::commit();
      return redirect()->back();
    } catch (\Exception $e) {
      DB::rollBack();
      toastr()->error($e->getMessage());
      return redirect()->back();
    }
    // $debts = $this->debt->paginate(10);

    // return view('content.debt.pay', compact('debts'));
  }
}
