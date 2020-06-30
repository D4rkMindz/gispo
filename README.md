# GISPO GiBM IT Sports Night Management Application
An application MVC template with Slim and CakePHP QueryBuilder.
This application is used at the [GiBM IT Sports Night](http://home.gibmit.ch/).

`bin/` executable files required to setup the application or do some simple tasks
`config/` configuration files
`public/` web server files (with index.php and .htaccess)
`templates/` template files
`resources/` other resource files like CSV-files or translations
`src/` PHP source code (the App namespace)
`tests/` test code
`tmp/` temporary files (logfiles, cache)

# Installation
To setup GISPO you have two options. Either you run the setup script or do it manually.
#### Setup script
Run `php bin/setup.php` in the project root directory to set up the application. It is required to have a setup.sql and 
a users.zip file in the `docs/` folder;

#### Manually
You have to create a database named like the `$config['dbconfig']['database']` value in `config/config.php`. You can rename this value to any name you like.
Rename `env.example.php` to `env.php` in the `config/` folder. Configure the required parameters like database host or user credentials.

Afterwards you can start your local Apache Server with [XAMPP](https://www.apachefriends.org/index.html).
To visit your Website you have to open http://localhost/<project_directory>/.

FORK of [D4rkMindz/app_template](https://github.com/D4rkMindz/app_template)

# Importing students

To import the students, you can simply run the following command
```bash
$ php bin/import.php -u /absolute/path/to/the/users.csv -i /absolute/path/to/the/images.zip
```

Otherwise its also possible to run the command without the arguments. You will be asked for it.

# Deployment

To automatically deploy the application, you need to have [ant](https://ant.apache.org/) installed.
Make sure that you have a `config/ant.<stage:[test|staging|prod]>.properties` file (named like `ant.prod.properties`) based on the `config/ant.example.properties` file.
Fill in all the values and run the following command. Afterwards, you will be asked, which stage you want to use. 
The configuration of the <stage> will then be used for the deployment.

```bash
$ ant deploy
Buildfile: /gispo/build.xml
deploy:
    [input] Which config should be used? (test, staging, prod)
```

## Notice
rename example.setup.sql to setup.sql and rename example.users.zip to users.zip to execute setup.php without any errors.

# License
[GNU GPL](LICENSE)