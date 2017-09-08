// var GLOB = (function(GLOB, $) {

// 	GLOB.Select = {

// 		settings: {

// 			// These elements get tooltips applied
// 			selector: '.selectric',

// 			// merg these settings with data-attributes; attributes take preference
// 			defaultOptions: {

// 			}
// 		},

// 		attach: function() {

// 			$(GLOB.Select.settings.selector).each(function(i, el) {
// 				var options = {};

// 				$.extend(
// 					true,
// 					options,
// 					GLOB.Select.settings.defaultOptions,
// 					$(el).data()
// 					);

// 				$(el).selectric(options);
// 			});

// 		}
// 	}

	
// 	// initialize
// 	$(function () {
// 		GLOB.Select.attach();
// 	});

// 	return GLOB;

// })(GLOB || {}, jQuery);
