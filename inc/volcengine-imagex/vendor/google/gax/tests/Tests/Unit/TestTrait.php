<?php
/*
 * Copyright 2017 Google LLC
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
namespace Google\ApiCore\Tests\Unit;

use Grpc\UnaryCall;
use Google\ApiCore\Testing\MockResponse;
use Google\ApiCore\Testing\MockRequest;
use Google\ApiCore\Testing\MockStatus;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\Protobuf\Any;
use Google\Rpc\Code;
use Google\Rpc\Status;

trait TestTrait
{
    public function createMockRequest($token = null, $pageSize = null)
    {
        $request = new MockRequest();
        if ($token) {
            $request->setPageToken($token);
        }
        if ($pageSize) {
            $request->setPageSize($pageSize);
        }
        return $request;
    }

    public function createMockResponse($pageToken = null, $resourcesList = [])
    {
        $mockResponse = new MockResponse();
        if ($pageToken) {
            $mockResponse->setNextPageToken($pageToken);
        }
        if ($resourcesList) {
            $mockResponse->setResourcesList($resourcesList);
        }
        return $mockResponse;
    }

    public function createMockCall($response = null, $status = null)
    {
        $status = $status ?: new MockStatus(Code::OK);
        $call = $this->getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $call->method('wait')
            ->will($this->returnValue([$response, $status]));

        return $call;
    }

    public function createCallWithResponseSequence($sequence)
    {
        foreach ($sequence as $key => $value) {
            if (!is_array($value)) {
                $sequence[$key] = [$value, null];
            }
        }
        $mockCall = $this->getMockBuilder(MockCall::class)
            ->setMethods(['takeAction'])
            ->getMock();
        $mockCall->method('takeAction')
            ->will(call_user_func_array([$this, 'onConsecutiveCalls'], $sequence));

        return $mockCall;
    }

    public function createOperationsClient($transport = null)
    {
        $this->requiresGrpcExtension();

        $client = new OperationsClient([
            'apiEndpoint' => '',
            'scopes' => [],
            'transport' => $transport,
        ]);

        return $client;
    }

    /**
     * @param \Google\Rpc\Code $code
     * @param String $message
     * @return Status
     */
    public function createStatus($code, $message)
    {
        $status = new Status();
        $status->setCode($code);
        $status->setMessage($message);
        return $status;
    }

    /**
     * @param $value \Google\Protobuf\Internal\Message;
     * @return Any
     */
    public function createAny($value)
    {
        $any = new Any();
        $any->setValue($value->serializeToString());
        return $any;
    }

    public static function requiresGrpcExtension()
    {
        if (!extension_loaded('grpc')) {
            self::markTestSkipped('Must have the grpc extension installed to run this test.');
        }
        if (defined('HHVM_VERSION')) {
            self::markTestSkipped('gRPC is not supported on HHVM.');
        }
    }
}
