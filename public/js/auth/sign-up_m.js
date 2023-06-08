const form = document.getElementById('sign-up-form');

var validator = FormValidation.formValidation(form, {
    fields: {
        'name': {
            validators: {
                notEmpty: {
                    message: 'Name is required'
                }
            }
        },
        'email': {
            validators: {
                notEmpty: {
                    message: 'Email is required'
                },
                emailAddress: {
                    message: 'Email is invalid'
                },
                remote: {
                    message: 'Email is not available or has been used by other',
                    method: 'GET',
                    url: '/check-email',
                }
            }
        },
        'username': {
            validators: {
                notEmpty: {
                    message: 'Username is required'
                },
                remote: {
                    message: 'Username is not available or has been used by other',
                    data: function () {
                        return {
                            username: form.querySelector('[name="username"]').value,
                        };
                    },
                    method: 'GET',
                    url: '/check-username'
                },
                regexp: {
                    message: 'Username is invalid',
                    regexp: '^(?=[a-zA-Z0-9._]{4,20}$)(?!.*[_.]{2})[^_.].*[^_.]$'
                }
            }
        },
        'password': {
            validators: {
                notEmpty: {
                    message: 'Password is required'
                }
            }
        },
        'password_confirmation': {
            validators: {
                identical: {
                    compare: function() {
                        return form.querySelector('[name="password"]').value;
                    },
                    message: 'The password and its confirm are not the same'
                }
            }
        }
    },
    plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap: new FormValidation.plugins.Bootstrap5({
            rowSelector: '.fv-row',
            eleInvalidClass: '',
            eleValidClass: ''
        })
    }
});

const submitButton = document.getElementById('sign-up-submit');
submitButton.addEventListener('click', function (e) {
    e.preventDefault();

    // validate form before submit
    if (validator) {
        validator.validate().then(function (status){
            if (status == 'Valid') {
                submitButton.disabled = true;
                form.submit();
            }
        });
    }
});