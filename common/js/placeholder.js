//place holder
(function ($) {
	$.fn.placeHolder = function() {
		var str = navigator.userAgent.toLowerCase();
		return this.each(function(index) {

			if(str.indexOf('trident/4.0') != -1 || str.indexOf('trident/5.0') != -1 || str.indexOf('msie 7.0') != -1 || str.indexOf('msie 6.0') != -1 || str.indexOf('msie 9.0') != -1) {
				var message = $(this).attr("placeholder");
				var type = $(this).attr("type");

				if($(this).val().length < 1) {
					if(type == "password") {
						$(this).attr("type", "text");
					}

					$(this).val(message);
				}

				$(this).focusin(function() {
					if($(this).val() == message) {
						$(this).val("");
						if(type == "password") {
							$(this).attr("type", "password");
						}
					}
				});

				$(this).focusout(function() {
					if($(this).val().length < 1) {
						if(type == "password") {
							$(this).attr("type", "text");
						}

						$(this).val(message);
					}
				});
			}
		});

	};

	$.fn.removeHoler = function() {
		var str = navigator.userAgent.toLowerCase();
		return this.each(function(index) {
			if(str.indexOf('trident/4.0') != -1 || str.indexOf('trident/5.0') != -1 || str.indexOf('msie 7.0') != -1 || str.indexOf('msie 6.0') != -1) {
				var message = $(this).attr("placeholder");
				if($(this).val() == message) {
					$(this).val("");
				}
			}
		});
	};

	$.fn.resetHolder = function() {
		var str = navigator.userAgent.toLowerCase();
		return this.each(function(index) {
			if(str.indexOf('trident/4.0') != -1 || str.indexOf('trident/5.0') != -1 || str.indexOf('msie 7.0') != -1 || str.indexOf('msie 6.0') != -1) {
				var message = $(this).attr("placeholder");
				var type = $(this).attr("type");

				if($(this).val().length < 1) {
					if(type == "password") {
						$(this).attr("type", "text");
					}

					$(this).val(message);
				}
			}
		});
	};
}) (jQuery);