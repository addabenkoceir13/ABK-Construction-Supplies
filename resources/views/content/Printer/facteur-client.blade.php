<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>{{ __('Print Invoice') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link rel="stylesheet" href="{{ asset('print/assets/bootstrap.min.css') }}">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('print/assets/style.css') }}">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">


<body dir="rtl">
    <!-- Invoice 3 start -->
    <div class="invoice-3 invoice-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="invoice-inner">
                        <div class="invoice-info" id="invoice_wrapper">
                            <div class="invoice-headar">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="invoice-name">
                                            <!-- logo started -->
                                            <div class="logo">
                                                <img src="{{ asset('assets/img/logos/logo-v2.jpg') }}" style="">
                                            </div>
                                            <!-- logo ended -->
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="invoice">
                                            <h1 class="text-end inv-header-1 mb-0">{{ __('Invoice No:') }}
                                                {{ str_pad($debt->id, 4, '0', STR_PAD_LEFT) . '/' . $debt->created_at->format('Y') }}
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number">
                                            <h4 class="inv-title-1">{{ __('Invoice To') }}</h4>
                                            <p class="invo-addr-1 mb-0">
                                                {{ $debt->fullname }}
                                            </p>
                                            <p class="invo-addr-1 mb-0">{{ $debt->phone }}</p>


                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-30">
                                        <div class="invoice-number text-end">
                                            <h4 class="inv-title-1">{{ __('Bill To') }}</h4>
                                            <p class="invo-addr-1 mb-0">
                                                مؤسسة عدة بن قصير لمستلزمات البناء
                                            </p>
                                            <p class="invo-addr-1 mb-0">06 61785937</p>
                                            <p class="invo-addr-1 mb-0">07 70932767</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 mb-30">
                                        <h4 class="inv-title-1">{{ __('Date') }}</h4>
                                        <p class="invo-addr-1 mb-0">{{ __('Due Date:') }} {{ $debt->date_debut_debt }}
                                        </p>
                                    </div>
                                    {{-- <div class="col-sm-6 text-end mb-30">
                                        <h4 class="inv-title-1">Payment Method</h4>
                                        <p class="inv-from-1 mb-0">Credit Card</p>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="invoice-center">
                                <div class="order-summary">
                                    <h4>{{ __('Order summary') }}</h4>
                                    <div class="table-outer">
                                        <table class="default-table invoice-table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Product details') }}</th>
                                                    <th>{{ __('Qty') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($debt->getDebtProduct as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->name_category }} </td>
                                                        <td>{{ $item->quantity }}
                                                            {{ $item->getSubcategory->display_name }}</td>
                                                        <td>{{ number_format($item->amount, 2) }} {{ __('DZ') }}
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <tr>
                                                    <td colspan="3"><strong>{{ __('Net total:') }}</strong></td>
                                                    <td><strong>{{ number_format($debt->total_debt_amount, 2) }}
                                                            {{ __('DZ') }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="invoice-btn-section clearfix d-print-none">
                            <a href="javascript:window.print()" class="btn btn-lg btn-print">
                                <i class="fa fa-print"></i> {{ __('Print Invoice') }}
                            </a>
                            {{-- <a id="invoice_download_btn" class="btn btn-lg btn-download btn-theme">
                                <i class="fa fa-download"></i> {{ __('Download Invoice') }}
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Invoice 3 end -->

    {{-- <script src="{{ asset('print/assets/js/app.js') }}"></script> --}}
    <script src="{{ asset('print/assets/js/html2canvas.js') }}"></script>
    <script src="{{ asset('print/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('print/assets/js/jspdf.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '#invoice_download_btn', function () {
            console.log('====================================');
            console.log('download');
            console.log('====================================');
                var contentWidth = $("#invoice_wrapper").width();
                var contentHeight = $("#invoice_wrapper").height();
                var topLeftMargin = 20;
                var pdfWidth = contentWidth + (topLeftMargin * 2);
                var pdfHeight = (pdfWidth * 1.5) + (topLeftMargin * 2);
                var canvasImageWidth = contentWidth;
                var canvasImageHeight = contentHeight;
                var totalPDFPages = Math.ceil(contentHeight / pdfHeight) - 1;

                html2canvas($("#invoice_wrapper")[0], {allowTaint: true}).then(function (canvas) {
                    canvas.getContext('2d');
                    var imgData = canvas.toDataURL("image/jpeg", 1.0);
                    var pdf = new jsPDF('p', 'pt', [pdfWidth, pdfHeight]);
                    pdf.addImage(imgData, 'JPG', topLeftMargin, topLeftMargin, canvasImageWidth, canvasImageHeight);
                    for (var i = 1; i <= totalPDFPages; i++) {
                        pdf.addPage(pdfWidth, pdfHeight);
                        pdf.addImage(imgData, 'JPG', topLeftMargin, -(pdfHeight * i) + (topLeftMargin * 4), canvasImageWidth, canvasImageHeight);
                    }
                    pdf.save("sample-invoice.pdf");
                });
            });
        });
    </script>

</body>

</html>
