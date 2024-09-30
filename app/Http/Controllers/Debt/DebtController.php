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

class DebtController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = now();
        $dateToday = $date->format('Y-m-d');

        $debts = $this->debt->paginate(10);
        $categories = $this->category->all();
        $supplier = $this->supplier->SelectSupplier();

        return view('content.debt.index', compact('debts', 'categories' , 'supplier', 'dateToday'));
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
                $quantity  = $quantities[$index];
                $amount    = $amounts[$index];
                $dateDebt  = $dateDebts[$index];
                $total    += $amount;

                $dataDebtProduct = array_replace( [
                    'debt_id'   => $debt->id,
                    'name'      => $products[$index],
                    'quantity'  => $quantity,
                    'amount'    => $amount,
                    'date_debt' => $dateDebt,
                ]);

                $this->debtProduct->create($dataDebtProduct);
            }

            $dataDebtTotal = array_replace( [
                'total_debt_amount' => $total,
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

        return view('content.Debt.view', compact('debt'));
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

        return view('content.Debt.edit', compact('debt', 'categories', 'dateToday'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
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

            foreach ($products as $index => $product) {
                // Process each product, quantity, and amount
                $quantity  = $quantities[$index];
                $amount    = $amounts[$index];
                $dateDebt  = $dateDebts[$index];
                $idOld     = $Ids[$index];
                $total    += $amount;

                $dataDebtProduct = array_replace( [
                    'name'      => $products[$index],
                    'quantity'  => $quantity,
                    'amount'    => $amount,
                    'date_debt' => $dateDebt,
                ]);

                if ($idOld == 0) {
                    $dataDebtProduct = array_replace( [
                        'debt_id'   => $id,
                        'name'      => $products[$index],
                        'quantity'  => $quantity,
                        'amount'    => $amount,
                        'date_debt' => $dateDebt,
                    ]);

                    $this->debtProduct->create(data: $dataDebtProduct);
                }
                else {
                    $this->debtProduct->Update($id,$dataDebtProduct);
                }

            }
            $dataDebtTotal = array_replace( [
                'total_debt_amount' => $total,
            ]);

            $this->debt->update($id, $dataDebtTotal);

            toastr()->success(__('Debt updated successfully'));

            DB::commit();
            return redirect()->route('debt.index')->withSuccess(__('Debt updated successfully'));
        }
        catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            // toastr()->error($e->getMessage());
            // return redirect()->back();
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
        dd($id);
    }
}
