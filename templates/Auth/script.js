$(function () {
    $('[data-id=login]').on('click', function () {
        $('[data-id=error]').remove();
        var usernameField = $('[data-id=username]');
        var username = usernameField.val();
        var password = $('[data-id=password]').val();
        var url = baseurl() + 'auth';
        sendPostAjax(url, JSON.stringify({username: username, password: password})).then(function (response) {
            if (response.success) {
                window.location.href = baseurl();
            } else {
                usernameField.after('<small data-id="error" class="form-text text-muted">Benutzername oder Passwort falsch</small>\n')
            }
        })
    });
});