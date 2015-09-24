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
            var formatter = new RegExp("\\{(" + ucfirstMethod + "\:)?" + i + "\\}", "gm");
            var pluralise = new RegExp("\\[([a-z]+)\\|([a-z]+):(" + i + ")\\]", "gmi");
            var argument = arguments[i + 1];

            // Detech ucfirst as toWords() always returns lowercase.
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
        var result = 'data-simpleforms-' + name;
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
            serverErrorAlert: 'Something went wrong on the server, and so this form could not be submitted. The form has been left as-is so that you may leave it open and try again in a few minutes.',
            scrollTime: 500,
            onSubmitStart: function() {},
            onSubmitEnd: function() {},
            onServerError: null,
        }
        var configuration = $.extend(defaultConfiguration, userConfiguration);

        $('input[type=submit]', this).val(function() {
            return $(this).data('value');
        });

        // Register a new handler for the form's submit event.
        this.on('submit', function(event) {

            // Halt the default handler.
            event.preventDefault();

            // Register vars specific to this form.
            var _this = $(this);
            var serialisedData = _this.sf_serialiseObject();

            // Set the formState method, which enables and disables
            // the form, as required.
            var formState = function(state) {
                var formElements = $(':input', _this);
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
                // Disable the form.
                formState(0);
                $('input[type=submit]').val(configuration.interimButtonText);
                if ($.isFunction(configuration.onSubmitStart)) {
                    configuration.onSubmitStart();
                }
            }
            var submitEnd = function() {
                // Enable the form.
                formState(1);
                $('input[type=submit]', _this).val(function() {
                    return $(this).data('value');
                });
                if ($.isFunction(configuration.onSubmitEnd)) {
                    configuration.onSubmitEnd();
                }
            }

            // Disable the form and do any callbacks.
            submitStart();

            // AJAXify!
            $.ajax({
                async: true,
                data: serialisedData,
                dataType: 'json',
                global: false,
                type: 'post',
                method: 'post',
                url: _this.attr('action'),
                statusCode: {

                    // Internal Server Error
                    500: function() {
                        // Upon a server error, call the specified user method,
                        // or fallback to default alert (string is configurable).
                        if ($.isFunction(configuration.onServerError)) {
                            configuration.onServerError();
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

                        // Remove any existing errors.
                        $(dataSelector('formerror'), _this).hide().html(errorNotification).show();
                        $(dataSelector('fielderror'), _this).hide();
                        $(':input', _this).removeAttr(dataSelector('fieldhaserror', false));

                        // Now display the validation errors to the user.
                        $.each(errors, function(inputObject, message) {
                            if (message instanceof Array) {
                                console.log(message[0]);
                            }
                            var _inputObject = plate('[name={0}]', inputObject);
                            $(_inputObject, _this).attr(dataSelector('fieldhaserror', false), '');
                            $(plate('[{0}={1}]', dataSelector('fielderror', false), inputObject), _this).html(message).show();
                        });

                        // Focus on the first error field
                        var firstKey = function() {
                            for (var props in errors) {
                                return props;
                            }
                        }
                        $(plate('[name={0}]', firstKey()), _this).focus();

                    }
                },
            }).always(function() {

                // Scroll to top of form.
                $('html, body').animate({
                    scrollTop: _this.offset().top
                }, configuration.scrollTime);

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
        return "[data-simpleforms!=''][data-simpleforms]";
    }

    /**
     * Just prepare the forms already.
     * Even better.
     */
    window.simpleForms = function(configuration) {
        $(allSimpleForms()).prepareSimpleForms();
    }

})(jQuery, window);
