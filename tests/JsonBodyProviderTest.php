<?php

namespace Kumatch\Test\Silex;

use Kumatch\Silex\JsonBodyProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

class JsonBodyProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Application */
    protected $app;

    protected function setUp()
    {
        parent::setUp();

        $this->app = new Application();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function parseJsonBody()
    {
        $self = $this;
        $body = array(
            "number" => 123,
            "string" => "foo",
            "array" => array (5, 27, 42),
            "object" => (object)array("bar" => "baz"),
            "true" => true,
            "false" => false,
        );

        $this->app->register(new JsonBodyProvider());
        $this->app->post("/", function (Request $req) use ($body, $self) {
            $self->assertCount(6, $req->request);

            $self->assertEquals($req->request->get("number"), $body["number"]);
            $self->assertEquals($req->request->get("string"), $body["string"]);
            $self->assertEquals($req->request->get("array"), $body["array"]);
            $self->assertTrue($req->request->get("true"));
            $self->assertFalse($req->request->get("false"));

            $obj = $req->request->get("object");
            $self->assertEquals($obj["bar"], $body["object"]->bar);

            return "Done.";
        });

        $client = new Client($this->app);
        $client->request('POST', '/',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($body)
        );
        $response = $client->getResponse();

        $this->assertEquals("Done.", $response->getContent());
    }

    /**
     * @test
     */
    public function notParsingIfContentTypeIsNotApplicationJson()
    {
        $self = $this;
        $body = array(
            "number" => 123,
            "string" => "foo",
            "array" => array (5, 27, 42),
            "object" => (object)array("bar" => "baz"),
            "true" => true,
            "false" => false,
        );

        $this->app->register(new JsonBodyProvider());
        $this->app->post("/", function (Request $req) use ($self) {
            $self->assertCount(0, $req->request);

            return "Done.";
        });

        $client = new Client($this->app);
        $client->request('POST', '/',
            array(),
            array(),
            array('CONTENT_TYPE' => 'plain/text'),
            json_encode($body)
        );
        $response = $client->getResponse();

        $this->assertEquals("Done.", $response->getContent());
    }

    /**
     * @test
     */
    public function notReplaceIfBodyContentIsNotJson()
    {
        $self = $this;
        $body = array(
            "number" => 123,
            "string" => "foo",
            "array" => array (5, 27, 42),
            "object" => (object)array("bar" => "baz"),
            "true" => true,
            "false" => false,
        );

        $this->app->register(new JsonBodyProvider());
        $this->app->post("/", function (Request $req) use ($self) {
            $self->assertCount(0, $req->request);

            return "Done.";
        });

        $client = new Client($this->app);
        $client->request('POST', '/',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($body) . "..broken!!!"
        );
        $response = $client->getResponse();

        $this->assertEquals("Done.", $response->getContent());
    }
}