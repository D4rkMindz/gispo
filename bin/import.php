<?php

use App\Model\UserModel;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/bootstrap.php';

$container = app()->getContainer();
$out = new ConsoleOutput();

$file = get_file($out);
$students = read_students($file, $out);

$existing = [];
$out->writeln(sprintf('Importing %s users', count($students)));
$progress = new ProgressBar($out, count($students));
$progress->start();
foreach ($students as $key => $student) {
    $progress->advance();
    $user = $container->get(UserModel::class);
    if ($user->exists($student['barcode'])) {
        $existing[] = $student;
        unset($students[$key]);
        continue;
    }

    $students[$key]['id'] = $user->save($student);
}
$progress->finish();
$out->writeln('');

$out->writeln('<info>Done</info>');

/**
 *
 *
 * @param string $file
 * @param OutputInterface $out
 * @return array
 */
function read_students(string $file, OutputInterface $out): array
{
    $handle = fopen($file, 'r');
    $students = [];
    $withoutBarcode = [];
    while (($record = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
        if (!empty($record[4])) {
            $students[] = [
                'last_name' => $record[0],
                'first_name' => $record[1],
                'photo_file_name' => $record[2],
                'email' => $record[3],
                'barcode' => $record[4],
            ];
        } else {
            $withoutBarcode[] = [
                'last_name' => $record[0],
                'first_name' => $record[1],
                'photo_file_name' => $record[2],
                'email' => $record[3],
            ];
        }
    }

    if (!empty($withoutBarcode)) {
        echo sprintf("%s Students without barcode:\n", count($withoutBarcode));
        $table = new Table($out);
        $table->setHeaders(['First name', 'Last name', 'Image', 'Email']);
        $table->setRows($withoutBarcode);
        $table->setStyle('borderless');
        $table->render();
    }

    return $students;
}

/**
 *
 *
 * @return false|mixed|string
 */
function get_file(OutputInterface $out)
{
    $file = getopt('f:')['f'];
    if (!file_exists($file)) {
        $file = realpath(__DIR__ . '/' . $file);
        if (!file_exists($file)) {
            $out->writeln(sprintf('<error>File %s not found</error>', $file));
            exit(255);
        }
    }

    return $file;
}