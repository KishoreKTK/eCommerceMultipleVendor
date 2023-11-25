<?php
use Illuminate\Support\Carbon;
?>
@extends('layouts.dashboard_layout')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <div class="d-lg-flex w-100 justify-content-between align-items-center">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                    <i class="mdi mdi-home"></i>
                </span> My Account
            </h3>
            <p class="mb-0 text-muted">{{ Carbon::now()->format('l \\, jS \\of F Y'); }}</p>
        </div>
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
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Update Password</h4>
                    <form action="{{ route('seller.UpdatePassword') }}" method="POST" class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputPassword4">Current Password</label>
                            <input type="password" name="old_password" class="form-control" id="exampleInputPassword4" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword4">New Password</label>
                            <input type="password" name="password" class="form-control" id="exampleInputPassword4" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword4">Conform New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="exampleInputPassword4" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-gradient-primary mr-2 float-right">Submit</button>
                        {{-- <button class="btn btn-light">Cancel</button> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



