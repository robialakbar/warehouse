@extends('layouts.main')
@section('title', __('DEFECT'))
@section('custom-css')
<link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<link href="{{ asset('plugins/DataTables/datatables.min.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
		</div>
	</div>
</div>
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="card card-body p-0">
				<div class="modal-body">
					<div class="row justify-content-center">
						<img width="150px" src="/img/barcode_scanner.png" />
					</div>
					<div class="card">
						<div class="card-body m-0 p-0">
							<div class="input-group input-group-lg p-0">
								<input type="text" class="form-control" id="pcode" name="pcode" min="0" placeholder="Input Product Code / Scan Barcode">
								<div class="input-group-append">
									<button class="btn btn-primary" id="button-check" onclick="productCheck()">
										<i class="fas fa-search"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<div id="form" class="card">
						<div class="card-body">
							<form role="form" id="stock-update" method="post" action="{{ action('ProductController@defectStore') }}">
								@csrf
							{{-- 	<div class="form-group row">
									<label for="no_nota" class="col-sm-4 col-form-label">{{ __('No. Nota') }}</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="no_nota" name="no_nota" required>
									</div>
								</div>
								<div class="form-group row">
									<label for="name" class="col-sm-4 col-form-label">{{ __('Name') }}</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="name" name="name" required>
									</div>
								</div> --}}
								<table class="table table-bordered table-sm">
									<thead>
										<tr>
											<th scope="col" >Code</th>
											<th scope="col" >Product Name</th>
											<th scope="col" class="text-center">Amount</th>
											<th scope="col" class="text-center">Stock</th>
											<th scope="col" >Action</th>
										</tr>
									</thead>
									<tbody id="dataBarcode">
										<th colspan="5" id="noData" class="text-center">Belum Ada Data</th>
									</tbody>
									<input type="hidden" name="type" value="3">
								</table>
								<hr>
								<div class="col-12">
									<button class="btn btn-primary col-12">Submit</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="card card-body">
			<h5 class="text-center">List Defect</h5>
				<table class="table table-bordered" id="DataStock">
					<thead>
						<tr>
							<th scope="col">No</th>
							<th scope="col">Date</th>
							<th scope="col">Shelf Name</th>
							<th scope="col">Product Code</th>
							<th scope="col">Product Name</th>
							<th scope="col">Total Amount</th>
							<th scope="col">Scaned By</th>
						</tr>
					</thead>
					<tbody>
						@forelse($stockDefact as $stock)
						<tr>
							<th scope="row">{{ $loop->index +1 }}</th>
							<td>{{ date('d-m-Y H:i:s', strtotime($stock->datetime)) }}</td>
							<td>{{ $stock->shelf_name }}</td>
							<td>{{ $stock->product_code }}</td>
							<td>{{ $stock->product_name }}</td>
							<td>{{ $stock->total_amount }}</td>
							<td>{{ $stock->name }}</td>
						</tr>
						@empty
						<th>
							<td>No Data</td>
						</th>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>

</section>
@endsection
@section('custom-js')
<script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
<script src="/plugins/toastr/toastr.min.js"></script>
<script src="/plugins/select2/js/select2.full.min.js"></script>
<script src="/plugins/moment/moment.min.js"></script>
<script src="/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="/plugins/daterangepicker/daterangepicker.js"></script>
<script src="/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script>
	$(function () {

		$('.select2').select2({
			theme: 'bootstrap4'
		});
		$('#stock_date').datetimepicker({
			viewMode: 'years',
			format: 'MM/DD/YYYY HH:mm:ss'
		});
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

	});
	$(document).on('click','.ubah', function(){
		$('#product-item-'+ $(this).data('product')).removeAttr('readonly').focus();
	})

	$(document).on('click','.hapus', function(){
		if(confirm('Yakin Hapus Produck')){
			$('#'+$(this).data('product')).remove();		
		}
		
	})
	$(document).on('keyup', '.amount', function(){
		let Stock = $(this).data('stock');
		if($(this).val() > Stock){
			$(this).val('');
			return toastr.error("Jumlah Ammount Melebihi Stock!"); 
		}
	})

	$('#pcode').on('input', function() {
		$.ajax({
			url: '/products/check/'+pcode,
			type: "GET",
			data: {"pcode": $(this).val()},
			dataType: "json",
			success:function(data) {
				
				if(data.status == 1){
					if($('#noData').length){
						$('#noData').remove();
					}
					if($('#'+data.data.product_id).length){
						var itemSekarang = $('#product-item-'+data.data.product_code).val();
						itemSekarang++;
						if(data.data.availableStock < itemSekarang){
							$('#product-item-'+data.data.product_code).val('').val(itemSekarang);
							return toastr.error("Jumlah Ammount Melebihi Stock!");

						}
						$('#product-item-'+data.data.product_code).val('').val(itemSekarang);
					}else{
						if(data.data.availableStock == 0){
							return toastr.error("Tidak Ada Stock!");
						}
						console.log(data.product_code);
						$('#dataBarcode').append(`
							<tr id="`+data.data.product_id+`">
							<td scope="col">`+data.data.product_code+`</td>
							<td scope="col">`+data.data.product_name+`</td>
							<td scope="col" class="text-center"><input class="amount" type="number" id="product-item-`+data.data.product_code+`" name="item[`+data.data.product_id+`]" value="1" readonly data-stock="`+data.data.availableStock+`"></td>
							<td scope="col" class="text-center">`+data.data.availableStock+`</td>
							<td scope="col">
							<button type="button" class="btn btn-xs btn-warning ubah" data-product="`+data.data.product_code+`"  >Ubah Amount</button>
							<button type="button" class="btn btn-xs btn-danger hapus" data-product="`+data.data.product_id+`"  >Hapus</button>
							</td>
							</tr>
							`);
					}

					$('#pcode').val('');
				}

			}, error:function(){

			}
		});

	});

	$('#DataStock').DataTable();


	function productCheck(){
		var pcode = $('#pcode').val();
		if(pcode.length > 0){

			$.ajax({
				url: '/products/check/'+pcode,
				type: "GET",
				data: {"pcode": pcode},
				dataType: "json",
				success:function(data) {

					if(data.status == 1){
						if($('#noData').length){
							$('#noData').remove();
						}
						if($('#'+data.data.product_id).length){
							var itemSekarang = $('#product-item-'+data.data.product_code).val();
							itemSekarang++;
							if(data.data.availableStock < itemSekarang){
								$('#product-item-'+data.data.product_code).val('').val(itemSekarang);
								return toastr.error("Jumlah Ammount Melebihi Stock!");

							}
							$('#product-item-'+data.data.product_code).val('').val(itemSekarang);
						}else{
							if(data.data.availableStock == 0){
								return toastr.error("Tidak Ada Stock!");
							}
							console.log(data.product_code);
							$('#dataBarcode').append(`
								<tr id="`+data.data.product_id+`">
								<td scope="col">`+data.data.product_code+`</td>
								<td scope="col">`+data.data.product_name+`</td>
								<td scope="col" class="text-center"><input class="amount" type="number" id="product-item-`+data.data.product_code+`" name="item[`+data.data.product_id+`]" value="1" readonly data-stock="`+data.data.availableStock+`"></td>
								<td scope="col" class="text-center">`+data.data.availableStock+`</td>
								<td scope="col">
								<button type="button" class="btn btn-xs btn-warning ubah" data-product="`+data.data.product_code+`"  >Ubah Amount</button>
								<button type="button" class="btn btn-xs btn-danger hapus" data-product="`+data.data.product_id+`"  >Hapus</button>
								</td>
								</tr>
								`);
						}

						$('#pcode').val('');
					}

				}, error:function(data){
					return toastr.error("Product Tidak Ditemukan!");
				}
			});

		} else {
			toastr.error("Product Code belum diisi!");
		}
	}

</script>
@if(Session::has('success'))
<script>toastr.success('{!! Session::get("success") !!}');</script>
@endif
@if(Session::has('error'))
<script>toastr.error('{!! Session::get("error") !!}');</script>
@endif
@if(!empty($errors->all()))
<script>toastr.error('{!! implode("", $errors->all("<li>:message</li>")) !!}');</script>
@endif
@endsection