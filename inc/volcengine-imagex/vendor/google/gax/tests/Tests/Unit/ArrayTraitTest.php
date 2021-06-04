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

use Google\ApiCore\ArrayTrait;
use PHPUnit\Framework\TestCase;

class ArrayTraitTest extends TestCase
{
    private $implementation;

    public function setUp()
    {
        $this->implementation = new ArrayTraitStub();
    }

    public function testPluck()
    {
        $value = '123';
        $key = 'key';
        $array = [$key => $value];
        $actualValue = $this->implementation->call('pluck', [$key, &$array]);

        $this->assertEquals($value, $actualValue);
        $this->assertEquals([], $array);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPluckThrowsExceptionWithInvalidKey()
    {
        $array = [];
        $this->implementation->call('pluck', ['not_here', &$array]);
    }

    public function testPluckArray()
    {
        $keys = ['key1', 'key2'];
        $array = [
            'key1' => 'test',
            'key2' => 'test'
        ];
        $expectedArray = $array;

       $actualValues = $this->implementation->call('pluckArray', [$keys, &$array]);

       $this->assertEquals($expectedArray, $actualValues);
       $this->assertEquals([], $array);
    }

    public function testIsAssocTrue()
    {
        $actual = $this->implementation->call('isAssoc', [[
            'test' => 1,
            'test' => 2
        ]]);

        $this->assertTrue($actual);
    }

    public function testIsAssocFalse()
    {
        $actual = $this->implementation->call('isAssoc', [[1, 2, 3]]);

        $this->assertFalse($actual);
    }

    public function testArrayFilterRemoveNull()
    {
        $input = [
            'null' => null,
            'false' => false,
            'zero' => 0,
            'float' => 0.0,
            'empty' => '',
            'array' => [],
        ];

        $res = $this->implementation->call('arrayFilterRemoveNull', [$input]);
        $this->assertFalse(array_key_exists('null', $res));
        $this->assertTrue(array_key_exists('false', $res));
        $this->assertTrue(array_key_exists('zero', $res));
        $this->assertTrue(array_key_exists('float', $res));
        $this->assertTrue(array_key_exists('empty', $res));
        $this->assertTrue(array_key_exists('array', $res));
    }

    /**
     * @dataProvider subsetArrayData
     */
    public function testSubsetArray($keys, $array, $expectedSubset)
    {
        $actualSubset = $this->implementation->call('subsetArray', [$keys, $array]);
        $this->assertSame($expectedSubset, $actualSubset);
    }

    public function subsetArrayData()
    {
        return [
            [
                ['one', 2],
                [
                    'one' => 'value-of-one',
                    2 => 'value-of-two',
                    'three' => 'value-of-three'
                ],
                [
                    'one' => 'value-of-one',
                    2 => 'value-of-two',
                ],
            ],
            [
                ['one', 2, 'four'],
                [
                    'one' => 'value-of-one',
                    2 => 'value-of-two',
                    'three' => 'value-of-three'
                ],
                [
                    'one' => 'value-of-one',
                    2 => 'value-of-two',
                ],
            ]
        ];
    }
}

class ArrayTraitStub
{
    use ArrayTrait;

    public function call($fn, array $args)
    {
        return call_user_func_array([$this, $fn], $args);
    }
}
