<?php
/*
 * Copyright 2016 Google LLC
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

use Google\ApiCore\Call;
use Google\ApiCore\Page;
use Google\ApiCore\PageStreamingDescriptor;
use Google\ApiCore\Testing\MockStatus;
use Google\Rpc\Code;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    use TestTrait;

    private function createPage($responseSequence)
    {
        $mockRequest = $this->createMockRequest('token');

        $pageStreamingDescriptor = PageStreamingDescriptor::createFromFields([
            'requestPageTokenField' => 'pageToken',
            'responsePageTokenField' => 'nextPageToken',
            'resourceField' => 'resourcesList'
        ]);

        $internalCall = $this->createCallWithResponseSequence($responseSequence);
        $callable = function () use ($internalCall) {
            list($response, $status) = call_user_func_array(
                array($internalCall, 'takeAction'),
                func_get_args()
            );
            return $promise = new \GuzzleHttp\Promise\Promise(function () use (&$promise, $response) {
                $promise->resolve($response);
            });
        };

        $call = new Call('method', [], $mockRequest);
        $options = [];

        $response = $callable($call, $options)->wait();
        return new Page($call, $options, $callable, $pageStreamingDescriptor, $response);
    }

    public function testNextPageMethods()
    {
        $responseA = $this->createMockResponse('nextPageToken1', ['resource1']);
        $responseB = $this->createMockResponse('', ['resource2']);

        $page = $this->createPage([
            [$responseA, new MockStatus(Code::OK, '')],
            [$responseB, new MockStatus(Code::OK, '')],
        ]);

        $this->assertEquals($page->hasNextPage(), true);
        $this->assertEquals($page->getNextPageToken(), 'nextPageToken1');

        $nextPage = $page->getNextPage();

        $this->assertEquals($nextPage->hasNextPage(), false);
        $this->assertEquals($nextPage->getNextPageToken(), '');

        $newRequest = $nextPage->getRequestObject();
        $this->assertEquals($newRequest->getPageToken(), 'nextPageToken1');

        // Call serializeToJsonString - this will test for bugs in the protobuf
        // c extension (if installed) that can cause segmentation faults.
        $newRequest->serializeToJsonString();
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Could not complete getNextPage operation
     */
    public function testNextPageMethodsFailWithNoNextPage()
    {
        $responseA = $this->createMockResponse('', ['resource1']);
        $page = $this->createPage([
            [$responseA, new MockStatus(Code::OK, '')],
        ]);

        $this->assertEquals($page->hasNextPage(), false);
        $page->getNextPage();
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage pageSize argument was defined, but the method does not
     */
    public function testNextPageMethodsFailWithPageSizeUnsupported()
    {
        $responseA = $this->createMockResponse('nextPageToken1', ['resource1']);
        $responseB = $this->createMockResponse('', ['resource2']);
        $page = $this->createPage([
            [$responseA, new MockStatus(Code::OK, '')],
            [$responseB, new MockStatus(Code::OK, '')],
        ]);

        $page->getNextPage(3);
    }

    public function testPageElementMethods()
    {
        $response = $this->createMockResponse(
            'nextPageToken1',
            ['resource1', 'resource2', 'resource3']
        );
        $page = $this->createPage([
            [$response, new MockStatus(Code::OK, '')],
        ]);

        $this->assertEquals($page->getPageElementCount(), 3);
        $results = iterator_to_array($page);
        $this->assertEquals($results, ['resource1', 'resource2', 'resource3']);
    }
}
