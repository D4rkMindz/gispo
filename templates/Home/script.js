class Home {
	constructor() {
		this.registerSearch();
		this.getUsers();
		this.registerAutoAction();
	}

	registerActions() {
		var checkin = $('button[data-type=danger]');
		var checkout = $('button[data-type=success]');
		var row = $('tbody tr td:not(:last-child)');

		checkin.unbind().on('click', (event) => {
			event.preventDefault();
			let userId = $(event.target).closest('tr').data('user');
			this.checkin(userId, null);
		});

		checkout.unbind().on('click', (event) => {
			event.preventDefault();
			let userId = $(event.target).closest('tr').data('user');
			this.checkout(userId, null);
		});

		row.unbind().on('click', (event) => {
			event.preventDefault();
			var id = $(event.target).closest('tr').data('user');
			let url = baseurl() + 'users/' + id;
			window.location.href = url;
		});

		$('[data-id=get-csv]').on('click', (event) => {
			event.preventDefault();
			let url = baseurl() + 'api/users/csv';
			window.location.href = baseurl() + "api/users/csv";
		})
	}

	registerSearch() {
		var $this = this;
		var barcodeInput = $('[data-id=barcode]');
		var firstnameInput = $('[data-id=first-name]');
		var lastnameInput = $('[data-id=last-name]');
		var emailInput = $('[data-id=email]');

		barcodeInput.on('input', (event) => {
			$this.setLoadingRow();
			var barcode = barcodeInput.val();
			if ($.trim(barcode).length >= 13) {
				let url = baseurl() + 'api/users/search';
				let data = $this.getData();
				sendPostAjax(url, JSON.stringify(data))
					.then((response) => {
						$this.render(response);
					});
			}

			$this.checkIfEmtpty(barcodeInput, 13);
		});

		firstnameInput.on('input', () => {
			$this.setLoadingRow();
			var firstname = firstnameInput.val();
			if ($.trim(firstname).length >= 2) {
				let url = baseurl() + 'api/users/search';
				let data = $this.getData();
				sendPostAjax(url, JSON.stringify(data))
					.then((response) => {
						$this.render(response);
					});
			}
			$this.checkIfEmtpty(firstnameInput, 2);
		});

		lastnameInput.on('input', () => {
			$this.setLoadingRow();
			var lastname = lastnameInput.val();
			if ($.trim(lastname).length >= 2) {
				let url = baseurl() + 'api/users/search';
				let data = $this.getData();
				sendPostAjax(url, JSON.stringify(data))
					.then((response) => {
						$this.render(response);
					});
			}
			$this.checkIfEmtpty(lastnameInput, 2);
		});

		emailInput.on('input', () => {
			$this.setLoadingRow();
			var email = emailInput.val();
			if ($.trim(email).length >= 2) {
				let url = baseurl() + 'api/users/search';
				let data = $this.getData();
				sendPostAjax(url, JSON.stringify(data))
					.then((response) => {
						$this.render(response);
					});
			}
			$this.checkIfEmtpty(emailInput, 2);
		});

		$('[data-id=submit]').on('click', (event) => {
			event.preventDefault();
			$this.setLoadingRow();
			var lEmail = $.trim(emailInput.val()).length;
			var lBarcode = $.trim(barcodeInput.val()).length;
			var lFirstname = $.trim(firstnameInput.val()).length;
			var lLastname = $.trim(lastnameInput.val()).length;

			var barcode = barcodeInput.val();
			let autoAction = $('[data-id=auto-action]').is(':checked');

			if (autoAction && barcode.length >= 11) {
				let selected = $('input[name=auto-action-type]:checked', $('[data-id=auto-action-selection]')).val();
				if (selected === 'checkin') {
					this.checkin(null, barcode);
				} else {
					this.checkout(null, barcode);
				}
			}

			if (lEmail >= 1 || lBarcode >= 1 || lFirstname >= 1 || lLastname >= 1) {
				let url = baseurl() + 'api/users/search';
				let data = $this.getData();
				sendPostAjax(url, JSON.stringify(data))
					.then((response) => {
						$this.render(response);
					});
			} else {
				notify({type: 'warning', msg: 'Data required'})
			}
		})
	}

	registerAutoAction() {
		var autoAction = $('[data-id=auto-action]');
		autoAction.on('change', () => {
			let condition = autoAction.is(':checked');
			var checkout = $('[data-id=auto-action-checkout]');
			var checkin = $('[data-id=auto-action-checkin]');
			if (condition) {
				checkout.prop('disabled', false);
				checkin.prop('disabled', false);
			} else {
				checkout.prop('disabled', true);
				checkin.prop('disabled', true);
			}
		});
	}

	getUsers() {
		var $this = this;
		let url = baseurl() + 'api/users';
		sendGetAjax(url, JSON.stringify({})).then((response) => {
			$this.render(response);
		}, (xhr) => {
			console.log(xhr)
		});
	}

	setLoadingRow() {
		let row = '<tr><td colspan="7" class="text-center">Loading . . .</td></tr>';
		var tbody = $('[data-id=users-table]').find('tbody');
		if (tbody.find('td:first-child').prop('colspan') !== 7) {
			tbody.prepend(row);
		}
	}

	checkIfEmtpty(input, length) {
		var rowCount = $('[data-id=users-table] tbody tr').length;
		if (input.val().length <= length && rowCount < 10) {
			this.getUsers();
		}
	}

	getData() {
		var barcodeInput = $('[data-id=barcode]');
		var firstnameInput = $('[data-id=first-name]');
		var lastnameInput = $('[data-id=last-name]');
		var emailInput = $('[data-id=email]');
		return {
			barcode: barcodeInput.val(),
			first_name: firstnameInput.val(),
			last_name: lastnameInput.val(),
			email: emailInput.val(),
		};
	}

	render(response) {
		let template = $('#users').html();
		let rendered = Mustache.render(template, response);
		$('[data-id=loading]').remove();
		$('[data-id=userdata]').html(rendered);
		this.registerActions();
	}

	checkin(userId, barcode) {
		$('[data-id=barcode]').prop('disabled', true);
		var row = $('[data-user=' + userId + ']') || $('[data-barcode=' + barcode + ']');
		var buttonField = row.find('td:last-child');
		buttonField.html('<button class="btn btn-block btn-danger ld-ext-left running" style="min-height: 39px" disabled><div class="ld ld-ring ld-spin" style="margin-left: 30px"></div></button>');
		let data = {
			'user_id': userId,
			'barcode': barcode,
		};
		let url = baseurl() + 'api/users/checkin';
		sendPostAjax(url, JSON.stringify(data)).then((response) => {
				notify({type: 'success', msg: response.message});
				let btn = row.find('button.btn');
				btn.remove();
				row.removeClass('table-danger').addClass('table-success');
				let newbtn = '<button class="btn btn-block btn-success" data-type="success">' + response.button + '</button>';
				buttonField.append(newbtn);
				this.registerActions();
				$('[data-id=barcode]').prop('disabled', false).val('').focus();
			},
			(xhr) => {
				notify({type: 'warning', msg: xhr.responseJSON.message || 'Error'});
				$('[data-id=barcode]').prop('disabled', false).val('').focus();
			});
	}

	checkout(userId, barcode) {
		$('[data-id=barcode]').prop('disabled', true);
		var row = $('[data-user=' + userId + ']') || $('[data-barcode=' + barcode + ']');
		var buttonField = row.find('td:last-child');
		buttonField.html('<button class="btn btn-block btn-success ld-ext-left running" style="min-height: 39px" disabled><div class="ld ld-ring ld-spin" style="margin-left: 30px"></div></button>');
		let data = {
			'user_id': userId,
			'barcode': barcode
		};
		let url = baseurl() + 'api/users/checkout';
		sendPostAjax(url, JSON.stringify(data)).then((response) => {
				notify({type: 'info', msg: response.message});
				let btn = row.find('button.btn');
				btn.remove();
				row.removeClass('table-success').addClass('table-danger');
				let newbtn = '<button class="btn btn-block btn-danger" data-type="danger">' + response.button + '</button>';
				row.find('td:last-child').append(newbtn);
				this.registerActions();
				$('[data-id=barcode]').prop('disabled', false).val('').focus();
			},
			(xhr) => {
				notify({type: 'warning', msg: xhr.responseJSON.message || 'Error'});
				$('[data-id=barcode]').prop('disabled', false).val('').focus();
			});
	}
}

$(() => {
	new Home()
});