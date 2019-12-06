# Laravel Test

an application which fetches data from xml and json endpoints using cronjob

## Installation
Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env


Run the database migrations (Set the database connection in .env before migrating)

    php artisan migrate

Populate the database with seed data.
    
    php artisan db:seed
	
Start the local development server

    php artisan serve

Add entry into your crontab to run API daily

    crontab -e
	
Add following line to your crontab
	* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1



## Requirements

- php > 7.1
- composer
- crontab
