<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    } //
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    } //
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'payment_method', 'id');
    } //
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'others_id', 'id');
    } //

}
