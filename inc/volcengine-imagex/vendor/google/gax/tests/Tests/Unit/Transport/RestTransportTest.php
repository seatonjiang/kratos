<?php
/*
 * Copyright 2018 Google LLC
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above
 * copyright notice, this list of conditions and the following disclaimer
 * in the documentation and/or other materials provided with the
 * distribution.
 *     * Neither the name of Google Inc. nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Google\ApiCore\Tests\Unit\Transport;

use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Call;
use Google\ApiCore\RequestBuilder;
use Google\ApiCore\Testing\MockRequest;
use Google\ApiCore\Testing\MockResponse;
use Google\ApiCore\Transport\RestTransport;
use Google\Auth\FetchAuthTokenInterface;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class RestTransportTest extends TestCase
{
    private $call;

    public function setUp()
    {
        $this->call = new Call(
            'Testing123',
            MockResponse::class,
            new MockRequest()
        );
    }

    private function getTransport(callable $httpHandler = null, $apiEndpoint = 'http://www.example.com')
    {
        $request = new Request('POST', $apiEndpoint);
        $requestBuilder = $this->getMockBuilder(RequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestBuilder->method('build')
            ->willReturn($request);

        return new RestTransport(
            $requestBuilder,
            $httpHandler ?: HttpHandlerFactory::build()
        );
    }

    /**
     * @param $apiEndpoint
     * @dataProvider startUnaryCallDataProvider
     */
    public function testStartUnaryCall($apiEndpoint)
    {
        $expectedRequest = new Request(
            'POST',
            "$apiEndpoint",
            [],
            ""
        );

        $body = ['name' => 'hello', 'number' => 15];

        $httpHandler = function (RequestInterface $request, array $options = []) use ($body, $expectedRequest) {
            $this->assertEquals($expectedRequest, $request);
            return Promise\promise_for(
                new Response(
                    200,
                    [],
                    json_encode($body)
                )
            );
        };

        $response = $this->getTransport($httpHandler, $apiEndpoint)
            ->startUnaryCall($this->call, [])
            ->wait();

        $this->assertEquals($body['name'], $response->getName());
        $this->assertEquals($body['number'], $response->getNumber());
    }

    public function startUnaryCallDataProvider()
    {
        return [
            ["www.example.com"],
            ["www.example.com:443"],
            ["www.example.com:447"],
        ];
    }

    /**
     * @expectedException \Exception
     */
    public function testStartUnaryCallThrowsException()
    {
        $httpHandler = function (RequestInterface $request, array $options = []) {
            return Promise\rejection_for(new \Exception());
        };

        $this->getTransport($httpHandler)
            ->startUnaryCall($this->call, [])
            ->wait();
    }

    /**
     * @expectedException \Google\ApiCore\ApiException
     */
    public function testStartUnaryCallThrowsRequestException()
    {
        $httpHandler = function (RequestInterface $request, array $options = []) {
            return Promise\rejection_for(
                RequestException::create(
                    new Request('POST', 'http://www.example.com'),
                    new Response(
                        404,
                        [],
                        json_encode([
                            'error' => [
                                'status' => 'NOT_FOUND',
                                'message' => 'Ruh-roh.'
                            ]
                        ])
                    )
                )
            );
        };

        $this->getTransport($httpHandler)
            ->startUnaryCall($this->call, [])
            ->wait();
    }

    /**
     * @dataProvider buildDataRest
     */
    public function testBuildRest($apiEndpoint, $restConfigPath, $config, $expectedTransport)
    {
        $actualTransport = RestTransport::build($apiEndpoint, $restConfigPath, $config);
        $this->assertEquals($expectedTransport, $actualTransport);
    }

    public function buildDataRest()
    {
        $uri = "address.com";
        $apiEndpoint = "$uri:443";
        $restConfigPath = __DIR__ . '/../testdata/test_service_rest_client_config.php';
        $requestBuilder = new RequestBuilder($apiEndpoint, $restConfigPath);
        $httpHandler = [HttpHandlerFactory::build(), 'async'];
        return [
            [
                $apiEndpoint,
                $restConfigPath,
                ['httpHandler' => $httpHandler],
                new RestTransport($requestBuilder, $httpHandler)
            ],
            [
                $apiEndpoint,
                $restConfigPath,
                [],
                new RestTransport($requestBuilder, $httpHandler),
            ],
        ];
    }

    /**
     * @dataProvider buildInvalidData
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testBuildInvalid($apiEndpoint, $restConfigPath, $args)
    {
        RestTransport::build($apiEndpoint, $restConfigPath, $args);
    }

    public function buildInvalidData()
    {
        $restConfigPath = __DIR__ . '/../testdata/test_service_rest_client_config.php';
        return [
            [
                "addresswithtoo:many:segments",
                $restConfigPath,
                [],
            ],
            [
                "address.com",
                "badpath",
                [],
            ],
        ];
    }

    /**
     * @expectedException \Google\ApiCore\ApiException
     * @expectedExceptionMessage <html><body>This is an HTML response<\/body><\/html>
     * @expectedExceptionCode 5
     */
    public function testNonJsonResponseException()
    {
        $httpHandler = function (RequestInterface $request, array $options = []) {
            return Promise\rejection_for(
                RequestException::create(
                    new Request('POST', 'http://www.example.com'),
                    new Response(
                        404,
                        [],
                        "<html><body>This is an HTML response</body></html>"
                    )
                )
            );
        };

        $this->getTransport($httpHandler)
            ->startUnaryCall($this->call, [])
            ->wait();
    }

    public function testAudienceOption()
    {
        $credentialsWrapper = $this->prophesize(CredentialsWrapper::class);
        $credentialsWrapper->getAuthorizationHeaderCallback('an-audience')
            ->shouldBeCalledOnce()
            ->willReturn(function() { return []; });

        $options = [
            'audience' => 'an-audience',
            'credentialsWrapper' => $credentialsWrapper->reveal(),
        ];

        $httpHandler = function (RequestInterface $request, array $options = []) {
            return Promise\promise_for(new Response(200, [], '{}'));
        };

        $this->getTransport($httpHandler)
            ->startUnaryCall($this->call, $options)
            ->wait();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The "headers" option must be an array
     */
    public function testNonArrayHeadersThrowsException()
    {
        $options = [
            'headers' => 'not-an-array',
        ];

        $this->getTransport()
            ->startUnaryCall($this->call, $options);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Expected array response from authorization header callback
     */
    public function testNonArrayAuthorizationHeaderThrowsException()
    {
        $credentialsWrapper = $this->prophesize(CredentialsWrapper::class);
        $credentialsWrapper->getAuthorizationHeaderCallback(null)
            ->shouldBeCalledOnce()
            ->willReturn(function() { return ''; });

        $options = [
            'credentialsWrapper' => $credentialsWrapper->reveal(),
        ];

        $this->getTransport()
            ->startUnaryCall($this->call, $options);
    }
}
