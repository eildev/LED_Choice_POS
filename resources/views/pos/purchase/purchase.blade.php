@extends('master')
@section('title', '| Add Purchase')
@section('admin')

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase</li>
        </ol>
    </nav>
    <form id="purchaseForm" class="row" enctype="multipart/form-data">
        {{-- form  --}}
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title">Create Purchase</h6>
                            {{-- <button class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModalLongScollable"><i class="fa-solid fa-plus"></i> Add
                                Supplier
                            </button> --}}
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="ageSelect" class="form-label">Supplier</label>
                                <select class="js-example-basic-single form-select select-supplier supplier_id"
                                    data-width="100%" onclick="errorRemove(this);" name="supplier_id">
                                    <option value="">Select Supplier</option>
                                </select>
                                <span class="text-danger supplier_id_error"></span>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Purchase Date</label>
                                {{-- <div class="input-group flatpickr" id="flatpickr-date">
                                <input type="date" class="form-control purchase_date" placeholder="" data-input
                                    onkeyup="errorRemove(this);" onblur="errorRemove(this);" max="<?php echo date('Y-m-d'); ?>">
                                <span class="input-group-text input-group-addon" data-toggle><i
                                        data-feather="calendar"></i></span>
                            </div> --}}
                                <div class="input-group flatpickr me-2 mb-2 mb-md-0" id="dashboardDate">
                                    <span class="input-group-text input-group-addon bg-transparent border-primary"
                                        data-toggle><i data-feather="calendar" class="text-primary"></i></span>
                                    <input type="text" name="date"
                                        class="form-control bg-transparent border-primary purchase_date"
                                        placeholder="Select date" data-input>
                                </div>
                                <span class="text-danger purchase_date_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                @php
                                    $category = App\Models\Category::where('slug', 'via-sell')->first();
                                    $products = collect();
                                    if ($category) {
                                        $products = App\Models\Product::where('category_id', '!=', $category->id)
                                            ->orderBy('stock', 'asc')
                                            ->get();
                                    } else {
                                        $products = App\Models\Product::orderBy('stock', 'asc')->get();
                                    }
                                @endphp
                                <label for="ageSelect" class="form-label">Product</label>
                                <select class="js-example-basic-single form-select product_select" data-width="100%"
                                    onclick="errorRemove(this);">
                                    @if ($products->count() > 0)
                                        <option selected disabled>Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock }}
                                                {{ $product->unit->name }})</option>
                                        @endforeach
                                    @else
                                        <option selected disabled>
                                            Please Add Product</option>
                                    @endif
                                </select>
                                <span class="text-danger product_select_error"></span>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="formFile">Invoice File/Picture upload</label>
                                <input class="form-control document_file" name="document" type="file" id="formFile"
                                    onclick="errorRemove(this);" onblur="errorRemove(this);">
                                <span class="text-danger document_file_error"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="formFile">Invoice Number</label>
                                <input class="form-control" name="invoice" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- table  --}}
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="card-title">Purchase Table</h6>
                        </div>

                        <div id="" class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#SL</th>
                                        <th>Product</th>
                                        <th>Rate</th>
                                        <th>Qty</th>
                                        <th>Sub Total</th>
                                        <th>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="showData">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    Total :
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control total border-0 "
                                                        name="total" readonly value="0.00" />
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    Discount :
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control discount_amount"
                                                        name="discount_amount" value="0.00" />
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    Carrying Cost :
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control carrying_cost"
                                                        name="carrying_cost" value="0.00" />
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    Sub Total :
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control grand_total border-0 "
                                                        name="sub_total" readonly value="0.00" />
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="my-3">
                            <button class="btn btn-primary payment_btn" data-bs-toggle="modal"
                                data-bs-target="#paymentModal"><i class="fa-solid fa-money-check-dollar"></i>
                                Payment</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModalLongScollable" tabindex="-1"
            aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Add Supplier Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- <form id="signupForm" class="supplierForm row"> --}}
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Supplier Name <span
                                        class="text-danger">*</span></label>
                                <input id="defaultconfig" class="form-control supplier_name" maxlength="255"
                                    name="name" type="text" onkeyup="errorRemove(this);"
                                    onblur="errorRemove(this);">
                                <span class="text-danger supplier_name_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Email</label>
                                <input id="defaultconfig" class="form-control email" maxlength="39" name="email"
                                    type="email">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Phone Nnumber <span
                                        class="text-danger">*</span></label>
                                <input id="defaultconfig" class="form-control phone" maxlength="39" name="phone"
                                    type="tel" onkeyup="errorRemove(this);" onblur="errorRemove(this);">
                                <span class="text-danger phone_error"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Address</label>
                                <input id="defaultconfig" class="form-control address" maxlength="39" name="address"
                                    type="text">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Opening Receivable</label>
                                <input id="defaultconfig" class="form-control opening_receivable" maxlength="39"
                                    name="opening_receivable" type="number">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Opening Payable</label>
                                <input id="defaultconfig" class="form-control opening_payable" maxlength="39"
                                    name="opening_payable" type="number">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary save_supplier">Save</button>
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>


        {{-- payement modal  --}}
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="exampleModalScrollableTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalScrollableTitle">Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="" class="table-responsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Paying Items :</th>
                                        <th>
                                            <span class="paying_items">0</span>
                                        </th>
                                        <th>Sub Total :</th>
                                        <th>
                                            <input type="number" name="subTotal" class="subTotal form-control border-0 "
                                                readonly value="00">
                                        </th>
                                    </tr>
                                    <tr>
                                        {{-- <th>Total Payable :</th> --}}
                                        <th>Previous Due:</th>
                                        <th>
                                            (<span class="previous_due">00</span>TK)
                                        </th>
                                        <th>Grand Total:</th>
                                        <th>
                                            <input type="number" name="grand_total"
                                                class="grandTotal form-control border-0 " readonly value="00">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="due_text">Due:</th>
                                        <th>
                                            (<span class="final_due">00</span>TK)
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        {{-- <form id="signupForm" class="supplierForm row"> --}}
                        <div class="supplierForm row">


                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Transaction Method <span
                                        class="text-danger">*</span></label>
                                @php
                                    $payments = App\Models\Bank::get();
                                @endphp
                                <select class="form-select payment_method" data-width="100%" onclick="errorRemove(this);"
                                    onblur="errorRemove(this);" name="payment_method">
                                    @if ($payments->count() > 0)
                                        @foreach ($payments as $payemnt)
                                            <option value="{{ $payemnt->id }}">
                                                {{ $payemnt->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Transaction</option>
                                    @endif
                                </select>
                                <span class="text-danger payment_method_error"></span>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Tax</label>
                                @php
                                    $taxs = App\Models\Tax::get();
                                @endphp
                                <select class="form-select tax" data-width="100%" onclick="errorRemove(this);"
                                    onblur="errorRemove(this);" value="" name="tax">
                                    @if ($taxs->count() > 0)
                                        <option selected disabled>Select Taxes</option>
                                        @foreach ($taxs as $taxs)
                                            <option value="{{ $taxs->percentage }}">
                                                {{ $taxs->percentage }} %
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected disabled>Please Add Tax</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3 col-12">
                                <label for="name" class="form-label">Pay Amount <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <input class="form-control total_payable border-end-0 rounded-0" name="total_payable"
                                        type="number">
                                    <button class="btn btn-info border-start-0 rounded-0 paid_btn">Paid</button>
                                </div>
                                <span class="text-danger total_payable_error"></span>
                            </div>
                            <div class="mb-3 col-md-12">
                                {{-- <label for="name" class="form-label">Note</label> --}}
                                <input name="note" class="form-control note" id=""
                                    placeholder="Enter Note (Optional)" rows="3"></input>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-cart-shopping"></i>
                                Purchase</button>
                        </div>

                    </div>
                </div>
            </div>
    </form>




    <script>
        // error remove
        function errorRemove(element) {
            if (element.value != '') {
                $(element).siblings('span').hide();
                $(element).css('border-color', 'green');
            }
        }
        $(document).ready(function() {

            // show error
            function showError(name, message) {
                $(name).css('border-color', 'red');
                $(name).focus();
                $(`${name}_error`).show().text(message);
            }


            // show supplier
            function supplierView() {
                // console.log('hello')
                $.ajax({
                    url: '/supplier/view',
                    method: 'GET',
                    success: function(res) {
                        const suppliers = res.data;
                        // console.log(suppliers);
                        $('.select-supplier').empty();
                        if (suppliers.length > 0) {
                            $('.select-supplier').html(
                                `<option selected disabled>Select a Supplier</option>`);
                            $.each(suppliers, function(index, supplier) {
                                $('.select-supplier').append(
                                    `<option value="${supplier.id}">${supplier.name}</option>`
                                );
                            })
                        } else {
                            $('.select-supplier').html(`
                    <option selected disable>Please add supplier</option>`)
                        }
                    }
                })
            }
            supplierView();

            //Supplier Data find
            function fetchSupplierDetails(supplierId) {
                $.ajax({
                    url: `/supplier/details/${supplierId}`,
                    method: 'GET',
                    success: function(res) {
                        const supplier = res.data;
                        // console.log(supplier)
                        if (supplier.wallet_balance > 0) {
                            $('.previous_due').text(supplier.wallet_balance);
                        } else {
                            $('.previous_due').text(0);
                        }
                    }
                });
            } //
            $(document).ready(function() {
                supplierView();
                $('.select-supplier').on('change', function() {
                    const selectedSupplierId = $(this).val();
                    if (selectedSupplierId) {
                        fetchSupplierDetails(selectedSupplierId);
                    }
                });
            });

            //Previous Due Show Data End
            // save supplier
            const saveSupplier = document.querySelector('.save_supplier');
            saveSupplier.addEventListener('click', function(e) {
                e.preventDefault();
                // alert('ok')
                let formData = new FormData($('.supplierForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/supplier/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {

                        if (res.status == 200) {
                            $('#exampleModalLongScollable').modal('hide');
                            $('.supplierForm')[0].reset();
                            supplierView();
                            toastr.success(res.message);
                        } else {
                            if (res.error.name) {
                                showError('.supplier_name', res.error.name);
                            }
                            if (res.error.phone) {
                                showError('.phone', res.error.phone);
                            }
                        }
                    }
                });
            })
            let totalQuantity = 0;

            // Function to update total quantity
            function updateTotalQuantity() {
                totalQuantity = 0;
                $('.quantity').each(function() {
                    let quantity = parseFloat($(this).val());
                    if (!isNaN(quantity)) {
                        totalQuantity += quantity;
                    }
                });
                // console.log(totalQuantity);
            }
            // Function to update SL numbers
            function updateSLNumbers() {
                $('.showData > tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // select product
            $('.product_select').change(function() {
                let id = $(this).val();
                let supplier = $('.select-supplier').val();
                // alert(id);
                if (supplier) {
                    if ($(`.data_row${id}`).length === 0 && id) {
                        $.ajax({
                            url: '/product/find/' + id,
                            type: 'GET',
                            dataType: 'JSON',
                            success: function(res) {
                                const product = res.data;
                                // console.log(product);
                                // $('.showData').empty();
                                // updateSLNumbers();

                                $('.showData').append(
                                    `<tr class="data_row${product.id}">
                                        <td>

                                        </td>
                                        <td>
                                            <input type="text" class="form-control product_name${product.id} border-0 "  name="product_name[]" readonly value="${product.name ?? ""}" />
                                        </td>
                                        <td>
                                            <input type="hidden" class="product_id" name="product_id[]" readonly value="${product.id ?? 0}" />
                                            <input type="number" class="form-control product_price${product.id} border-0 "  name="unit_price[]" readonly value="${product.cost ?? 0}" />
                                        </td>
                                        <td>
                                            <input type="number" product-id="${product.id}" class="form-control quantity" name="quantity[]" min="1" value="" />

                                            <div class="validation-message text-danger" style="display: none;">Please enter a quantity of at least 1.</div>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control product_subtotal${product.id} border-0 "  name="total_price[]" readonly value="00.00" />
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-danger btn-icon purchase_delete" data-id=${product.id}>
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>`
                                );
                                // Update SL numbers
                                updateSLNumbers();
                            }
                        })
                    }
                } else {
                    toastr.warning('Please Select Supplier');
                }

            })

            // Function to recalculate total
            function calculateTotal() {
                let total = 0;
                $('.quantity').each(function() {
                    let productId = $(this).attr('product-id');
                    let qty = parseFloat($(this).val());
                    let price = parseFloat($('.product_price' + productId).val());
                    total += qty * price;
                });
                $('.total').val(total.toFixed(2));
            }

            // grandTotalCalulate
            function calculateGrandTotal() {
                let discountAmount = $('.discount_amount').val();
                let total = parseFloat($('.total').val());
                $('.grand_total').val(total - discountAmount);
            }
            calculateGrandTotal();


            // Function to update grand total when a product is added or deleted
            function updateGrandTotal() {
                calculateTotal();
                calculateGrandTotal();
                updateTotalQuantity();
            }

            $(document).on('keyup', '.quantity', function() {
                let id = $(this).attr("product-id")
                let quantity = $(this).val();
                quantity = parseInt(quantity);
                let productPrice = $('.product_price' + id).val();
                productPrice = parseFloat(productPrice);
                let subTotal = $('.product_subtotal' + id);
                let subTotalPrice = parseFloat(quantity * productPrice).toFixed(2);
                subTotal.val(subTotalPrice);
                updateGrandTotal();
            })

            // discount
            $('.discount_amount').change(function() {
                calculateGrandTotal();
            })
            $('.discount_amount').keyup(function() {
                calculateGrandTotal();
            })

            // purchase Delete
            $(document).on('click', '.purchase_delete', function(e) {
                // alert('ok');
                let id = $(this).attr('data-id');
                let dataRow = $('.data_row' + id);
                dataRow.remove();
                // Recalculate grand total
                updateGrandTotal();
                updateSLNumbers();
                updateTotalQuantity();
            });

            // payment button click event
            $('.payment_btn').click(function(e) {
                e.preventDefault();
                let cumtomer_due = parseFloat($('.previous_due').text());
                let subtotal = parseFloat($('.grand_total').val());
                $('.subTotal').val(subtotal);
                let grandTotal = cumtomer_due + subtotal;
                $('.grandTotal').val(grandTotal);
                $('.paying_items').text(totalQuantity);
                var isValid = true;
                //Quantity Message
                $('.quantity').each(function() {
                    var quantity = $(this).val();
                    if (!quantity || quantity < 1) {
                        isValid = false;
                        return false;
                    }
                });
                if (!isValid) {
                    event.preventDefault();
                    // alert('Please enter a quantity of at least 1 for all products.');
                    toastr.error('Please enter a quantity of at least 1 .)');
                    $('#paymentModal').modal('hide');
                } else {

                }
                //End Quantity Message
            })

            // paid amount
            $('.paid_btn').click(function(e) {
                e.preventDefault();
                // alert('ok');
                let grandTotal = $('.grandTotal').val();
                $('.total_payable').val(grandTotal);
                totalDue();
            })
            // total_payable
            $('.total_payable').keyup(function(e) {
                // alert('ok');
                totalDue();
            })
            // due
            function totalDue() {
                let pay = parseFloat($('.total_payable').val());
                let grandTotal = parseFloat($('.grandTotal').val());
                let due = (grandTotal - pay).toFixed(2);
                if (due > 0) {
                    $('.final_due').text(due);
                    $('.due_text').text('Due');
                } else {
                    $('.final_due').text(-(due));
                    $('.due_text').text('Return');
                }
            }
            // console.log(totalQuantity);

            $('.tax').change(function() {
                let grandTotal = parseFloat($('.grand_total').val());
                let value = parseFloat($(this).val());
                let previousDue = parseFloat($('.previous_due').text());

                let taxTotal = ((grandTotal * value) / 100);
                taxTotal = (taxTotal + grandTotal);
                let totalAmount = taxTotal + previousDue;
                $('.grandTotal').val(totalAmount);
            })


            $('#purchaseForm').submit(function(event) {
                event.preventDefault();
                let formData = new FormData($('#purchaseForm')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/purchase/store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // console.log(res);
                        if (res.status == 200) {
                            $('#paymentModal').modal('hide');
                            toastr.success(res.message);
                            let id = res.purchaseId;
                            window.location.href = '/purchase/invoice/' + id;

                        } else if (res.status == 400) {
                            toastr.warning(res.message);
                            showError('.payment_method',
                                'please Select Another Payment Method');
                        } else {
                            console.log(res.error);
                            if (res.error.payment_method == null) {
                                $('#paymentModal').modal('hide');
                                if (res.error.supplier_id) {
                                    showError('.supplier_id', res.error.supplier_id);
                                }
                                // if (res.error.products) {
                                //     showError('.product_select', res.error.products);
                                // }
                                if (res.error.purchase_date) {
                                    showError('.purchase_date', res.error.purchase_date);
                                }
                                if (res.error.document) {
                                    showError('.document_file', res.error.document);
                                }
                            } else {
                                if (res.error.payment_method) {
                                    showError('.payment_method', res.error.payment_method);
                                }
                            }
                        }
                    }
                });
            });

            // $('.purchase_btn').click(function(e) {
            //     e.preventDefault();

            //     event.preventDefault();
            //     // alert('ok');
            //     let supplier_id = $('.select-supplier').val();
            //     let purchase_date = $('.purchase_date').val();
            //     let formattedPurchaseDate = moment(purchase_date, 'DD-MMM-YYYY').format(
            //         'YYYY-MM-DD HH:mm:ss');
            //     let total_quantity = totalQuantity;
            //     let total_amount = parseFloat($('.total').val());
            //     let discount = $('.promotion_id').val();
            //     let sub_total = parseFloat($('.grand_total').val());
            //     let tax = $('.tax').val();
            //     let grand_total = parseFloat($('.grandTotal').text());
            //     let discount_amount = sub_total - total_amount;
            //     let paid = $('.total_payable').val();
            //     let due = grand_total - paid;
            //     let carrying_cost = $('.carrying_cost').val();
            //     let note = $('.note').val();
            //     let document = $('.document_file').val();
            //     let payment_method = $('.payment_method').val();
            //     // let product_id = $('.product_id').val();
            //     // console.log(total_quantity);

            //     let products = [];

            //     $('tr[class^="data_row"]').each(function() {
            //         let row = $(this);
            //         // Get values from the current row's elements
            //         let product_id = row.find('.product_id').val();
            //         let quantity = row.find('input[name="quantity[]"]').val();
            //         let unit_price = row.find('input[name="unit_price[]"]').val();

            //         // Create an object with the gathered data
            //         let product = {
            //             product_id,
            //             quantity,
            //             unit_price,
            //         };

            //         // Push the object into the products array
            //         products.push(product);
            //     });

            //     let formData = new FormData($('#purchaseForm')[0]);
            //     console.log(formData);
            //     // let allData = {
            //     //     // for purchase table
            //     //     supplier_id,
            //     //     purchase_date: formattedPurchaseDate,
            //     //     total_quantity,
            //     //     total_amount,
            //     //     discount,
            //     //     discount_amount,
            //     //     sub_total,
            //     //     tax,
            //     //     grand_total,
            //     //     paid,
            //     //     due,
            //     //     carrying_cost,
            //     //     note,
            //     //     payment_method,
            //     //     document,
            //     //     products,
            //     //     formData
            //     // }

            //     // console.log(allData);
            //     $.ajaxSetup({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         }
            //     });

            //     $.ajax({
            //         url: '/purchase/store',
            //         type: 'POST',
            //         data: formData,
            //         processData: false,
            //         contentType: false,
            //         success: function(res) {
            //             if (res.status == 200) {
            //                 // console.log(res.data);
            //                 $('#paymentModal').modal('hide');
            //                 // $('.supplierForm')[0].reset();
            //                 // supplierView();
            //                 toastr.success(res.message);
            //                 let id = res.purchaseId;
            //                 // console.log(id)

            //                 window.location.href = '/purchase/invoice/' + id;

            //             } else {

            //                 if (res.error.payment_method == null) {
            //                     $('#paymentModal').modal('hide');
            //                     if (res.error.supplier_id) {
            //                         showError('.supplier_id', res.error.supplier_id);
            //                     }
            //                     if (res.error.products) {
            //                         showError('.product_select', res.error.products);
            //                     }
            //                     if (res.error.purchase_date) {
            //                         showError('.purchase_date', res.error.purchase_date);
            //                     }
            //                 } else {
            //                     if (res.error.payment_method) {
            //                         showError('.payment_method', res.error.payment_method);
            //                     }
            //                 }
            //             }
            //         }
            //     });
            // })

        });
    </script>
@endsection
