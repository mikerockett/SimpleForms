/*--
 * SimpleForms for ProcessWire
 * A simple form processor. Uses AJAX, configurable with JSON. Front-end is up to you.
 *
 * Front-end jQuery plugin
 *
 * Copyright (c) 2015, Mike Rockett. All Rights Reserved.
 * Licence: MIT License - http://mit-license.org/
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

            // Check for plurals/singulars
            if (input.match(pluralise)) {
                input = input.replace(pluralise, (argument === 1 || argument.toLowerCase() === 'one') ? "$1" : "$2");
            }
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
     * Convert numbers to words
     * @copyright 2006, Stephen Chapman
     * @see http://javascript.about.com
     * @param  string s input
     * @return string   converted input
     */
    var toWords = function(s) {
        var th = ['', 'thousand', 'million', 'billion', 'trillion'];
        var dg = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var tn = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        var tw = ['twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        s = s.toString();
        s = s.replace(/[\, ]/g, '');
        if (s != parseFloat(s)) {
            return 'not a number';
        }
        var x = s.indexOf('.');
        if (x == -1) {
            x = s.length;
        }
        if (x > 15) {
            return 'too big';
        }
        var n = s.split('');
        var str = '';
        var sk = 0;
        for (var i = 0; i < x; i++) {
            if ((x - i) % 3 == 2) {
                if (n[i] == '1') {
                    str += tn[Number(n[i + 1])] + ' ';
                    i++;
                    sk = 1;
                } else if (n[i] != 0) {
                    str += tw[n[i] - 2] + ' ';
                    sk = 1;
                }
            } else if (n[i] != 0) {
                str += dg[n[i]] + ' ';
                if ((x - i) % 3 == 0) str += 'hundred ';
                sk = 1;
            }
            if ((x - i) % 3 == 1) {
                if (sk) str += th[(x - i - 1) / 3] + ' ';
                sk = 0;
            }
        }
        if (x != s.length) {
            var y = s.length;
            str += 'point ';
            for (var i = x + 1; i < y; i++) {
                str += dg[n[i]] + ' ';
            }
        }

        return str.replace(/\s+/g, ' ').trim();
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
                var formElements = $(':input', form);
                switch (state) {
                    case 0:
                        formElements.prop('disabled', true);
                        break;
                    case 1:
                        formElements.prop('disabled', false);
                        break;
                }
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
                        var errorCountWords = toWords(errorCount);
                        var errorNotification = plate(responseData.error, errorCountWords, errorCount);

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
