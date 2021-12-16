@extends('layouts.main')
@section('title', __('WIP History'))
@section('custom-css')
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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
                <button type="button" class="btn btn-primary" onclick="download('xls')"><i class="fas fa-file-excel"></i> Export Product (XLS)</button>
                <div class="card-tools">
                    <form>
                        <div class="input-group input-group">
                            <input type="text" class="form-control" name="q" placeholder="Search">
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
                <div class="table-responsive">
                    <table id="table" class="table table-sm table-bordered table-hover table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>{{ __('Product Code') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Date In') }}</th>
                                <th>{{ __('Scan Product In By') }}</th>
                                <th>{{ __('Date Out') }}</th>
                                <th>{{ __('Scan Product Out By') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($products) > 0)
                            @foreach($products as $key => $d)
                            @php
                                $data = [
                                            "no"        => $products->firstItem() + $key,
                                            "pid"       => $d->product_wip_id,
                                            "pcode"     => $d->product_code,
                                            "pname"     => $d->product_name,
                                            "pamount"   => $d->product_amount,
                                            "date_in"   => date("d/m/Y H:i:s", strtotime($d->date_in)),
                                            "date_out"  => date("d/m/Y H:i:s", strtotime($d->date_out)),
                                        ];
                            @endphp
                            <tr>
                                <td class="text-center">{{ $data['no'] }}</td>
                                <td class="text-center">{{ $data['pcode'] }}</td>
                                <td>{{ $data['pname'] }}</td>
                                <td class="text-center">{{ $data['pamount'] }}</td>
                                <td class="text-center">{{ $data['date_in'] }}</td>
                                <td class="text-center">{{ $d->getUserCreate->name }}</td>
                                <td class="text-center">{{ $data['date_out'] }}</td>
                                <td class="text-center">{{ $d->getUserUpdate->name }}</td>
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
</section>
@endsection
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(function () {
            var user_id;
            $('.select2').select2({
            theme: 'bootstrap4'
            });
        });

        function download(type){
            window.location.href="{{ route('products.wip.history') }}?search={{ Request::get('search') }}&dl="+type;
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