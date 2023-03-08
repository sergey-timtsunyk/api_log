# Application for processing logs file

![tests](https://github.com/serjio-dev/api_log/workflows/app-checker-code/badge.svg)

### Technology
* PHP 8.1
* Symfony 5.4
* Elasticsearch 8.6

#### Start

Setup docker:

```shell
docker-compose up -d
```
Open docker container and setup app: 

```shell
docker-compose exec php-fpm bash

composer install

php bin/console app:creat-index 

php bin/console app:read-logs
```

If need run test:
```shell
docker-compose exec php-fpm bash

composer install --dev

php bin/phpunit

comopser phpcf
```

`comopser phpcf` - checking code to PSR 12

#### REST Api

`http://localhost:8080/log?count=2&page=2`

`count, page` - optional params




#### Need step for implement:
- `Log/Handler/LogIndex/BatchHandler.php` 
  - add time limit for processing
  
- `Controller/Log/Controller.php`
  - add filter for search
  - move query builder to another class 
  - add authentication 
  
- `php bin/console app:read-logs`
  - add config for supervisor
  - add time limit for processing and exit from command
  
- logic coverage by tests
- move configs from docker and application to .env for flexibility
- Add make commands for app 
