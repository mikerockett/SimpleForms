/**
 * SimpleForms for ProcessWire
 * A simple form processor. Uses AJAX, configurable with JSON. Front-end is up to you.
 *
 * Front-end jQuery plugin
 *
 * @copyright 2015-2017, Mike Rockett. All Rights Reserved.
 * @license MIT License - http://mit-license.org/
 */

(function($, window) {

    /**
     * String template parser.
     * @return string
     */
    var plate = function() {
        var input = arguments[0];
        var ucfirstMethod = 'ucfirst';

        // Loop through each argument, checking for replacements.
        for (var i = 0; i < arguments.length - 1; i++) {
            var formatter = new RegExp("\\{(" + ucfirstMethod + "\:)?" + i + "\\}", "igm");
            var pluralise = new RegExp("\\[([a-z]+)\\|([a-z]+):(" + i + ")\\]", "igm");
            var argument = arguments[i + 1];

            // Detect ucfirst as toWords() always returns lowercase.
            if (input.match(formatter) && input.match(formatter)[0].indexOf(ucfirstMethod) >= 0) {
                argument = argument.charAt(0).toUpperCase() + argument.slice(1);
            }

            // Replace the input
            input = input.replace(formatter, argument);
        }

        return input;
    }

    var dataSelector = function(name, attr) {
        if (typeof attr === "undefined" || attr === null) {
            attr = true;
        }
        var result = 'data-sf-' + name;
        if (attr === true) {
            result = plate('[{0}]', result);
        }

        return result;
    }

    /**
     * Serialise form object
     * @return array
     */
    $.fn.sf_serialiseObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });

        return o;
    };

    /**
     * Get the object length (count)
     * @param  Object object
     * @return int
     */
    var objectLength = function(obj) {
        // Modern method.
        if (Object.keys) {
            return Object.keys(obj).length;
        }
        // Classic method.
        var result = 0;
        for (var prop in obj) {
            if (this.hasOwnProperty(prop)) {
                result++;
            }
        }

        return result;
    }



    /**
     * SimpleForms plugin
     * @param  array userConfiguration The user's config input
     * @return void
     */
    $.fn.prepareSimpleForms = function(userConfiguration) {

        // Set the default configuration.
        var defaultConfiguration = {
            interimButtonText: 'Just a moment...',
            logErrors: true,
            processingClass: 'processing',
            scroll: {
                enabled: true,
                offset: 0,
                duration: 500,
                easing: 'swing',
            },
            serverErrorAlert: 'Something went wrong on the server, and so this form could not be submitted. The form has been left as-is so that you may leave it open and try again in a few minutes.',
            events: {

                // Supplementary methods
                submitStart: function(form) {},
                submitEnd: function(form) {},

                // Override methods
                success: null, // function(form, message)
                failure: null, // function(form, errors, message)
                serverError: null, // function(message)
            }
        }
        var configuration = $.extend(true, defaultConfiguration, userConfiguration);

        // Get the form object, for use in methods
        var form = this;

        // Enable "disabled" persistence.
        $(':input[disabled]', form).attr(dataSelector('disabled', false), 'disabled');

        // Check any inputmask/formatter.js implementations from config.
        $.each(['inputmask|mask', 'formatter|pattern'], function(index, opts) {
            var opts = opts.split("|");
            var plugin = opts[0];
            var matcher = opts[1];
            if (configuration[plugin] && $.fn[plugin]) {
                $.each(configuration[plugin], function(input, args) {
                    if ($.type(args) === "string") {
                        var str = args;
                        var args = {};
                        args[matcher] = str;
                    }
                    $(plate('[name={0}]', input), form)[plugin](args);
                })
            }
        });

        // Initialise the submit button text.
        $('input[type=submit]', this).val(function() {
            return $(this).data('value');
        });

        // Register a new handler for the form's submit event.
        this.on('submit', function(event) {

            // Halt the default handler.
            event.preventDefault();

            // Register vars specific to this form.
            var formData = new FormData(form[0]);

            // Set the formState method, which enables and disables
            // the form, as required.
            var formState = function(state) {
                var formElements = $(plate(':input:not([{0}])',dataSelector('disabled', false)), form);
                formElements.prop('disabled', !state);
            }

            // Set before and after methods
            var submitStart = function() {

                // Add processingClass to form
                form.addClass(configuration.processingClass);

                // Disable the form.
                formState(0);

                // Change button text
                $('input[type=submit]', form).val(configuration.interimButtonText);

                // Call user function
                if ($.isFunction(configuration.events.submitStart)) {
                    configuration.events.submitStart(form);
                }
            }
            var submitEnd = function() {

                // Remove processingClass from form
                form.removeClass(configuration.processingClass);

                // Enable the form.
                formState(1);

                // Restore button text
                $('input[type=submit]', form).val(function() {
                    return $(this).data('value');
                });

                // Call user function
                if ($.isFunction(configuration.events.submitEnd)) {
                    configuration.events.submitEnd(form);
                }
            }

            // Disable the form and do any callbacks.
            submitStart();

            // AJAXify!
            $.ajax(form.attr('action'), {
                async: true,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                type: 'post',
                method: 'post',
                statusCode: {

                    // Internal Server Error
                    500: function(response) {

                        // Parse the responseText
                        var responseData = $.parseJSON(response.responseText);

                        // Log server error and then call the specified user method
                        // or fallback to default alert (string is configurable).
                        if (configuration.logErrors === true) {
                            console.log(responseData.error);
                        }
                        if ($.isFunction(configuration.events.serverError)) {
                            configuration.events.serverError(responseData.error);
                        } else {
                            alert(configuration.serverErrorAlert);
                        }
                    },

                    // Unprocessable Entity
                    422: function(response) {

                        // Parse the responseText
                        var responseData = $.parseJSON(response.responseText);

                        // Fetch the validation errors.
                        var errors = responseData.errors;

                        // Set the error notification text.
                        var errorCount = objectLength(errors);
                        var errorNotification = responseData.error;

                        // Call user method or fallback to default.
                        if ($.isFunction(configuration.events.failure)) {
                            configuration.events.failure(form, errors, errorNotification);
                        } else {

                            // Remove any existing errors.
                            $(dataSelector('formerror'), form).hide().html(errorNotification).show();
                            $(dataSelector('fielderror'), form).hide();
                            $(':input', form).removeAttr(dataSelector('fieldhaserror', false));

                            // Now display the validation errors to the user.
                            $.each(errors, function(inputObject, message) {
                                if (message instanceof Array) {
                                    console.log(message[0]);
                                }
                                var _inputObject = plate('[name={0}]', inputObject);
                                $(_inputObject, form).attr(dataSelector('fieldhaserror', false), '');
                                $(plate('[{0}={1}]', dataSelector('fielderror', false), inputObject), form).html(message).show();
                            });
                        }

                        // Focus on the first error field
                        var firstKey = function() {
                            for (var props in errors) {
                                return props;
                            }
                        }
                        $(plate('[name={0}]', firstKey()), form).focus();
                    },

                    // OK
                    200: function(response) {

                        // Call user event or fallback to default message.
                        if ($.isFunction(configuration.events.success)) {
                            configuration.events.success(form, response.success);
                        } else {
                            form.html(plate('<div data-sf-success class="sfSuccessMessage">{0}</div>', response.success));
                        }
                    }
                },
            }).always(function() {

                // Scroll to top of form.
                if (configuration.scroll.enabled) {
                    $('html,body').animate({
                        scrollTop: form.offset().top - configuration.scroll.offset,
                    }, {
                        duration: configuration.scroll.duration,
                        easing: configuration.scroll.easing,
                    });
                }

                // Enable the form and perform any callbacks.
                submitEnd();
            });

            return true;
        });
    }

    /**
     * Form selector method.
     * Quite handy indeed.
     */
    window.allSimpleForms = function() {
        return "[data-sf!=''][data-sf]";
    }

    /**
     * Just prepare the forms already.
     * Even better.
     */
    window.simpleForms = function(configuration) {
        $(allSimpleForms()).prepareSimpleForms(configuration);
    }

})(jQuery, window);
