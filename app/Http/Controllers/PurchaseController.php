<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\ActualPayment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('pos.purchase.purchase');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'date' => 'required',
            // 'products' => 'required',
            'payment_method' => 'required',
            'document' => 'file|mimes:jpg,pdf,png,svg,webp,jpeg,gif|max:5120'
        ]);
        if ($validator->passes()) {
            $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
            // dd($oldBalance);
            if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->total_payable ?? 0) {
                $totalQty = 0;
                $totalAmount = 0;
                // Assuming all arrays have the same length
                $arrayLength = count($request->product_name);
                for ($i = 0; $i < $arrayLength; $i++) {
                    $totalQty += $request->quantity[$i];
                    $totalAmount += ($request->unit_price[$i] * $request->quantity[$i]);
                }
                $purchaseDate = Carbon::createFromFormat('d-M-Y', $request->date)->format('Y-m-d');
                // dd($request->tax);

                // purchase table Crud
                $purchase = new Purchase;
                $purchase->branch_id = Auth::user()->branch_id;
                $purchase->supplier_id = $request->supplier_id;
                $purchase->purchase_date =  $purchaseDate;
                $purchase->total_quantity =  $totalQty;
                $purchase->total_amount =  $totalAmount;
                $purchase->invoice = $request->invoice;
                $purchase->discount_amount = $request->discount_amount;
                $purchase->sub_total = $request->sub_total;
                $purchase->tax = $request->tax;
                $purchase->grand_total = $request->grand_total;
                $purchase->paid = $request->total_payable ?? 0;
                $purchase->due = $request->grand_total - $request->total_payable ?? 0;
                $purchase->carrying_cost = $request->carrying_cost;
                $purchase->payment_method = $request->payment_method;
                $purchase->note = $request->note;
                if ($request->document) {
                    $docName = rand() . '.' . $request->document->getClientOriginalExtension();
                    $request->document->move(public_path('uploads/purchase/'), $docName);
                    $purchase->document = $docName;
                }
                $purchase->save();
                // dd("OK");
                // dd($purchase->id)

                // get purchaseId
                $purchaseId = $purchase->id;

                for ($i = 0; $i < $arrayLength; $i++) {
                    $items = new PurchaseItem;
                    $items->purchase_id = $purchaseId;
                    $items->product_id = $request->product_id[$i];
                    $items->unit_price = $request->unit_price[$i];
                    $items->quantity = $request->quantity[$i];
                    $items->total_price = $request->unit_price[$i] * $request->quantity[$i];
                    $items->save();

                    $items2 = Product::findOrFail($request->product_id[$i]);
                    $items2->stock += $request->quantity[$i];
                    $items2->save();
                }
                // actual payment CRUD
                $actualPayment = new ActualPayment;
                $actualPayment->branch_id =  Auth::user()->branch_id;
                $actualPayment->payment_type =  'pay';
                $actualPayment->payment_method =  $request->payment_method;
                $actualPayment->supplier_id = $request->supplier_id;
                $actualPayment->amount = $request->total_payable ?? 0;
                $actualPayment->date =  $purchaseDate;
                $actualPayment->save();

                // account Transaction crud
                $accountTransaction = new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->purpose =  'Purchase';
                $accountTransaction->reference_id = $purchaseId;
                $accountTransaction->account_id =  $request->payment_method;
                $accountTransaction->debit = $request->total_payable ?? 0;
                $accountTransaction->balance = $oldBalance->balance - $request->total_payable ?? 0;
                $accountTransaction->created_at = Carbon::now();
                $accountTransaction->save();


                // get Transaction Model
                $lastTransaction = Transaction::where('supplier_id', $request->supplier_id)->latest()->first();
                $transaction = new Transaction;
                $transaction->branch_id = Auth::user()->branch_id;
                $transaction->date =   $purchaseDate;
                $transaction->payment_type = 'pay';
                $transaction->particulars = 'Purchase#' . $purchaseId;
                $transaction->supplier_id = $request->supplier_id;
                $transaction->payment_method = $request->payment_method;
                if ($lastTransaction) {
                    $transaction->debit = $lastTransaction->debit + $request->total_payable ?? 0;
                    $transaction->credit = $lastTransaction->credit + $request->sub_total;
                    $transaction->balance = $lastTransaction->balance + ($request->sub_total - $request->total_payable ?? 0);
                } else {
                    $transaction->credit = $request->sub_total;
                    $transaction->debit = $request->total_payable ?? 0;
                    $transaction->balance = $request->sub_total - $request->total_payable ?? 0;
                }
                $transaction->save();

                // Supplier Crud
                $supplier = Supplier::findOrFail($request->supplier_id);
                $supplier->total_receivable = $supplier->total_receivable + $request->sub_total;
                $supplier->total_payable = $supplier->total_payable + $request->total_payable ?? 0;
                $supplier->wallet_balance = $supplier->wallet_balance + ($request->sub_total - $request->total_payable ?? 0);
                $supplier->save();


                return response()->json([
                    'status' => 200,
                    'purchaseId' => $purchase->id,
                    'message' => 'successfully save',
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Not Enough Balance in this Account. Please choose Another Account or Deposit Account Balance.',
                ]);
            }
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }
    public function invoice($id)
    {
        $purchase = Purchase::findOrFail($id);
        return view('pos.purchase.invoice', compact('purchase'));
    }

    public function view()
    {
        $purchase = Purchase::where('branch_id', Auth::user()->branch_id)->latest()->get();
        // return view('pos.purchase.view');
        return view('pos.purchase.view', compact('purchase'));
    }

    public function viewAll()
    {
        $purchase = Purchase::where('branch_id', Auth::user()->branch_id)->latest()->get();
        if ($purchase) {
            return response()->json([
                'status' => 200,
                'data' => $purchase,
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'message' => "No Data Found"
            ]);
        }
    }
    public function supplierName($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json([
            'status' => 200,
            'supplier' => $supplier
        ]);
    }

    public function viewDetails($id)
    {
        $purchase = Purchase::findOrFail($id);
        return view('pos.purchase.show', compact('purchase'));
    }
    public function edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        return view('pos.purchase.edit', compact('purchase'));
    }
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return back()->with('message', "Purchase successfully Deleted");
    }
    public function filter(Request $request)
    {
        // dd($request->all());
        $purchaseQuery = Purchase::query();

        // Filter by product_id if provided
        if ($request->product_id != "Select Product") {
            $purchaseQuery->whereHas('purchaseItem', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            });
        }
        // Filter by supplier_id if provided
        if ($request->supplier_id != "Select Supplier") {
            $purchaseQuery->where('supplier_id', $request->supplier_id);
        }

        // Filter by date range if both start_date and end_date are provided
        if ($request->start_date && $request->end_date) {
            $purchaseQuery->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        // Execute the query
        $purchase = $purchaseQuery->get();

        return view('pos.purchase.table', compact('purchase'))->render();
        // return response()->json([
        //     'status' => 200,
        //     'data' => $purchase
        // ]);
    }

    public function find($id)
    {
        // dd($id);
        // $purchaseId = 'Purchase#' + $id;
        $purchase = Purchase::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $purchase
        ]);
    }
    // transaction edit
    public function editTransaction(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "transaction_account" => 'required',
            "amount" => 'required',
        ]);
        $validator->after(function ($validator) use ($id, $request) {
            $purchase = Purchase::findOrFail($id);
            if ($request->amount > $purchase->due) {
                $validator->errors()->add('amount', 'The amount cannot be greater than the due amount.');
            }
        });
        if ($validator->passes()) {
            $oldBalance = AccountTransaction::where('account_id', $request->transaction_account)->latest('created_at')->first();
            if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->amount) {
                $purchase = Purchase::findOrFail($id);
                $purchase->paid = $purchase->paid - $request->amount;
                $purchase->due = $purchase->due - $request->amount;
                $purchase->save();

                // supplier related CRUD
                $supplier = Supplier::findOrFail($purchase->supplier_id);
                $supplier->total_payable = $supplier->total_payable - $request->amount;
                $supplier->wallet_balance = $supplier->wallet_balance - $request->amount;
                $supplier->save();

                // account Transaction crud
                $accountTransaction = new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->purpose =  'Purchase';
                $accountTransaction->reference_id = $id;
                $accountTransaction->account_id =  $request->transaction_account;
                $accountTransaction->debit = $request->amount;
                $oldBalance = AccountTransaction::where('account_id', $request->transaction_account)->latest('created_at')->first();
                $accountTransaction->balance = $oldBalance->balance - $request->total_payable ?? 0;
                $accountTransaction->created_at = Carbon::now();
                $accountTransaction->save();

                // transaction related CRUD
                $transaction = new Transaction;
                $transaction->date = $request->payment_date;
                $transaction->payment_type = 'pay';
                $transaction->particulars = 'Purchase#' . $id;
                $transaction->supplier_id = $supplier->id;
                $transaction->debit = $transaction->debit + $request->amount;
                $transaction->balance = $transaction->balance - $request->amount;
                $transaction->payment_method = $request->transaction_account;
                $transaction->save();

                return response()->json([
                    'status' => 200,
                    'message' => "Update successful",
                    'purchase' => $purchase
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Not Enough Balance in Account. Please choose Another Account or Deposit Account Balance',
                ]);
            }
        } else {
            return response()->json([
                'status' => 500,
                'error' => $validator->errors()
            ]);
        }
    }
    public function purchaseItem($id)
    {
        $purchaseItem = PurchaseItem::where('purchase_id', $id)->get();
        if ($purchaseItem) {
            return response()->json([
                'status' => 200,
                'purchaseItem' => $purchaseItem
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Data Not Found'
            ]);
        }
    }
    public function productName($id)
    {
        $product = Product::findOrFail($id);
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Data Not Found'
            ]);
        }
    }
    public function getSupplierDetails($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json(['data' => $supplier], 200);
    }
}