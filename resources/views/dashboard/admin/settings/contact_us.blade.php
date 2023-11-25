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
              <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
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
                        <div class="p-2 flex-grow-1">
                            <h4 class="card-title">Contact Lists</h4>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>MOBILE</th>
                                    <th>INTEREST TO SELL</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($contactuslist) != 0)
                                    @foreach ($contactuslist as $key=>$contact)
                                    <tr>
                                        <td scope="row">{{ $key + $contactuslist->firstItem() }}</td>
                                        <td>{{ $contact->first_name }}</td>
                                        <td><a class="badge badge-primary" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                                        <td><a class="badge badge-dark" href="tel:+ {{ $contact->mobile }}">{{ $contact->mobile }}</a></td>
                                        <td>
                                            @if($contact->interest_to_sell == '1')
                                                <span class="badge badge-pill badge-success">YES</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">NO</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#"
                                                @if(is_null($contact->replied_at))
                                                class="btn btn-sm btn-inverse-warning view_message"
                                                @else
                                                class="btn btn-sm btn-inverse-success view_message"
                                                @endif
                                            data-contact-id="{{ $contact->id }}"
                                            data-name="{{ $contact->first_name }}" data-email="{{ $contact->email }}" data-createddt="{{ date("F j, Y, g:i a", strtotime($contact->created_at)) }}"
                                            data-mobile="{{ $contact->mobile }}" data-interested_toSell="{{ $contact->interest_to_sell }}"
                                            data-message="{{ $contact->message }}" data-reply="{{ $contact->reply }}" data-replieddt="{{ date("F j, Y, g:i a", strtotime($contact->replied_at)) }}">
                                            @if(is_null($contact->replied_at))
                                            <i class="mdi mdi-message-alert"></i> Reply</a>
                                            @else
                                            <i class="mdi mdi-message-text"></i> View</a>
                                            @endif

                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6">No Messages Yet</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="mt-2 float-right">
                            {!! $contactuslist->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ViewContactMessage" tabindex="-1" role="dialog" aria-labelledby="viewAddContentFormLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Contact Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title" id="cust_name"></h4>
                        <h6 class="card-subtitle text-muted" id="cust_contact_det"></h6>
                        <p class="mt-4">Message <span class="float-right badge badge-pill badge-dark" id="send_date"></span></p>
                        <div class="border border-1 border-secondary rounded p-2 mb-3">
                            <p id="message_data"></p>
                        </div>
                        <div id="replied_id">
                            <p class="mt-4">Replied <span class="float-right badge badge-pill badge-dark" id="replied_date"></span></p>
                            <div class="border border-1 border-secondary rounded p-2 mb-3">
                                <p id="reply_data"></p>
                            </div>
                        </div>
                        <div id="reply_form_id">
                            <form action="{{ Route('admin.ReplyContactus')}}" method="post">
                                @csrf
                                <input type="hidden" name="contact_id" id="hidden_contact_val" value="">
                                <div class="form-group">
                                    <label for="my-textarea">Reply</label>
                                    <textarea id="my-textarea2" class="form-control" name="reply" required rows="6"></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-secondary mx-2" data-dismiss="modal">Close</button>
                                    <button type="Submit" class="btn btn-sm btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
      </div>
    </div>
</div>


@endsection

@section('pagescript')
<script>
$(document).ready(function(){
    $("body").on("click",".view_message",function(){
        id          =   $(this).attr('data-contact-id');
        name        =   $(this).attr('data-name');
        email       =   $(this).attr('data-email');
        mobile      =   $(this).attr('data-mobile');
        interest_sell = $(this).attr('data-interested_toSell');
        message     =   $(this).attr('data-message');
        reply       =   $(this).attr('data-reply');
        msg_date    =   $(this).attr('data-createddt');
        reply_dt    =   $(this).attr('data-replieddt');
        contact_html    =   '';
        contact_html    +=  '<a class="badge badge-primary mx-1" href="mailto:'+email+'">'+email+'</a>';
        contact_html    +=  '<a class="badge badge-info mx-1" href="tel:+'+mobile+'">'+mobile+'</a>';
        if(interest_sell == '1')
        contact_html    +=  '<span class="badge badge-pill badge-success mx-1">Interested to Sell</span>';
        $("#hidden_contact_val").val(id);
        $("#cust_name").html(name);
        $("#cust_contact_det").html(contact_html);
        $("#message_data").html(message);
        $("#send_date").html(msg_date);
        if(reply === ''){
            $("#replied_id").hide();
            $("#reply_form_id").show();
        } else {
            $("#replied_id").show();
            $("#reply_form_id").hide();
            $("#reply_data").html(reply);
            $("#replied_date").html(reply_dt);
        }
        $("#ViewContactMessage").modal("show");
    });
});
</script>
@endsection
