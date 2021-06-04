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
use Google\ApiCore\FixedSizeCollection;
use Google\ApiCore\PageStreamingDescriptor;
use Google\ApiCore\Testing\MockStatus;
use Google\Rpc\Code;
use PHPUnit\Framework\TestCase;

class FixedSizeCollectionTest extends TestCase
{
    use TestTrait;

    private function createPage($responseSequence)
    {
        $mockRequest = $this->createMockRequest('token', 3);

        $pageStreamingDescriptor = PageStreamingDescriptor::createFromFields([
            'requestPageTokenField' => 'pageToken',
            'requestPageSizeField' => 'pageSize',
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

        $call = new Call('method', 'decodeType', $mockRequest);
        $options = [];

        $response = $callable($call, $options)->wait();
        return new Page($call, $options, $callable, $pageStreamingDescriptor, $response);
    }

    public function testFixedCollectionMethods()
    {
        $responseA = $this->createMockResponse(
            'nextPageToken1',
            ['resource1', 'resource2']
        );
        $responseB = $this->createMockResponse(
            'nextPageToken2',
            ['resource3', 'resource4', 'resource5']
        );
        $responseC = $this->createMockResponse(
            'nextPageToken3',
            ['resource6', 'resource7']
        );
        $responseD = $this->createMockResponse(
            '',
            ['resource8', 'resource9']
        );
        $page = $this->createPage([
            [$responseA, new MockStatus(Code::OK, '')],
            [$responseB, new MockStatus(Code::OK, '')],
            [$responseC, new MockStatus(Code::OK, '')],
            [$responseD, new MockStatus(Code::OK, '')],
        ]);

        $fixedSizeCollection = new FixedSizeCollection($page, 5);

        $this->assertEquals($fixedSizeCollection->getCollectionSize(), 5);
        $this->assertEquals($fixedSizeCollection->hasNextCollection(), true);
        $this->assertEquals($fixedSizeCollection->getNextPageToken(), 'nextPageToken2');
        $results = iterator_to_array($fixedSizeCollection);
        $this->assertEquals(
            $results,
            ['resource1', 'resource2', 'resource3', 'resource4', 'resource5']
        );

        $nextCollection = $fixedSizeCollection->getNextCollection();

        $this->assertEquals($nextCollection->getCollectionSize(), 4);
        $this->assertEquals($nextCollection->hasNextCollection(), false);
        $this->assertEquals($nextCollection->getNextPageToken(), '');
        $results = iterator_to_array($nextCollection);
        $this->assertEquals(
            $results,
            ['resource6', 'resource7', 'resource8', 'resource9']
        );
    }

    public function testIterateCollections()
    {
        $responseA = $this->createMockResponse(
            'nextPageToken1',
            ['resource1', 'resource2']
        );
        $responseB = $this->createMockResponse(
            '',
            ['resource3', 'resource4']
        );
        $page = $this->createPage([
            [$responseA, new MockStatus(Code::OK, '')],
            [$responseB, new MockStatus(Code::OK, '')],
        ]);

        $collection = new FixedSizeCollection($page, 2);

        $results = [];
        $iterations = 0;
        foreach ($collection->iterateCollections() as $nextCollection) {
            $results = array_merge($results, iterator_to_array($nextCollection));
            $iterations++;
        }
        $this->assertEquals(
            $results,
            ['resource1', 'resource2', 'resource3', 'resource4']
        );
        $this->assertEquals(2, $iterations);
    }

    /**
     * @expectedException LengthException
     * @expectedExceptionMessage API returned a number of elements exceeding the specified page size limit
     */
    public function testApiReturningMoreElementsThanPageSize()
    {
        $responseA = $this->createMockResponse(
            'nextPageToken1',
            ['resource1', 'resource2']
        );
        $responseB = $this->createMockResponse(
            'nextPageToken2',
            ['resource3', 'resource4', 'resource5']
        );
        $page = $this->createPage([
            [$responseA, new MockStatus(Code::OK, '')],
            [$responseB, new MockStatus(Code::OK, '')],
        ]);

        $collection = new FixedSizeCollection($page, 3);
        $collection->getNextCollection();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage collectionSize must be > 0.
     */
    public function testEmptyCollectionThrowsException()
    {
        $collectionSize = 0;
        $page = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        new FixedSizeCollection($page, $collectionSize);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage collectionSize must be greater than or equal to the number of elements in initialPage
     */
    public function testInvalidPageCount()
    {
        $collectionSize = 1;
        $page = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects($this->exactly(2))
            ->method('getPageElementCount')
            ->will($this->returnValue(2));
        new FixedSizeCollection($page, $collectionSize);
    }
}
