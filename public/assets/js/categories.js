$(document).ready(function() {
    var base_url = window.location.origin;

    $("body").on("click", ".viewcatdet", function() {
        var cat_id = $(this).attr("data-category_id");
        var catname = $(this).attr("data-categoryname");
        var catdesc = $(this).attr("data-categorydesc");
        var cat_img = $(this).attr("data-cat_img");
        var cat_uploader = $(this).attr("data-cat_uploader");
        var cat_products = $(this).attr("data-cat_products");
        var created_at = $(this).attr("data-cat_created_dt");
        var cat_remarks = $(this).attr("data-cat_remarks");
        var cat_reason = $(this).attr("data-cat_reason");

        $("#update_category_id").val(cat_id);
        $("#category_title").html(catname + " Details");
        $("#cat_img_show_id").attr("src", cat_img);
        $("#cat_title_show").html(catname);
        $("#cat_desc_show").html(catdesc);
        $("#product_count_show").html(cat_products);
        $("#uploaded_show_id").html(cat_uploader);
        // $("#new_cat_reason").html(cat_reason);
        $("#created_dt_show").html(created_at);
        $("#product_count_row").show();
        $("#verify_form").hide();
        $("#ShowCategoryDetail").modal("show");
        $("#approvalerror").hide();
    });

    $("body").on("click", ".verify_category", function() {
        var cat_id = $(this).attr("data-category_id");
        var catname = $(this).attr("data-categoryname");
        var catdesc = $(this).attr("data-categorydesc");
        var cat_img = $(this).attr("data-cat_img");
        var cat_uploader = $(this).attr("data-cat_uploader");
        var cat_products = $(this).attr("data-cat_products");
        var created_at = $(this).attr("data-cat_created_dt");
        var cat_remarks = $(this).attr("data-cat_remarks");
        var cat_reason = $(this).attr("data-cat_reason");
        $("#update_category_id").val(cat_id);
        $('.new_cat_request_reason').html(cat_reason);
        $("#category_title").html(catname + " Details");
        $("#cat_img_show_id").attr("src", cat_img);
        $("#cat_title_show").html(catname);
        $("#cat_desc_show").html(catdesc);
        $("#product_count_show").html(cat_products);
        if (cat_uploader === '')
            uploaded = 'Admin'
        else
            uploaded = cat_uploader

        $("#uploaded_show_id").html(uploaded);
        $("#new_cat_reason").html(cat_reason);
        $("#created_dt_show").html(created_at);
        $("#product_count_row").hide();
        $("#verify_form").show();
        $("#approvalerror").hide();
        $("#ShowCategoryDetail").modal("show");
    });

    $("body").on('click', '#submit_verified_form', function() {
        var formdata = {};
        formdata.cat_id = $("#update_category_id").val();
        formdata.approval_type = $('input[name="approval"]:checked').val();
        formdata.approval_remarks = $(".approval_remarks").val();
        $.ajax({
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            url: base_url + "/admin/categories/verify_category",
            data: formdata,
            dataType: "JSON",
            success: function(msg) {
                console.log(msg);
                if (msg['status'] == true) {
                    window.location.href = base_url + "/admin/categories/";
                    $("#approvalerror").hide();
                    $("#ShowCategoryDetail").modal("hide");
                } else {
                    $("#ShowCategoryDetail").modal("show");
                    $("#approvalerror").show();
                    $("#approvalerrormsg").html(msg['message']);
                }
            }
        });
    });

    $("body").on("click", ".edit_category", function() {
        var cat_id = $(this).attr("data-category_id");
        var catname = $(this).attr("data-categoryname");
        var catdesc = $(this).attr("data-categorydesc");
        var cat_img = $(this).attr("data-cat_img");
        $("#edit_category_id").val(cat_id);
        $("#cat_curr_img").attr("src", cat_img);
        $("#Edit_cat_name").val(catname);
        $("#edit_desc_id").val(catdesc);
        $("#EditCategoryModel").modal("show");
    });


    $('#EditCategory').validate({ // your rules and options,
        rules: {
            name: {
                required: true,
            },
            description: {
                required: true,
            }
        },
        messages: {
            name: {
                required: "<font color='red'>Please provide a title for Category</font>",
            },
            description: {
                required: "<font color='red'>Please provide Description</font>",
            },
        },
        submitHandler: function(form) {
            var data = new FormData(form);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: base_url + "/admin/categories/UpdateCategory",
                type: "POST",
                dataType: 'json',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result['status'] == true) {
                        window.location.href = base_url + "/admin/categories/";
                        $('#EditCategoryModel').modal('hide');
                        iziToast.success({
                            timeout: 3000,
                            id: 'success',
                            title: 'Success',
                            message: result['message'],
                            position: 'bottomRight',
                            transitionIn: 'bounceInLeft',
                        });
                    } else {
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

});