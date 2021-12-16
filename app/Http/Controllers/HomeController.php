<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
USE Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getWarehouse(){
		$controller = new ProductController;
		return $controller->getWarehouse();
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $warehouse = $this->getWarehouse();
        return View::make("home")->with(compact("warehouse"));
    }

    public function stockOut(Request $request)
    {
        
        return view('stock_out');
    }

    public function defect(Request $request)
    {
    	if(Session::has('selected_warehouse_id')){
			$warehouse_id = Session::get('selected_warehouse_id');
		} else {
			$warehouse_id = DB::table('warehouse')->first()->warehouse_id;
		}

        $stockDefact = Stock::leftJoin("products", "stock.product_id", "=", "products.product_id")
		->leftJoin("shelf", "stock.shelf_id", "=", "shelf.shelf_id")
		->leftJoin("users", "stock.user_id", "=", "users.id")
		->select("stock.*", "products.product_code", "products.product_name", "products.sale_price", "shelf.shelf_name", "users.name", DB::raw('SUM(product_amount) as total_amount'))
		->where("products.warehouse_id", $warehouse_id)
		->where("type",'3')
		->groupBy('product_id')
		->get();
		
        return view('defect', compact('stockDefact'));
    }
}
