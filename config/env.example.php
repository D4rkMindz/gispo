<?php
$env = [];

$env['displayErrorDetails'] = {{display_error_details}};
$env['db']['host'] = '{{db_host}}';
$env['db']['port'] = '{{db_port}}';
$env['db']['username'] = '{{db_username}}';
$env['db']['password'] = '{{db_password}}';


$env['twig']['assetCache']['minify'] = {{minify}};
$env['twig']['assetCache']['cache_enabled'] = {{cache_enabled}};

return $env;
