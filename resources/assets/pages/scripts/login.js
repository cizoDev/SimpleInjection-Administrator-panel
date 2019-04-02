var Login = function() {

    var handleLogin = function() {

        $('.login-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                username: {
                    required: "Username is required."
                },
                password: {
                    required: "Password is required."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-danger', $('.login-form')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {

                //define form data
                var fd = new FormData();
                //append data                
                $.each($('.login-form').serializeArray(), function (i, obj) {
                    fd.append(obj.name, obj.value)
                })

                $.ajax({
                    url: BASEURL + '/manage/dologin',
                    type: "post",
                    processData: false,
                    contentType: false,
                    data: fd,
                    beforeSend: function () {
                    },
                    success: function (res) {
                        if (res.status == '1')// in case genre added successfully
                        {
                            swal({
                                title: "Success!!",
                                text: res.message + ' Redirecting....',
                                type: "success",
                                showConfirmButton: false
                            });
                            $('.login-form')[0].reset();
                            //redirect to dashboard
                            setTimeout(function () {//redirect to dashboard after 3 seconds
                                location.href = BASEURL + '/' + '/manage/dashboard';
                            }, 2500);


                        } else { // in case error occuer
                            swal({
                                title: "Error!!",
                                text: res.message,
                                type: "error",
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Try Again!",
                            });
                            return false;
                        }
                    },
                    error: function (e) {

                        swal({
                            title: "Error!!",
                            text: e.statusText,
                            type: "error",
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Try Again!",
                        });
                        //return false
                        return false;
                    },
                    complete: function () {
                    }
                }, "json");
                return false;
            }
        });

        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $('.login-form').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    }

    var handleForgetPassword = function() {
        $('#forgot-password').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                email_fp: {
                    required: true,
                    email: true
                }
            },
            messages: {
                 email_fp: {
                    required: "Email is required."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   

            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {

               //define form data
                var fd = new FormData();
                //append data                
                $.each($('#forgot-password').serializeArray(), function (i, obj) {
                    fd.append(obj.name, obj.value)
                })

                $.ajax({
                    url: BASEURL + '/manage/forgotpasswordapp',
                    type: "post",
                    processData: false,
                    contentType: false,
                    data: fd,
                    beforeSend: function () {
                    },
                    success: function (res) {
                        console.log(res);
                        if (res.success == '1')// in case genre added successfully
                        {
                            swal({
                                title: "Success!!",
                                text: res.message,
                                type: "success",
                                showConfirmButton: true
                            });
                            $('#forgot-password')[0].reset();
                        } else { // in case error occuer
                            swal({
                                title: "Error!!",
                                text: res.message,
                                type: "error",
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Try Again!",
                            });
                            return false;
                        }
                    },
                    error: function (e) {

                        swal({
                            title: "Error!!",
                            text: e.statusText,
                            type: "error",
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Try Again!",
                        });
                        //return false
                        return false;
                    },
                    complete: function () {
                    }
                }, "json");
                return false;
            }
        });

        $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit();
                }
                return false;
            }
        });

        jQuery('#forget-password').click(function() {
            jQuery('.login-form').hide();
            jQuery('.forget-form').show();
        });

        jQuery('#back-btn').click(function() {
            jQuery('.login-form').show();
            jQuery('.forget-form').hide();
        });

    }

    return {
        //main function to initiate the module
        init: function() {
            handleLogin();
            handleForgetPassword();
        }

    };

}();

jQuery(document).ready(function() {
    Login.init();
});