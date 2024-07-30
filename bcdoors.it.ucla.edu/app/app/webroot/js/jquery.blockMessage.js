/*
 ========================
 ==      REQUIRED      ==
 ========================
 * jquery-1.4.2.min.js
 * jquery.functions.js
 ------------------------
 */
var assetUrl = 'https://assets-nicheprofitclassroom-com.s3.amazonaws.com';
var img_loader = new Image();
img_loader.src = assetUrl + '/img/ajax-loader.gif';
var img_loader_icon = new Image();
img_loader_icon.src = assetUrl + '/img/icons/information.png';
var img_loader_alert = new Image();
img_loader_alert.src = assetUrl + '/img/icons/exclamation.png';
var img_loader_busy = new Image();
img_loader_busy.src = assetUrl + '/img/busy.gif';

;(
    function($)
    {
        jQuery.blockMessage =
        {
            defaultElement: null,
            element:  null,
            backgroundColor: '#000',
            overlayCSS: {},
            css: {},
            visible: false,
            show:  function(element, message, width, baseZ)
            {
                if ($.isDefined(element))
                    $.blockMessage.element = element;
                else if (!$.isEmpty($.blockMessage.defaultElement))
                    $.blockMessage.element = $.blockMessage.defaultElement;

                baseZ = $.isDefined(baseZ) ? baseZ : 1000;
                width = (width == null) ? 275 : width;
                if (!$.isDefined(message) || message != null)
                {
                    message = $.isDefined(message) ? message : 'Please wait while we process your request';
                    message = '<center>' +
                        '<table style="z-index: 5000;">' +
                        '<tr>' +
                        '<td rowspan="2"><img src="' + assetUrl + '/img/icons/information.png" width="25 height="25"></td>' +
                        '<td>' +
                        '<span id="blockSpan" style="font-size:10px;font-weight:normal;padding-left:8px;text-align:center;">' +
                        message +
                        '</span>' +
                        '</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td align="center"><img src="' + assetUrl + '/img/ajax-loader.gif" width="220" height="19"></td>' +
                        '</tr>' +
                        '</table>' +
                        '</center>';
                }
                left = ($(window).width() - width) / 2;
                this.css["width"] = width + 'px';
                this.css["left"] = left + 'px';
                if ($.isEmpty($.blockMessage.element))
                    $.blockUI({ message : message, baseZ: baseZ, overlayCSS: this.overlayCSS, css: this.css});
                else
                    $($.blockMessage.element).block({ message : message, baseZ: baseZ, overlayCSS: this.overlayCSS, css: this.css});

                $.blockMessage.visible = true;
            },
            show2:  function(options)
            {
                var width = 275;
                var message = null;
                var baseZ = 1000;
                var overlayCSS = this.overlayCSS;

                if ($.isDefined(options))
                {
                    if ($.isDefined(options["element"]))
                        $.blockMessage.element = options["element"];
                    else if (!$.isEmpty($.blockMessage.defaultElement))
                        $.blockMessage.element = $.blockMessage.defaultElement;

                    if ($.isDefined(options["width"]))
                        width = (options["width"] == null) ? width : options["width"];

                    if ($.isDefined(options["overlayCSS"]))
                        overlayCSS = options["overlayCSS"];

                    if ($.isDefined(options["baseZ"]))
                        baseZ = options["baseZ"];

                    if ($.isDefined(options["message"]))
                        message = options["message"];
                }
                if (message != null)
                {
                    message = $.isDefined(message) ? message : 'Please wait while we process your request';
                    message = '<center>' +
                        '<table style="z-index: 5000;">' +
                        '<tr>' +
                        '<td rowspan="2"><img src="' + assetUrl + '/img/icons/information.png" width="25 height="25"></td>' +
                        '<td>' +
                        '<span id="blockSpan" style="font-size:10px;font-weight:normal;padding-left:8px;text-align:center;">' +
                        message +
                        '</span>' +
                        '</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td align="center"><img src="' + assetUrl + '/img/ajax-loader.gif" width="220" height="19"></td>' +
                        '</tr>' +
                        '</table>' +
                        '</center>';
                }
                var left = ($(window).width() - width) / 2;
                this.css["width"] = width + 'px';
                this.css["left"] = left + 'px';

                if ($.isEmpty($.blockMessage.element))
                    $.blockUI({ message : message, baseZ: baseZ, overlayCSS: overlayCSS, css: this.css});
                else
                    $($.blockMessage.element).block({ message : message, baseZ: baseZ, overlayCSS: overlayCSS, css: this.css});

                $.blockMessage.visible = true;
            },
            alert:  function(element, message, width)
            {
                if (!$.isEmpty(element))
                    $.blockMessage.element = element;

                width = (width == null) ? 275 : width;
                message = message == null ? 'Please wait while we process your request' : message;
                if (message == $.empty)
                    message = null;
                else
                {
                    message = '<center>' +
                        '<table border="0" width="100%">' +
                        '<tr>' +
                        '<td rowspan="2"><img src="' + assetUrl + '/img/icons/exclamation.png" width="25 height="25"></td>' +
                        '<td>' +
                        '<span style="font-size:10px;font-weight:normal;padding-left:8px;text-align:center;">' +
                        message +
                        '</span>' +
                        '</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td align="center" class="buttons"><button TYPE="button" id="but_close" name="but_close" onclick="$.blockMessage.hide();" class="generalButton negative close_button"><img src="' + assetUrl + '/img/icons/door_out.png" alt="Close" />Close</button></td>' +
                        '</tr>' +
                        '</table>' +
                        '</center>';
                }
                left = ($(window).width() - width) / 2;

                if ($.isEmpty($.blockMessage.element))
                    $.blockUI({ message : message, css: { width: width + 'px', left: left + 'px' }});
                else
                    $($.blockMessage.element).block({ message : message, css: { width: width + 'px', left: left + 'px' }});


                $.blockMessage.visible = true;
            },
            busy:  function(options)
            {
                var message = null;
                var overlayCSS = {backgroundColor: '#fff'};
                var css = {backgroundColor: 'transparent', border: '0px'};

                if ($.isDefined(options))
                {
                    if ($.isDefined(options["element"]))
                        $.blockMessage.element = options["element"];
                    else if (!$.isEmpty($.blockMessage.defaultElement))
                        $.blockMessage.element = $.blockMessage.defaultElement;

                    if ($.isDefined(options["overlayCSS"]))
                        overlayCSS = options["overlayCSS"];
                }
                message = '<img src="' + assetUrl + '/img/busy.gif" width="20" height="20" border="0">';

                if ($.isEmpty($.blockMessage.element))
                    $.blockUI({ message : message, overlayCSS: overlayCSS, css: css});
                else
                    $($.blockMessage.element).block({ message : message, overlayCSS: overlayCSS, css: css});

                $.blockMessage.visible = true;
            },
            hide:  function(element)
            {
                if ($.isEmpty($.blockMessage.element) || ($.isDefined(element) && $.isEmpty(element)))
                    $.unblockUI();
                else if ($.isDefined(element))
                    $(element).unblock();
                else
                    $($.blockMessage.element).unblock();

                $.blockMessage.visible = false;
            }
        };
    }
    )(jQuery);