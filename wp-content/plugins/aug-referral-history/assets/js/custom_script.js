jQuery(document).ready(function($){

    // Referral code validation
    $('#referral').on('input', function() {
        var referral_code = $(this).val();

        if (referral_code.length != 0) {
            $('#reg_user').prop('disabled', true);
            
            if (referral_code.length > 3) {
                
                jQuery.ajax({
                    url: my_ajax_object.ajax_url,
                    type: "POST",
                    dataType: 'json',
                    data: ({
                        action: 'fn_validate_referral',
                        referral_code: referral_code
                    }),
                    beforeSend: function() {},
                    success: function(data) {
                        console.log(data.counter)
                        jQuery('#ref-status').html(data.message);
                    },
                    complete: function() {
                        $('#reg_user').prop('disabled', false);
                    }
                });
            }
        } else {
            $('#reg_user').prop('disabled', false);
        }
    });

    // Register user call
    jQuery('form#referral-form').submit(function(e) {

        e.preventDefault();

        jQuery.ajax({
            url: my_ajax_object.ajax_url,
            type: "POST",
            dataType: 'json',
            data: ({
                action: 'fn_register_user',
                first_name: jQuery('#reg_firstName').val(),
                last_name: jQuery('#reg_lastName').val(),
                email: jQuery('#reg_email').val(),
                password: jQuery('#reg_password').val(),
                referral_code: jQuery('#referral').val()
            }),
            beforeSend: function() {},
            success: function(data) {
                
                if (data.loggedin == false) {
                    jQuery('.reg_form_error_message').html('<strong>Error: </strong>An Account Is Already Registered With Your Email Address.');
                    return false;
                } else {
                    location.reload();
                }
            },
            complete: function() {}
        });
        return false;

    });

});