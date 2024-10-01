<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Returns;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    public function Return($id)
    {
        $sale = Sale::findOrFail($id);
        return view('pos.return.return-invoice', compact('sale'));
    }
    public function ReturnItems($id)
    {
        $sales = SaleItem::findOrFail($id);
        $sales->load('product');
        return response()->json([
            'status' => '200',
            'sale_items' => $sales
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required',
            'customer_id' => 'required',
            'formattedReturnDate' => 'required',
            'refund_amount' => 'required',
            'paymentMethod' => 'required',
        ]);
        if ($validator->passes()) {
            $oldBalance = AccountTransaction::where('account_id', $request->paymentMethod)->latest('created_at')->first();
            // dd($oldBalance);
            if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->refund_amount ?? 0) {
                $total_return_profit = 0;
                foreach ($request->sale_items as $sale_item) {
                    $saleItem = SaleItem::findOrFail($sale_item['product_id']);

                    // dd($Product->stock);
                    $old_subtotal = $saleItem->sub_total;
                    $old_quantity = $saleItem->qty;
                    $avg_selling_price = $old_subtotal / $old_quantity;
                    $actual_total_return_price = $avg_selling_price * $sale_item['quantity'];
                    $return_profit = $actual_total_return_price - $sale_item['total_price'];


                    $saleItem->save();
                    $Product = Product::findOrFail($saleItem->product_id);
                    $Product->stock = $Product->stock + $sale_item['quantity'];
                    $Product->save();
                    $total_return_profit += $return_profit;
                }

                $returns = new Returns;
                $returns->return_invoice_number = rand(123456, 99999);
                $returns->branch_id = Auth::user()->branch_id;
                $returns->sale_id = $request->sale_id;
                $returns->customer_id = $request->customer_id;
                $returns->return_date = $request->formattedReturnDate;
                $returns->refund_amount = $request->refund_amount;
                $returns->return_reason = $request->note ?? '';

                $returns->total_return_profit = $total_return_profit;
                $returns->status = 1;
                $returns->processed_by = Auth::user()->id;
                $returns->save();


                // dd($total_return_profit);

                foreach ($request->sale_items as $sale_item) {
                    // dd($sale_item['product_id']);
                    $returnItems = new ReturnItem;
                    $returnItems->return_id = $returns->id;
                    $returnItems->product_id = (int)$sale_item['product_id'];
                    $returnItems->quantity = (int)$sale_item['quantity'];
                    $returnItems->return_price = (int)$sale_item['return_price'];
                    $returnItems->product_total = (int)$sale_item['total_price'];

                    $saleItem = SaleItem::findOrFail($sale_item['product_id']);
                    $old_subtotal = $saleItem->sub_total;
                    $old_quantity = $saleItem->qty;
                    $actual_selling_price = $old_subtotal / $old_quantity;
                    $actual_total_return_price = $actual_selling_price * $sale_item['quantity'];
                    $return_profit = $actual_total_return_price - $sale_item['total_price'];

                    $total_cost_price = $saleItem->total_purchase_cost;
                    $purchase_cost = $total_cost_price / $old_quantity;

                    $each_product_sell_profit = $actual_selling_price - $purchase_cost;

                    $sell_profit = $each_product_sell_profit * $sale_item['quantity'];
                    $saleItem->total_profit = ($saleItem->total_profit - $sell_profit) +  $return_profit;

                    // dd($return_profit);
                    $returnItems->return_profit = $return_profit;

                    // $returnItems->return_reason = $request->note;
                    $returnItems->save();
                }

                $sales = Sale::findOrFail($request->sale_id);
                $sales->returned = $request->refund_amount;
                $sales->profit = $sales->profit - $total_return_profit;
                $sales->save();

                // customer crud
                $customer = Customer::findOrFail($request->customer_id);
                $customerDue = $customer->wallet_balance;

                // account Transaction Crud
                // $bank = Bank::where('name', "=", "Cash")->first();
                $lastTransaction = AccountTransaction::where('account_id', $request->paymentMethod)->latest('created_at')->first();
                $accountTransaction =  new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->reference_id = $returns->id;
                $accountTransaction->purpose =  'Return';
                $accountTransaction->account_id =  $request->paymentMethod;
                $accountTransaction->created_at = Carbon::now();

                // transaction CRUD
                $lastTransactionData = Transaction::latest('created_at')->first();
                $transaction = new Transaction;
                $transaction->date = $request->formattedReturnDate;
                $transaction->particulars = 'Adjust Due Collection';
                $transaction->others_id = $returns->id;
                $transaction->branch_id = Auth::user()->branch_id;
                $transaction->payment_method = $request->paymentMethod;
                $transaction->created_at = Carbon::now();
                if ($request->adjustDue == 'yes') {
                    if ($customerDue > $request->refund_amount) {
                        $dueBalance = $customerDue - $request->refund_amount;
                        $customer->wallet_balance = $customer->wallet_balance - $dueBalance;

                        $accountTransaction->credit = $request->refund_amount;
                        $accountTransaction->balance = $lastTransaction->balance + $request->refund_amount;

                        $transaction->payment_type = 'receive';
                        $transaction->credit = $request->refund_amount;

                        $transaction->balance = $lastTransactionData->balance + $request->refund_amount;
                    } else {
                        $returnBalance = $request->refund_amount - $customerDue;
                        $customer->wallet_balance = 0;

                        $cal1 = $lastTransaction->balance + $customerDue;
                        $cal2 = $cal1 - $request->refund_amount;
                        $accountTransaction->debit = $returnBalance;
                        $accountTransaction->balance = $cal2;

                        $calculation = $lastTransactionData->balance + $customerDue;
                        $calculation2 = $calculation - $request->refund_amount;
                        $transaction->payment_type = 'pay';
                        $transaction->debit = $returnBalance;
                        $transaction->balance = $calculation2;
                    }
                } else {
                    $accountTransaction->debit = $request->refund_amount;
                    $accountTransaction->balance = $lastTransaction->balance - $request->refund_amount;
                    $transaction->payment_type = 'pay';
                    $transaction->debit = $request->refund_amount;
                    $transaction->balance = $lastTransactionData->balance - $request->refund_amount;
                }
                $customer->save();
                $accountTransaction->save();
                $transaction->save();

                return response()->json([
                    'status' => '200',
                    'message' => 'Product Return successful',
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
                'error' => $validator->messages(),
            ]);
        }
    }
    public function returnProductsList()
    {
        if (Auth::user()->id == 1) {
            $returns = Returns::get();
        } else {
            $returns = Returns::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }

        return view('pos.return.return-view', compact('returns'));
    }
}
