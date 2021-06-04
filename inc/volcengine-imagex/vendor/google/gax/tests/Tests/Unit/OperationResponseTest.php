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

use Google\ApiCore\LongRunning\Gapic\OperationsGapicClient;
use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\OperationResponse;
use Google\LongRunning\Operation;
use Google\Protobuf\Any;
use PHPUnit\Framework\TestCase;

class OperationResponseTest extends TestCase
{
    use TestTrait;

    public function testBasic()
    {
        $opName = 'operations/opname';
        $opClient = $this->createOperationsClient();
        $op = new OperationResponse($opName, $opClient);

        $this->assertSame($opName, $op->getName());
        $this->assertSame($opClient, $op->getOperationsClient());
    }

    public function testWithoutResponse()
    {
        $opName = 'operations/opname';
        $opClient = $this->createOperationsClient();
        $op = new OperationResponse($opName, $opClient);

        $this->assertNull($op->getLastProtoResponse());
        $this->assertFalse($op->isDone());
        $this->assertNull($op->getResult());
        $this->assertNull($op->getError());
        $this->assertNull($op->getMetadata());
        $this->assertFalse($op->operationSucceeded());
        $this->assertFalse($op->operationFailed());
        $this->assertEquals([
            'operationReturnType' => null,
            'metadataReturnType' => null,
            'initialPollDelayMillis' => 1000.0,
            'pollDelayMultiplier' => 2.0,
            'maxPollDelayMillis' => 60000.0,
            'totalPollTimeoutMillis' => 0.0,
        ], $op->getDescriptorOptions());
    }

    public function testWithResponse()
    {
        $opName = 'operations/opname';
        $opClient = $this->createOperationsClient();
        $protoResponse = new Operation();
        $op = new OperationResponse($opName, $opClient, [
            'lastProtoResponse' => $protoResponse,
        ]);

        $this->assertSame($protoResponse, $op->getLastProtoResponse());
        $this->assertFalse($op->isDone());
        $this->assertNull($op->getResult());
        $this->assertNull($op->getError());
        $this->assertNull($op->getMetadata());
        $this->assertFalse($op->operationSucceeded());
        $this->assertFalse($op->operationFailed());
        $this->assertEquals([
            'operationReturnType' => null,
            'metadataReturnType' => null,
            'initialPollDelayMillis' => 1000.0,
            'pollDelayMultiplier' => 2.0,
            'maxPollDelayMillis' => 60000.0,
            'totalPollTimeoutMillis' => 0.0,
        ], $op->getDescriptorOptions());

        $response = $this->createAny($this->createStatus(0, "response"));
        $error = $this->createStatus(2, "error");
        $metadata = $this->createAny($this->createStatus(0, "metadata"));

        $protoResponse->setDone(true);
        $protoResponse->setResponse($response);
        $protoResponse->setMetadata($metadata);
        $this->assertTrue($op->isDone());
        $this->assertSame($response, $op->getResult());
        $this->assertSame($metadata, $op->getMetadata());
        $this->assertTrue($op->operationSucceeded());
        $this->assertFalse($op->operationFailed());

        $protoResponse->setError($error);
        $this->assertNull($op->getResult());
        $this->assertSame($error, $op->getError());
        $this->assertFalse($op->operationSucceeded());
        $this->assertTrue($op->operationFailed());
    }

    public function testWithOptions()
    {
        $opName = 'operations/opname';
        $opClient = $this->createOperationsClient();
        $protoResponse = new Operation();
        $op = new OperationResponse($opName, $opClient, [
            'operationReturnType' => '\Google\Rpc\Status',
            'metadataReturnType' => '\Google\Protobuf\Any',
            'lastProtoResponse' => $protoResponse,
        ]);

        $this->assertSame($protoResponse, $op->getLastProtoResponse());
        $this->assertFalse($op->isDone());
        $this->assertNull($op->getResult());
        $this->assertNull($op->getError());
        $this->assertNull($op->getMetadata());
        $this->assertEquals([
            'operationReturnType' => '\Google\Rpc\Status',
            'metadataReturnType' => '\Google\Protobuf\Any',
            'initialPollDelayMillis' => 1000.0,
            'pollDelayMultiplier' => 2.0,
            'maxPollDelayMillis' => 60000.0,
            'totalPollTimeoutMillis' => 0.0,

        ], $op->getDescriptorOptions());

        $innerResponse = $this->createStatus(0, "response");
        $innerMetadata = new Any();
        $innerMetadata->setValue("metadata");

        $response = $this->createAny($innerResponse);
        $metadata = $this->createAny($innerMetadata);

        $protoResponse->setDone(true);
        $protoResponse->setResponse($response);
        $protoResponse->setMetadata($metadata);
        $this->assertTrue($op->isDone());
        $this->assertEquals($innerResponse, $op->getResult());
        $this->assertEquals($innerMetadata, $op->getMetadata());
    }

    /**
     * @dataProvider pollingDataProvider
     */
    public function testPolling($op, $pollArgs, $expectedSleeps, $expectedComplete)
    {
        $op->pollUntilComplete($pollArgs);

        $this->assertEquals($op->isDone(), $expectedComplete);
        $this->assertEquals($op->getSleeps(), $expectedSleeps);
    }

    public function pollingDataProvider()
    {
        $pollingArgs = [
            'initialPollDelayMillis' => 10.0,
            'pollDelayMultiplier' => 3.0,
            'maxPollDelayMillis' => 50.0,
            'totalPollTimeoutMillis' => 100.0,
        ];
        return [
            [$this->createOperationResponse([], 3), [], [1000.0, 2000.0, 4000.0], true], // Defaults
            [$this->createOperationResponse([], 3), $pollingArgs, [10, 30, 50], true], // Args to pollUntilComplete
            [$this->createOperationResponse($pollingArgs, 3), [], [10, 30, 50], true], // Args to constructor
            [$this->createOperationResponse([], 4), [
                'totalPollTimeoutMillis' => 80.0,
            ] + $pollingArgs, [10, 30, 50], false], // Polling timeout
        ];
    }

    private function createOperationResponse($options, $reloadCount)
    {
        $opName = 'operations/opname';
        return new FakeOperationResponse($opName, $this->createOperationClient($reloadCount), $options);
    }

    private function createOperationClient($reloadCount)
    {
        $opClient = $this->getMock(
            OperationsClient::class,
            ['getOperation'],
            [[
                'apiEndpoint' => '',
                'scopes' => [],
            ]]
        );
        for ($i = 0; $i < $reloadCount - 1; $i++) {
            $opClient->expects($this->at($i))
                ->method('getOperation')
                ->willReturn(new Operation());
        }
        $opClient->expects($this->at($reloadCount - 1))
            ->method('getOperation')
            ->willReturn((new Operation())->setDone(true));
        return $opClient;
    }
}

class FakeOperationResponse extends OperationResponse
{
    private $currentTime = 0;
    public function getSleeps()
    {
        return $this->sleeps;
    }

    public function sleepMillis($millis)
    {
        $this->currentTime += $millis;
        $this->sleeps[] = $millis;
    }

    public function setTimes($times)
    {
        $this->times = $times;
    }

    public function getCurrentTimeMillis()
    {
        return $this->currentTime;
    }
}
