Contact communication view
==========================


## How to install

- Download composer executable

- Install composer libraries  
`
php composer.phar install
`
- Setup the database configuration in parameters.yml (database, user and password)
- Create database  
`
php bin/console doctrine:database:create
`
- Create database schema
`
php bin/console doctrine:schema:create
`

## Running the app
- Launch the webserver
`
php bin/console server:run
`
- Visit http://127.0.0.1:8000

## Run tests
`
./vendor/bin/phpunit -c phpunit-test.xml
`

## Notes

I´ve created a list of issues in the repository, that shows how the features would have been delivered with enought time.

The entities defined are:
- PhoneOwner
- Contact
- Operation

Doctrine relations only have been used in Operation, as the other entities did not need them for the current implementation.

About the code, I´ve tried to create a flexible tool for the import of the logs, that is able to retrieve, process, create and update the contents of the logs. In the future, it would allow to be extended to other formats and locations.
As the code has a good coverage, it can be refactored easily to take out logic into new classes or any changes needed (improve validation, error handling, create new factory methods, ...).

In this iteration, the phone number used is hardcoded, but with minimal changes it will work with any number.
