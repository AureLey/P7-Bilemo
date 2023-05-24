# P7-Bilemo [![Codacy Badge](https://app.codacy.com/project/badge/Grade/3e06104152d2496e893e8b805f940674)](https://app.codacy.com/gh/AureLey/P7-Bilemo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
_Creation Web AP_



## Setup

Set PHP in 8.1

Get git repository and clone it

```
git clone https://github.com/AureLey/P7-Bilemo.git
```

### Get composer dependencies

```
composer install
```

### Database creation, migrations and fixtures

#### Database :
```
php bin/console doctrine:database:create
```
#### Migration : 
```
php bin/console doctrine:migrations:migrate
```
#### Fixtures
```
php bin/console doctrine:fixtures:load
```

#### JWT Authentification :
```
php bin/console lexik:jwt:generate-keypair
```
Your keys will land in config/jwt/private.pem and config/jwt/public.pem

## Launch local server 
```
symfony server:start
```

## Nelmio is use for documentation
```
[symfony server:start](http://127.0.0.1:8000/api/doc)
```
if the local server is different your_address/api/doc

## Default Credentials

### CustomerUser : 
```
customeruser0@gmail.com
```
### Password : 
```
customeruser0
```
_You can replace 0 by [0-4], 5 customerUser are created in fixtures_

