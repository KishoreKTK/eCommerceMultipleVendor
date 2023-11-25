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
                    <h4 class="card-title">Edit My Profile</h4>
                    <hr class="my-4">
                    <form action="{{ route('admin.UpdateProfile') }}" method="POST" class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputEmail3">Email address</label>
                            <input type="email" name="email" class="form-control" id="exampleInputEmail3" placeholder="Email" readonly
                            value="{{ auth()->guard('admin')->user()->email }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1">Name</label>
                            <input type="text" name="name" class="form-control" id="exampleInputName1" placeholder="Name"
                            value="{{ auth()->guard('admin')->user()->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1">Mobile</label>
                            <input type="text" name="phone" class="form-control mobile-nub" id="exampleInputName1" placeholder="Enter Mobile Number"
                            value="{{ Auth::guard('admin')->user()->phone }}" required>
                        </div>
                        <div class="form-group">
                            <label>Profile</label>
                            <input type="file" class="form-control" name="profile" accept="image/png, image/gif, image/jpeg">
                        </div>
                        <p>
                            @if(!empty(Auth::guard('admin')->user()->profile))
                                <img src="{{ Auth::guard('admin')->user()->profile }}" alt="image" height="150px">
                            @endif
                        </p>

                        <button type="submit" class="btn btn-gradient-primary mr-2 float-right">Submit</button>
                        {{-- <button class="btn btn-light">Cancel</button> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



