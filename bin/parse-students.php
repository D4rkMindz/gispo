<?php

$options = getopt('f:i:');
$file = $options['f'];
$images = $options['i'];

if (empty($file) || empty($images)) {
    echo "Please submit the -f CSV-file and the -i images zip-file";
    die();
}

$question = readline("\nThis script deletes all existing data. Do you really want to execute it? [y/n]");
if ($question !== 'y') {
    echo "\nCancelled";
    die();
}

$question = readline("\nAre you really sure? [y/n]");
if ($question !== 'y') {
    echo "\nCancelled";
    die();
}
echo "Importing users...";
require_once __DIR__ . '/../config/bootstrap.php';
$app = app();

use Cake\Database\Connection;

try {
$handle = fopen(__DIR__ . '/' .$file, 'r');
} catch (Throwable $exception) {
    echo "File invalid.\nAborting.";
    die();
}

$data = [];

while (!feof($handle)) {
    $line = fgetcsv($handle, 0, ';');
    if (!empty($line)) {
        $data[] = [
            'first_name' => $line[3],
            'last_name' => $line[2],
            'email' => $line[6],
            'barcode' => $line[4],
            'photo_file_name' => $line[5],
        ];
    }
}
fclose($handle);

$container = $app->getContainer();
/** @var Connection $connection */
$connection = $container->get(Connection::class);
$connection->delete('user_has_actions');
$connection->delete('users');
$query = $connection->newQuery();
$query->insert(['first_name', 'last_name', 'email', 'barcode', 'photo_file_name'])->into('users');
foreach ($data as $record) {
    $query->values($record);
}
$query->execute();

$imageDirectory = __DIR__ . '/../public/img/users';
if (!is_dir($imageDirectory)) {
    mkdir($imageDirectory, 755, true);
}

$images = __DIR__ . '/' . $images;

if (!file_exists($images)) {
    echo "Images file not found\nAborting.";
    die();
}

exec("unzip -o {$images} -d {$imageDirectory}");
