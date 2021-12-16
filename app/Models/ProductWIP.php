<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWIP extends Model
{
	use HasFactory;
	protected $table = "products_wip";
	public $timestamps = false; 
	protected $fillable = ['customer', 'no_nota', 'product_amount','created_by','updated_by',];

	public function getUserCreate(){
		return $this->belongsTo(User::class, 'updated_by', 'id')->withDefault([
			'name' => 'N/A',
		]);
	}

	public function getUserUpdate(){
		return $this->belongsTo(User::class, 'created_by', 'id')->withDefault([
			'name' => 'N/A',
		]);
	}
}
