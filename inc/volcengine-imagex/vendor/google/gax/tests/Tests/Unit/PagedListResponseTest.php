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
use Google\ApiCore\CallSettings;
use Google\ApiCore\Page;
use Google\ApiCore\PagedListResponse;
use Google\ApiCore\PageStreamingDescriptor;
use PHPUnit\Framework\TestCase;

class PagedListResponseTest extends TestCase
{
    use TestTrait;

    public function testNextPageToken()
    {
        $mockRequest = $this->createMockRequest('mockToken');
        $mockResponse = $this->createMockResponse('nextPageToken1', ['resource1']);

        $pageAccessor = $this->makeMockPagedCall($mockRequest, $mockResponse);

        $page = $pageAccessor->getPage();
        $this->assertEquals($page->getNextPageToken(), 'nextPageToken1');
        $this->assertEquals(iterator_to_array($page->getIterator()), ['resource1']);
    }

    public function testIterateAllElements()
    {
        $mockRequest = $this->createMockRequest('mockToken');
        $mockResponse = $this->createMockResponse('', ['resource1']);

        $pageAccessor = $this->makeMockPagedCall($mockRequest, $mockResponse);

        $result = iterator_to_array($pageAccessor->iterateAllElements());

        $this->assertEquals(['resource1'], $result);
    }

    public function testIterator()
    {
        $mockRequest = $this->createMockRequest('mockToken');
        $mockResponse = $this->createMockResponse('', ['resource1']);

        $pageAccessor = $this->makeMockPagedCall($mockRequest, $mockResponse);

        $result = iterator_to_array($pageAccessor);

        $this->assertEquals(['resource1'], $result);
    }

    /**
     * @param mixed $mockRequest
     * @param mixed $mockResponse
     * @param array $resourceField
     * @return PagedListResponse
     */
    private function makeMockPagedCall($mockRequest, $mockResponse, $resourceField = 'resourcesList')
    {
        $pageStreamingDescriptor = PageStreamingDescriptor::createFromFields([
            'requestPageTokenField' => 'pageToken',
            'responsePageTokenField' => 'nextPageToken',
            'resourceField' => $resourceField,
        ]);

        $callable = function () use ($mockResponse) {
            return $promise = new \GuzzleHttp\Promise\Promise(function () use (&$promise, $mockResponse) {
                $promise->resolve($mockResponse);
            });
        };

        $call = new Call('method', [], $mockRequest);
        $options = [];

        $response = $callable($call, $options)->wait();

        $page = new Page($call, $options, $callable, $pageStreamingDescriptor, $response);
        $pageAccessor = new PagedListResponse($page);

        return $pageAccessor;
    }

    public function testMapFieldNextPageToken()
    {
        $mockRequest = $this->createMockRequest('mockToken');
        $mockResponse = $this->createMockResponse('nextPageToken1');
        $mockResponse->setResourcesMap(['key1' => 'resource1']);

        $pageAccessor = $this->makeMockPagedCall($mockRequest, $mockResponse, 'resourcesMap');

        $page = $pageAccessor->getPage();
        $this->assertEquals($page->getNextPageToken(), 'nextPageToken1');
        $this->assertEquals(iterator_to_array($page->getIterator()), ['key1' => 'resource1']);
    }

    public function testMapFieldIterateAllElements()
    {
        $mockRequest = $this->createMockRequest('mockToken');
        $mockResponse = $this->createMockResponse();
        $mockResponse->setResourcesMap(['key1' => 'resource1']);

        $pageAccessor = $this->makeMockPagedCall($mockRequest, $mockResponse, 'resourcesMap');

        $result = iterator_to_array($pageAccessor->iterateAllElements());

        $this->assertEquals(['key1' => 'resource1'], $result);
    }

    public function testMapFieldIterator()
    {
        $mockRequest = $this->createMockRequest('mockToken');
        $mockResponse = $this->createMockResponse();
        $mockResponse->setResourcesMap(['key1' => 'resource1']);

        $pageAccessor = $this->makeMockPagedCall($mockRequest, $mockResponse, 'resourcesMap');

        $result = iterator_to_array($pageAccessor);

        $this->assertEquals(['key1' => 'resource1'], $result);
    }
}
