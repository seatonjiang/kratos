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

use Google\ApiCore\Call;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Tests\Unit\TestTrait;
use Google\ApiCore\Testing\MockGrpcTransport;
use Google\ApiCore\Testing\MockRequest;
use Google\ApiCore\Transport\GrpcTransport;
use Google\ApiCore\Transport\Grpc\UnaryInterceptorInterface;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\Message;
use Google\Protobuf\Internal\RepeatedField;
use Google\Rpc\Code;
use Google\Rpc\Status;
use Grpc\BaseStub;
use Grpc\CallInvoker;
use Grpc\ChannelCredentials;
use Grpc\ClientStreamingCall;
use Grpc\Interceptor;
use Grpc\ServerStreamingCall;
use Grpc\UnaryCall;
use PHPUnit\Framework\TestCase;
use stdClass;

class GrpcTransportTest extends TestCase
{
    use TestTrait;

    public function setUp()
    {
        $this->requiresGrpcExtension();
    }

    private function callCredentialsCallback(MockGrpcTransport $transport)
    {
        $mockCall = new Call('method', '', null);
        $options = [];

        $response = $transport->startUnaryCall($mockCall, $options)->wait();
        $args = $transport->getRequestArguments();
        return call_user_func($args['options']['call_credentials_callback']);
    }

    public function testClientStreamingSuccessObject()
    {
        $response = new Status();
        $response->setCode(Code::OK);
        $response->setMessage('response');

        $status = new stdClass;
        $status->code = Code::OK;

        $clientStreamingCall = $this->getMockBuilder(ClientStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientStreamingCall->method('write');
        $clientStreamingCall->method('wait')
            ->will($this->returnValue([$response, $status]));

        $transport = new MockGrpcTransport($clientStreamingCall);

        $stream = $transport->startClientStreamingCall(
            new Call('method', null),
            []
        );

        /* @var $stream \Google\ApiCore\ClientStream */
        $actualResponse = $stream->writeAllAndReadResponse([]);
        $this->assertEquals($response, $actualResponse);
    }

    /**
     * @expectedException \Google\ApiCore\ApiException
     * @expectedExceptionMessage client streaming failure
     */
    public function testClientStreamingFailure()
    {
        $request = "request";
        $response = "response";

        $status = new stdClass;
        $status->code = Code::INTERNAL;
        $status->details = 'client streaming failure';

        $clientStreamingCall = $this->getMockBuilder(ClientStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientStreamingCall->method('wait')
            ->will($this->returnValue([$response, $status]));

        $transport = new MockGrpcTransport($clientStreamingCall);

        $stream = $transport->startClientStreamingCall(
            new Call('takeAction', null),
            []
        );

        $stream->readResponse();
    }

    public function testServerStreamingSuccess()
    {
        $response = "response";

        $status = new stdClass;
        $status->code = Code::OK;

        $message = $this->createMockRequest();

        $serverStreamingCall = $this->getMockBuilder(\Grpc\ServerStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serverStreamingCall->method('responses')
            ->will($this->returnValue([$response]));
        $serverStreamingCall->method('getStatus')
            ->will($this->returnValue($status));

        $transport = new MockGrpcTransport($serverStreamingCall);

        /* @var $stream \Google\ApiCore\ServerStream */
        $stream = $transport->startServerStreamingCall(
            new Call('takeAction', null, $message),
            []
        );

        $actualResponsesArray = [];
        foreach ($stream->readAll() as $actualResponse) {
            $actualResponsesArray[] = $actualResponse;
        }

        $this->assertEquals([$response], $actualResponsesArray);
    }

    public function testServerStreamingSuccessResources()
    {
        $responses = ['resource1', 'resource2'];
        $repeatedField = new RepeatedField(GPBType::STRING);
        foreach ($responses as $response) {
            $repeatedField[] = $response;
        }

        $response = $this->createMockResponse('nextPageToken', $repeatedField);

        $status = new stdClass;
        $status->code = Code::OK;

        $message = $this->createMockRequest();

        $call = $this->getMockBuilder(\Grpc\ServerStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $call->method('responses')
            ->will($this->returnValue([$response]));
        $call->method('getStatus')
            ->will($this->returnValue($status));

        $transport = new MockGrpcTransport($call);

        $call = new Call('takeAction',
            null,
            $message,
            ['resourcesGetMethod' => 'getResourcesList']
        );
        $options = [];

        /* @var $stream \Google\ApiCore\ServerStream */
        $stream = $transport->startServerStreamingCall(
            $call,
            $options
        );

        $actualResponsesArray = [];
        foreach ($stream->readAll() as $actualResponse) {
            $actualResponsesArray[] = $actualResponse;
        }
        $this->assertEquals($responses, $actualResponsesArray);
    }

    /**
     * @expectedException \Google\ApiCore\ApiException
     * @expectedExceptionMessage server streaming failure
     */
    public function testServerStreamingFailure()
    {
        $status = new stdClass;
        $status->code = Code::INTERNAL;
        $status->details = 'server streaming failure';

        $message = $this->createMockRequest();

        $serverStreamingCall = $this->getMockBuilder(\Grpc\ServerStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serverStreamingCall->method('responses')
            ->will($this->returnValue(['response1']));
        $serverStreamingCall->method('getStatus')
            ->will($this->returnValue($status));

        $transport = new MockGrpcTransport($serverStreamingCall);

        /* @var $stream \Google\ApiCore\ServerStream */
        $stream = $transport->startServerStreamingCall(
            new Call('takeAction', null, $message),
            []
        );

        foreach ($stream->readAll() as $actualResponse) {
            // for loop to trigger generator and API exception
        }
    }

    public function testBidiStreamingSuccessSimple()
    {
        $response = "response";
        $status = new stdClass;
        $status->code = Code::OK;

        $bidiStreamingCall = $this->getMockBuilder(\Grpc\BidiStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bidiStreamingCall->method('read')
            ->will($this->onConsecutiveCalls($response, null));
        $bidiStreamingCall->method('getStatus')
            ->will($this->returnValue($status));

        $transport = new MockGrpcTransport($bidiStreamingCall);

        /* @var $stream \Google\ApiCore\BidiStream */
        $stream = $transport->startBidiStreamingCall(
            new Call('takeAction', null),
            []
        );

        $actualResponsesArray = [];
        foreach ($stream->closeWriteAndReadAll() as $actualResponse) {
            $actualResponsesArray[] = $actualResponse;
        }
        $this->assertEquals([$response], $actualResponsesArray);
    }

    public function testBidiStreamingSuccessObject()
    {
        $response = new Status();
        $response->setCode(Code::OK);
        $response->setMessage('response');

        $status = new stdClass;
        $status->code = Code::OK;

        $bidiStreamingCall = $this->getMockBuilder(\Grpc\BidiStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bidiStreamingCall->method('read')
            ->will($this->onConsecutiveCalls($response, null));
        $bidiStreamingCall->method('getStatus')
            ->will($this->returnValue($status));

        $transport = new MockGrpcTransport($bidiStreamingCall);

        /* @var $stream \Google\ApiCore\BidiStream */
        $stream = $transport->startBidiStreamingCall(
            new Call('takeAction', null),
            []
        );

        $actualResponsesArray = [];
        foreach ($stream->closeWriteAndReadAll() as $actualResponse) {
            $actualResponsesArray[] = $actualResponse;
        }
        $this->assertEquals([$response], $actualResponsesArray);
    }

    public function testBidiStreamingSuccessResources()
    {
        $responses = ['resource1', 'resource2'];
        $repeatedField = new RepeatedField(GPBType::STRING);
        foreach ($responses as $response) {
            $repeatedField[] = $response;
        }

        $response = $this->createMockResponse('nextPageToken', $repeatedField);

        $status = new stdClass;
        $status->code = Code::OK;

        $bidiStreamingCall = $this->getMockBuilder(\Grpc\BidiStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bidiStreamingCall->method('read')
            ->will($this->onConsecutiveCalls($response, null));
        $bidiStreamingCall->method('getStatus')
            ->will($this->returnValue($status));

        $transport = new MockGrpcTransport($bidiStreamingCall);

        $call = new Call(
            'takeAction',
            null,
            null,
            ['resourcesGetMethod' => 'getResourcesList']
        );

        /* @var $stream \Google\ApiCore\BidiStream */
        $stream = $transport->startBidiStreamingCall(
            $call,
            []
        );

        $actualResponsesArray = [];
        foreach ($stream->closeWriteAndReadAll() as $actualResponse) {
            $actualResponsesArray[] = $actualResponse;
        }
        $this->assertEquals($responses, $actualResponsesArray);
    }

    /**
     * @expectedException \Google\ApiCore\ApiException
     * @expectedExceptionMessage bidi failure
     */
    public function testBidiStreamingFailure()
    {
        $response = "response";
        $status = new stdClass;
        $status->code = Code::INTERNAL;
        $status->details = 'bidi failure';

        $bidiStreamingCall = $this->getMockBuilder(\Grpc\BidiStreamingCall::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bidiStreamingCall->method('read')
            ->will($this->onConsecutiveCalls($response, null));
        $bidiStreamingCall->method('getStatus')
            ->will($this->returnValue($status));

        $transport = new MockGrpcTransport($bidiStreamingCall);

        /* @var $stream \Google\ApiCore\BidiStream */
        $stream = $transport->startBidiStreamingCall(
            new Call('takeAction', null),
            []
        );

        foreach ($stream->closeWriteAndReadAll() as $actualResponse) {
            // for loop to trigger generator and API exception
        }
    }

    public function testAudienceOption()
    {
        $message = $this->createMockRequest();

        $call = $this->prophesize(Call::class);
        $call->getMessage()->willReturn($message);
        $call->getMethod()->shouldBeCalled();
        $call->getDecodeType()->shouldBeCalled();

        $credentialsWrapper = $this->prophesize(CredentialsWrapper::class);
        $credentialsWrapper->getAuthorizationHeaderCallback('an-audience')
            ->shouldBeCalledOnce();
        $hostname = '';
        $opts = ['credentials' => ChannelCredentials::createInsecure()];
        $transport = new GrpcTransport($hostname, $opts);
        $options = [
            'audience' => 'an-audience',
            'credentialsWrapper' => $credentialsWrapper->reveal(),
        ];
        $transport->startUnaryCall($call->reveal(), $options);
    }

    /**
     * @dataProvider buildDataGrpc
     */
    public function testBuildGrpc($apiEndpoint, $config, $expectedTransportProvider)
    {
        $expectedTransport = $expectedTransportProvider();
        $actualTransport = GrpcTransport::build($apiEndpoint, $config);
        $this->assertEquals($expectedTransport, $actualTransport);
    }

    public function buildDataGrpc()
    {
        $uri = "address.com";
        $apiEndpoint = "$uri:447";
        $apiEndpointDefaultPort = "$uri:443";
        return [
            [
                $apiEndpoint,
                [],
                function () use ($apiEndpoint) {
                    return new GrpcTransport(
                        $apiEndpoint,
                        [
                            'credentials' => null,
                        ],
                        null
                    );
                },
            ],
            [
                $uri,
                [],
                function () use ($apiEndpointDefaultPort) {
                    return new GrpcTransport(
                        $apiEndpointDefaultPort,
                        [
                            'credentials' => null,
                        ],
                        null
                    );
                },
            ],
        ];
    }

    /**
     * @dataProvider buildInvalidData
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testBuildInvalid($apiEndpoint, $args)
    {
        GrpcTransport::build($apiEndpoint, $args);
    }

    public function buildInvalidData()
    {
        return [
            [
                "addresswithtoo:many:segments",
                [],
            ],
            [
                'example.com',
                [
                    'channel' => 'not a channel',
                ]
            ]
        ];
    }

    /**
     * @dataProvider interceptorDataProvider
     */
    public function testExperimentalInterceptors($callType, $interceptor)
    {
        $transport = new GrpcTransport(
            'example.com',
            [
                'credentials' => ChannelCredentials::createInsecure()
            ],
            null,
            [$interceptor]
        );
        $r = new \ReflectionProperty(BaseStub::class, 'call_invoker');
        $r->setAccessible(true);
        $r->setValue(
            $transport,
            new MockCallInvoker(
                $this->buildMockCallForInterceptor($callType)
            )
        );
        $call = new Call(
            'method1',
            '',
            new MockRequest()
        );

        if ($callType === UnaryCall::class) {
            $transport->startUnaryCall($call, [
                'transportOptions' => [
                    'grpcOptions' => [
                        'call-option' => 'call-option-value'
                    ]
                ]
            ]);

            return;
        }

        $transport->startServerStreamingCall($call, [
            'transportOptions' => [
                'grpcOptions' => [
                    'call-option' => 'call-option-value'
                ]
            ]
        ]);
    }

    public function interceptorDataProvider()
    {
        if ($this->useDeprecatedInterceptors()) {
            return [
                [
                    UnaryCall::class,
                    new TestUnaryInterceptorDeprecated()
                ],
                [
                    UnaryCall::class,
                    new TestInterceptorDeprecated()
                ],
                [
                    ServerStreamingCall::class,
                    new TestInterceptorDeprecated()
                ]
            ];
        }
        return [
            [
                UnaryCall::class,
                new TestUnaryInterceptor()
            ],
            [
                UnaryCall::class,
                new TestInterceptor()
            ],
            [
                ServerStreamingCall::class,
                new TestInterceptor()
            ]
        ];
    }

    private function buildMockCallForInterceptor($callType)
    {
        $mockCall = $this->getMockBuilder($callType)
            ->disableOriginalConstructor()
            ->getMock();
        $mockCall->method('start')
            ->with(
                $this->isInstanceOf(Message::class),
                $this->equalTo([]),
                $this->equalTo([
                    'call-option' => 'call-option-value',
                    'test-interceptor-insert' => 'inserted-value'
                ])
            );

        if ($callType === UnaryCall::class) {
            $mockCall->method('wait')
                ->will($this->returnValue([
                    null,
                    Code::OK
                ]));
        }

        return $mockCall;
    }

    private function useDeprecatedInterceptors()
    {
        $reflector = new \ReflectionClass(Interceptor::class);
        $params = $reflector->getMethod('interceptUnaryUnary')->getParameters();
        return $params[3]->getName() === 'metadata';
    }
}

class MockCallInvoker implements CallInvoker
{
    public function __construct($mockCall)
    {
        $this->mockCall = $mockCall;
    }

    public function createChannelFactory($hostname, $opts)
    {
        // no-op
    }

    public function UnaryCall($channel, $method, $deserialize, $options)
    {
        return $this->mockCall;
    }

    public function ServerStreamingCall($channel, $method, $deserialize, $options)
    {
        return $this->mockCall;
    }

    public function ClientStreamingCall($channel, $method, $deserialize, $options)
    {
        // no-op
    }

    public function BidiStreamingCall($channel, $method, $deserialize, $options)
    {
        // no-op
    }
}

class TestInterceptor extends Interceptor
{
    public function interceptUnaryUnary(
        $method,
        $argument,
        $deserialize,
        $continuation,
        array $metadata = [],
        array $options = []
    ) {
        $options['test-interceptor-insert'] = 'inserted-value';
        return $continuation($method, $argument, $deserialize, $metadata, $options);
    }

    public function interceptUnaryStream(
        $method,
        $argument,
        $deserialize,
        $continuation,
        array $metadata = [],
        array $options = []
    ) {
        $options['test-interceptor-insert'] = 'inserted-value';
        return $continuation($method, $argument, $deserialize, $metadata, $options);
    }
}

class TestInterceptorDeprecated extends Interceptor
{
    public function interceptUnaryUnary(
        $method,
        $argument,
        $deserialize,
        array $metadata = [],
        array $options = [],
        $continuation
    ) {
        $options['test-interceptor-insert'] = 'inserted-value';
        return $continuation($method, $argument, $deserialize, $metadata, $options);
    }

    public function interceptUnaryStream(
        $method,
        $argument,
        $deserialize,
        array $metadata = [],
        array $options = [],
        $continuation
    ) {
        $options['test-interceptor-insert'] = 'inserted-value';
        return $continuation($method, $argument, $deserialize, $metadata, $options);
    }
}

class TestUnaryInterceptor extends Interceptor
{
    public function interceptUnaryUnary(
        $method,
        $argument,
        $deserialize,
        $continuation,
        array $metadata = [],
        array $options = []
    ) {
        $options['test-interceptor-insert'] = 'inserted-value';
        return $continuation($method, $argument, $deserialize, $metadata, $options);
    }
}

class TestUnaryInterceptorDeprecated implements UnaryInterceptorInterface
{
    public function interceptUnaryUnary(
        $method,
        $argument,
        $deserialize,
        array $metadata,
        array $options,
        callable $continuation
    ) {
        $options['test-interceptor-insert'] = 'inserted-value';
        return $continuation($method, $argument, $deserialize, $metadata, $options);
    }
}
