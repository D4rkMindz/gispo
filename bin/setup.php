<?php
#!/usr/bin/env php
$start = microtime(true);

require_once __DIR__ . '/../lib/util.php';

echo "---------------------------------------------------------\n";
echo "¦                                                    ¦\n";
echo "¦       ███ █ ███ ███ ███   ███ ███ ███ GiBM IT      ¦\n";
echo "¦       █   █ █   █ █ █ █   █ █ █ █ █ █ Sports       ¦\n";
echo "¦       █ █ █ ███ ███ █ █   ███ ███ ███ Night        ¦\n";
echo "¦       █ █ █   █ █   █ █   █ █ █   █                ¦\n";
echo "¦       ███ █ ███ █   ███   █ █ █   █   2018         ¦\n";
echo "¦                                                    ¦\n";
echo "---------------------------------------------------------\n";
echo "executing setup\n";

$defaults = get_configuration();
$dbconfig = configure_application($defaults['showErrors']);
setup_database($dbconfig);
install_dependencies($defaults['flag']);
unzip_images();

$end = microtime(true);

$duration = $end - $start;
echo "---------------------------------------------------------\n";
echo "\n";
echo "\tFinished in " . (round($duration, 0.001)) . " seconds";
echo "\n";
echo "\n";
echo "---------------------------------------------------------\n";
echo "¦                                                       ¦\n";
echo "¦      ███ ███  ███ ███   ███ ███ ███ █ █ █ ███ ███ ███ ¦\n";
echo "¦      █ █ █ █  █   █ █   █   █   █ █ █ █ █ █   █   █   ¦\n";
echo "¦      █ █ ██   █   ███   ███ ██  ██  █ █ █ █   ██  ███ ¦\n";
echo "¦      █ █ █ █  █   █ █     █ █   █ █ █ █ █ █   █     █ ¦\n";
echo "¦ by   ███ █  █ ███ █ █   ███ ███ █ █  █  █ ███ ███ ███ ¦\n";
echo "¦                                                       ¦\n";
echo "---------------------------------------------------------";

/**
 * Get default configuration.
 *
 * @return array
 */
function get_configuration()
{
    do {
        $env = (string)readline("Are you setting up the application in a prod or dev environment? [p/d]\t");
        if ($env == 'p' || $env == 'd') {
            break;
        }
    } while (true);

    $flag = "";
    $showErrors = "true";

    if ($env === "p") {
        $flag = "--no-dev";
        $showErrors = "false";
    }

    return [
        'flag' => $flag,
        'showErrors' => $showErrors,
    ];
}

/**
 * Create env.php file
 *
 * @param $showErrors
 * @return array $db DB-Config
 */
function configure_application($showErrors): array
{
    $db = [];
    do {
        $correct = false;
        $db["host"] = readline("Please enter the database host (default = 127.0.0.1): \t");
        $db["host"] = !empty($db['host']) ? $db['host'] : '127.0.0.1';

        $db["port"] = readline("Please enter the database port (default = 3306): \t");
        $db["port"] = !empty($db['port']) ? $db['port'] : '3306';

        $db["username"] = readline("Please enter the database user (default = root): \t");
        $db["username"] = !empty($db['username']) ? $db['username'] : 'root';

        $db["password"] = readline("Please enter the database password (default = \"\"): \t");
        $db["password"] = !empty($db['password']) ? $db['password'] : '';

        $c = readline("Is the data correct? [y/n]: \t");
        if ($c === 'y') {
            $correct = true;
        }
    } while ($correct === false);

    $envString = file_get_contents(__DIR__ . '/../config/env.example.php');
    $showErrorsStr = $showErrors ? 'true' : 'false';
    $envString = str_replace('{{display_error_details}}', $showErrorsStr, $envString);
    $envString = str_replace('{{db_host}}', $db['host'], $envString);
    $envString = str_replace('{{db_port}}', $db['port'], $envString);
    $envString = str_replace('{{db_username}}', $db['username'], $envString);
    $envString = str_replace('{{db_password}}', $db['password'], $envString);
    $envString = str_replace('{{minify}}', $showErrorsStr, $envString);
    $envString = str_replace('{{cache_enabled}}', $showErrorsStr, $envString);

    echo "Configuring application\n";
    $envFile = realpath(__DIR__ . '/../config/env.php');
    file_put_contents($envFile, $envString);
    echo "Configured application successfully.\nFor further details see " . $envFile . "\n";

    return $db;
}

/**
 * Setup database.
 *
 * @param $dbconfig
 * @return void
 */
function setup_database($dbconfig)
{
    echo "---------------------------------------------------------\n";
    echo "Setting up the database\n";
    $dsn = "mysql:host=" . $dbconfig["host"];
    $db = new PDO($dsn, $dbconfig["username"], $dbconfig["password"]);

    $sqlFile = realpath(__DIR__ . '/../docs/setup.sql');
    $query = file_get_contents($sqlFile);

    $stmt = $db->prepare($query);

    if ($stmt->execute()) {
        echo "Setup database successfully\n";
    } else {
        echo "There was a problem setting up the database\n";
        echo "Please execute " . $sqlFile . "\n";
    }
}

/**
 * Install dependencies.
 *
 * @param $flag
 * @return void
 */
function install_dependencies($flag)
{
    echo "---------------------------------------------------------\n";
    echo "installing dependencies\n";
    $pharFile = __DIR__ . '/composer.phar';
    if (!is_file($pharFile)) {
        $data = file_get_contents('https://getcomposer.org/composer.phar');
        $handle = fopen($pharFile, 'w+');
        fwrite($handle, $data);
        fclose($handle);
    }
    $dir = __DIR__ . '/../';
    $projdir = realpath($dir);
    system("cd $projdir");
    if (is_dir($dir . 'vendor')) {
        rrmdir($dir . 'vendor');
    }
    $command = "php " . $pharFile . " install --no-suggest " . $flag;
    $command = trim($command);
    system($command);

    echo "Installed dependencies successfully\n";
}

/**
 * Unzip images to correct place
 *
 * @return void
 */
function unzip_images()
{
    echo "---------------------------------------------------------\n";
    echo "Unzipping images\n";
    $zipFile = realpath(__DIR__ . '/../docs/users.zip');
    $zipDest = realpath(__DIR__ . '/../public/img/users');

    if (is_dir($zipDest)) {
        rrmdir($zipDest);
    }
    mkdir($zipDest);
    $zip = new ZipArchive();
    $res = $zip->open($zipFile);
    if ($res) {
        $zip->extractTo($zipDest);
        $zip->close();
        echo "Unzipped images successfully\n";
    } else {
        echo "Unzipping images failed\n";
        echo "Please unzip file manually\n";
        echo "Source:\t\t" . $zipFile . "\n";
        echo "Destination:\t" . $zipDest . "\n";
    }
}
