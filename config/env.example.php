<?php
$env = [];

$env['displayErrorDetails'] = (bool)'{{display_error_details}}';
$env['db']['host'] = '{{db_host}}';
$env['db']['port'] = '{{db_port}}';
$env['db']['username'] = '{{db_username}}';
$env['db']['password'] = '{{db_password}}';


$env['twig']['assetCache']['minify'] = (bool)'{{minify}}';
$env['twig']['assetCache']['cache_enabled'] = (bool)'{{cache_enabled}}';

return $env;
