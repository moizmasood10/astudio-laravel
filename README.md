**Laravel API+ Filament CRUD**

1) Scaffolded the Models, Migrations, and Factories using Blueprint.
1) Installed API module using php artisan install:api
1) **Auth**
1) Created API routes for Auth
1) Created AuthController for the store, login, and logout functions.
1) Created AuthTest to test the API for Auth.
4) **User**
1) Created API routes for User
1) Created UserController for the User store, index, show, destroy, update methods.
1) Created UserTest to test the API routes for User
5) **Project**
1) Created API routes for Project
1) Created ProjectController for the Project store, index, show, destroy, update methods.
1) Created ProjectTest to test the API routes for Project
6) **Timesheet**
1) Created API routes for Timesheet
1) Created TimesheetController for the Timesheet store, index, show, destroy, update methods.
1) Created UserTest to test the API routes for Timesheet
7) **Filament Admin CRUD Panel (Do check this out!)**
1) Installed the panel for easy CRUD capabilities.
1) Can be accessed using /admin

**Instructions**

The tests have been formulated to test the API endpoints, and can be viewed in the tests directory under the Unit subdirectory.

1. Pull the GitHub repository <https://github.com/moizmasood10/astudio-laravel>
1. Run composer install
1. Run php artisan serve
1. Run php artisan test

To view the filament admin panel, first seed the database, use ‘php artisan migrate:fresh -- seed’

● The seeders have already been created

Then head over to /admin, and login using

![](Aspose.Words.619a0d11-d73b-4e28-9546-9200ce52cef7.001.jpeg)

![](Aspose.Words.619a0d11-d73b-4e28-9546-9200ce52cef7.002.jpeg)

![](Aspose.Words.619a0d11-d73b-4e28-9546-9200ce52cef7.003.jpeg)
