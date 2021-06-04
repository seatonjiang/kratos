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

use Google\ApiCore\ResourceTemplate\RelativeResourceTemplate;
use PHPUnit\Framework\TestCase;

class RelativeResourceTemplateTest extends TestCase
{
    /**
     * @dataProvider validPathProvider
     * @param string $path
     * @param $expectedString
     */
    public function testValidPaths($path, $expectedString = null)
    {
        $template = new RelativeResourceTemplate($path);
        $this->assertEquals($expectedString ?: $path, $template->__toString());
    }

    public function validPathProvider()
    {
        return [
            ["foo"],
            ["5"],
            ["5five/4/four"],
            ["*"],
            ["**"],
            ["{foo}", "{foo=*}"],
            ["{foo=*}"],
            ["{foo=**}"],
            ["foo/*"],
            ["*/foo"],
            ["*/*/*/*"],
            ["**/*/*"],
            ["foo/**/bar/*"],
            ["foo/*/bar/**"],
            ["foo/helloazAZ09-.~_what"],
        ];
    }

    /**
     * @dataProvider invalidPathProvider
     * @expectedException \Google\ApiCore\ValidationException
     * @param string $path
     */
    public function testInvalidPaths($path, $expectedExceptionMessage = null)
    {
        if (isset($expectedExceptionMessage)) {
            $this->setExpectedException($this->getExpectedException(), $expectedExceptionMessage);
        }
        new RelativeResourceTemplate($path);
    }

    public function invalidPathProvider()
    {
        return [
            [
                null,                  // Null path
                "Cannot construct RelativeResourceTemplate from null string"
            ],
            [
                "",                    // Empty path
                "Cannot construct RelativeResourceTemplate from empty string"
            ],
            [
                "foo:bar/baz",         // Action containing '/'
                "Error parsing 'foo:bar/baz' at index 7: Unexpected characters in literal segment foo:bar",
            ],
            [
                "foo/**/**",           // Multiple '**'
                "Cannot parse 'foo/**/**': cannot contain more than one path wildcard"
            ],
            [
                "foo//bar",            // Consecutive '/'
                "Error parsing 'foo//bar' at index 4: Unexpected empty segment (consecutive '/'s are invalid)",
            ],
            [
                "foo/",                // Trailing '/'
                "Error parsing 'foo/' at index 3: invalid trailing '/'"
            ],
            ["foo:bar:baz"],           // Multiple ':'
            ["foo/bar*baz"],           // Mixed literal and '*'
            ["foo/**/{var=**}"],       // Multiple '**' nested
            ["foo/{bizz=**}/{var=**}"],// Multiple '**' nested
            ["foo/{bizz=**/**}"],      // Multiple '**' nested
            ["foo/{bar={baz}}"],       // Nested {}
            ["foo/{bar=fizz=buzz}"],   // Multiple '=' in variable
            ["foo/{bar"],              // Unmatched '{'
            ["foo/{bar}/{bar}"],       // Duplicate variable key
            ["foo/{bar/baz}"],         // Variable containing '/'
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
        $template = new RelativeResourceTemplate($pathTemplate);
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
        $template = new RelativeResourceTemplate($pathTemplate);
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
        $template = new RelativeResourceTemplate($pathTemplate);
        $this->assertEquals($expectedPath, $template->render($bindings));
    }

    public function matchData()
    {
        return [
            [
                'buckets/*/*/objects/*',
                'buckets/f/o/objects/bar',
                ['$0' => 'f', '$1' => 'o', '$2' => 'bar'],
            ],
            [
                'buckets/{hello}',
                'buckets/world',
                ['hello' => 'world'],
            ],
            [
                'buckets/{hello}',
                'buckets/5',
                ['hello' => '5'],
            ],
            [
                'buckets/{hello=*}',
                'buckets/world',
                ['hello' => 'world'],
            ],
            [
                'buckets/*',
                'buckets/foo',
                ['$0' => 'foo'],
            ],
            [
                'buckets/*/*/*/objects/*',
                'buckets/f/o/o/objects/google.com:a-b',
                ['$0' => 'f', '$1' => 'o', '$2' => 'o', '$3' => 'google.com:a-b'],
            ],
            [
                'buckets/*/objects/**',
                'buckets/foo/objects/bar/baz',
                ['$0' => 'foo', '$1' => 'bar/baz'],
            ],
            [
                'foo/*/{bar=*/rar/*}/**/*',
                'foo/fizz/fuzz/rar/bar/bizz/buzz/baz',
                ['$0' => 'fizz', '$1' => 'bizz/buzz', '$2' => 'baz', 'bar' => 'fuzz/rar/bar'],
            ],
            [
                'buckets/*',
                'buckets/{}!@#$%^&*()+=[]\|`~-_',
                ['$0' => '{}!@#$%^&*()+=[]\|`~-_'],
            ],
            [
              'foos/{foo}_{oof}',
              'foos/imafoo_thisisanoof',
              ['foo' => 'imafoo', 'oof' => 'thisisanoof'],
            ],
            [
              'foos/{foo}.{oof}_{bar}.{car}',
              'foos/food.doof_mars.porsche',
              ['foo' => 'food', 'oof' => 'doof', 'bar' => 'mars', 'car' => 'porsche'],
            ],
            [
              'foos/{foo}_{oof}-{bar}.{baz}~{car}/projects/{project}/locations/{state}~{city}.{cell}',
              'foos/food_doof-mars.bazz~porsche/projects/someProject/locations/wa~sea.fre3',
              ['foo' => 'food', 'oof' => 'doof', 'bar' => 'mars', 'baz' => 'bazz', 'car' => 'porsche', 'project' => 'someProject', 'state' => 'wa', 'city' => 'sea', 'cell' => 'fre3'],
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
        $template = new RelativeResourceTemplate($pathTemplate);
        $template->match($path);
    }

    /**
     * @param string $pathTemplate
     * @param string $path
     * @dataProvider invalidMatchData
     */
    public function testFailMatches($pathTemplate, $path)
    {
        $template = new RelativeResourceTemplate($pathTemplate);
        $this->assertFalse($template->matches($path));
    }

    public function invalidMatchData()
    {
        return [
            [
                'buckets/*/*/objects/*',
                'buckets/f/o/objects/bar/far', // Extra '/far'
            ],
            [
                'buckets/*/*/objects/*',
                'buckets/f/o/objects', // Missing final wildcard
            ],
            [
                'foo/*/bar',
                'foo/bar', // Missing middle wildcard
            ],
            [
                'foo/*/bar',
                'foo/fizz/buzz/bar', // Too many segments for middle wildcard
            ],
            [
                'foo/**/bar',
                'foo/bar', // Missing middle wildcard
            ],
            [
                'buckets',
                'bouquets', // Wrong literal
            ],
            [
                'buckets',
                'bucketseller', // Wrong literal
            ],
            [
                'foo/*/{bar=*/rar/*}/**/*',
                'foo/fizz/fuzz/rat/bar/bizz/buzz/baz',
            ],
        ];
    }

    /**
     * @param string $pathTemplate
     * @param array $bindings
     * @dataProvider invalidRenderData
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testFailRender($pathTemplate, $bindings, $expectedExceptionMessage = null)
    {
        if (isset($expectedExceptionMessage)) {
            $this->setExpectedException($this->getExpectedException(), $expectedExceptionMessage);
        }
        $template = new RelativeResourceTemplate($pathTemplate);
        $template->render($bindings);
    }

    public function invalidRenderData()
    {
        return [
            [
                'buckets/*/*/objects/*',
                ['$0' => 'f', '$2' => 'bar'], // Missing key
                "Error rendering 'buckets/*/*/objects/*': missing required binding '$1' for segment '*'\n" .
                 "Provided bindings: Array\n" .
                 "(\n" .
                 "    [$0] => f\n" .
                 "    [$2] => bar\n" .
                 ")\n",
            ],
            [
                'buckets/{hello}',
                ['hellop' => 'world'], // Wrong key
            ],
            [
                'buckets/{hello=*}',
                ['hello' => 'world/weary'], // Invalid binding
            ],
            [
                'buckets/{hello=*}',
                ['hello' => ''], // Invalid binding
                "Error rendering 'buckets/{hello=*}': expected binding 'hello' to match segment '{hello=*}', instead got ''\n" .
                "Provided bindings: Array\n" .
                "(\n" .
                "    [hello] => \n" .
                ")\n",
            ],
            [
                'buckets/{hello=*}',
                ['hello' => null], // Invalid binding
                "Error rendering 'buckets/{hello=*}': expected binding 'hello' to match segment '{hello=*}', instead got null\n" .
                "Provided bindings: Array\n" .
                "(\n" .
                "    [hello] => \n" .
                ")\n",
            ],
            [
                'buckets/*/objects/**',
                ['$0' => 'foo', '$1' => ''],  // Invalid binding
                "Error rendering 'buckets/*/objects/**': expected binding '$1' to match segment '**', instead got ''\n" .
                "Provided bindings: Array\n" .
                "(\n" .
                "    [$0] => foo\n" .
                "    [$1] => \n" .
                ")\n",
            ],
            [
                'buckets/*/objects/**',
                ['$0' => 'foo', '$1' => null],  // Invalid binding
                "Error rendering 'buckets/*/objects/**': expected binding '$1' to match segment '**', instead got null\n" .
                "Provided bindings: Array\n" .
                "(\n" .
                "    [$0] => foo\n" .
                "    [$1] => \n" .
                ")\n",
            ],
            [
                'foo/*/{bar=*/rar/*}/**/*:action',
                ['$0' => 'fizz', '$1' => 'bizz/buzz', '$2' => 'baz', 'bar' => 'fuzz/rat/bar'],
                // Invalid binding
            ],
        ];
    }
}
