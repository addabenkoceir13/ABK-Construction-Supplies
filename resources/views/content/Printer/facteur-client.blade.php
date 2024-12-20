<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('assets/css/css-invoice.css') }}">
  <title>{{ __('Debts') }}</title>
  <style>
    /* Styles for Print */
    @media print {
      .no-print {
        display: none;
      }
      .print-area {
        font-family: Arial, sans-serif;
        color: #333;
      }
      .print-area h4, .print-area h5 {
        color: #000;
      }
      .table thead th {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
      }
      .table tbody td {
        border: 1px solid #dee2e6;
      }
    }
  </style>
</head>
<body dir="rtl">

  <page size="A4" id="content-to-print" style="background: white;">
    <div class="py-4">
      <div class="px-14 py-6">
        <table class="w-full border-collapse border-spacing-0">
          <tbody>
            <tr>
              <td class="w-full align-top">
                <div>
                  <img src="{{ asset('assets/img/logos/logo-v2.jpg') }}" style="border-radius: 50%;" width="8%"  />
                </div>
              </td>

              <td class="align-top">
                <div class="text-sm">
                  <table class="border-collapse border-spacing-0">
                    <tbody>
                      <tr>
                        <td class="border-r pr-4">
                          <div>
                            <p class="whitespace-nowrap text-slate-400 text-right">{{ __('Date') }}</p>
                            <p class="whitespace-nowrap font-bold text-main text-right">{{ now()->format('Y-m-d') }}</p>
                          </div>
                        </td>
                        <td class="pl-4">
                          <div>
                            <p class="whitespace-nowrap text-slate-400 text-right">{{ __('Invoice') }} #</p>
                            <p class="whitespace-nowrap font-bold text-main text-right"><p> {{ str_pad($debt->id, 4, '0', STR_PAD_LEFT) .'/'. $debt->created_at->format('Y') }}</p></p>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-slate-100 px-14 py-6 text-sm">
        <table class="w-full border-collapse border-spacing-0">
          <tbody>
            <tr>
              <td class="w-1/2 align-top">
                <div class="text-sm text-neutral-600">
                  <h3 class="whitespace-nowrap font-bold">{{ __('Client Information') }}</h3>
                  <p><strong>{{ __('Client name') }}:</strong> {{ $debt->fullname }}</p>
                  <p><strong>{{ __('Client phone') }}:</strong> {{ $debt->phone }}</p>
                </div>
              </td>
              <td class="w-1/2 align-top text-right">
                <div class="text-sm text-neutral-600">
                  <h3 class="whitespace-nowrap font-bold">{{ __('Company Information') }}:</h3>
                  <p><strong>{{ __('Company Name') }}:</strong> {{ config('app.locale') == 'en' ?  config('variables.templateName') :  config('variables.templateNameAr') }}</p>
                  <p><strong>{{ __('Company phone') }}:</strong> <span>0661785937</span></p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="px-14 py-10 text-sm text-neutral-700">
        <table class="w-full border-collapse border-spacing-0">
          <thead>
            <tr>
              <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">#</td>
              <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">{{ __('Product details') }}</td>
              <td class="border-b-2 border-main pb-3 pl-2 text-main font-bold text-main">{{ __('Qty') }}</td>
              <td class="border-b-2 border-main pb-3 pl-2 text-main font-bold text-main">{{ __('Price') }}</td>
            </tr>
          </thead>
          <tbody>
            @foreach ($debt->getDebtProduct as $item )
              <tr>
                <td class="border-b py-3 pl-3">{{  $loop->iteration }}</td>
                <td class="border-b py-3 pl-2">{{ $item->name_category }}</td>
                <td class="border-b py-3 pl-2 text-right">{{ $item->quantity }} {{ $item->getSubcategory->display_name }}</td>
                <td class="border-b py-3 pl-2 text-right">{{ $item->amount }} {{ __('DZ') }}</td>
              </tr>
            @endforeach

            <tr>
              <td colspan="4">
                <table class="w-full border-collapse border-spacing-0">
                  <tbody>
                    <tr>
                      <td class="w-full"></td>
                      <td>
                        <table class="w-full border-collapse border-spacing-0">
                          <tbody>
                            <tr>
                              <td class="border-b p-3">
                                <div class="whitespace-nowrap text-slate-400">{{ __('Net total:') }}</div>
                              </td>
                              <td class="border-b p-3 text-right">
                                <div class="whitespace-nowrap font-bold text-main">{{ number_format($debt->total_debt_amount, 2) }} {{ __('DZ') }}</div>
                              </td>
                            </tr>
                            {{-- <tr>
                              <td class="p-3">
                                <div class="whitespace-nowrap text-slate-400">VAT total:</div>
                              </td>
                              <td class="p-3 text-right">
                                <div class="whitespace-nowrap font-bold text-main">$64.00</div>
                              </td>
                            </tr> --}}
                            <tr>
                              <td class="bg-main p-3">
                                <div class="whitespace-nowrap font-bold text-white">{{ __('Total:') }}</div>
                              </td>
                              <td class="bg-main p-3 text-right">
                                <div class="whitespace-nowrap font-bold text-white">{{ number_format($debt->total_debt_amount, 2) }} {{ __('DZ') }}</div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </page>

    <!-- Print Button -->
    <div class="no-print text-center mt-4">
      <button id="print-button" class="btn btn-primary">{{ __('Print Invoice') }}</button>
    </div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
      $(document).ready(function() {
    $('#print-button').on('click', function() {
        // Select the content you want to print
        var printContents = document.getElementById("content-to-print").innerHTML;

        // Open a new window and write the content into it
        var printWindow = window.open("", "_blank", "width=800,height=600");
        printWindow.document.open();
        printWindow.document.write(`
            <html>
                <head>
                    <title>Invoice</title>
                    <style>
                        /* Add your print-specific CSS here */
                        body { font-family: Arial, sans-serif; direction: rtl; }
                        /* Additional styling for print version */
                    </style>
                    <link rel="stylesheet" href="{{ asset('assets/css/css-invoice.css') }}">
                </head>
                <body onload="window.print(); window.close();">
                    ${printContents}
                </body>
            </html>
        `);
        printWindow.document.close();
    });
});

  </script>
</body>
</html>


