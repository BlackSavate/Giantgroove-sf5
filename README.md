#   G i a n t g r o o v e - s f 5 
 
## Launch local server
`php -S 127.0.0.1:8000 -t public`

## Compile and watch assets
`yarn encore dev --watch`

## Testing
### run all tests of the application
`php bin/phpunit`
### run all tests in the Util/ directory
`php bin/phpunit tests/Util`
### run test for the Foo class
`php bin/phpunit tests/Util/FooTest.php`

## Generate migrations
### clear cache
`php bin/console doctrine:cache:clear-metadata`
### generate file
`php bin/console doctrine:migrations:diff`
### migrate
`php bin/console doctrine:migrations:migrate`