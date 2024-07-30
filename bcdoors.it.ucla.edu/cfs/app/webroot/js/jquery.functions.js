$.fn.enable = function()
{
	return $(this).attr('disabled', false);
};
$.fn.disable = function()
{
	return $(this).attr('disabled', true);
};
$.fn.check = function()
{
	return $(this).attr('checked', true);
};
$.fn.uncheck = function()
{
	return $(this).attr('checked', false);
};
$.fn.outerHtml = function()
{
	return $('<div></div>').append( this[0] || '' ).html();
};
$.fn.readonly = function(state)
{
	if (state == undefined)
		return $(this).attr('readonly');
	else
		return $(this).attr('readonly', state);
};
$.fn.isDisabled = function()
{
	return $(this).attr('disabled') == "true";
};
$.fn.isChecked = function()
{
	return $(this).is(':checked');
};

$.fn.hasAttr = function(name)
{
	return this.attr(name) !== undefined;
};
$.fn.isVisible = function(name)
{
	var result = true;
	var re = new RegExp('display:\\snone;', 'i');
	if ($(this).length > 0)
	{
		$(this).each
		(
			function()
			{
				if ($(this).css('display') && $(this).css('display').toLowerCase() == 'none')
					result = false;
				else if ($(this).is('[style]'))
				{
					result = $(this).attr('style').match(re) == null;
				}

				if (result)
					return result;
			}
		);
	}
	else
		result = false;

	return result;
};
;(
	function($)
	{
		jQuery.empty = "";
		jQuery.space = function(quantity)
		{
			var spaces = $.empty;
			var oneSpace = " ";

			if (quantity > 0)
			{
				for (var loopCNT = 1; loopCNT <= quantity; loopCNT++)
					spaces += oneSpace;
			}

			return spaces;
		};
		jQuery.uid = function()
		{
			var date = new Date();
			var randomNumber = Math.floor(Math.random() * 11);

			return String(date.getTime()) + String(randomNumber);
		};
		jQuery.date = {
			months : [["Jan", "January"], ["Feb", "February"], ["Mar", "March"], ["Apr", "April"], ["May", "May"], ["Jun", "June"], ["Jul", "July"], ["Aug", "August"], ["Sep", "September"], ["Oct", "October"], ["Nov", "November"], ["Dec", "December"]],
			days : ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			format : function(value, pattern)
			{
				var date = null;
				pattern = $.isDefined(pattern) ? pattern : "yyyy-mm-dd";
				value = $.isDefined(value) ? value : $.empty;

				if ($.isEmpty(value))
					date = new Date();
				else
					date = new Date($.list.first(value, " ").replace(/-|\./gi, "/"));

				switch (pattern.toLowerCase())
				{
					case "yyyy-mm-dd":
						value = date.getFullYear() + "-" + ("00" + (date.getMonth() + 1)).slice(-2) + "-" + ("00" + date.getDate()).slice(-2);
						break;
					case "yyyy-m-d":
						value = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
						break;
					case "yy-mm-dd":
						value = String(date.getFullYear()).slice(-2) + "-" + ("00" + (date.getMonth() + 1)).slice(-2) + "-" + ("00" + date.getDate()).slice(-2);
						break;
					case "yy-m-d":
						value = String(date.getFullYear()).slice(-2) + "-" + (date.getMonth() + 1) + "-" + date.getDate();
						break;
					case "mm/dd/yyyy":
						value =  ("00" + (date.getMonth() + 1)).slice(-2) + "/" + ("00" + date.getDate()).slice(-2) + "/" + date.getFullYear();
						break;
					case "m/d/yyyy":
						value =  (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
						break;
					case "m/d/yy":
						value =  (date.getMonth() + 1) + "/" + date.getDate() + "/" + String(date.getFullYear()).slice(-2);
						break;
					case "mm/dd/yy":
						value =  ("00" + (date.getMonth() + 1)).slice(-2) + "/" + ("00" + date.getDate()).slice(-2) + "/" + String(date.getFullYear()).slice(-2);
						break;
					case "yy/mm/dd":
						value = String(date.getFullYear()).slice(-2) + "/" + ("00" + (date.getMonth() + 1)).slice(-2) + "/" + ("00" + date.getDate()).slice(-2);
						break;
					case "yyyy/m/d":
						value = date.getFullYear() + "/" + (date.getMonth() + 1) + "/" + date.getDate();
						break;
					case "yy/m/d":
						value = String(date.getFullYear()).slice(-2) + "/" + (date.getMonth() + 1) + "/" + date.getDate();
						break;
					case "f dd, yyyy":
						value =  $.date.months[date.getMonth()][0] + " " + ("00" + date.getDate()).slice(-2) + ", " + date.getFullYear();
						break;
					case "ff dd, yyyy":
						value =  $.date.months[date.getMonth()][1] + " " + ("00" + date.getDate()).slice(-2) + ", " + date.getFullYear();
						break;
					case "f d, yyyy":
						value =  $.date.months[date.getMonth()][0] + " " + date.getDate() + ", " + date.getFullYear();
						break;
					case "ff d, yyyy":
						value =  $.date.months[date.getMonth()][1] + " " + date.getDate() + ", " + date.getFullYear();
						break;
				}

				return value;
			},
			add : {
				"day": function(value, increment, pattern)
				{
					var date = null;
					pattern = $.isDefined(pattern) ? pattern : "yyyy-mm-dd";
					value = $.isDefined(value) ? value : $.empty;

					if ($.isEmpty(value))
						date = new Date();
					else
						date = new Date($.list.first(value, " ").replace(/-|\./gi, "/"));

					date.setDate(date.getDate() + increment);

					return $.date.format(date.getFullYear() + "/" + (date.getMonth() + 1) + "/" + date.getDate(), pattern);
				},
				"month": function(value, increment, pattern)
				{
					var date = null;
					pattern = $.isDefined(pattern) ? pattern : "yyyy-mm-dd";
					value = $.isDefined(value) ? value : $.empty;

					if ($.isEmpty(value))
						date = new Date();
					else
						date = new Date($.list.first(value, " ").replace(/-|\./gi, "/"));

					date.setMonth(date.getMonth() + increment);

					return $.date.format(date.getFullYear() + "/" + (date.getMonth() + 1) + "/" + date.getDate(), pattern);
				},
				"year": function(value, increment, pattern)
				{
					var date = null;
					pattern = $.isDefined(pattern) ? pattern : "yyyy-mm-dd";
					value = $.isDefined(value) ? value : $.empty;

					if ($.isEmpty(value))
						date = new Date();
					else
						date = new Date($.list.first(value, " ").replace(/-|\./gi, "/"));

					date.setFullYear(date.getFullYear() + increment);

					return $.date.format(date.getFullYear() + "/" + (date.getMonth() + 1) + "/" + date.getDate(), pattern);
				}
			},
			valid :	function(value)
			{
				//Basic check for format validity

				//!/Invalid|NaN/.test(new Date(value));
				var validformat = /^\d{2}\/\d{2}\/\d{4}$/;
				var result = false;

				if (!validformat.test(value))
					result = false;
				else
				{
					//Detailed check for valid date ranges
					var monthfield = value.split("/")[0];
					var dayfield = value.split("/")[1];
					var yearfield = value.split("/")[2];
					var dayobj = new Date(yearfield, monthfield - 1, dayfield);
					if ((dayobj.getMonth() + 1 != monthfield) || (dayobj.getDate() != dayfield) || (dayobj.getFullYear() != yearfield))
						result = false;
					else
						result = true;
				}

				return result
			}
		};
		jQuery.time = {
			format : function(value, pattern)
			{
				var date = null;
				pattern = $.isDefined(pattern) ? pattern : "h:m:s a";
				value = $.isDefined(value) ? value : $.empty;

				if ($.isEmpty(value))
					date = new Date();
				else
					date = new Date(value.replace(/-|\./gi, "/"));

				var hours = date.getHours()
				var minutes = date.getMinutes()
				var seconds = date.getSeconds()
				var suffix = "AM";
				if (hours >= 12)
				{
					suffix = "PM";
					hours = hours - 12;
				}
				if (hours == 0)
				{
					hours = 12;
				}

				switch (pattern)
				{
					case 'h:m a':
						value = ("00" + (hours)).slice(-2) + ":" + ("00" + (minutes)).slice(-2) + ' ' + suffix.toLowerCase();

						break;

					case 'h:m:s A':
						value = ("00" + (hours)).slice(-2) + ":" + ("00" + (minutes)).slice(-2) + ":" + ("00" + seconds).slice(-2) + ' ' + suffix.toUpperCase();

						break;
					case 'h:m A':
						value = ("00" + (hours)).slice(-2) + ":" + ("00" + (minutes)).slice(-2) + ' ' + suffix.toUpperCase();

						break;
					case 'h':
						value = ("00" + (hours)).slice(-2);

						break;
					case 'm':
						value = ("00" + (minutes)).slice(-2);

						break;
					case 's':
						value = ("00" + (seconds)).slice(-2);

						break;

					case 'H:m:s':
						value = ("00" + (date.getHours())).slice(-2) + ":" + ("00" + (minutes)).slice(-2) + ":" + ("00" + seconds).slice(-2);

						break;
					case 'H:m':
						value = ("00" + (date.getHours())).slice(-2) + ":" + ("00" + (minutes)).slice(-2);

						break;

					case 'H:m:s':
						value = ("00" + (date.getHours())).slice(-2) + ":" + ("00" + (minutes)).slice(-2) + ":" + ("00" + seconds).slice(-2);

						break;
					case 'H:m':
						value = ("00" + (date.getHours())).slice(-2) + ":" + ("00" + (minutes)).slice(-2);

						break;
					case 'H':
						value = ("00" + (date.getHours())).slice(-2);

						break;

					default:
						value = ("00" + (hours)).slice(-2) + ":" + ("00" + (minutes)).slice(-2) + ":" + ("00" + seconds).slice(-2) + ' ' + suffix.toLowerCase();
						break;
				}

				return value;
			}
		};
		jQuery.isChecked = function(selector)
		{
			var result = true;

			if ($(selector).length > 0)
				result = $(selector).is(":checked");

			return result;
		};
		jQuery.isDefined = function(value)
		{
			return !(value == undefined) || !(value === undefined);
		};
		jQuery.isDisabled = function(selector)
		{
			var result = true;

			if ($(selector).length > 0)
				result = $(selector).attr("disabled");

			return result;
		};
		jQuery.isEmpty = function(value)
		{
			var result = false;

			if ($.isObject(value))
				result = $.isEmptyObject(value);
			else if ($.isArray(value))
				result = value.length == 0;
			else
				result = !$.isDefined(value) || value == null || $.trim(value) == $.empty;

			return result;
		};
		jQuery.isNull = function(value)
		{
			return value == null;
		};
		jQuery.isFloat = function(value)
		{
			return typeof(value) == "float";
		};
		jQuery.isNumber = function(value)
		{
			return typeof(value) == "number" || !(isNaN(value));
		};
		jQuery.isObject = function(value)
		{
			return typeof(value) == "object";
		};
		jQuery.isString = function(value)
		{
			return typeof(value) == "string";
		};
		jQuery.capitalize = function(value)
		{
			var parts = value.split(' ');
			for (var loopCNT = 0; loopCNT < parts.length; loopCNT++)
			{
				parts[loopCNT] = parts[loopCNT].charAt(0).toUpperCase() + parts[loopCNT].slice(1);
			}

			return parts.join(' ');
		};
		jQuery.strip = {
			first : function(value, ifEqualTo)
			{
				if (jQuery.isEmpty(value))
					value = $.empty;
				else
				{
					if (value.length > 0)
					{
						if (ifEqualTo == undefined)
							value = value.substr(1, value.length);
						else if (jQuery.isArray(ifEqualTo))
						{
							while (jQuery.inArray(value.charAt(0), ifEqualTo) != -1)
							{
								value = value.substr(1, value.length);
								if (value == $.empty)
									break;
							}
						}
						else if (ifEqualTo == value.substr(0, ifEqualTo.length))
							value = value.substr(ifEqualTo.length, value.length);
					}
				}
				return value;
			},
			last : function(value, ifEqualTo)
			{
				if (jQuery.isEmpty(value))
					value = $.empty;
				else
				{
					if (value.length > 0)
					{
						if (ifEqualTo == undefined)
							value = value.substr(0, value.length - 1);
						else if (ifEqualTo == value.substr(-ifEqualTo.length))
							value = value.substr(0, value.length - ifEqualTo.length);
					}
				}
				return value;
			}
		};
		jQuery.left = {
			trim:  function(value)
			{
				if (value.length == 0)
					return $.empty;
				else
				{
					if ($.isString(value))
					{
						while (value.charAt(0) == $.space(1))
						{
							value = value.substr(1, value.length);
						}
					}
				}
				return value;
			},
			string:  function(value, length)
			{
				return value.substr(0, length);
			}
		};
		jQuery.right = {
			trim:  function(value)
			{
				if (value.length == 0)
					return $.empty;
				else
				{
					if ($.isString(value))
					{
						while ($.empty + value.charAt(value.length - 1) == $.space(1))
						{
							value = value.substr(0, value.length - 1);
						}
					}
				}
				return value;
			},
			string:  function(value, length)
			{
				return value.substr(value.length - length, length);
			}

		};
		/*
		 jQuery.trim = 	function(value)
		 {
		 var result = value;
		 if ($.isString(value))
		 {
		 if (!(value == undefined) && value != $.empty)
		 result = $.left.trim($.right.trim(value));
		 }
		 return result;
		 }
		 ;
		 */
		jQuery.keys = {
			first:  function(values)
			{   var result = $.empty;
				if ($.isPlainObject(values) || $.isObject(values))
				{
					for (key in values)
					{
						result = key;
						break;
					}
				}

				return result;
			},
			find:  function(key, values)
			{   var result = null;
				if ($.isPlainObject(values) || $.isObject(values))
				{
					for (item in values)
					{
						if (key == item)
						{
							result = values[key];
							break;
						}
					}
				}

				return result;
			},
			all:  function(values)
			{   var result = new Array();
				if ($.isPlainObject(values))
				{
					for (key in values)
					{
						result.push(key);
					}
				}

				return result;
			}
		};
		jQuery.timeStamp = function()
		{   var date = new Date();

			return date.getTime();
		};
		jQuery.list = {
			delimiter : ",",
			length : function(values, delimiter)
			{
				if (arguments.length < 2 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				var list = new Array();

				if ($.trim(values) != $.empty)
					list = values.split(delimiter);

				return list.length;
			},
			get : function(values, index, delimiter)
			{
				if (arguments.length < 3 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				var list = new Array();
				var result = $.empty;

				index = index == 0 ? 1 : index;

				if (!$.isEmpty(values) &&
					!isNaN(index) &&
					index <= $.list.length(values, delimiter))
				{
					list = values.split(delimiter);
					result = list[index - 1];
				}

				return result;
			},
			first : function(values, delimiter)
			{
				if (arguments.length < 2 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				return $.list.get(values, 1, delimiter);
			},
			last : function(values, delimiter)
			{
				if (arguments.length < 2 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				return $.list.get(values, $.list.length(values, delimiter), delimiter);
			},
			prepend : function(values, item, delimiter)
			{
				if (arguments.length < 3 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				var list = new Array($.trim(values));

				if (list.length > 0)
				{
					list.unshift(item);
				}

				return list.join(delimiter);
			},
			append : function(values, item, delimiter)
			{
				if (arguments.length < 3 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				var list = new Array($.trim(values));

				if (list.length > 0)
				{
					list.push(item);
				}

				return list.join(delimiter);
			},
			remove : function(values, index, delimiter)
			{
				if (arguments.length < 2 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				var data = new Array();
				var list = new Array();

				index = index == 0 ? 1 : index;

				if (!$.isEmpty(values) &&
					!isNaN(index) &&
					index <= $.list.length(values, delimiter))
				{
					data = values.split(delimiter);
					count = data.length;

					for (var loopCNT = 0; loopCNT < count; loopCNT++)
					{
						if (loopCNT != (index - 1))
						{
							list.push(data[loopCNT]);
						}
					}
				}
				else
					list = values.split(delimiter);

				return list.join(delimiter);
			},
			removeFirst : function(values, delimiter)
			{
				if (arguments.length < 2 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				return $.list.remove(values, 1, delimiter);
			},
			removeLast : function(values, delimiter)
			{
				if (arguments.length < 2 || delimiter == $.empty)
					delimiter = $.list.delimiter;

				return $.list.remove(values, $.list.length(values, delimiter), delimiter);
			}
		};
		jQuery.image = {
			preload:  function(selector, imageName)
			{
				if ($.isEmpty(selector) && !$.isEmpty(imageName))
				{
					var values = $.isArray(imageName) ? imageName : [imageName];
					for (var loopCNT = 0; loopCNT < values.length; loopCNT++)
					{
						var pic1 = new Image();
						pic1.src = values[loopCNT];
					}
				}
				else if($(selector).attr("src") != null)
				{
					var ext = $.list.last($(selector).attr("src"), ".");
					if (ext != "")
					{
						var pic1 = new Image();
						pic1.src = $(selector).attr("src").replace("." + ext, "-on." + ext);
					}
				}

				return true;
			},
			swap:  function(selector, imageName)
			{
				if($(selector).attr("src") != null)
				{
					$.image.preload(selector, imageName);
					var ext = $.list.last($(selector).attr("src"), ".");
					if (ext != "")
					{
						$(selector).hover
						(
							function()
							{
								$(selector).attr("src", $(selector).attr("src").replace("." + ext, "-on." + ext));
							},
							function()
							{
								$(selector).attr("src", $(selector).attr("src").replace("-on." + ext, "." + ext));
							}
						);
					}
				}

				return true;
			}
		};
		jQuery.wrap = function(text, maxLength, breakChar)
		{
			breakChar = $.isDefined(breakChar) ? breakChar : "\n";
			//var lines = text.replace(/\r/gi, $.empty).split(breakChar);
			var lines = text.replace(/\r/gi, $.empty).split(/\n\n/gi);

			var words = new Array();
			var wordWrap = new Array();
			var sentence = $.empty;
			var temp = $.empty;

			for (var loopCNT = 0; loopCNT < lines.length; loopCNT++)
			{
				sentence = $.empty;
				lines[loopCNT] = lines[loopCNT].replace(/\n/gi, ' ');
				words = lines[loopCNT].length > 0 ? lines[loopCNT].split($.space(1)) : new Array();
				if (words.length > 0)
				{
					for (var wordCNT = 0; wordCNT < words.length; wordCNT ++)
					{
						temp = sentence + (sentence != $.empty ? $.space(1) : $.empty) + words[wordCNT];
						if (temp.length >= maxLength)
						{
							wordWrap.push(sentence);
							sentence = $.empty;
						}
						sentence += (sentence != "" ? " " : "") + words[wordCNT];
					}
					if (sentence != $.empty)
						wordWrap.push(sentence);
				}
				else
				{
					wordWrap.push($.empty);
				}
				wordWrap.push($.empty);
			}

			return wordWrap.join("\n");
		};
		jQuery.request = {
			get: function(url, options)
			{
				var result = {response: $.empty, error: $.empty, params: {}};
				var type = "html";
				var async = true;
				var data = {};
				var callback = null;

				if ($.isDefined(options))
				{
					data = $.isDefined(options.data) && $.isObject(options.data) ? options.data: {};
					type = $.isDefined(options.type) && !$.isEmpty(options.type) ? options.type: "html";
					async = $.isDefined(options.async) ? options.async: true;
					callback = $.isDefined(options.callback) && $.isFunction(options.callback) ? options.callback: callback;
				}
				$.ajax
				(
					{
						url: url,
						cache: false,
						dataType: options.type,
						type: "get",
						data: options.data,
						async: options.async,
						error: function (xhr, ajaxOptions, thrownError)
						{
							result = {response: $.empty, error: thrownError, params: data};
							if (!($.isNull(callback)))
								callback(result);
						},
						success: function(xml)
						{
							result = {response: xml, error: $.empty, params: data};
							if (!($.isNull(callback)))
								callback(result);
						}
					}
				);

				return result;
			},
			post: function(url, options)
			{
				var result = {response: $.empty, error: $.empty, params: {}};
				var type = "html";
				var async = true;
				var data = {};
				var callback = null;

				if ($.isDefined(options))
				{
					data = $.isDefined(options.data) && $.isObject(options.data) ? options.data: {};
					type = $.isDefined(options.type) && !$.isEmpty(options.type) ? options.type: "html";
					async = $.isDefined(options.async) ? options.async: true;
					callback = $.isDefined(options.callback) && $.isFunction(options.callback) ? options.callback: callback;
				}
				$.ajax
				(
					{
						url: url,
						cache: false,
						dataType: options.type,
						type: "post",
						data: options.data,
						async: options.async,
						error: function (xhr, ajaxOptions, thrownError)
						{
							result = {response: $.empty, error: thrownError, params: options.data};
							if (!($.isNull(callback)))
								callback(result);
						},
						success: function(xml)
						{
							result = {response: xml, error: $.empty, params: options.data};
							if (!($.isNull(callback)))
								callback(result);
						}
					}
				);

				return result;
			}
		};
		jQuery.queryString = function()
		{
			var queryString = document.location.search;
			var queryParams = new Array();
			var data = new Array();
			var keys = null;

			if (!$.isEmpty(queryString))
			{
				queryParams = queryString.substr(queryString.indexOf("?") + 1).split("&");
			}
			for (var loopCNT = 0; loopCNT < queryParams.length; loopCNT++)
			{
				keys = queryParams[loopCNT].split("=");

				if (keys.length > 0)
				{
					data[$.trim(keys[0])] = (keys.length > 1 ? unescape($.trim(keys[1])) : $.empty);
				}
			}
			return data;
		};
		jQuery.parse = {
			xml:  function(node)
			{
				var data = {};
				if($.isObject(node))
				{
					var children = $(node).children();
					if (children.length > 0)
					{
						$(children).each
						(
							function()
							{
								if ($(this).children().length == 0)
									data[$(this)[0].nodeName] = $.trim($(this).text());
								else
								{
									var childName = $(this)[0].nodeName;
									data[childName] = new Array();
									$(this).children().each
									(
										function()
										{
											data[childName].push($.parse.xml($(this)));
										}
									);
								}
							}
						);
					}
				}

				return data;
			},
			url:  function(values)
			{
				var queryParams = new Array();
				var data = new Array();
				var keys = null;

				if (!$.isEmpty(values))
				{
					if (values.indexOf("?") != -1)
						queryParams = values.substr(values.indexOf("?") + 1).split("&");
					else
						queryParams = values.split("&");
				}
				for (var loopCNT = 0; loopCNT < queryParams.length; loopCNT++)
				{
					keys = queryParams[loopCNT].split("=");

					if (keys.length > 0)
					{
						data[$.trim(keys[0])] = (keys.length > 1 ? unescape($.trim(keys[1])) : $.empty);
					}
				}
				return data;
			}
		};
		jQuery.scrollTo = {
			element:  function(selector)
			{
				var result = false;
				if ($.isDefined(selector))
				{
					if ($(selector).length > 0)
					{
						var position = $(selector).offset();
						window.scrollTo(position.left, position.top);
						result = true;
					}
				}
				return result;
			}
		};
		jQuery.object = {
			prepend:  function(object, value)
			{
				var result = null;
				if ($.isObject(object) && $.isObject(value))
				{
					result = value;
					$.each
					(	object,
						function(index, value)
						{
							if (!$.isDefined(result[index]))
								result[index] = value;
						}
					);
				}

				return result;
			},
			insertAfter:  function(object, value, index)
			{
				var result = null;
				if ($.isObject(object) && $.isObject(value))
				{
					result = value;
					$.each
					(	object,
						function(index, value)
						{
							if (!$.isDefined(result[index]))
								result[index] = value;
						}
					);
				}

				return result;
			}
		};
		jQuery.email = {
			isValid:  function(value)
			{
				return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(value);
			}
		};
		jQuery.url = {
			isValid:  function(value)
			{
				return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
			},
			appendValue:  function(url, value)
			{
				var params = $.parse.url(url);
				var values = $.parse.url(value);
				for (var key in values)
				{
					if (!$.isDefined(params[key]))
					{
						url += (url.indexOf('?') == -1 ? '?' : '&') + key + '=' + values[key];
					}
				}

				return url;
			},
		};
		jQuery.html = {
			validate:  function(html)
			{
				var value = $.empty;
				//$.getScript(S3AssetUrl + "/js/htmlparser.min.js?uid=00001");
				//$.request.get(S3AssetUrl + "/js/htmlparser.min.js?uid=00001", {'type': 'script', async: false});
				//$.ajax({url: "/js/htmlparser.min.js?uid=00001",  dataType: "script", "async": false});
				//$.ajax({url: S3AssetUrl + "/js/htmlparser.min.js?uid=00001",  dataType: "script", "async": false, "success": function() {}});

				//$.get(S3AssetUrl + "/js/htmlparser.min.js?uid=00001", {}, null, "script");
				if (!$.isEmpty(html))
				{
					try
					{
						if(typeof HTMLtoXML == 'function')
							value = HTMLtoXML(html);
						else
							console.log('missing');
					}
					catch(err)
					{

					}
				}

				return value;
			},
			hasTags:  function(html, exclude)
			{
				var startTag = new RegExp("<([A-Z][A-Z0-9]*)\\b[^>]*>", "gi");
				var endTag = new RegExp("<\/([A-Z][A-Z0-9]*)\\b[^>]*>", "gi");
				var regEx = new RegExp("<([A-Z][A-Z0-9]*)\\b[^>]*>|<\/([A-Z][A-Z0-9]*)\\b[^>]*>", "gi");
				var matches = null;
				var result = false;
				var expressions = [];

				if ($.trim(html) != "")
				{
					matches = html.match(regEx);
					if (matches != null)
					{
						if ($.isArray(exclude) && exclude.length > 0)
						{
							for (var loopCNT2 = 0; loopCNT2 < exclude.length; loopCNT2++)
							{
								expressions.push('< *' + exclude[loopCNT2] + '[^>]*>|</' + exclude[loopCNT2] + '>');
							}
							for (var loopCNT = 0; loopCNT < matches.length; loopCNT++)
							{
								regEx = new RegExp(expressions.join("|"), 'gi');
								if(!regEx.test(matches[loopCNT]))
								{
									result = true;
									break;
								}
							}
						}
						else
							result = true;
					}
				}

				return result;
			},
			hasIllegalTags:  function(html, exclude)
			{
				var illegal_tags = new Array("!doctype", "html", "head", "meta", "title", "link", "script", "body", "form", "iframe");
				var regEx = null;
				var matches = null;
				var expression = $.empty;
				var error = $.empty;

				if ($.trim(html) != $.empty)
				{
					for (var loopCNT = 0; loopCNT < illegal_tags.length; loopCNT++)
					{
						if ($.isArray(exclude) && exclude.length > 0)
						{
							if ($.inArray(illegal_tags[loopCNT], exclude) != -1)
								continue;
						}

						expression = '< *' + illegal_tags[loopCNT] + '[^>]*>|</' + illegal_tags[loopCNT] + '>';
						regEx = new RegExp(expression, 'gi');
						matches = html.match(regEx);
						if (matches != null)
						{
							error = '<' + illegal_tags[loopCNT] + '> and </' + illegal_tags[loopCNT] + '> are not permitted';
							break;
						}
					}
				}

				return error;
			},
			hasComments:  function(html)
			{
				var regEx = null;
				var matches = null;
				var expression = $.empty;
				var error = $.empty;

				if ($.trim(html) != $.empty)
				{
					regEx = new RegExp("<!--|-->", "gi");
					matches = html.match(regEx);
					if (matches != null)
						error = 'Please remove all comments. Comments begin with `<!--` and end with `-->`';
				}

				return error;
			},
			encode: function(value)
			{
				return $.isEmpty(value) ? $.empty : $('<div/>').text(value).html();
			},
			decode: function(value)
			{
				return $.isEmpty(value) ? $.empty : $('<div/>').html(value).text();

			}
		};
	}
	)(jQuery);

(function($){
	var strings = {
		strConversion: {
			// tries to translate any objects type into string gracefully
			__repr: function(i){
				switch(this.__getType(i)) {
					case 'array':case 'date':case 'number':
					return i.toString();
					case 'object':
						var o = [];
						for (x=0; x<i.length; i++) { o.push(i+': '+ this.__repr(i[x])); }
						return o.join(', ');
					case 'string':
						return i;
					default:
						return i;
				}
			},
			// like typeof but less vague
			__getType: function(i) {
				if (!i || !i.constructor) { return typeof(i); }
				var match = i.constructor.toString().match(/Array|Number|String|Object|Date/);
				return match && match[0].toLowerCase() || typeof(i);
			},
			//+ Jonas Raoni Soares Silva
			//@ http://jsfromhell.com/string/pad [v1.0]
			__pad: function(str, l, s, t){
				var p = s || ' ';
				var o = str;
				if (l - str.length > 0) {
					o = new Array(Math.ceil(l / p.length)).join(p).substr(0, t = !t ? l : t == 1 ? 0 : Math.ceil(l / 2)) + str + p.substr(0, l - t);
				}
				return o;
			},
			__getInput: function(arg, args) {
				var key = arg.getKey();
				switch(this.__getType(args)){
					case 'object': // Thanks to Jonathan Works for the patch
						var keys = key.split('.');
						var obj = args;
						for(var subkey = 0; subkey < keys.length; subkey++){
							obj = obj[keys[subkey]];
						}
						if (typeof(obj) != 'undefined') {
							if (strings.strConversion.__getType(obj) == 'array') {
								return arg.getFormat().match(/\.\*/) && obj[1] || obj;
							}
							return obj;
						}
						else {
							// TODO: try by numerical index
						}
						break;
					case 'array':
						key = parseInt(key, 10);
						if (arg.getFormat().match(/\.\*/) && typeof args[key+1] != 'undefined') { return args[key+1]; }
						else if (typeof args[key] != 'undefined') { return args[key]; }
						else { return key; }
						break;
				}
				return '{'+key+'}';
			},
			__formatToken: function(token, args) {
				var arg   = new Argument(token, args);
				return strings.strConversion[arg.getFormat().slice(-1)](this.__getInput(arg, args), arg);
			},

			// Signed integer decimal.
			d: function(input, arg){
				var o = parseInt(input, 10); // enforce base 10
				var p = arg.getPaddingLength();
				if (p) { return this.__pad(o.toString(), p, arg.getPaddingString(), 0); }
				else   { return o; }
			},
			// Signed integer decimal.
			i: function(input, args){
				return this.d(input, args);
			},
			// Unsigned octal
			o: function(input, arg){
				var o = input.toString(8);
				if (arg.isAlternate()) { o = this.__pad(o, o.length+1, '0', 0); }
				return this.__pad(o, arg.getPaddingLength(), arg.getPaddingString(), 0);
			},
			// Unsigned decimal
			u: function(input, args) {
				return Math.abs(this.d(input, args));
			},
			// Unsigned hexadecimal (lowercase)
			x: function(input, arg){
				var o = parseInt(input, 10).toString(16);
				o = this.__pad(o, arg.getPaddingLength(), arg.getPaddingString(),0);
				return arg.isAlternate() ? '0x'+o : o;
			},
			// Unsigned hexadecimal (uppercase)
			X: function(input, arg){
				return this.x(input, arg).toUpperCase();
			},
			// Floating point exponential format (lowercase)
			e: function(input, arg){
				return parseFloat(input, 10).toExponential(arg.getPrecision());
			},
			// Floating point exponential format (uppercase)
			E: function(input, arg){
				return this.e(input, arg).toUpperCase();
			},
			// Floating point decimal format
			f: function(input, arg){
				return this.__pad(parseFloat(input, 10).toFixed(arg.getPrecision()), arg.getPaddingLength(), arg.getPaddingString(),0);
			},
			// Floating point decimal format (alias)
			F: function(input, args){
				return this.f(input, args);
			},
			// Floating point format. Uses exponential format if exponent is greater than -4 or less than precision, decimal format otherwise
			g: function(input, arg){
				var o = parseFloat(input, 10);
				return (o.toString().length > 6) ? Math.round(o.toExponential(arg.getPrecision())): o;
			},
			// Floating point format. Uses exponential format if exponent is greater than -4 or less than precision, decimal format otherwise
			G: function(input, args){
				return this.g(input, args);
			},
			// Single character (accepts integer or single character string).
			c: function(input, args) {
				var match = input.match(/\w|\d/);
				return match && match[0] || '';
			},
			// String (converts any JavaScript object to anotated format)
			r: function(input, args) {
				return this.__repr(input);
			},
			// String (converts any JavaScript object using object.toString())
			s: function(input, args) {
				return input.toString && input.toString() || ''+input;
			}
		},

		format: function(str, args) {
			var end    = 0;
			var start  = 0;
			var match  = false;
			var buffer = [];
			var token  = '';
			var tmp    = (str||'').split('');
			for(start=0; start < tmp.length; start++) {
				if (tmp[start] == '{' && tmp[start+1] !='{') {
					end   = str.indexOf('}', start);
					token = tmp.slice(start+1, end).join('');
					if (tmp[start-1] != '{' && tmp[end+1] != '}') {
						var tokenArgs = (typeof arguments[1] != 'object')? arguments2Array(arguments, 2): args || [];
						buffer.push(strings.strConversion.__formatToken(token, tokenArgs));
					}
					else {
						buffer.push(token);
					}
				}
				else if (start > end || buffer.length < 1) { buffer.push(tmp[start]); }
			}
			return (buffer.length > 1)? buffer.join(''): buffer[0];
		},

		calc: function(str, args) {
			return eval(format(str, args));
		},

		repeat: function(s, n) {
			return new Array(n+1).join(s);
		},

		UTF8encode: function(s) {
			return unescape(encodeURIComponent(s));
		},

		UTF8decode: function(s) {
			return decodeURIComponent(escape(s));
		},

		tpl: function() {
			var out = '';
			var render = true;
			// Set
			// $.tpl('ui.test', ['<span>', helloWorld ,'</span>']);
			if (arguments.length == 2 && $.isArray(arguments[1])) {
				this[arguments[0]] = arguments[1].join('');
				return $(this[arguments[0]]);
			}
			// $.tpl('ui.test', '<span>hello world</span>');
			if (arguments.length == 2 && $.isString(arguments[1])) {
				this[arguments[0]] = arguments[1];
				return $(this[arguments[0]]);
			}
			// Call
			// $.tpl('ui.test');
			if (arguments.length == 1) {
				return $(this[arguments[0]]);
			}
			// $.tpl('ui.test', false);
			if (arguments.length == 2 && arguments[1] == false) {
				return this[arguments[0]];
			}
			// $.tpl('ui.test', {value:blah});
			if (arguments.length == 2 && $.isObject(arguments[1])) {
				return $($.format(this[arguments[0]], arguments[1]));
			}
			// $.tpl('ui.test', {value:blah}, false);
			if (arguments.length == 3 && $.isObject(arguments[1])) {
				return (arguments[2] == true)
					? $.format(this[arguments[0]], arguments[1])
					: $($.format(this[arguments[0]], arguments[1]));
			}
		}
	};

	var Argument = function(arg, args) {
		this.__arg  = arg;
		this.__args = args;
		this.__max_precision = parseFloat('1.'+ (new Array(32)).join('1'), 10).toString().length-3;
		this.__def_precision = 6;
		this.getString = function(){
			return this.__arg;
		};
		this.getKey = function(){
			return this.__arg.split(':')[0];
		};
		this.getFormat = function(){
			var match = this.getString().split(':');
			return (match && match[1])? match[1]: 's';
		};
		this.getPrecision = function(){
			var match = this.getFormat().match(/\.(\d+|\*)/g);
			if (!match) { return this.__def_precision; }
			else {
				match = match[0].slice(1);
				if (match != '*') { return parseInt(match, 10); }
				else if(strings.strConversion.__getType(this.__args) == 'array') {
					return this.__args[1] && this.__args[0] || this.__def_precision;
				}
				else if(strings.strConversion.__getType(this.__args) == 'object') {
					return this.__args[this.getKey()] && this.__args[this.getKey()][0] || this.__def_precision;
				}
				else { return this.__def_precision; }
			}
		};
		this.getPaddingLength = function(){
			var match = false;
			if (this.isAlternate()) {
				match = this.getString().match(/0?#0?(\d+)/);
				if (match && match[1]) { return parseInt(match[1], 10); }
			}
			match = this.getString().match(/(0|\.)(\d+|\*)/g);
			return match && parseInt(match[0].slice(1), 10) || 0;
		};
		this.getPaddingString = function(){
			var o = '';
			if (this.isAlternate()) { o = ' '; }
			// 0 take precedence on alternate format
			if (this.getFormat().match(/#0|0#|^0|\.\d+/)) { o = '0'; }
			return o;
		};
		this.getFlags = function(){
			var match = this.getString().matc(/^(0|\#|\-|\+|\s)+/);
			return match && match[0].split('') || [];
		};
		this.isAlternate = function() {
			return !!this.getFormat().match(/^0?#/);
		};
	};

	var arguments2Array = function(args, shift) {
		var o = [];
		for (l=args.length, x=(shift || 0)-1; x<l;x++) { o.push(args[x]); }
		return o;
	};
	$.extend({"string": strings});
//    $.extend(strings);

})(jQuery);