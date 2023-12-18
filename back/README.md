# O'BABY

Production access:

https://obaby.simschab.cloud

Api documentation:

https://obaby.simschab.cloud/api

# To install the project

Clone the repository

```bash
git clone git@github.com:O-clock-Meduse/projet-05-obaby-back.git
```

install composer dependencies

```bash
composer install
```

make your .env.local file

```bash
cp .env .env.local
```

add your database credentials to .env.local

Add Mercure credentials to .env.local for SSE and Real Time communication
(paste the following lines at the end of the file)

```bash
MERCURE_URL=https://mercure.simschab.cloud/.well-known/mercure
# The public URL of the Mercure hub, used by the browser to connect
MERCURE_PUBLIC_URL=https://mercure.simschab.cloud/.well-known/mercure
# The secret used to sign the JWTs
MERCURE_JWT_SECRET="!ChangeThisMercureHubJWTSecretKey!"
```

Allow all origin in .env.local to try the app using the front

```bash
CORS_ALLOW_ORIGIN=^http?://.*?$
```

add your `mailtrap` credentials to .env.local to try the mailer component depending features (eg: password reset or contact form)

```bash
MAILER_DSN=smtp://<username>:<password>@<host>:<port>
```

or use `mailhog` on localhost to try the mailer component depending features (eg: password reset or contact form)

```bash
MAILER_DSN=smtp://localhost:1025
```

add a fake admin mail to .env.local

```bash
ADMIN_EMAIL="xxxxxxx@xxxx.xx"
```

uncomment the line admin mail parameter in `/config/services.yaml` to use the fake admin mail parameter from .env.local

```bash
admin_email: '%env(ADMIN_EMAIL)%'
```

create database

```bash
bin/console d:d:c
```

create tables

```bash
bin/console d:m:m
```

load fixtures

```bash
bin/console d:f:l
```

launch the local server

```bash
symfony serve
```

go to https://127.0.0.1:8000/api to see the api documentation
