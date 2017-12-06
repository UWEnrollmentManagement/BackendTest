<?php

namespace UWDOEM\REST\Backend\Test;

use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;


abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var APIFaker */
    protected $faker;

    /** @var string $appClass */
    protected $appClass;

    public function __construct($name = null, $appClass, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $instance = $this;
        $this->faker = new APIFaker(
            [
                'reference' => function($resourceType) use ($instance) {
                    $response = $instance->doCreateRequiredOnly($resourceType);
                    $responseData = $instance->responseToArray($response);

                    return $responseData['data']['id'];
                },
            ]
        );
    }

    protected function doRequest($method, $path, $data = null)
    {
        $appClass = $this->appClass;
        $app = $appClass::get();

        $vars = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => Uri::createFromString($path),
            'CONTENT_TYPE' => 'application/json;charset=utf8',
        ];


        $env = Environment::mock($vars);
        $request = Request::createFromEnvironment($env);

        if($data) {
            $request->getBody()->write(json_encode($data));   
        }

        $app->getContainer()['request'] = $request;

        return $app->run(true);
    }

    protected function doCreate($resourceType, $requestData = [])
    {
        $requestData = $this->faker->fake($resourceType, $requestData);

        // Build the request
        $request = [
            'method' => 'POST',
            'path' => "/$resourceType/",
            'data' => $requestData,
        ];

        // Issue the request
        return $this->doRequest($request['method'], $request['path'], $request['data']);
    }

    protected function doCreateRequiredOnly($resourceType, $requestData = [])
    {
        $requestData = $this->faker->fakeRequiredOnly($resourceType, $requestData);

        // Build the request
        $request = [
            'method' => 'POST',
            'path' => "/$resourceType/",
            'data' => $requestData,
        ];

        // Issue the request
        return $this->doRequest($request['method'], $request['path'], $request['data']);
    }

    protected function responseToArray(Response $response)
    {
        $body = (string)$response->getBody();

        $responseData = json_decode($body, true);
        $this->assertNotNull($responseData, "Response should be valid json. Instead was: " . (string)$body);

        return $responseData;
    }

    protected function assertArrayHasKeys($keys, $array, $additionalMessage=null) {

        foreach ($keys as $key) {
            $this->assertArrayHasKey(
                $key,
                $array,
                "Array must contain key `$key`, but only contains keys: " . implode(', ', array_keys($array)) . " $additionalMessage"
            );
        }
    }

    protected function assertHasRequiredResponseElements($responseData, $message=null)
    {
        $requiredResponseFields = [
            "success",
            "status",
            "previous",
            "current",
            "next",
            "time",
            "data",
            "error",
        ];

        $this->assertArrayHasKeys($requiredResponseFields, $responseData, $message);
    }
}
