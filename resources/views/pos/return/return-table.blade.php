@if ($returns->count() > 0)
    @foreach ($returns as $index => $data)
        <tr>
            <td class="id">{{ $index + 1 }}</td>
            <td>
                <a href="{{ route('return.products.invoice', $data->id) }}">
                    #{{ $data->return_invoice_number ?? 0 }}
                </a>

            </td>
            <td>
                {{ $data->customer->name ?? 0 }}
            </td>
            <td>
                <ul>
                    {{-- @dd($data->returnItem) --}}
                    @foreach ($data->returnItem as $item)
                        {{-- @dd($item->product) --}}
                        <li>{{ $item->product->name ?? '' }}</li>
                    @endforeach
                </ul>
            </td>
            <td>{{ $data->return_date ?? 'Date not Available' }}</td>
            <td>৳ {{ $data->refund_amount ?? 0 }}</td>
            <td>{{ $data->return_reason ?? 'Data not Available' }}</td>
            <td>
                ৳ {{ $data->total_return_profit ?? 0 }}
            </td>
            <td>
                ৳ {{ $data->processed_by ?? 0 }}
            </td>

        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9"> No Data Found</td>
    </tr>
@endif
