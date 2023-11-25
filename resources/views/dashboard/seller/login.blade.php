@extends('layouts.login_layout')
@section('form_title', 'Seller Login')
@section('form_content')
<form class="pt-3" action="{{ route('seller.check') }}" method="post">
    @if (Session::get('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif
    @if (Session::get('fail'))
    <div class="alert alert-danger">
        {{ Session::get('fail') }}
    </div>
    @endif
    @csrf

    <div class="form-group">
        <input type="email" name="selleremail" value="{{ old('selleremail') }}" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Username">
        <span class="text-danger">@error('selleremail'){{ $message }}@enderror</span>
    </div>

    <div class="form-group">
        <input type="password" name="password" value="{{ old('password') }}" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password">
        <span class="text-danger">@error('password'){{ $message }}@enderror</span>
    </div>

    <div class="my-2 d-flex justify-content-end align-items-center">
        {{-- <div class="form-check">
            <label class="form-check-label text-muted">
            <input type="checkbox" class="form-check-input"> Keep me signed in </label>
        </div> --}}
        <a href="{{ route('seller.getforgetpassword') }}" class="auth-link text-black">Forgot password?</a>
    </div>

    <div class="mt-3 mb-2">
        <button class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" >SIGN IN</button>
    </div>

    <div class="text-center mt-4 font-weight-light"> Don't have an account? <a href="{{ route('seller.register') }}" class="text-primary">Create</a>
  </div>
</form>

@endsection

