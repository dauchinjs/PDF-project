# PDF-project

## Table of contents
* [General info](#general-info)
* [Demonstration](#demonstration)
* [Technologies](#technologies)
* [Setup](#setup)

## General info

This project is a single page app with thumbnails of 20 PDF documents per page. Clicking on a PDF thumbnail, it opens up the document as a modal. It is possible to upload and delete the PDF documents with max size of 100kB.
This project also includes PHPUnit testing for each back-end controller function.

## Demonstration
(![image](https://user-images.githubusercontent.com/93677423/229494687-e4d8ae8d-dcaa-4068-9eb7-09e02cd3c24b.png)

## Technologies

Project is created with:
* PHP version: 8.0
* Laravel version: 9
* MySQL version: 8.0.31-0ubuntu0.20.04.2 for Linux on x86_64 ((Ubuntu))
* Composer version: 2.4.4
* PHPUnit version: 9.5

## Setup

1. Clone this repository `git clone https://github.com/dauchinjs/PDF-project.git`
2. Install all dependencies `composer install` and `npm install` and `npm run dev`
3. Create a database and rename the `.env.example` file to `.env` and add your credentials
4. Because you ran `npm run dev` open a new terminal and generate an app key using `php artisan key:generate`
5. Run migrations `php artisan migrate`
6. Before running tests:
    * make a test database
    * add the necessary credentials for DB_TEST_... in `.env` file
    * at the bottom of the file add a path to a PDF document (less than 100kB)
    * in terminal use `php artisan migrate --database={test-database}` (test-database = your test database)
    * in `phpunit.xml` file locate `<env name="DB_DATABASE" value=""/>` and add your test database name for the value
7. Run the local server using `php artisan serve`
8. To run tests use `php artisan test`
