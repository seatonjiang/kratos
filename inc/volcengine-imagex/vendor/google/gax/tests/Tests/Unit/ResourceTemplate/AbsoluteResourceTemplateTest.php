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
namespace Google\ApiCore\Tests\Unit\ResourceTemplate;

use Google\ApiCore\ResourceTemplate\AbsoluteResourceTemplate;
use PHPUnit\Framework\TestCase;

class AbsoluteResourceTemplateTest extends TestCase
{
    /**
     * @dataProvider validPathProvider
     * @param string $path
     * @param $expectedString
     */
    public function testValidPaths($path, $expectedString = null)
    {
        $template = new AbsoluteResourceTemplate($path);
        $this->assertEquals($expectedString ?: $path, $template->__toString());
    }

    public function validPathProvider()
    {
        return [
            ["/foo"],
            ["/*"],
            ["/**"],
            ["/{foo}", "/{foo=*}"],
            ["/{foo=*}"],
            ["/{foo=**}"],
            ["/foo/*"],
            ["/*/foo"],
            ["/foo/*:bar"],
            ["/*/foo:bar"],
            ["/*/*/*/*:foo"],
            ["/**/*/*:foo"],
            ["/foo/**/bar/*"],
            ["/foo/*/bar/**"],
            ["/foo/helloazAZ09-.~_what"],
        ];
    }

    /**
     * @dataProvider invalidPathProvider
     * @expectedException \Google\ApiCore\ValidationException
     * @param string $path
     */
    public function testInvalidPaths($path)
    {
        new AbsoluteResourceTemplate($path);
    }

    public function invalidPathProvider()
    {
        return [
            [null],                     // Null path
            [""],                       // Empty path
            ["foo"],                    // No leading '/'
            ["/foo:bar/baz"],           // Action containing '/'
            ["/foo:bar:baz"],           // Multiple ':'
            ["/foo/bar*baz"],           // Mixed literal and '*'
            ["/foo/**/**"],             // Multiple '**'
            ["/foo/**/{var=**}"],       // Multiple '**' nested
            ["/foo/{bizz=**}/{var=**}"],// Multiple '**' nested
            ["/foo/{bizz=**/**}"],      // Multiple '**' nested
            ["/foo/{bar={baz}}"],       // Nested {}
            ["/foo/{bar=fizz=buzz}"],   // Multiple '=' in variable
            ["/foo/{bar"],              // Unmatched '{'
            ["/foo/{bar}/{bar}"],       // Duplicate variable key
            ["/foo/{bar/baz}"],         // Variable containing '/'
            ["/foo//bar"],              // Consecutive '/'
            ["//bar"],                  // Consecutive '/'
            ["/foo/"],                  // Trailing '/'
        ];
    }

    /**
     * @param string $pathTemplate
     * @param string $path
     * @param array $expectedBindings
     * @dataProvider matchData
     */
    public function testMatch($pathTemplate, $path, $expectedBindings)
    {
        $template = new AbsoluteResourceTemplate($pathTemplate);
        $this->assertEquals(
            $expectedBindings,
            $template->match($path)
        );
    }

    /**
     * @param string $pathTemplate
     * @param string $path
     * @dataProvider matchData
     */
    public function testMatches($pathTemplate, $path)
    {
        $template = new AbsoluteResourceTemplate($pathTemplate);
        $this->assertTrue($template->matches($path));
    }

    /**
     * @param string $pathTemplate
     * @param string $expectedPath
     * @param array $bindings
     * @dataProvider matchData
     */
    public function testRender($pathTemplate, $expectedPath, $bindings)
    {
        $template = new AbsoluteResourceTemplate($pathTemplate);
        $this->assertEquals($expectedPath, $template->render($bindings));
    }

    public function matchData()
    {
        return [
            [
                '/buckets/*/*/objects/*',
                '/buckets/f/o/objects/bar',
                ['$0' => 'f', '$1' => 'o', '$2' => 'bar'],
            ],
            [
                '/buckets/{hello}',
                '/buckets/world',
                ['hello' => 'world'],
            ],
            [
                '/buckets/{hello=*}',
                '/buckets/world',
                ['hello' => 'world'],
            ],
            [
                '/buckets/*:action',
                '/buckets/foo:action',
                ['$0' => 'foo'],
            ],
            [
                '/buckets/*/*/*/objects/*:action',
                '/buckets/f/o/o/objects/google.com:a-b:action',
                ['$0' => 'f', '$1' => 'o', '$2' => 'o', '$3' => 'google.com:a-b'],
            ],
            [
                '/buckets/*/objects/**:action',
                '/buckets/foo/objects/bar/baz:action',
                ['$0' => 'foo', '$1' => 'bar/baz'],
            ],
            [
                '/foo/*/{bar=*/rar/*}/**/*:action',
                '/foo/fizz/fuzz/rar/bar/bizz/buzz/baz:action',
                ['$0' => 'fizz', '$1' => 'bizz/buzz', '$2' => 'baz', 'bar' => 'fuzz/rar/bar'],
            ],
            [
                '/buckets/*',
                '/buckets/{}!@#$%^&*()+=[]\|`~-_',
                ['$0' => '{}!@#$%^&*()+=[]\|`~-_'],
            ],
        ];
    }

    /**
     * @param string $pathTemplate
     * @param string $path
     * @dataProvider invalidMatchData
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testFailMatch($pathTemplate, $path)
    {
        $template = new AbsoluteResourceTemplate($pathTemplate);
        $template->match($path);
    }

    /**
     * @param string $pathTemplate
     * @param string $path
     * @dataProvider invalidMatchData
     */
    public function testFailMatches($pathTemplate, $path)
    {
        $template = new AbsoluteResourceTemplate($pathTemplate);
        $this->assertFalse($template->matches($path));
    }

    public function invalidMatchData()
    {
        return [
            [
                '/buckets/*/*/objects/*',
                '/buckets/f/o/objects/bar/far', // Extra '/far'
            ],
            [
                '/buckets/*/*/objects/*',
                '/buckets/f/o/objects', // Missing final wildcard
            ],
            [
                '/foo/*/bar',
                '/foo/bar', // Missing middle wildcard
            ],
            [
                '/foo/*/bar',
                '/foo/fizz/buzz/bar', // Too many segments for middle wildcard
            ],
            [
                '/foo/**/bar',
                '/foo/bar', // Missing middle wildcard
            ],
            [
                '/buckets/*:action',
                '/buckets/foo', // Missing action
            ],
            [
                '/buckets/*:action',
                '/buckets/foo:actingout', // Wrong action
            ],
            [
                '/buckets/*:action',
                '/buckets/foo:actionstations', // Wrong action
            ],
            [
                '/buckets',
                '/bouquets', // Wrong literal
            ],
            [
                '/buckets',
                '/bucketseller', // Wrong literal
            ],
            [
                '/foo/*/{bar=*/rar/*}/**/*:action',
                '/foo/fizz/fuzz/rat/bar/bizz/buzz/baz:action',
            ],
        ];
    }

    /**
     * @param string $pathTemplate
     * @param array $bindings
     * @dataProvider invalidRenderData
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testFailRender($pathTemplate, $bindings)
    {
        $template = new AbsoluteResourceTemplate($pathTemplate);
        $template->render($bindings);
    }

    public function invalidRenderData()
    {
        return [
            [
                '/buckets/*/*/objects/*',
                ['$0' => 'f', '$2' => 'bar'], // Missing key
            ],
            [
                '/buckets/{hello}',
                ['hellop' => 'world'], // Wrong key
            ],
            [
                '/buckets/{hello=*}',
                ['hello' => 'world/weary'], // Invalid binding
            ],
            [
                '/buckets/{hello=*}',
                ['hello' => ''], // Invalid binding
            ],
            [
                '/buckets/*/objects/**:action',
                ['$0' => 'foo', '$1' => ''],  // Invalid binding
            ],
            [
                '/foo/*/{bar=*/rar/*}/**/*:action',
                ['$0' => 'fizz', '$1' => 'bizz/buzz', '$2' => 'baz', 'bar' => 'fuzz/rat/bar'],
                // Invalid binding
            ],
        ];
    }
}
