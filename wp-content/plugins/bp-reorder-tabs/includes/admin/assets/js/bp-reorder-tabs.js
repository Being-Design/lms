jQuery(document).ready(function($) {
	bp_r_t_admin_bind();
});

function bp_r_t_admin_bind() {
	$ = jQuery.noConflict();
	$("#frm_bp_r_t_a_screen .sortable").sortable();
	$("#frm_bp_r_t_a_screen .sortable").disableSelection();

	$("#frm_bp_r_t_a_screen .nav-list > li").each(function() {
		if ($(this).find(".subnav-list").length > 0) {
			$(this).addClass('has-children').find(' > .drag-handle').append("<span class='dashicons dashicons-arrow-down'></span>");
		}
	});

	$("#frm_bp_r_t_a_screen .drag-handle > span").click(function(e) {
		e.preventDefault();
		$(this).toggleClass('dashicons-arrow-down').toggleClass('dashicons-arrow-up');
		$parent = $(this).closest('.nav');

		$parent.toggleClass('expanded').find('.subnav-list').slideToggle();
	});

	var bp_r_t_ajaxform_options = {
		dataType: 'json',
		beforeSerialize: function($form, options) {
			var config_data = {};
			//convert nav items positions into json array
			var counter_outer = 1;
			$form.find(" > .sortable > li").each(function(i, el_outer) {
				$el_outer = $(el_outer);
				if( 'undefined' == $el_outer.data('navid') || '' == $el_outer.data('navid') )
					return;//skip to next iteration
				
				var item_outer = {
					position: counter_outer,
					subnavs: {}
				};

				//does it have any sub nav?
				var counter_inner = 1;
				$el_outer.find(".sortable > li").each(function(j, el_inner) {
					var item_inner = {
						position: counter_inner
					};
					item_outer.subnavs[$(el_inner).data('navid')] = item_inner;
					counter_inner++;
				});

				config_data[$el_outer.data('navid')] = item_outer;
				counter_outer++;
			});

			$form.find('#config_data').val(json_encode(config_data));
		},
		beforeSubmit: function(arr, $form, options) {
			$form.find("div.updated").remove();
		},
		success: function(response) {
            $('.updated.bbreordertabs').remove();
			$('.wrap').find('h2').first().append("<div class='updated fade bbreordertabs'><p><strong>" + response.message + "</strong></p></div>");
			window.scrollTo(0,0);
		},
		error: function(jqXHR, textStatus, errorThrown) {

		}
	};
	$('#frm_bp_r_t_a_screen').ajaxForm(bp_r_t_ajaxform_options);
}



/* ++++++++++++++++++++++++++++++++++++++++++
 json_encode helper method 
 https://github.com/kvz/phpjs/blob/master/functions/json/json_encode.js
 ++++++++++++++++++++++++++++++++++++++++++ */
function json_encode(mixed_val) {
	// http://kevin.vanzonneveld.net
	// +      original by: Public Domain (http://www.json.org/json2.js)
	// + reimplemented by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +      improved by: Michael White
	// +      input by: felix
	// +      bugfixed by: Brett Zamir (http://brett-zamir.me)
	// *        example 1: json_encode(['e', {pluribus: 'unum'}]);
	// *        returns 1: '[\n    "e",\n    {\n    "pluribus": "unum"\n}\n]'
	/*
	 http://www.JSON.org/json2.js
	 2008-11-19
	 Public Domain.
	 NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
	 See http://www.JSON.org/js.html
	 */
	var retVal, json = this.window.JSON;
	try {
		if (typeof json === 'object' && typeof json.stringify === 'function') {
			retVal = json.stringify(mixed_val); // Errors will not be caught here if our own equivalent to resource
			//  (an instance of PHPJS_Resource) is used
			if (retVal === undefined) {
				throw new SyntaxError('json_encode');
			}
			return retVal;
		}

		var value = mixed_val;

		var quote = function(string) {
			var escapable = /[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
			var meta = {// table of character substitutions
				'\b': '\\b',
				'\t': '\\t',
				'\n': '\\n',
				'\f': '\\f',
				'\r': '\\r',
				'"': '\\"',
				'\\': '\\\\'
			};

			escapable.lastIndex = 0;
			return escapable.test(string) ? '"' + string.replace(escapable, function(a) {
				var c = meta[a];
				return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
			}) + '"' : '"' + string + '"';
		};

		var str = function(key, holder) {
			var gap = '';
			var indent = '    ';
			var i = 0; // The loop counter.
			var k = ''; // The member key.
			var v = ''; // The member value.
			var length = 0;
			var mind = gap;
			var partial = [];
			var value = holder[key];

			// If the value has a toJSON method, call it to obtain a replacement value.
			if (value && typeof value === 'object' && typeof value.toJSON === 'function') {
				value = value.toJSON(key);
			}

			// What happens next depends on the value's type.
			switch (typeof value) {
				case 'string':
					return quote(value);

				case 'number':
					// JSON numbers must be finite. Encode non-finite numbers as null.
					return isFinite(value) ? String(value) : 'null';

				case 'boolean':
				case 'null':
					// If the value is a boolean or null, convert it to a string. Note:
					// typeof null does not produce 'null'. The case is included here in
					// the remote chance that this gets fixed someday.
					return String(value);

				case 'object':
					// If the type is 'object', we might be dealing with an object or an array or
					// null.
					// Due to a specification blunder in ECMAScript, typeof null is 'object',
					// so watch out for that case.
					if (!value) {
						return 'null';
					}
					if ((this.PHPJS_Resource && value instanceof this.PHPJS_Resource) || (window.PHPJS_Resource && value instanceof window.PHPJS_Resource)) {
						throw new SyntaxError('json_encode');
					}

					// Make an array to hold the partial results of stringifying this object value.
					gap += indent;
					partial = [];

					// Is the value an array?
					if (Object.prototype.toString.apply(value) === '[object Array]') {
						// The value is an array. Stringify every element. Use null as a placeholder
						// for non-JSON values.
						length = value.length;
						for (i = 0; i < length; i += 1) {
							partial[i] = str(i, value) || 'null';
						}

						// Join all of the elements together, separated with commas, and wrap them in
						// brackets.
						v = partial.length === 0 ? '[]' : gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' : '[' + partial.join(',') + ']';
						gap = mind;
						return v;
					}

					// Iterate through all of the keys in the object.
					for (k in value) {
						if (Object.hasOwnProperty.call(value, k)) {
							v = str(k, value);
							if (v) {
								partial.push(quote(k) + (gap ? ': ' : ':') + v);
							}
						}
					}

					// Join all of the member texts together, separated with commas,
					// and wrap them in braces.
					v = partial.length === 0 ? '{}' : gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' : '{' + partial.join(',') + '}';
					gap = mind;
					return v;
				case 'undefined':
					// Fall-through
				case 'function':
					// Fall-through
				default:
					throw new SyntaxError('json_encode');
			}
		};

		// Make a fake root object containing our value under the key of ''.
		// Return the result of stringifying the value.
		return str('', {
			'': value
		});

	} catch (err) { // Todo: ensure error handling above throws a SyntaxError in all cases where it could
		// (i.e., when the JSON global is not available and there is an error)
		if (!(err instanceof SyntaxError)) {
			throw new Error('Unexpected error type in json_encode()');
		}
		this.php_js = this.php_js || {};
		this.php_js.last_error_json = 4; // usable by json_last_error()
		return null;
	}
}