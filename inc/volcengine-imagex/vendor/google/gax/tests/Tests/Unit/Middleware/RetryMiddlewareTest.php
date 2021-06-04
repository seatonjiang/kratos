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

namespace Google\ApiCore\Tests\Unit\Middleware;

use Google\ApiCore\ApiException;
use Google\ApiCore\ApiStatus;
use Google\ApiCore\Call;
use Google\ApiCore\Middleware\RetryMiddleware;
use Google\ApiCore\RetrySettings;
use Google\Protobuf\Internal\Message;
use Google\Rpc\Code;
use GuzzleHttp\Promise\Promise;
use PHPUnit\Framework\TestCase;
use stdClass;

class RetryMiddlewareTest extends TestCase
{
    /**
     * @expectedException Google\ApiCore\ApiException
     * @expectedExceptionMessage Call Count: 1
     */
    public function testRetryNoRetryableCode()
    {
        $call = $this->getMock(Call::class, [], [], '', false);
        $retrySettings = RetrySettings::constructDefault()
            ->with([
                'retriesEnabled' => false,
                'retryableCodes' => []
            ]);
        $callCount = 0;
        $handler = function(Call $call, $options) use (&$callCount) {
            return new Promise(function () use (&$callCount) {
                throw new ApiException('Call Count: ' . $callCount += 1, 0, '');
            });
        };
        $middleware = new RetryMiddleware($handler, $retrySettings);
        $response = $middleware(
            $call,
            []
        )->wait();
    }

    public function testRetryBackoff()
    {
        $call = $this->getMock(Call::class, [], [], '', false);
        $retrySettings = RetrySettings::constructDefault()
            ->with([
                'retriesEnabled' => true,
                'retryableCodes' => [ApiStatus::CANCELLED],
            ]);
        $callCount = 0;
        $handler = function(Call $call, $options) use (&$callCount) {
            $callCount += 1;
            return $promise = new Promise(function () use (&$promise, $callCount) {
                if ($callCount < 3) {
                    throw new ApiException('Cancelled!', Code::CANCELLED, ApiStatus::CANCELLED);
                }
                $promise->resolve('Ok!');
            });
        };
        $middleware = new RetryMiddleware($handler, $retrySettings);
        $response = $middleware(
            $call,
            []
        )->wait();

        $this->assertEquals('Ok!', $response);
        $this->assertEquals(3, $callCount);
    }

    /**
     * @expectedException Google\ApiCore\ApiException
     * @expectedExceptionMessage Retry total timeout exceeded.
     */
    public function testRetryTimeoutExceedsMaxTimeout()
    {
        $call = $this->getMock(Call::class, [], [], '', false);
        $retrySettings = RetrySettings::constructDefault()
            ->with([
                'retriesEnabled' => true,
                'retryableCodes' => [ApiStatus::CANCELLED],
                'totalTimeoutMillis' => 0,
            ]);
        $handler = function(Call $call, $options) {
            return new Promise(function () {
                throw new ApiException('Cancelled!', Code::CANCELLED, ApiStatus::CANCELLED);
            });
        };
        $middleware = new RetryMiddleware($handler, $retrySettings);
        $response = $middleware(
            $call,
            []
        )->wait();
    }

    /**
     * @expectedException Google\ApiCore\ApiException
     * @expectedExceptionMessage Retry total timeout exceeded.
     */
    public function testRetryTimeoutExceedsRealTime()
    {
        $call = $this->getMock(Call::class, [], [], '', false);
        $retrySettings = RetrySettings::constructDefault()
            ->with([
                'retriesEnabled' => true,
                'retryableCodes' => [ApiStatus::CANCELLED],
                'initialRpcTimeoutMillis' => 500,
                'totalTimeoutMillis' => 1000,
            ]);
        $handler = function(Call $call, $options) {
            return new Promise(function () use ($options) {
                // sleep for the duration of the timeout
                if (isset($options['timeoutMillis'])) {
                    usleep($options['timeoutMillis'] * 1000);
                }
                throw new ApiException('Cancelled!', Code::CANCELLED, ApiStatus::CANCELLED);
            });
        };
        $middleware = new RetryMiddleware($handler, $retrySettings);
        $response = $middleware(
            $call,
            []
        )->wait();
    }

    public function testTimeoutMillisCallSettingsOverwrite()
    {
        $handlerCalled = false;
        $timeout = 1234;
        $handler = function (Call $call, array $options) use (&$handlerCalled, $timeout) {
            $handlerCalled = true;
            $this->assertEquals($timeout, $options['timeoutMillis']);
            return $this->getMock(Promise::class);
        };
        $retrySettings = RetrySettings::constructDefault()
            ->with([
                'retriesEnabled' => true,
                'retryableCodes' => [ApiStatus::CANCELLED],
            ]);
        $middleware = new RetryMiddleware($handler, $retrySettings);

        $call = $this->getMock(Call::class, [], [], '', false);
        $options = ['timeoutMillis' => $timeout];
        $middleware($call, $options);
        $this->assertTrue($handlerCalled);
    }
}
