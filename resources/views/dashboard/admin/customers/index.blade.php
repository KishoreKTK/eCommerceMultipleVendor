@extends('layouts.dashboard_layout')
@section('pagecss')


@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <a href="{{ Route('admin.home') }}"><span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span></a>Customer Managment
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Customer Lists</li>
            </ol>
        </nav>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1"><h4 class="card-title">Customer List </h4></div>
                        {{-- <div class="p-2">
                            <a href="#" class="btn btn-outline-secondary btn-fw btn-sm">
                                <i class="mdi mdi-download btn-icon-prepend"></i> Download</a>
                        </div> --}}
                    </div>
                    <div class="table-responsive">
                        @include('dashboard.admin.customers.table_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')

@endsection
