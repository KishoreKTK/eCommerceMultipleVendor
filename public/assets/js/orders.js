$('document').ready(function() {
    // base_url    = windw.location.origin;
    var base_url = window.location.origin;
    // Owl Carousel
    var current_route = $("#current_route_name").val();
    var api_url = base_url + '/' + current_route;

    $("body").on("click", ".update_order_status", function() {
        $('#cover-spin').show();
        var order_type = $(this).attr('data-order_type');
        var payment_status = $(this).attr('data-payment_status');
        $("#intial_order_acceptance").hide();
        $("#status_needs_images").hide();
        var sub_attr = $(this).attr("data-suborder_id");
        var curr_status = parseFloat($(this).attr("data-current_status"));
        // Order Type =>  1 - Pickup; 2 - Deliver;

        // Order Status List
        // -- -- -- -- -- --
        // 1 Order Placed
        // 2 Order Declined
        // 3 Order Accepted
        // 4 Order in Progress
        // 5 Awaiting Pickup
        // 6 Shipped
        // 7 Delivered
        // 8 Completed
        // 9 Refund Initiated
        // 10 Refund Completed

        // Order Status are Static comes from Table.
        if (curr_status == 3 && order_type == 1) {
            var newcurstatus = curr_status + 2;
        } else if (curr_status == 3 && order_type == 2) {
            var newcurstatus = curr_status + 3;
        } else if (curr_status == 5 && order_type == 1) {
            var newcurstatus = curr_status + 3;
        } else if (curr_status == 2) {
            var newcurstatus = 10;
        } else {
            var newcurstatus = curr_status + 1;
        }
        order_id = $(this).attr("data-order_id");
        $.ajax({
            type: "GET",
            url: api_url + '/order/GetOrderStatus?order_type=' + order_type + '&curr_status=' + curr_status,
            success: function(msg) {
                $('#cover-spin').hide();
                if (msg['status'] == true) {
                    li_html = '';
                    $.each(msg['data'], function(ex, status) {
                        let curloopid = status.id;
                        if (curloopid != 2) {
                            if (curloopid < curr_status) {
                                li_html += '<li><del>' + status.name + '</del></li>';
                            } else if (curloopid == curr_status) {
                                li_html += '<li><b><span class="text-primary">' + status.name + '</span></b></li>';
                            } else if (curr_status == 2) {
                                if (curloopid == 9) {
                                    li_html += '<li><b><span class="text-primary">' + status.name + '</span></b></li>';
                                } else {
                                    li_html += '<li><span class="text-muted">' + status.name + '</span></li>';
                                }
                            } else {
                                li_html += '<li><span class="text-muted">' + status.name + '</span></li>';
                            }

                            statusname = status.name;

                            if (curr_status != 1 && curloopid == newcurstatus) {
                                if (payment_status == 0 && newcurstatus == 8) {
                                    $("#update_status_form").hide();
                                    warning_html = '<div class="alert alert-danger" role="alert">Please update the payment status to complete this order! <div > ';
                                    $("#order_action_id").html(warning_html);
                                } else {
                                    $("#order_action_id").html('Move to ' + statusname + '');
                                }

                                $("#new_order_status_name").val(statusname);
                            }

                            if (curr_status == 1) {
                                $("#order_action_id").html('Take Action');
                                $("#new_order_status_name").val(statusname);
                            }
                        }
                    });

                    $("#ListOrderStatus").html(li_html);
                    if (curr_status == 1) {
                        $("#intial_order_acceptance").show();
                    }

                    $("#sub_attr_id").val(sub_attr);
                    $("#new_hidden_status_id").val(newcurstatus);
                    $("#curr_order_id").val(order_id);
                    $("#curr_status_id").val(curr_status);
                    $("#UpdateOrderStatus").modal('show');
                } else {
                    $("#intial_order_acceptance").hide();
                    $("#status_needs_images").hide();
                    iziToast.error({
                        timeout: 3000,
                        id: 'error',
                        title: 'Error',
                        message: msg['message'],
                        position: 'topRight',
                        transitionIn: 'fadeInDown'
                    });
                }
            }
        });
    });

    // Order Status Update form
    $('#update_status_form').validate({ // your rules and options,
        rules: {
            remarks: {
                // required: true,
                minlength: 2,
                maxlength: 250
            }
        },
        messages: {
            remarks: {
                // required: "<font color='red'>Please provide Remarks</font>",
                minlength: "<font color='red'>Enter Remarks with minimum 6 charecters</font>"
            }
        },
        submitHandler: function(form) {
            var data = new FormData(form);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: api_url + '/order/UpdateOrderStatus',
                type: "POST",
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#cover-spin').show();
                },
                success: function(result) {
                    if (result['status'] == true) {
                        location.reload();
                    } else {
                        $('#cover-spin').hide();
                        $('#approval_status').modal('hide');
                        iziToast.error({
                            timeout: 3000,
                            id: 'error',
                            title: 'Error',
                            message: result['message'],
                            position: 'topRight',
                            transitionIn: 'fadeInDown'
                        });
                    }
                }
            });
        }
    });

    $("body").on("click", ".view_status_track_details", function() {
        $("#track_table_id").show();
        $("#show_images_id").hide();
        var sub_order_id = $(this).attr("data-suborder_id");
        var order_id = $(this).attr("data-order_id");
        var order_type = $(this).attr('data-order_type');
        $.ajax({
            type: "GET",
            url: api_url + '/order/GetOrderTrack?sub_order_id=' + sub_order_id,
            beforeSend: function() {
                $('#cover-spin').show();
            },
            success: function(msg) {
                if (msg['status'] == true) {
                    var table_html = '';
                    var sno = 0;
                    $.each(msg['data'], function(ex, track) {
                        sno++;
                        table_html += '<tr>';
                        table_html += '<td>' + sno + '</td>';
                        table_html += '<td>' + track.status_name + '</td>';
                        table_html += '<td>' + track.remarks + '</td>';
                        table_html += '<td>' + track.orderdate + '</td>';
                        table_html += '</tr>';
                    });
                    $("#track_status_data").html(table_html);
                    $('#cover-spin').hide();
                    $("#ViewTrackDetails").modal("show");
                } else {
                    $('#cover-spin').hide();
                    iziToast.error({
                        timeout: 3000,
                        id: 'error',
                        title: 'Error',
                        message: msg['message'],
                        position: 'topRight',
                        transitionIn: 'fadeInDown'
                    });
                }
            }
        });
    });

    $("body").on('click', '.show_status_images', function() {
        var images = $(this).attr("data-images");
        var statusname = $(this).attr("data-status_name");
        let imgArr = images.split("|");
        let img_html = '';

        $.each(imgArr, function(e, img) {
            img_html += '<div class="col-md-4"><img class="img-responsive img-fluid m-2" src="' + base_url + '/' + img + ' " alt="Status Images"></div>';
        });
        $("#status_img_heading").html(statusname);
        $(".display_images").html(img_html);
        //  height="400px" width="400px"
        // $(this).trigger('destroy.owl-carousel');
        // $('.owl-carousel').data('owlCarousel').destroy();
        // $('.owl-carousel').trigger('refresh.owl.carousel');
        // $('.owl-carousel').refresh();
        // $('.owl-carousel').owlCarousel({
        //     items: 1,
        //     loop: true,
        //     margin: 10,
        //     nav: true,
        // });
        $("#track_table_id").hide();
        $("#show_images_id").show();
    });

    $("body").on("click", ".back_to_track", function() {
        $("#track_table_id").show();
        $("#show_images_id").hide();

    });
});