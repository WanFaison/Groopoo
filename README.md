HEAD
Before launching the code, navigate to the '.env' file in your Symfony back-end and check the database configuration 'DATABASE_URL'

In case you use a MariaDB server: DATABASE_URL="mysql://username:password@127.0.0.1:3306/groopoo?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
for example: DATABASE_URL="mysql://root:root@127.0.0.1:3306/groopoo?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

In case you use a MySQL server: DATABASE_URL="mysql://username:password@127.0.0.1:3306/groopoo?serverVersion=16&charset=utf8"
for example: DATABASE_URL="mysql://root:root@127.0.0.1:3306/groopoo?serverVersion=16&charset=utf8"

NB:- ensure the 'serverVersion' corresponds to yours e.g ?serverVersion=mariadb-10.4.32
    - if needed, check the server version from the phpMyAdmin dashboard

ALSO, download(from the link below) and add the 'node_module' folder to the front and 'jwt' folder to the back\config folder. They contain all angular/typescript dependencies and public/private keys respectively required for the webapp:
https://drive.google.com/file/d/1Yf4DA_BfaqI97kgc4KMWM2H0ce91bJmy/view?usp=drive_link

TO CONCLUDE, run 'composer install' command in the root directory of the back folder (this is to ensure that all base dependencies for symfony are available)

# Groopoo

This project was generated with [Angular CLI](https://github.com/angular/angular-cli) version 16.2.14.

## Development server

Run `ng serve` for a dev server. Navigate to `http://localhost:4200/`. The application will automatically reload if you change any of the source files.

## Code scaffolding

Run `ng generate component component-name` to generate a new component. You can also use `ng generate directive|pipe|service|class|guard|interface|enum|module`.

## Build

Run `ng build` to build the project. The build artifacts will be stored in the `dist/` directory.

## Running unit tests

Run `ng test` to execute the unit tests via [Karma](https://karma-runner.github.io).

## Running end-to-end tests

Run `ng e2e` to execute the end-to-end tests via a platform of your choice. To use this command, you need to first add a package that implements end-to-end testing capabilities.

## Further help

To get more help on the Angular CLI use `ng help` or go check out the [Angular CLI Overview and Command Reference](https://angular.io/cli) page.
 0740bf0 (initial commit)
