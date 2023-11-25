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
              <li class="breadcrumb-item active" aria-current="page">Contents</li>
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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4>Content Pages</h4>
                        <button class="btn btn-outline-primary btn-sm" id="add_new_question_form" type="button"
                        data-toggle="modal" data-target="#viewAddContentForm">
                        <i class="mdi mdi-comment-plus-outline Icon-lg"></i> Add Content Page</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @if(count($contentlist) != 0)
            @foreach ($contentlist as $content)
                <div class="col-lg-4 col-sm-12  grid-margin stretch-card">
                    <div class="card m-2 border border-primary rounded">
                        <div class="card-body">
                            <h5 class="card-title">{!! $content->title !!}</h5>
                            <hr>
                            <div class="d-flex justify-content-between">

                                <a href="{{ route('ViewContentPage',['title'=>$content->slug]) }}"
                                    class="btn btn-outline-primary btn-sm" target="_blank">View Page</a>

                                <div class="d-flex">
                                    <button type="button" class="mx-2 btn btn-outline-warning btn-sm edit_content"
                                    data-title="{{ $content->title }}" data-content="{{ $content->content }}"
                                    data-content-id="{{ $content->id }}"><i class="mdi mdi-pencil"></i></button>

                                    <form action="{{  Route('admin.deleteContent') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="content_id" value="{{ $content->id }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="mdi mdi-delete-forever"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 grid-margin stretch-card">
                <div class="card m-2 border border-primary rounded">
                    <div class="card-body">
                        <h5 class="card-title text-center">No Contents Added Yet</h5>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="viewAddContentForm" tabindex="-1" role="dialog" aria-labelledby="viewAddContentFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewAddContentFormLabel">Add New Content</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.postcontent') }}" method="POST" enctype="multipart/form-data" autocomplete="off" >
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputUsername1">Title</label>
                            <input type="text" class="form-control" id="exampleInputUsername1" name="title" required placeholder="Status Name" value="">
                        </div>

                        <div class="form-group">
                            <label for="my-textarea">Content</label>
                            <textarea id="my-textarea2" class="form-control summernote" name="content" required></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end p-2  m-2">
                        <button type="button" class="btn btn-secondary mx-2" data-dismiss="modal">Close</button>
                        <button type="Submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>
</div>

<div class="modal fade" id="viewContent_modal" tabindex="-1" role="dialog" aria-labelledby="viewContent_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewContent_modalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body" id="content_view_id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
      </div>
    </div>
</div>

<div class="modal fade" id="UpdateContentForm" tabindex="-1" role="dialog" aria-labelledby="UpdateContentFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="UpdateContentFormLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.updatecontent') }}" method="POST" enctype="multipart/form-data" autocomplete="off" >
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        @csrf
                        <input type="hidden" name="content_id" id="hidden_content_id" value="">
                        <div class="form-group">
                            <label for="edit_title_val">Title</label>
                            <input type="text" class="form-control" readonly id="edit_title_val" name="title" required placeholder="Status Name" value="">
                        </div>

                        <div class="form-group">
                            <label for="my-textarea">Content</label>
                            <textarea class="form-control summernote" name="content" id="edit_content_val" required></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end p-2  m-2">
                        <button type="button" class="btn btn-secondary mx-2" data-dismiss="modal">Close</button>
                        <button type="Submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
      </div>
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
        height: 300,
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
    // $("#add_new_question_form").click(function(){
    //     $("#faq_form_id").show();
    // });

    $("body").on("click",".view_content",function(){
        title   = $(this).attr('data-title');
        content = $(this).attr('data-content');
        $("#viewContent_modalLabel").html(title);
        $("#content_view_id").html(content);
        $("#viewContent_modal").modal("show");
    });


    $("body").on("click",".edit_content",function(){
        id      = $(this).attr('data-content-id');
        title   = $(this).attr('data-title');
        content = $(this).attr('data-content');
        console.log(content);
        $("#hidden_content_id").val(id);
        $("#edit_title_val").val(title);
        $('#edit_content_val').summernote('code',content);
        $("#UpdateContentForm").modal("show");
    });
});
</script>
@endsection
