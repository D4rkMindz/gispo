$(function () {
    $('[data-id=login]').on('click', function () {
        $('[data-id=error]').remove();
        const usernameField = $('[data-id=username]');
        const username = usernameField.val();
        const password = $('[data-id=password]').val();
        const url = baseurl() + 'auth';
        sendPostAjax(url, JSON.stringify({username: username, password: password})).then(function (response) {
            if (response.success) {
                window.location.href = baseurl();
            } else {
                usernameField.after('<small data-id="error" class="form-text text-muted">Benutzername oder Passwort falsch</small>\n')
            }
        })
    });
});