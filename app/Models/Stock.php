<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
	use HasFactory;

	protected $table = 'stock';
	protected $fillable = ['stock_id','warehouse_id','user_id','shelf_id','product_id','stock_name','no_nota','product_amount','type','datetime','ending_amount'];
}
