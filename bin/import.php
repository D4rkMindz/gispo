<?php

use App\Model\UserModel;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/bootstrap.php';

$container = app()->getContainer();
$in = new ArgvInput();
$definitions = new InputDefinition([
    new InputOption('users', 'u', InputOption::VALUE_OPTIONAL),
    new InputOption('images', 'i', InputOption::VALUE_OPTIONAL),
]);
$in->bind($definitions);
$out = new ConsoleOutput();

$file = get_users_file($in, $out);
$students = read_students($file, $out);
import_students($out, $students, $container);

$images = get_image_file($in, $out);
save_images($images, $out);

/**
 * Import students into db
 *
 * @param ConsoleOutput $out
 * @param array $students
 * @param ContainerInterface $container
 * @return void
 */
function import_students(ConsoleOutput $out, array $students, ContainerInterface $container): void
{
    $existing = [];
    $out->writeln(sprintf('Importing %s users', count($students)));
    $progress = new ProgressBar($out, count($students));
    $progress->start();
    foreach ($students as $key => $student) {
        $progress->advance();
        /** @var UserModel $user */
        $user = $container->get(UserModel::class);
        if ($user->exists($student['barcode'])) {
            $user->updateByBarcode($student);
            $existing[] = $student;
            unset($students[$key]);
            continue;
        }

        $students[$key]['id'] = $user->save($student);
    }
    $progress->finish();
    $out->writeln('');

    $out->writeln('<info>Done</info>');
}

/**
 * Read students from file
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
 * Get users file
 *
 * @param InputInterface $input
 * @param OutputInterface $out
 * @return false|mixed|string
 */
function get_users_file(InputInterface $input, OutputInterface $out)
{
    $file = null;
    if ($input->hasOption('users')) {
        $file = $input->getOption('users');
    }
    if (empty($file)) {
        $question = new Question('Where is the file to import? [csv]');
        $helper = new QuestionHelper();
        $file = $helper->ask($input, $out, $question);
    }
    if (!file_exists($file)) {
        $file = realpath(__DIR__ . '/' . $file);
        if (!file_exists($file)) {
            $out->writeln(sprintf('<error>File %s not found</error>', $file));
            exit(255);
        }
    }

    return $file;
}

/**
 * Get image file
 *
 * @param InputInterface $input
 * @param OutputInterface $out
 * @return false|mixed|string
 */
function get_image_file(InputInterface $input, OutputInterface $out)
{
    $file = null;
    if ($input->hasOption('images')) {
        $file = $input->getOption('images');
    }
    if (empty($file)) {
        $question = new Question('Where is the file to import? [zip]');
        $helper = new QuestionHelper();
        $file = $helper->ask($input, $out, $question);
    }
    if (!file_exists($file)) {
        $file = realpath(__DIR__ . '/' . $file);
        if (!file_exists($file)) {
            $out->writeln(sprintf('<error>File %s not found</error>', $file));
            exit(255);
        }
    }

    return $file;
}

/**
 * Save images
 *
 * @param string $file
 * @param OutputInterface $out
 * @return void
 */
function save_images(string $file, OutputInterface $out)
{
    if (!extension_loaded('zip')) {
        $out->writeln('<error>Please enable the ext-zip extension to continue</error>');
        return;
    }
    $zip = new ZipArchive();
    $res = $zip->open($file);
    if ($res) {
        if (!file_exists(__DIR__ . '/../public/img/users')) {
            mkdir(__DIR__ . '/../public/img/users', 0777, true);
        }
        $zip->extractTo(realpath(__DIR__ . '/../public/img/users'));
        $zip->close();
        $out->writeln('Extracted user images to public folder');

        return;
    }
    $out->writeln('Zip corrupt');
}