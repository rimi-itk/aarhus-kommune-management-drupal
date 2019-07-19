# Aarhus kommune management

This Drupal 7 module does stuff.

See https://github.com/rimi-itk/aarhus-kommune-management-documentation for general documentation.

## Installation

Downloading the code:

```sh
cd «drupal root»
mkdir -p sites/all/modules/contrib/
git clone --branch=master https://github.com/rimi-itk/aarhus-kommune-management-drupal-7 sites/all/modules/contrib/aarhus_kommune_management
```

Installing the module:

```sh
cd «site root»
drush --yes pm-enable aarhus_kommune_management
```

Configuration:

`admin/config/aarhus-kommune-management/users`

```sh
openssl genrsa -out private.key 2048
openssl rsa -in private.key -pubout -out public.key
```

## API endpoints

* `/aarhus-kommune-management/authenticate`
* `/aarhus-kommune-management/users`

## Examples

```sh
curl --silent --header 'accept: application/json' /aarhus-kommune-management/users
```

```sh
curl --silent --header 'content-type: application/json' --data @- http://mso-loop.docker.localhost:32792/aarhus-kommune-management/users <<'JSON'
{
 "create": [
  {
   "uuid": "hat-og-briller",
   "name": "Hat & briller"
  }
 ]
}
JSON
```
