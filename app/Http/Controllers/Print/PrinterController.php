<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Repositories\Debt\DebtRepository;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    private $debt;

    public function __construct(DebtRepository $debt)
    {
        $this->debt = $debt;
    }
    public function factuerClient($id,$fullname)
    {
        $debt = $this->debt->find($id);
        
        return view('content.Printer.facteur-client', compact('debt'));
    }
}
