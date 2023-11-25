$(document).ready(function() {

    // validation for select box
    $.validator.addMethod("valueNotEquals", function(e, t, a) {
        return a !== e
    }, "Please select field.")

    //Multiple Select Box
    $.validator.addMethod("needsSelection", function(value, element) {
        var count = $(element).find('option:selected').length;
        return count > 0;
    });

    function selectRefresh() {
        $(".select_attr_fields-multiple").select2();
    }

    // selectRefresh();
    $("#basic_product_details_section").show();
    $("#product_attributes_section").hide();
    $("#product_stock_combo_section").hide();

    var base_url = window.location.origin;
    var page_type = $("#current_page_type").val();

    // Validates Product Form
    $('#product_form_data').validate({ // your rules and options,
        rules: {
            name: {
                required: true,
                maxlength: 30
            },
            image: {
                required: true
            },
            category_id: {
                valueNotEquals: ''
            },
            description: {
                required: true
            },
            seller_id: {
                valueNotEquals: ''
            },
            // "banner[]": {
            //     needsSelection: true,
            // },
        },
        messages: {
            name: {
                required: "<span class='field_err'>Please provide name</span>",
                maxlength: "<span class='field_err'>Enter name with less than 30 charecters</span>"
            },
            image: {
                required: "<span class='field_err'>Please Upload File</span>"
            },
            category_id: {
                valueNotEquals: "<span class='field_err'>Please select Category</span>",
            },
            seller_id: {
                valueNotEquals: "<span class='field_err'>Please select Seller</span>",
            },
            // "banner[]": {
            //     needsSelection: "<span class='field_err'>Please select at least 1 Image</span>",
            // },
            description: {
                required: "<span class='field_err'>Please provide Description</span>"
            }
        },
        // debug: true,
        // submitHandler: function(form) {
        //     $(this).submit();
        // }
    });

    //=============================================================================================//
    //=================================  First Section ============================================//
    //=============================================================================================//

    // $("body").on("change", "#category_field_id", function() {
    //     let cat_id = $("#category_field_id").val();
    //     if (cat_id != '') {
    //         let ajax_url = '';
    //         if (page_type == "admin")
    //             ajax_url = base_url + '/admin/products/AddProduct';
    //         else
    //             ajax_url = base_url + '/seller/products/AddProduct';
    //         $.ajax({
    //             url: success: function(data) {
    //                 $("#product_attributes_based_on_cat").html(data);
    //             }
    //         });
    //     } else {
    //         iziToast.error({
    //             title: 'Error',
    //             message: 'Please Fill Basic Details to Continue',
    //             position: 'topRight',
    //         });
    //     }
    // });


    // $("body").on("click", ".check_basic_details_btn", function() {
    //     if ($('#product_form_data').valid()) {
    //         $("#basic_product_details_section").hide();
    //         $("#product_attributes_section").show();
    //         $("#product_stock_combo_section").hide();
    //     } else {
    //         iziToast.error({
    //             title: 'Error',
    //             message: 'Please Fill Basic Details to Continue',
    //             position: 'topRight',
    //         });
    //         $("#basic_product_details_section").show();
    //         $("#product_attributes_section").hide();
    //         $("#product_stock_combo_section").hide();
    //     }
    // });


    //=============================================================================================//
    //=================================  Second Section ===========================================//
    //=============================================================================================//

    // $(".back_to_check_basic_details_btn").on("click", function() {
    //     $("#basic_product_details_section").show();
    //     $("#product_attributes_section").hide();
    //     $("#product_stock_combo_section").hide();
    // });

    // $(".check_prdt_attr_btn").on("click", function() {
    //     $("#basic_product_details_section").hide();
    //     $("#product_attributes_section").hide();
    //     $("#product_stock_combo_section").show();
    // });


    //=============================================================================================//
    //=================================  Final Section ============================================//
    //=============================================================================================//
    // $('#price_stock_type a').on('click', function(e) {
    //     e.preventDefault()
    //     $(this).tab('show')
    // })

    // $(".back_to_check_prdt_attr_btn").on("click", function() {
    //     $("#basic_product_details_section").hide();
    //     $("#product_attributes_section").show();
    //     $("#product_stock_combo_section").hide();
    // });

    // $("body").on("click", ".submit_products_form", function(e) {
    //     e.preventDefault();
    //     $("#product_form_data").submit();
    // });


    // Add Specification Fields

    var maxGroup = 10;
    
    //add more fields group
    $(".addMore").click(function() {
        if ($('body').find('.fieldGroup').length < maxGroup) {
            var fieldHTML = '<div class="col-6 m-2 fieldGroup">' + $(".fieldGroupCopy").html() + '</div>';
            $('body').find('.fieldGroup:last').after(fieldHTML);
        }
    });

    //remove fields group
    $("body").on("click", ".remove", function() {
        $(this).parents(".fieldGroup").remove();
    });


    var count = 1;
    $(".add-image").click(function() {
        if (count < 5) {
            var fieldHTML = '<div class="col-md-6 m-2 image-group">' + $(".imagescopy").html() + '</div>';
            $('body').find('.image-group:last').after(fieldHTML);
            count = count + 1;

        }
    });

    $("body").on("click", ".remove-image", function() {
        count = count - 1;
        $(this).parents('.image-group').remove();
    });


});