<?php
namespace Kumatch\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class JsonBodyProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->before(function (Request $req) {
            if (0 === strpos($req->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($req->getContent(), true);
                $req->request->replace(is_array($data) ? $data : array());
            }
        });
    }

    public function boot(Application $app)
    {
    }
}