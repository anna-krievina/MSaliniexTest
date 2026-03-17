Execution of https://github.com/saleniex/homework3

This is an API that calls info about stations from https://data.gov.lv/dati/lv/dataset/hidrometeorologiskie-noverojumi/resource/c32c7afd-0d05-44fd-8b24-1de85b4bf11d

**For docker**

First in the project directory create a file `env.runtime`
with variables
```
APP_SECRET=
API_TOKEN=
```
(value can be any but API_TOKEN needs a value for authorization that is later used in requests)

then from project directory run
```
docker build -t m-saleniex-test:latest .
```
and then (port before : in -p can be different if ports conflict)
```
docker run -d --name m-saleniex-test -p 8000:8000 --env-file .env.runtime m-saleniex-test:latest
```

for repeated runs, first do (or run without `--name m-saleniex-test`)
```
docker rm m-saleniex-test
```

**locally**, the project can be run by (with PHP 8.4 and Symfony installed.)
for first time do
```
composer install
```
and create env.dev with variables (value can be any)
```
APP_SECRET=
API_TOKEN=
```
to run locally
```
php -S localhost:8000 -t public
```

from docker or locally, **service can be accessed** via (port can be different if changed in docker `-p`)

```
http://localhost:8000/api/stations
```
or
```
http://localhost:8000/api/stations/{stationId}
```

For API to work, bearer token (same as API_TOKEN) needs to be used in 'Authorization' header (Postman does this automatically when selecting Auth type "Bearer token")
