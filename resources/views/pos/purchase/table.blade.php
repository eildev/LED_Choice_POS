@if ($purchase->count() > 0)
    @foreach ($purchase as $index => $data)
        <tr>
            <td class="id">{{ $index + 1 }}</td>
            <td>{{ $data->id ?? 0 }}</td>
            <td>{{ $data->supplier->name ?? '' }}</td>
            <td>{{ $data->purchase_date ?? 0 }}</td>
            <td>
                <ul>
                    @foreach ($data->purchaseItem as $items)
                        <li>{{ $items->product->name ?? '' }}
                            <br>({{ $items->product->barcode ?? '' }})
                        </li>
                    @endforeach
                </ul>
            </td>
            <td>
                ৳ {{ $data->grand_total ?? 0 }}
            </td>
            <td>
                ৳ {{ $data->paid ?? 0 }}
            </td>
            <td>
                @if ($data->due > 0)
                    <span class="text-danger">৳ {{ $data->due ?? 0 }}</span>
                @else
                    ৳ {{ $data->due ?? 0 }}
                @endif

            </td>
            <td class="id">
                <div class="dropdown">
                    <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <a class="dropdown-item" href="{{ route('purchase.invoice', $data->id) }}"><i
                                class="fa-solid fa-file-invoice me-2"></i> Invoice</a>
                        <a class="dropdown-item money_receipt" href="#" data-bs-toggle="modal"
                            data-bs-target="#moneyReceiptModal" data-id="{{ $data->id }}"><i
                                class="fa-solid fa-receipt me-2"></i> Money Receipt</a>
                        <a class="dropdown-item " href="{{ route('purchase.view.details', $data->id) }}"><i
                                class="fa-solid fa-eye me-2"></i> Show</a>
                        {{-- @if ($data->due > 0)
                            <a class="dropdown-item add_payment" href="#" data-bs-toggle="modal"
                            data-bs-target="#paymentModal" data-id="{{ $data->id }}"><i
                                class="fa-solid fa-credit-card me-2"></i> Payment</a>
                        @endif --}}
                        {{-- @if (Auth::user()->can('purchase.edit'))
                            <a class="dropdown-item" href="{{ route('purchase.edit', $data->id) }}"><i
                                    class="fa-solid fa-pen-to-square me-2"></i> Edit</a>
                        @endif --}}
                        @if (Auth::user()->can('purchase.delete'))
                            <a class="dropdown-item" id="delete"
                                href="{{ route('purchase.destroy', $data->id) }}"><i
                                    class="fa-solid fa-trash-can me-2"></i>Delete</a>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9"> No Data Found</td>
    </tr>
@endif
