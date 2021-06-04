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

use Google\ApiCore\AgentHeader;
use Google\ApiCore\Call;
use Google\ApiCore\Middleware\AgentHeaderMiddleware;
use Google\ApiCore\Middleware\FixedHeaderMiddleware;
use PHPUnit\Framework\TestCase;

class FixedHeaderMiddlewareTest extends TestCase
{
    public function testCustomHeader()
    {
        $call = $this->getMock(Call::class, [], [], '', false);
        $fixedHeader = [
            'x-goog-api-client' => ['gl-php/5.5.0 gccl/0.0.0 gapic/0.9.0 gax/1.0.0 grpc/1.0.1']
        ];
        $handlerCalled = false;
        $callable = function(Call $call, $options) use ($fixedHeader, &$handlerCalled) {
            $this->assertEquals($fixedHeader, $options['headers']);
            $handlerCalled = true;
        };
        $middleware = new FixedHeaderMiddleware($callable, $fixedHeader);
        $middleware($call, []);
        $this->assertTrue($handlerCalled);
    }

    public function testCustomHeaderNoOverride()
    {
        $call = $this->getMock(Call::class, [], [], '', false);
        $fixedHeader = [
            'my-header' => ['some header string'],
            'fixed-only' => ['fixed header only'],
        ];
        $userHeader = [
            'my-header' => ['some alternate string'],
            'user-only' => ['user only header'],
        ];
        $expectedHeader = [
            'my-header' => ['some alternate string'],
            'fixed-only' => ['fixed header only'],
            'user-only' => ['user only header'],
        ];
        $handlerCalled = false;
        $callable = function(Call $call, $options) use ($expectedHeader, &$handlerCalled) {
            $this->assertEquals($expectedHeader, $options['headers']);
            $handlerCalled = true;
        };
        $middleware = new FixedHeaderMiddleware($callable, $fixedHeader);
        $middleware($call, ['headers' => $userHeader]);
        $this->assertTrue($handlerCalled);
    }

    public function testCustomHeaderOverride()
    {
        $call = $this->getMock(Call::class, [], [], '', false);
        $fixedHeader = [
            'my-header' => ['some header string'],
            'fixed-only' => ['fixed header only'],
        ];
        $userHeader = [
            'my-header' => ['some alternate string'],
            'user-only' => ['user only header'],
        ];
        $expectedHeader = [
            'my-header' => ['some header string'],
            'fixed-only' => ['fixed header only'],
            'user-only' => ['user only header'],
        ];
        $handlerCalled = false;
        $callable = function(Call $call, $options) use ($expectedHeader, &$handlerCalled) {
            $this->assertEquals($expectedHeader, $options['headers']);
            $handlerCalled = true;
        };
        $middleware = new FixedHeaderMiddleware($callable, $fixedHeader, true);
        $middleware($call, ['headers' => $userHeader]);
        $this->assertTrue($handlerCalled);
    }
}
