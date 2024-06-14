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
```bash 
    brew install imagemagick
    brew install pkg-config
    pecl install imagick 
```
## Installation
- Clone the repository and cd into wallet-management-app
- Install dependencies
```bash 
    composer install 
```
- Copy .env.example into .env file and setup the database connection 
- Run unit and feature tests => 
```bash 
    php artisan test 
```
- (Note the test will take a minute to test api rate limiter )
- Run the migration and seeder 
```bash 
    php artisan migrate  --seed 
```
- Generate the application key 
```bash 
    php artisan key:generate 
```
- Create JWT token secret 
```bash 
    php artisan jwt:secret 
```
- Setup smtp mail configuration in .env and the user can receive real notification 
- Start the server 
```bash 
    php artisan serve 
```
- Run the queue to run the event listeners and send notifications to users
```bash 
    php artisan queue:work 
```

## Credits
Acknowledge to whom give the opportunity to work on the project.

