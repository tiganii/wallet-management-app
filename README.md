# Wallet Management Application
This application is a simulation of basic wallet operations like add funds, transfer funds, create transactions PDF, Transfer QR Code  ..etc. 

## Table of Contents
- [Dependencies](#dependencies)
- [Installation](#installation)
- [Usage](#usage)
- [Credits](#credits)

## Dependencies
- "php": "^8.2"
- imagick php extension for generating QR Code png images
- - To install imagick on MacOS:
- - brew install imagemagick
- - brew install pkg-config
- - pecl install imagick 

## Installation
- Clone the repository and cd into wallet-management-app
- Install dependencies => composer install 
- Copy .env.example into .env file and setup the database connection 
- Run unit and feature tests => php artisan test
- Run the migration and seeder => php artisan migrate  —seed
- Generate the application key => php artisan key:generate
- Create JWT token secret =>  php artisan jwt:secret
- et smtp mail configuration and the user can receive real notification 
- Start the server => php artisan serve
- Run the queue to send notification => php artisan queue:work

## Credits
Acknowledge to whom give the opportunity to work on the project.

