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

## Notice
Replace CDN links with downloaded JS files to work offline.

# License
[GNU GPL](LICENSE)