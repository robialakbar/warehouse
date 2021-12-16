@extends('layouts.main')
@section('title', __('Products'))
@section('custom-css')
<link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<style type="text/css">
	.removeRow{
		background: #FF6347;
		color: #FFFFFF;
	}
</style>
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
		<div class="card">
			<div class="card-header">
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-product" onclick="addProduct()"><i class="fas fa-plus"></i> Add New Product</button>
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import-product" onclick="importProduct()"><i class="fas fa-file-excel"></i> Import Product (Excel)</button>
				<button type="button" class="btn btn-primary" onclick="download('xls')"><i class="fas fa-file-excel"></i> Export Product (XLS)</button>
				<div class="card-tools">
					<form>
						<div class="input-group input-group">
							<input type="text" class="form-control" name="q" placeholder="Search">
							<input type="hidden" name="category" value="{{ Request::get('category') }}">
							<input type="hidden" name="sort" value="{{ Request::get('sort') }}">
							<div class="input-group-append">
								<button class="btn btn-primary" type="submit">
									<i class="fas fa-search"></i>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="card-body">
				<div class="form-group row col-sm-12">
					<label for="sort" class="col-sm-3 col-form-label">Sort</label>
					<div class="col-sm-5">
						<form id="sorting" action="" method="get">
							<input type="hidden" name="q" value="{{ Request::get('q') }}">
							<input type="hidden" name="category" value="{{ Request::get('category') }}">
							<select class="form-control select2" style="width: 100%;" id="sort" name="sort">
								<option value="" {{ Request::get('sort') == null? 'selected':'' }}>-</option>
								<option value="name_az" {{ Request::get('sort') == 'name_az'? 'selected':'' }}>Nama Produk (A-Z)</option>
								<option value="name_za" {{ Request::get('sort') == 'name_za'? 'selected':'' }}>Nama Produk (Z-A)</option>
								<option value="category_az" {{ Request::get('sort') == 'category_az'? 'selected':'' }}>Kategori (A-Z)</option>
								<option value="category_za" {{ Request::get('sort') == 'category_za'? 'selected':'' }}>Kategori (Z-A)</option>
							</select>
						</form>
					</div>
					@if(auth()->user()->is_delete == 'Y')
					<div class="col-sm-3 float-right">
						<button class="btn btn-danger float-right" id="btn_checkbox" type="button">Delete Seleted Item</button>
					</div>
					@endif
				</div>
				<div class="table-responsive">
					<table id="table" class="table table-sm table-bordered ">
						<thead>
							<tr class="text-center">
								@if(auth()->user()->is_delete == 'Y')
								<th><input type="checkbox" class="checkboxHapusAll" ></th>
								@endif
								<th>No.</th>
								<th>{{ __('Kode Produk') }}</th>
								<th>{{ __('Nama Produk') }}</th>
								<th>{{ __('Kategori') }}</th>
								<th>{{ __('Jumlah') }}</th>
								@if(auth()->user()->is_show == 'Y')
								<th>{{ __('Harga Pembelian (Rp)') }}</th>
								@endif
								<th>{{ __('Harga Satuan (Rp)') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@if(count($products) > 0)
							@foreach($products as $key => $d)
							@php
							$data = [
								"no"        => $products->firstItem() + $key,
								"pid"       => $d->product_id,
								"pcode"     => $d->product_code,
								"pname"     => $d->product_name,
								"cname"     => $d->category_name,
								"cval"      => $d->category_id,
								"pamount"   => $d->product_amount,
								"pprice"    => $d->purchase_price,
								"sprice"    => $d->sale_price
							];
							@endphp
							<tr id="{{ $d->product_id }}">
								@if(auth()->user()->is_delete == 'Y')
								<td class="text-center"><input type="checkbox" class="checkboxHapus" name="hapus[]" value="{{ $d->product_id }}"></td>
								@endif
								<td class="text-center">{{ $data['no'] }}</td>
								<td class="text-center">{{ $data['pcode'] }}</td>
								<td>{{ $data['pname'] }}</td>
								<td>{{ $data['cname'] }}</td>
								<td class="text-center"><span class="{{ ($data['pamount'] <= 10)? 'badge bg-warning':'' }}">{{ $data['pamount'] }}</span></td>
								@if(auth()->user()->is_show == 'Y')
								<td class="text-center">{{ number_format($data['pprice'], 2, ",", ".") }}</td>
								@endif
								<td class="text-center">{{ number_format($data['sprice'], 2, ",", ".") }}</td>
								<td class="text-center"><button title="Edit Produk" type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#add-product" onclick="editProduct({{ json_encode($data) }})"><i class="fas fa-edit"></i></button> <button title="Lihat Barcode" type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#lihat-barcode" onclick="barcode({{ $d->product_code }})"><i class="fas fa-barcode"></i></button> @if(Auth::user()->role == 0)<button title="Hapus Produk" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#delete-product" onclick="deleteProduct({{ json_encode($data) }})"><i class="fas fa-trash"></i></button>@endif</td>
							</tr>
							@endforeach
							@else
							<tr class="text-center">
								<td colspan="8">{{ __('No data.') }}</td>
							</tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div>
			{{ $products->appends(request()->except('page'))->links("pagination::bootstrap-4") }}
		</div>
	</div>
	<div class="modal fade" id="add-product">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 id="modal-title" class="modal-title">{{ __('Add New Product') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form role="form" id="save" action="{{ route('products.save') }}" method="post">
						@csrf
						<input type="hidden" id="save_id" name="id">
						<div class="form-group row">
							<label for="product_code" class="col-sm-4 col-form-label">{{ __('Product Code') }}</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="product_code" name="product_code">
							</div>
						</div>
						<div class="form-group row">
							<label for="product_name" class="col-sm-4 col-form-label">{{ __('Product Name') }}</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="product_name" name="product_name">
							</div>
						</div>
						<div class="form-group row">
							<label for="purchase_price" class="col-sm-4 col-form-label">{{ __('Purchase Price') }} (Rp)</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="purchase_price" name="purchase_price">
							</div>
						</div>
						<div class="form-group row">
							<label for="sale_price" class="col-sm-4 col-form-label">{{ __('Sale Price') }} (Rp)</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="sale_price" name="sale_price">
							</div>
						</div>
						<div class="form-group row">
							<label for="category" class="col-sm-4 col-form-label">Category</label>
							<div class="col-sm-8">
								<select class="form-control select2" style="width: 100%;" id="category" name="category">
								</select>
							</div>
						</div>
						<div id="barcode_preview_container" class="form-group row">
							<label class="col-sm-4 col-form-label">Barcode</label>
							<div class="col-sm-8">
								<img id="barcode_preview"/>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
					<button id="button-save" type="button" class="btn btn-primary" onclick="document.getElementById('save').submit();">{{ __('Tambahkan') }}</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="lihat-barcode">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 id="modal-title" class="modal-title">{{ __('Barcode') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="text-center">
						<input type="hidden" id="pcode_print">
						<img id="barcode"/>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Tutup') }}</button>
					<button type="button" class="btn btn-primary" onclick="printBarcode()">{{ __('Print Barcode') }}</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="delete-product">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 id="modal-title" class="modal-title">{{ __('Delete Product') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form role="form" id="delete" action="{{ route('products.delete') }}" method="post">
						@csrf
						@method('delete')
						<input type="hidden" id="delete_id" name="id">
					</form>
					<div>
						<p>Anda yakin ingin menghapus product code <span id="pcode" class="font-weight-bold"></span>?</p>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
					<button id="button-save" type="button" class="btn btn-danger" onclick="document.getElementById('delete').submit();">{{ __('Ya, hapus') }}</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="import-product">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Import Product (Excel)</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form role="form" enctype="multipart/form-data" id="import" action="{{ route('products.import') }}" method="post">
						@csrf
						<div class="form-group">
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="file" name="file">
								<label class="custom-file-label" for="file">Choose file</label>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
					<button type="button" class="btn btn-default" id="download-template">{{ __('Download Template') }}</button>
					<button type="button" class="btn btn-primary" onclick="$('#import').submit();">{{ __('Import') }}</button>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('custom-js')
<script src="/plugins/toastr/toastr.min.js"></script>
<script src="/plugins/select2/js/select2.full.min.js"></script>
<script src="/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script>
	$(function () {
		bsCustomFileInput.init();
		var user_id;
		$('.select2').select2({
			theme: 'bootstrap4'
		});

		$('#product_code').on('change', function() {
			var code = $('#product_code').val();
			if(code != null && code != ""){
				$("#barcode_preview").attr("src", "/products/barcode/"+code);
				$('#barcode_preview_container').show();
			}
		});
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
	});

	$('#sort').on('change', function() {
		$("#sorting").submit();
	});

	function getCategory(val){
		$.ajax({
			url: '/products/categories',
			type: "GET",
			data: {"format": "json"},
			dataType: "json",
			success:function(data) {                    
				$('#category').empty();
				$('#category').append('<option value="">.:: Select Category ::.</option>');
				$.each(data, function(key, value) {
					if(value.category_id == val){
						$('#category').append('<option value="'+ value.category_id +'" selected>'+ value.category_name +'</option>');
					} else {

						$('#category').append('<option value="'+ value.category_id +'">'+ value.category_name +'</option>');
					}
				});
			}
		});
	}

	$('.checkboxHapus').click(function(){
		if($(this).is(':checked')){
			$(this).closest('tr').addClass('removeRow');
		}else{
			$(this).closest('tr').removeClass('removeRow');
		}
	})

	$('.checkboxHapusAll').click(function() {
		$('.checkboxHapus').prop('checked', this.checked);
		if($(this).is(':checked')){
			$('.checkboxHapus').closest('tr').addClass('removeRow');
		} else {
			$('.checkboxHapus').closest('tr').	removeClass('removeRow');
		}
	});

	$('#btn_checkbox').click(function(){
		if($(':checkbox:checked').length > 0){

			if(confirm("Apakah Anda yakin ingin menghapus data ini?")){
				var id = [];

				$(':checkbox:checked').each(function(i){
					id[i] = $(this).val();
				});

				if(id.length === 0){
					alert("Pilih minimal satu data");
				}else{
					$.ajax({
						url:'{{ action('ProductController@deleteCheckbox') }}',
						method:'POST',
						data:{id:id},
						success:function(data){
							console.log(data);
							if(data.status == 1){
								toastr.success(data.message);
								for(var i=0; i<id.length; i++){
									$('tr#'+id[i]+'').fadeOut('slow');
								}
							} else {
								toastr.error(data.message);
							}
							
						}
					});
				}
			} else {
				return false;
			}
		}
	});


	function resetForm(){
		$('#save').trigger("reset");
		$('#barcode_preview_container').hide();
	}

	function addProduct(){
		$('#modal-title').text("Add New Product");
		$('#button-save').text("Tambahkan");
		resetForm();
		getCategory();
	}

	function editProduct(data){
		$('#modal-title').text("Edit Product");
		$('#button-save').text("Simpan");
		resetForm();
		$('#save_id').val(data.pid);
		$('#product_code').val(data.pcode);
		$('#product_name').val(data.pname);
		$('#purchase_price').val(data.pprice);
		$('#sale_price').val(data.sprice);
		getCategory(data.cval);
		$('#product_code').change();
	}

	function barcode(code){
		$("#pcode_print").val(code);
		$("#barcode").attr("src", "/products/barcode/"+code);
	}

	function printBarcode(){
		var code    = $("#pcode_print").val();
		var url     = "/products/barcode/"+code+"?print=true";
		window.open(url,'window_print','menubar=0,resizable=0');
	}

	function deleteProduct(data){
		$('#delete_id').val(data.pid);
		$('#pcode').text(data.pcode);
	}

	$("#download-template").click(function(){
		$.ajax({
			url: '/downloads/template_import_product.xls',
			type: "GET",
			xhrFields: {
				responseType: 'blob'
			},
			success:function(data) {                    
				var a = document.createElement('a');
				var url = window.URL.createObjectURL(data);
				a.href = url;
				a.download = "template_import_product.xls";
				document.body.append(a);
				a.click();
				a.remove();
				window.URL.revokeObjectURL(url);
			}
		});
	});

	function download(type){
		window.location.href="{{ route('products') }}?search={{ Request::get('search') }}&dl="+type;
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