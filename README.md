# Laravel RESTful

## Instalación

Agregar al archivo composer.json del proyecto Laravel principal este repositorio. Luego instalar con composer.

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/mchamper/laravel-restful.git"
    }
]
```

```
composer require mchamper/laravel-restful
```

## Utilización

Una vez instalado tendrás disponible la clase RESTful para integrar con cualquier clase de Eloquent:

```
use Mchamper\LaravelRestful\IRESTfulController;
use Mchamper\LaravelRestful\RESTful;

$params = request()->query();
$res = (new RESTful(new Model(), $params))->paginate();
```
