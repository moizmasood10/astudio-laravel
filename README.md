**Laravel API+ Filament CRUD**

Scaffolded the Models, Migrations, and Factories using Blueprint.

Installed API module using php artisan install:api

**Auth**
- Created API routes for Auth
- Created AuthController for the store, login, and logout functions.
- Created AuthTest to test the API for Auth.

**User**
- Created API routes for User
- Created UserController for the User store, index, show, destroy, update methods.
- Added filters to the index method.
- Created UserTest to test the API routes for User

**Project**
- Created API routes for Project
- Created ProjectController for the Project store, index, show, destroy, update methods.
- Created ProjectTest to test the API routes for Project

**Timesheet**
- Created API routes for Timesheet
- Created TimesheetController for the Timesheet store, index, show, destroy, update methods.
- Created UserTest to test the API routes for Timesheet

**Filament Admin CRUD Panel (Do check this out!)**
Installed the panel for easy CRUD capabilities. 
Can be accessed using /admin



**Instructions**

The tests have been formulated to test the API endpoints, and can be viewed in the tests directory under the Unit subdirectory.

- Pull the GitHub repository https://github.com/moizmasood10/astudio-laravel
- Run composer install
- Run php artisan serve
- Run php artisan test

To view the filament admin panel, first seed the database, use ‘php artisan migrate:fresh -- seed’
The seeders have already been created

Then head over to /admin, and login using 
- moiz@gmail.com
- moiz1234

![Create](imgs/create.png)
![Table](imgs/table.png)
![Tests](imgs/tests.png)



