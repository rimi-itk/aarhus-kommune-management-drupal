# Aarhus kommune management

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
