@extends('master')
@section('title', '| Return')
@section('admin')
    <div class="row mt-0">
        <div class="col-lg-4 grid-margin stretch-card mb-3">
            <div class="card">
                <div class="card-body px-4 py-2">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="card-title">Basic Details</h6>
                            <div class="row">
                                <div class="col-sm-6 d-flex justify-content-between align-items-center">
                                    <label for="exampleInputUsername2" class="col-form-label">Order Id </label>
                                    <span>:</span>
                                </div>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                        </b>{{ $sale->invoice_number ?? 00 }}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 d-flex justify-content-between align-items-center">
                                    <label for="exampleInputUsername2" class="col-form-label">Customer Name</label>
                                    <span>:</span>
                                </div>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                        </b>{{ $sale->customer->name ?? '' }}</label>
                                </div>
                            </div>
                            <div class="row">
                                <label for="exampleInputMobile" class="col-sm-6 col-form-label">Product Price :</label>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                        </b>{{ number_format($sale->total, 2) ?? 0 }}</label>
                                </div>
                            </div>
                            <div class="row">
                                <label for="exampleInputUsername2" class="col-sm-6 col-form-label">Discount :</label>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                            {{ number_format($sale->actual_discount, 2) ?? 0 }}</b></label>
                                </div>
                            </div>
                            <div class="row">
                                <label for="exampleInputEmail2" class="col-sm-6 col-form-label">Previous Due :</label>
                                <div class="col-sm-6 text-end">
                                    @php
                                        $total = $sale->total - $sale->actual_discount;
                                        $previousDue = $sale->receivable - $total;
                                    @endphp
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                            {{ number_format($previousDue, 2) ?? 0 }}</b></label>
                                </div>
                            </div>
                            <div class="row">
                                <label for="exampleInputEmail2" class="col-sm-6 col-form-label">Total Receivable :</label>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                            {{ number_format($sale->receivable, 2) ?? 0 }}</b></label>
                                </div>
                            </div>
                            <div class="row">
                                <label for="exampleInputMobile" class="col-sm-6 col-form-label">Total Paid :</label>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                            {{ number_format($sale->paid, 2) ?? 0 }}</b></label>
                                </div>
                            </div>
                            <div class="row">
                                <label for="exampleInputMobile" class="col-sm-6 col-form-label">Due :</label>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                            {{ $sale->due > 0 ? $sale->due : 0 }}</b></label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="exampleInputMobile" class="col-sm-6 col-form-label">Returned Product
                                    Value :</label>
                                <div class="col-sm-6 text-end">
                                    <label for="exampleInputUsername2" class="col-form-label"><b>
                                            00</b></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body px-4 py-2">
                    <div class="mb-3">
                        <h6 class="card-title">Items</h6>
                    </div>

                    <div id="" class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Discount</th>
                                    <th>Sub Total</th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($saleItems->count() > 0)
                                    @foreach ($saleItems as $saleItem)
                                        <tr data-id="{{ $saleItem->id }}">
                                            <td>{{ $saleItem->product['name'] }}</td>
                                            <td>{{ $saleItem->rate }}</td>
                                            <td>{{ $saleItem->qty }}</td>
                                            <td>{{ $saleItem->discount }}</td>
                                            <td>{{ $saleItem->sub_total }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-danger no_return_btn">
                                                    No Return
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6">No Product to Return</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end">
                                        Total
                                        <br>
                                        Discount
                                        <br>
                                        Previous Due
                                        <br>
                                        Grand Total
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    <td colspan="1" class="text-end">
                                        <span class="productTotal"> {{ number_format($sale->total, 2) ?? 0 }}</span> <br>
                                        <span class="discount">{{ number_format($sale->actual_discount, 2) ?? 0 }}</span>
                                        <br>
                                        <span class="previousDue"></span>{{ number_format($previousDue, 2) ?? 0 }} <br>
                                        <span
                                            class="grandTotal">{{ number_format($sale->final_receivable, 2) ?? 0 }}</span>
                                        <br>
                                        <br>
                                        <button class="btn btn-primary return_product">
                                            Return Products
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hidden {
            display: none !important;
        }

        #printFrame {
            display: none;
        }
    </style>
    <iframe id="printFrame" src="" width="0" height="0"></iframe>

    <script>
        $(document).ready(function() {

            function updateTotals() {
                let total = 0;
                let discount = parseFloat($('.discount').text()) || 0;
                let previousDue = parseFloat("{{ $previousDue, 2 ?? 0 }}");
                // console.log(previousDue);

                $('tbody tr').each(function() {
                    if (!$(this).hasClass('hidden')) {
                        let subTotal = parseFloat($(this).find('td:nth-child(5)').text()) || 0;
                        total += subTotal;
                    }
                });

                let finalReceivable = (total - discount) + previousDue;

                $('.productTotal').text(total.toFixed(2));
                $('.grandTotal').text(finalReceivable.toFixed(2));
            }

            $(document).on('click', '.no_return_btn', function(e) {
                e.preventDefault();
                $(this).closest('tr').addClass('hidden');
                updateTotals();
            });


            $(document).on('click', '.return_product', function(e) {
                e.preventDefault();

                let productTotal = parseFloat($('.productTotal').text()) || 0;
                let discount = parseFloat($('.discount').text()) || 0;
                let previousDue = parseFloat("{{ $previousDue, 2 ?? 0 }}") || 0;
                let grandTotal = parseFloat($('.grandTotal').text()) || 0;
                let customerId = '{{ $customer->id }}'
                let saleId = '{{ $sale->id }}'

                let returnedProducts = [];

                $('tbody tr').each(function() {
                    if (!$(this).hasClass('hidden')) {
                        let product = {
                            id: $(this).attr('data-id'),
                            name: $(this).find('td:nth-child(1)').text(),
                            price: parseFloat($(this).find('td:nth-child(2)').text()) || 0,
                            qty: parseFloat($(this).find('td:nth-child(3)').text()) || 0,
                            discount: parseFloat($(this).find('td:nth-child(4)').text()) || 0,
                            sub_total: parseFloat($(this).find('td:nth-child(5)').text()) || 0,
                        };
                        returnedProducts.push(product);
                    }
                });

                console.log(returnedProducts);
                const allData = {
                    productTotal,
                    discount,
                    previousDue,
                    grandTotal,
                    customerId,
                    saleId,
                    returnedProducts
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/return/store',
                    type: 'POST',
                    data: allData,
                    success: function(response) {
                        if (response.status == 200) {
                            alert('Products returned successfull!');
                        } else {
                            alert('Products returned unsuccessfull');
                        }

                    }
                });
            });
        })
    </script>


@endsection
