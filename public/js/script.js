/**
 * Base url
 *
 @returns {|jQuery}
 */
function baseurl() {
	return $('head base').attr('href');
}

/**
 * Send GET AJAX request.
 *
 * @param url {string} - url
 * @param data
 * @return Promise
 */
function sendGetAjax(url, data) {
	return new Promise((resolve, reject = function (xhr) {
		hideLoader();
		notify({
			type: 'error',
			msg: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error. Please try it again later'
		});
	}) => {
		var token = window.localStorage.getItem('token');

		$.ajax({
			method: 'GET',
			contentType: 'application/json',
			cache: false,
			processData: true,
			headers: {"Authorization": token},
			url: url
		}).done(function (response) {
			resolve(response);
		}).fail((xhr) => {
			reject(xhr)
		});
	});
}

/**
 * Make a notification.
 *
 * This function requires NotifJS
 *
 * @param {Object} options
 * @param {string} options.type (fails, error)
 * @param {string} options.msg ("Hello World!")
 * @returns {!Array.<string>}
 */
function notify(options) {
	if (options.type === 'success') {
		options = $.extend({bgcolor: '#28A745'}, options);
	}

	if (options.type === 'warning') {
		options = $.extend({bgcolor: '#DC3545'}, options);
	}

	options = $.extend({
		position: 'center',
		multiline: true,
		zindex: 9999999,
		opacity: 0.9
	}, options);
	return notif(options);
}

/**
 * Send POST AJAX request.
 *
 * @param url
 * @param data
 * @returns {Promise}
 */
function sendPostAjax(url, data) {
	return new Promise((resolve, reject) => {
		let token = window.localStorage.getItem('token');
		$.ajax({
			method: 'POST',
			contentType: 'application/json',
			processData: true,
			dataType: 'json',
			headers: {"Authorization": token},
			url: url,
			data: data
		}).done(function (response) {
			resolve(response);
		}).fail(function (xhr) {
			reject(xhr);
		});
	});
}

/**
 * Show loading animation.
 */
function showLoader() {
	$("#loader").show();
}

/**
 * Hide loading animation.
 */
function hideLoader() {
	$("#loader").hide();
}