@extends('master')
@section('admin')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Yearly Report</li>
        </ol>
    </nav>


    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Yearly Report</h6>
                    <div class="table-responsive">
                        <table id="example" class="table">
                            <thead>
                                <tr>
                                    <th class="id">#</th>
                                    <th>Month</th>
                                    <th>Total Purchase</th>
                                    <th>Total Sale</th>
                                    <th>Profit</th>
                                    <th>Expanse</th>
                                    <th>Salary</th>
                                    <th>Net Profit</th>
                                    {{-- <th class="id">Action</th> --}}
                                </tr>
                            </thead>
                            {{-- @dd($monthlyReports) --}}
                            <tbody id="showData">
                                @php
                                    $key = 1;
                                @endphp
                                @foreach ($monthlyReports as $report)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        @php
                                            $key++;
                                        @endphp
                                        <td>{{ $report['month'] }}</td>
                                        <td> {{ $report['totalPurchaseCost'] }}</td>
                                        <td> {{ $report['totalSale'] }}</td>
                                        <td> {{ $report['totalProfit'] }}</td>
                                        <td> {{ $report['totalExpense'] }}</td>
                                        <td> {{ $report['totalSalary'] }}</td>
                                        <td> {{ $report['finalProfit'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
