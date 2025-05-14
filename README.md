## Celie

Celie is a backend project designed to help women who have experienced domestic violence. The application allows users to register emergency contact numbers. When the first emergency contact is triggered, the others will be notified via WhatsApp. Additionally, the application enables victims to share their location with emergency contacts.

This backend project is structured using the template from tsi34d-framework-template.

### Dependências

- Docker
- Docker Compose

### To run

#### Clone Repository

```
$ git clone git@github.com:SI-DABE/problem-track.git
$ cd problem-track
```

#### Define the env variables

```
$ cp .env.example .env
```

#### Install the dependencies

```
$ ./run composer install
```

#### Up the containers

```
$ docker compose up -d
```

ou

```
$ ./run up -d
```

#### Create database and tables

```
$ ./run db:reset
```

#### Populate database

```
$ ./run db:populate
```

### Fixed uploads folder permission

```
sudo chown www-data:www-data public/assets/uploads
```

#### Run the tests

```
$ docker compose run --rm php ./vendor/bin/phpunit tests --color
```

ou

```
$ ./run test
```

#### Run the linters

[PHPCS](https://github.com/PHPCSStandards/PHP_CodeSniffer/)

```
$ ./run phpcs
```

[PHPStan](https://phpstan.org/)

```
$ ./run phpstan
```

Access [localhost](http://localhost)

### Teste de API

#### Rota não autenticada

```shell
curl -H "Accept: application/json" localhost/problems
```

#### Rota autenticada

Neste caso precisa alterar o valor do PHPSESSID de acordo com a o id da sua sessão.

```shell
curl -H "Accept: application/json" -b "PHPSESSID=5f55f364a48d87fb7ef9f18425a8ae88" localhost/problems
```
