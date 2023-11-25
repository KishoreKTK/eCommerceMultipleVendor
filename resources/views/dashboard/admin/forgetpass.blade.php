@extends('layouts.login_layout')
@section('form_title', 'Forgot Password')
@section('form_content')

<form class="pt-3" action="{{ route('admin.forgetpassword') }}" method="post">
    @if (Session::get('fail'))
        <div class="alert alert-danger">
            {{ Session::get('fail') }}
        </div>
    @endif
    @csrf

    <div class="form-group">
        <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" id="exampleInputEmail12" placeholder="Enter Your Email" required>
        <span class="text-danger">@error('email'){{ $message }}@enderror</span>
    </div>
    <div>
        <button type="submit" class="btn btn-gradient-warning btn-sm float-right" id="form_submit_btn">Submit</button>
        <a href="{{ route('admin.home') }}" class="btn btn-gradient-secondary btn-sm float-right mx-2">Back to Login</a>
    </div>
  </div>
</form>

@endsection
