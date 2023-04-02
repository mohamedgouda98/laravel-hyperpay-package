<?php


namespace Gouda\LaravelHyperpay\Models;


use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['card_name', 'status', 'amount', 'payment_id'];

}