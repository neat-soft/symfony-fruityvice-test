# Welcome to Fruit!

This is symfony test project with datatable.
### Requirements
1. Install PHP 8.1 or higher
2. Install Symfony CLI
3. Install Composer  2.5.4
4. Install MySQL 

### Configuration
1. Create DB name with **fruit**
2. Update `.env` file

	- Change **DB_NAME** value  to `fruit`
	- Change **DB_USER** and  **DB_PASSWORD** with your MySQL user
3. Install vendor by run command `composer install` in your project root directory 

### DB migration
	`php bin/console doctrine:migrations:migrate`
	
### Run Console Command
In your project root directory run console command `php bin/console app:fetch-fruit`
This console command fetch new fruits from fruityvice.com and save into DB.

### Run Server
`symfony server:start`