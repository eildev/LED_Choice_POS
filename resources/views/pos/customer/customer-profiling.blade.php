@extends('master')
@section('title')
    | {{ $data->name }}
@endsection
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                @if ($isCustomer)
                    Customer
                @else
                    Supplier
                @endif Profile
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card border-0 shadow-none">
                <div class="card-body ">
                    <div class="container-fluid d-flex justify-content-between">
                        <div class="col-lg-3 ps-0">
                            <h5 class="mt-2">{{ $branch->name ?? '' }}</h5>
                            <hr>
                            <p class="show_branch_address">{{ $branch->address ?? '' }}</p>
                            <p class="show_branch_email">{{ $branch->email ?? '' }}</p>
                            <p class="show_branch_phone">{{ $branch->phone ?? '' }}</p>
                        </div>
                        <div>
                            <button type="button"
                                class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0 print-btn">
                                <i class="btn-icon-prepend" data-feather="printer"></i>
                                Print
                            </button>
                        </div>

                    </div>

                </div>
                <div class="card-body show_ledger">
                    <div class="container-fluid w-100">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td>Account Of</td>
                                                <td>{{ $data->name ?? '' }}</td>
                                                <td>Address</td>
                                                <td>{{ $data->address ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Contact No.</td>
                                                <td>{{ $data->phone ?? '' }}</td>
                                                <td>Total Receivable</td>
                                                <td>{{ $data->total_receivable ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Receivable</td>
                                                <td>{{ $data->total_receivable ?? '' }}</td>
                                                <td>Email</td>
                                                <td>{{ $data->email ?? '' }}</td>

                                            </tr>
                                            <tr>
                                                <td>Due</td>
                                                <td>{{ $data->wallet_balance ?? '' }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="my-3 text-center">
                        @if ($isCustomer)
                            Customer
                        @else
                            Supplier
                        @endif Ledger
                    </h4>
                    <div class="container-fluid w-100">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Invoice</th>
                                                <th>Particulars</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Balance</th>
                                                <th class="id">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalDebit = 0;
                                                $totalCredit = 0;
                                                $totalBalance = 0;
                                            @endphp

                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->date ?? '' }}</td>
                                                    <td>
                                                        @php
                                                            $particularData = $transaction->particularData();
                                                        @endphp

                                                        @if ($particularData)
                                                            @if ($isCustomer)
                                                                @if ($particularData->invoice_number)
                                                                    <a
                                                                        href="{{ route('sale.invoice', $particularData->id) }}">
                                                                        #{{ $particularData->invoice_number ?? 0 }}
                                                                    </a>
                                                                @else
                                                                    <a
                                                                        href="{{ route('return.products.invoice', $particularData->id) }}">
                                                                        #{{ $particularData->return_invoice_number ?? 0 }}
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <a
                                                                    href="{{ route('purchase.invoice', $particularData->id) }}">
                                                                    #{{ $particularData->invoice ?? 0 }}
                                                                </a>
                                                            @endif
                                                        @else
                                                            <a
                                                                href="{{ route('transaction.invoice.receipt', $transaction->id) }}">
                                                                #{{ rand(000000, 999999) }}
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $transaction->particulars ?? 0 }}</td>
                                                    <td>{{ $transaction->debit ?? 0 }}</td>
                                                    <td>{{ $transaction->credit ?? 0 }}</td>
                                                    <td>{{ $transaction->balance ?? 0 }}</td>
                                                    <td class="id">
                                                        <a href="#" class="btn-sm btn-primary"><i
                                                                class="fa-solid fa-print"></i></a>
                                                    </td>
                                                </tr>
                                                @php
                                                    $totalDebit += $transaction->debit ?? 0;
                                                    $totalCredit += $transaction->credit ?? 0;
                                                    $totalBalance += $transaction->balance ?? 0;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>Total</td>
                                                <td>৳ {{ number_format($totalDebit, 2) }}</td>
                                                <td>৳ {{ number_format($totalCredit, 2) }}</td>
                                                <td>৳ {{ number_format($totalBalance, 2) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .page-content {
                margin-top: 0 !important;
                padding-top: 0 !important;
                width: 100% !important;
            }

            button,
            a,
            .filter_box,
            nav,
            .footer,
            .id,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_info {
                display: none !important;
            }

            table {
                padding-right: 50px !important;
            }
        }
    </style>

    <script>
        // print
        document.querySelector('.print-btn').addEventListener('click', function(e) {
            e.preventDefault();
            $('#dataTableExample').removeAttr('id');
            $('.table-responsive').removeAttr('class');
            // Trigger the print function
            window.print();
        });
    </script>
@endsection
