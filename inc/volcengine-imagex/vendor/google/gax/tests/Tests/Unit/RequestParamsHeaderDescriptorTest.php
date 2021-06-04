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

use Google\ApiCore\RequestParamsHeaderDescriptor;
use PHPUnit\Framework\TestCase;

class RequestParamsHeaderDescriptorTest extends TestCase
{
    public function testEmpty()
    {
        $expectedHeader = [RequestParamsHeaderDescriptor::HEADER_KEY => ['']];

        $agentHeaderDescriptor = new RequestParamsHeaderDescriptor([]);
        $header = $agentHeaderDescriptor->getHeader();

        $this->assertEquals($expectedHeader, $header);
    }

    public function testSingleValue()
    {
        $expectedHeader = [RequestParamsHeaderDescriptor::HEADER_KEY => ['field1=value_1']];

        $agentHeaderDescriptor = new RequestParamsHeaderDescriptor(['field1' => 'value_1']);
        $header = $agentHeaderDescriptor->getHeader();

        $this->assertSame($expectedHeader, $header);
    }

    public function testMultipleValues()
    {
        $expectedHeader = [
            RequestParamsHeaderDescriptor::HEADER_KEY => ['field1=value_1&field2.field3=value_2']
        ];

        $agentHeaderDescriptor = new RequestParamsHeaderDescriptor([
            'field1' => 'value_1',
            'field2.field3' => 'value_2'
        ]);
        $header = $agentHeaderDescriptor->getHeader();

        $this->assertSame($expectedHeader, $header);
    }

    public function testNonAsciiCharsAppendsBinToHeaderKey()
    {
        $val = 'こんにちは';
        $expectedHeader = [
            RequestParamsHeaderDescriptor::HEADER_KEY . '-bin' => ['field1=' . $val]
        ];

        $agentHeaderDescriptor = new RequestParamsHeaderDescriptor(['field1' => $val]);
        $header = $agentHeaderDescriptor->getHeader();

        $this->assertSame($expectedHeader, $header);
    }
}
