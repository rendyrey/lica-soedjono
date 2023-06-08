/* ------------------------------------------------------------------------------
 *
 *  # Form validation
 *
 *  Demo JS code for form_validation.html page
 *
 * ---------------------------------------------------------------------------- */


// Setup module
// ------------------------------

var FormValidation = function() {
    //
    // Setup module components
    //

    // Validation config
    var _componentValidation = function() {
        if (!$().validate) {
            console.warn('Warning - validate.min.js is not loaded.');
            return;
        }

        // Initialize
        var validator = $('.form-validate-jquery').validate({
            ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
            errorClass: 'fv-plugins-message-container invalid-feedback',
            successClass: 'validation-valid-label',
            validClass: 'validation-valid-label',
            highlight: function(element, errorClass) {
                $(element).removeClass(errorClass);
            },
            unhighlight: function(element, errorClass) {
                $(element).removeClass(errorClass);
            },
            // success: function(label) {
            //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
            // },

            // Different components require proper error label placement
            errorPlacement: function(error, element) {

                // Unstyled checkboxes, radios
                if (element.parents().hasClass('form-check')) {
                    error.appendTo( element.parents('.form-check').parent() );
                }

                // Input with icons and Select2
                else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                    error.appendTo( element.parent() );
                }

                // Input group, styled file input
                else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                    error.appendTo( element.parent().parent() );
                }

                // Other elements
                else {
                    error.insertAfter(element);
                }
            },
            rules: {
                name: {
                    required: true
                },
                username: {
                    minlength: 1,
                    remote: {
                        url: base + '/check-username',
                        type: 'GET',
                        data: {
                            username: function() {
                                return document.querySelector('[name="username"]').value;
                            }
                        }

                    },
                    required: true,
                },
                password: {
                    minlength: 8,
                    required: true
                },
                password_confirmation: {
                    equalTo: '#password'
                },
                email: {
                    minlength: 8,
                    required: true,
                    email: true,
                    remote: {
                        url: base + '/check-email',
                        type: 'GET',
                        data: {
                            email: function() {
                                return document.querySelector('[name="email"]').value;
                            }
                        }

                    },
                }

            },
            messages: {
                email: {
                    required: 'Email is required',
                    minlength: 'Please input at least {0} characters'
                },
                password_confirmation: {
                    equalTo: "The password doesn't match"
                },
                custom: {
                    required: 'This is a custom error message'
                },
                basic_checkbox: {
                    minlength: 'Please select at least {0} checkboxes'
                },
                styled_checkbox: {
                    minlength: 'Please select at least {0} checkboxes'
                },
                switchery_group: {
                    minlength: 'Please select at least {0} switches'
                },
                switch_group: {
                    minlength: 'Please select at least {0} switches'
                },
                agree: 'Please accept our policy'
            }
        });

        // Reset form
        $('#reset').on('click', function() {
            validator.resetForm();
        });
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentValidation();
        }
    }
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
    FormValidation.init();
});
