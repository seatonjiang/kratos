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

use Google\ApiCore\AgentHeader;
use Google\ApiCore\Version;
use PHPUnit\Framework\TestCase;

class AgentHeaderTest extends TestCase
{
    public function testWithoutInput()
    {
        $expectedHeader = [AgentHeader::AGENT_HEADER_KEY => [
            'gl-php/' . phpversion() .
            ' gapic/' .
            ' gax/' . Version::getApiCoreVersion() .
            ' grpc/' . phpversion('grpc')
        ]];

        $header = AgentHeader::buildAgentHeader([]);
        $this->assertSame($expectedHeader, $header);
    }

    public function testWithInput()
    {
        $expectedHeader = [AgentHeader::AGENT_HEADER_KEY => [
            'gl-php/4.4.4 gccl/1.1.1 gapic/2.2.2 gax/3.3.3 grpc/5.5.5'
        ]];

        $header = AgentHeader::buildAgentHeader([
            'libName' => 'gccl',
            'libVersion' => '1.1.1',
            'gapicVersion' => '2.2.2',
            'apiCoreVersion' => '3.3.3',
            'phpVersion' => '4.4.4',
            'grpcVersion' => '5.5.5',
        ]);

        $this->assertSame($expectedHeader, $header);
    }

    public function testWithoutVersionInput()
    {
        $expectedHeader = [AgentHeader::AGENT_HEADER_KEY => [
            'gl-php/' . phpversion() .
            ' gccl/ gapic/ gax/' . Version::getApiCoreVersion() .
            ' grpc/' . phpversion('grpc')
        ]];

        $header = AgentHeader::buildAgentHeader([
            'libName' => 'gccl',
        ]);

        $this->assertSame($expectedHeader, $header);
    }

    public function testWithNullVersionInput()
    {
        $expectedHeader = [AgentHeader::AGENT_HEADER_KEY => [
            'gl-php/' . phpversion() .
            ' gccl/ gapic/ gax/' . Version::getApiCoreVersion() .
            ' grpc/' . phpversion('grpc')
        ]];

        $header = AgentHeader::buildAgentHeader([
            'libName' => 'gccl',
            'libVersion' => null,
            'gapicVersion' => null,
            'apiCoreVersion' => null,
            'phpVersion' => null,
            'grpcVersion' => null,
        ]);

        $this->assertSame($expectedHeader, $header);
    }

    public function testGetGapicVersionWithVersionFile()
    {
        require_once __DIR__ . '/testdata/src/GapicClientStub.php';
        $expectedVersion = '1.2.3-dev';
        $actualVersion = AgentHeader::readGapicVersionFromFile(\GapicClientStub::class);
        $this->assertEquals($expectedVersion, $actualVersion);
    }

    public function testGetGapicVersionWithNoAvailableVersion()
    {
        $this->assertEquals('', AgentHeader::readGapicVersionFromFile(__CLASS__));
    }
}
