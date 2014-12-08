Silex JSON body provider
===========

A Silex service provider for JSON body parsing.
See silex cookbook. http://silex.sensiolabs.org/doc/cookbook/json_request_body.html

[![Build Status](https://travis-ci.org/kumatch/silex-json-body-provider.png?branch=master)](https://travis-ci.org/kumatch/silex-json-body-provider)

Install
-----

    $ composer require kumatch/silex-json-body-provider


Example
-----

```php
<?php

use Silex\Application;
use Kumatch\Silex\JsonBodyProvider;

$app = new Application();
$app->register(new JsonBodyProvider());

// ex. post JSON '{"value":42}'
$app->post("/", function (Request $req) {
    $value = $req->request->get("value");  // 42
});
```


License
--------

Licensed under the [MIT License](http://kumatch.mit-license.org/).
