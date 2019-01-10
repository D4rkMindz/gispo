<?php

use Slim\App;
use Symfony\Component\Translation\Translator;

/**
 * Get app.
 *
 * @return App
 */
function app(): App
{
    static $app = null;
    if ($app === null) {
        $config = ['settings' => require_once __DIR__ . '/../config/config.php'];
        $app = new App($config);
    }

    return $app;
}

/**
 * Translation function (i18n).
 *
 * @param $message
 * @return string
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function __($message)
{
    /* @var $translator Translator */
    $translator = app()->getContainer()->get(Translator::class);
    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}

/**
 * Remove path tree
 *
 * @param string $delete Path to delete
 * @return bool true if directory removed
 */
function rrmdir(string $delete): bool
{
    $files = array_diff(scandir($delete), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$delete/$file")) ? rrmdir("$delete/$file") : unlink("$delete/$file");
    }
    return rmdir($delete);
}

/**
 * Returns a ISO-8859-1 encoded string or array.
 *
 * @param mixed $data String or array to convert.
 * @return mixed Encoded data.
 */
function encode_iso($data)
{
    if ($data === null || $data === '') {
        return $data;
    }
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = encode_iso($value);
        }
        return $data;
    } else {
        if (mb_check_encoding($data, 'UTF-8')) {
            return mb_convert_encoding($data, 'ISO-8859-1', 'auto');
        } else {
            return $data;
        }
    }
}
