h
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>

<body dir="rtl">
    <table id="datatable-debt" class="table table-hover is-stripedt">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Debts') }}</th>
                <th>{{ __('Create At') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>

        </thead>
        <tbody>
            @foreach ($debts as $debt)
                <tr data-row-id="{{ $debt->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $debt->fullname }}</td>
                    <td>{{ $debt->phone }}</td>
                    <td>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                @foreach ($debt->getDebtProduct as $item)
                                    <tr>
                                        <td>{{ $item->name_category }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->amount, 2) }} {{ __('DZ') }}</td>
                                @endforeach
                                <tr>
                                    <td colspan="2">{{ __('Total') }}</td>
                                    <td>{{ number_format($debt->total_debt_amount, 2) }} {{ __('DZ') }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                    <td>{{ $debt->date_debut_debt }}</td>
                    <td>
                        @if ($debt->status == 'paid')
                            <span class="badge bg-label-success me-1">{{ __('Paid') }}</span>
                        @else
                            <span class="badge bg-label-warning me-1">{{ __('Unpaid') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
