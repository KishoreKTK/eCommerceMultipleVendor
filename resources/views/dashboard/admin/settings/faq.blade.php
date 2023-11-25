@extends('layouts.dashboard_layout')
@section('pagecss')
<link rel="stylesheet" href="{{ asset('assets/vendors/summernote/summernote.min.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <a href="{{ Route('admin.home') }}"><span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span></a>Settings
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">FAQ</li>
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
                    <div class="d-flex justify-content-between">
                        <h4>Frequently Asked Question</h4>
                        <button class="btn btn-outline-primary btn-sm" id="add_new_question_form" type="button">
                            <i class="mdi mdi-comment-plus-outline Icon-lg"></i> Add New Question</button>
                    </div>

                    <hr>
                    <form action="{{ route('admin.postfaq') }}" method="POST" enctype="multipart/form-data" class="hide" id="faq_form_id">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="my-textarea">Question</label>
                                    <textarea id="my-textarea" class="form-control summernote" name="question"></textarea>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="my-textarea">Answer</label>
                                    <textarea id="my-textarea2" class="form-control summernote" name="answer"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-right">Submit</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @if(count($faqlist) != 0)
            @foreach ($faqlist as $faq)
                <div class="col-lg-6 col-sm-12  grid-margin stretch-card">
                    <div class="card m-2 border border-success rounded">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">{!! $faq->question !!}</h5>
                                <div><a href="#" class="mdi mdi-pencil text-warning"></a>
                                <a href="#" class="mdi mdi-delete-forever text-danger"></a></div>
                            </div>
                            <hr>
                            <p class="card-text">{!! $faq->answer !!}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 grid-margin stretch-card">
                <div class="card m-2 border border-primary rounded">
                    <div class="card-body">
                        <h5 class="card-title text-center">No Questions Asked Yet</h5>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>


@endsection

@section('pagescript')
<script src="{{ asset('assets/vendors/summernote/summernote.min.js') }}"></script>
<script>
$(document).ready(function(){
    $("#faq_form_id").hide();
    $('.summernote').summernote({
        placeholder: 'Add Content Here',
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    $("#add_new_question_form").click(function(){
        $("#faq_form_id").show();
    });
});
</script>
@endsection
