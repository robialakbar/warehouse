@extends('layouts.main')
@section('title', __('Stock Out'))
@section('custom-css')
<link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
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
			<div class="card card-body">
				<div class="modal-body">
					<div class="row justify-content-center">
						<img width="150px" src="/img/barcode_scanner.png" />
					</div>
					<div class="card">
						<div class="card-body">
							<div class="input-group input-group-lg">
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
							<form role="form" id="stock-update" method="post" action="{{ action('ProductController@product_stock2') }}">
								@csrf
								<div class="form-group row">
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
								</div>
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
										<input type="hidden" name="type" value="0">
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
	</div>

</section>
@endsection
@section('custom-js')
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
					// 	$('#pid').val(data.data.product_id);
					// 	$('#pcode').val(data.data.product_code);
					// 	$('#pname').val(data.data.product_name);
					// 	if($('#type').val() == 0){
					// 		getShelf($('#pid').val());
					// 	} else {
					// 		getShelf();
					// 	}
					// 	enableStockInput();
					// } else {
					// 	disableStockInput();
					// 	toastr.error("Product Code tidak dikenal!");
					// }
					// $('#pcode').prop("disabled", false);
					// $('#button-check').prop("disabled", false);
				}, error:function(){
					// $('#pcode').prop("disabled", false);
					// $('#button-check').prop("disabled", false);
				}
			});

		// $("#form").hide();
		// $("#button-update").hide();
	});

	function resetForm(){
		$('#form').trigger("reset");
		$('#pcode').val('');
		$("#button-update").hide();
		$("#date").hide();
		$('#pcode').prop("disabled", false);
		$('#button-check').prop("disabled", false);
	}

	function stockForm(type=1){
		$("#form").hide();
		resetForm();
		$("#type").val(type);
		if(type == 0){
			$('#modal-title').text("Stock Out");
			$('#button-update').text("Stock Out");
			$("#date").show();
		} else if(type == 1){
			$('#modal-title').text("Stock In");
			$('#button-update').text("Stock In");
			$("#date").hide();
		} else {
			$('#modal-title').text("Retur");
			$('#button-update').text("Retur");
			$("#date").hide();
		}
	}

	function getShelf(pid=null){
		var type = $('#type').val();
		$.ajax({
			url: '/products/shelf',
			type: "GET",
			data: {"format":"json", "product_id":pid},
			dataType: "json",
			success:function(data) {
				$('#shelf').empty();
				$('#shelf').append('<option value="">.:: Select Shelf ::.</option>');
				$.each(data, function(key, value) {
					if(type == 0){
						$('#shelf').append('<option value="'+ value.shelf_id +'">'+ value.shelf_name +' (Stock: '+value.product_amount+')</option>');
					} else {
						$('#shelf').append('<option value="'+ value.shelf_id +'">'+ value.shelf_name +'</option>');
					}
				});
			}
		});
	}

	function enableStockInput(){
		$('#button-update').prop("disabled", false);
		$("#button-update").show();
		$('#form').show();
	}

	function disableStockInput(){
		$('#button-update').prop("disabled", true);
		$("#button-update").hide();
		$('#form').hide();
	}


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

	function stockUpdate(){
		loader();
		$('#pcode').prop("disabled", true);
		$('#button-check').prop("disabled", true);
		$('#button-update').prop("disabled", true);
		disableStockInput();
		var data = {
			product_id:$('#pid').val(),
			name:$('#name').val(),
			no_nota:$('#no_nota').val(),
			amount:$('#pamount').val(),
			stock_date:$('#stock_date_text').val(),
			shelf:$('#shelf').val(),
			type:$('#type').val(),
		}

		$.ajax({
			url: '/products/stockUpdate',
			type: "post",
			data: JSON.stringify(data),
			dataType: "json",
			contentType: 'application/json',
			success:function(data) {
				loader(0);
				if(data.status == 1){
					toastr.success(data.message);
					resetForm();
				} else {
					toastr.error(data.message);
					enableStockInput();
					$('#pcode').prop("disabled", false);
					$('#button-check').prop("disabled", false);
				}
			}, error:function(){
				loader(0);
				toastr.error("Unknown error! Please try again later!");
				resetForm();
			}
		});
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