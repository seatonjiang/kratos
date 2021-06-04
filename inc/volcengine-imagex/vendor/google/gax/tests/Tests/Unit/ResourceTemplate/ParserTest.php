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

use Google\ApiCore\ResourceTemplate\Parser;
use Google\ApiCore\ResourceTemplate\RelativeResourceTemplate;
use Google\ApiCore\ResourceTemplate\Segment;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private static function literalSegment($value, $separator = '/')
    {
        return new Segment(Segment::LITERAL_SEGMENT, $value, null, null, $separator);
    }

    private static function wildcardSegment()
    {
        return new Segment(Segment::WILDCARD_SEGMENT);
    }

    private static function doubleWildcardSegment()
    {
        return new Segment(Segment::DOUBLE_WILDCARD_SEGMENT);
    }

    private static function variableSegment($key, $template, $separator = '/')
    {
        return new Segment(Segment::VARIABLE_SEGMENT, null, $key, $template, $separator);
    }

    /**
     * @dataProvider validPathProvider
     * @param string $path
     * @param Segment[] $expectedSegments
     */
    public function testParseSegments($path, $expectedSegments)
    {
        $actualSegments = Parser::parseSegments($path);
        $this->assertEquals($expectedSegments, $actualSegments);
    }

    public function testParseBasicSegments() {
      $this->testParseSegments("foo/bar/baz",
        [self::literalSegment("foo"),
        self::literalSegment("bar"),
        self::literalSegment("baz")]);

      $this->testParseSegments("foos/{foo}/bars/{bar}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*")),
        self::literalSegment("bars"),
        self::variableSegment("bar", new RelativeResourceTemplate("*"))]);
    }

    public function testParseBasicNonSlashSeparators() {
      $this->testParseSegments("foos/{foo}_{oof}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '_'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"))]);

      $this->testParseSegments("foos/{foo}-{oof}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '-'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"))]);

      $this->testParseSegments("foos/{foo}~{oof}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '~'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"))]);

      $this->testParseSegments("foos/{foo}.{oof}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '.'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"))]);
    }

    public function testParseMultipleNonSlashSeparators() {
      $this->testParseSegments("foos/{foo}_{oof}-{bar}.{baz}~{car}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '_'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"), '-'),
        self::variableSegment("bar", new RelativeResourceTemplate("*"), '.'),
        self::variableSegment("baz", new RelativeResourceTemplate("*"), '~'),
        self::variableSegment("car", new RelativeResourceTemplate("*"))]);

      $this->testParseSegments("foos/{foo}.{oof}_{bar}.{car}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '.'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"), '_'),
        self::variableSegment("bar", new RelativeResourceTemplate("*"), '.'),
        self::variableSegment("car", new RelativeResourceTemplate("*"))]);

      $this->testParseSegments("foos/{foo}-{oof}.{bar}~{car}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '-'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"), '.'),
        self::variableSegment("bar", new RelativeResourceTemplate("*"), '~'),
        self::variableSegment("car", new RelativeResourceTemplate("*"))]);
    }

    public function testParseNonSlashSeparatorsWithParents() {
      $this->testParseSegments("foos/{foo}_{oof}-{bar}.{baz}~{car}/projects/{project}/locations/{state}~{city}.{cell}",
        [self::literalSegment("foos"),
        self::variableSegment("foo", new RelativeResourceTemplate("*"), '_'),
        self::variableSegment("oof", new RelativeResourceTemplate("*"), '-'),
        self::variableSegment("bar", new RelativeResourceTemplate("*"), '.'),
        self::variableSegment("baz", new RelativeResourceTemplate("*"), '~'),
        self::variableSegment("car", new RelativeResourceTemplate("*")),
        self::literalSegment("projects"),
        self::variableSegment("project", new RelativeResourceTemplate("*")),
        self::literalSegment("locations"),
        self::variableSegment("state", new RelativeResourceTemplate("*"), '~'),
        self::variableSegment("city", new RelativeResourceTemplate("*"), '.'),
        self::variableSegment("cell", new RelativeResourceTemplate("*"))]);

      $this->testParseSegments("customers/{customer_id}/userLocationViews/{country_criterion_id}~{is_targeting_location}",
        [self::literalSegment("customers"),
        self::variableSegment("customer_id", new RelativeResourceTemplate("*")),
        self::literalSegment("userLocationViews"),
        self::variableSegment("country_criterion_id", new RelativeResourceTemplate("*"), '~'),
        self::variableSegment("is_targeting_location", new RelativeResourceTemplate("*"))]);
    }

    public function validPathProvider()
    {
        $singlePathTests = [
            ["foo", [self::literalSegment("foo")]],
            ["helloazAZ09-.~_what", [self::literalSegment("helloazAZ09-.~_what")]],
            ["*", [self::wildcardSegment()]],
            ["**", [self::doubleWildcardSegment()]],
            ["{foo}", Parser::parseSegments("{foo=*}")],
            ["{foo=*}", [self::variableSegment("foo", new RelativeResourceTemplate("*"))]],
            ["{foo=**}", [self::variableSegment("foo", new RelativeResourceTemplate("**"))]],
        ];

        $comboPathPieces = [
            ["foo", [self::literalSegment("foo")]],
            ["helloazAZ09-.~_what", [self::literalSegment("helloazAZ09-.~_what")]],
            ["*", [self::wildcardSegment()]],
            ["*", [self::wildcardSegment()]],
            ["**", [self::doubleWildcardSegment()]],
        ];

        // Combine the pieces in $comboPathPieces in every possible order
        $comboPathTests = [];
        foreach (self::yieldAllSequences($comboPathPieces) as $comboSequence) {
            $pathPieces = [];
            $segments = [];
            foreach ($comboSequence as list($path, $segmentArray)) {
                $pathPieces[] = $path;
                $segments = array_merge($segments, $segmentArray);
            }
            $comboPathTests[] = [implode('/', $pathPieces), $segments];
        }

        return $singlePathTests + $comboPathTests;
    }

    /**
     * @dataProvider sequenceProvider
     * @param $sequence
     * @param $expectedSequences
     */
    public function testYieldAllSequences($sequence, $expectedSequences)
    {
        $actual = iterator_to_array(self::yieldAllSequences($sequence));
        $this->assertEquals($expectedSequences, $actual);
    }

    public function sequenceProvider()
    {
        return [
            [['a'], [['a']]],
            [['a', 'b'], [
                ['a'],
                ['a', 'b'],
                ['b'],
                ['b', 'a'],
            ]],
            [['a', 'b', 'c'], [
                ['a'],
                ['a', 'b'],
                ['a', 'b', 'c'],
                ['a', 'c'],
                ['a', 'c', 'b'],
                ['b'],
                ['b', 'a'],
                ['b', 'a', 'c'],
                ['b', 'c'],
                ['b', 'c', 'a'],
                ['c'],
                ['c', 'a'],
                ['c', 'a', 'b'],
                ['c', 'b'],
                ['c', 'b', 'a'],
            ]],
        ];
    }

    private static function yieldAllSequences($items)
    {
        $keys = array_keys($items);
        foreach ($keys as $key) {
            $itemsCopy = $items;
            $value = $itemsCopy[$key];
            yield [$value];
            unset($itemsCopy[$key]);
            foreach (self::yieldAllSequences($itemsCopy) as $subsequence) {
                yield array_merge([$value], $subsequence);
            }
        }
    }

    /**
     * @dataProvider invalidPathProvider
     * @expectedException \Google\ApiCore\ValidationException
     * @param string $path
     */
    public function testParseInvalid($path)
    {
        Parser::parseSegments($path);
    }

    public function invalidPathProvider()
    {
        return [
            [null],                     // Null path
            [""],                       // Empty path
            ["/foo"],                   // Leading '/'
            ["foo:bar"],                // Contains ':'
            ["foo{barbaz"],             // Contains '{'
            ["foo}barbaz"],             // Contains '}'
            ["foo{bar}baz"],            // Contains '{' and '}'
            ["{}"],                     // Empty var
            ["{foo#bar}"],              // Invalid var
            ["{foo.bar=baz"],           // Unbalanced '{'
            ["{foo.bar=baz=fizz}"],     // Multiple '=' in variable
            ["{foo.bar=**/**}"],        // Invalid resource template
            ["/foo"],                   // Leading '/'
            ["foo//bar"],               // Consecutive '/'
            ["foo/"],                   // Trailing '/'
        ];
    }

    /**
     * @param string $literal
     * @dataProvider validLiterals
     */
    public function testIsValidLiteral($literal)
    {
        $this->assertTrue($this->invokeStaticMethod(Parser::class, 'isValidLiteral', [$literal]));
    }

    public function validLiterals()
    {
        return [
            ["foo"],
            ["helloazAZ09-.~_what"],
            ["5"],
            ["5five"],
        ];
    }

    /**
     * @param string $literal
     * @dataProvider invalidLiterals
     */
    public function testFailIsValidLiteral($literal)
    {
        $this->assertFalse($this->invokeStaticMethod(Parser::class, 'isValidLiteral', [$literal]));
    }

    public function invalidLiterals()
    {
        return [
            [null],
            [""],
            ["fo\$o"],
            ["fo{o"],
            ["fo}o"],
            ["fo/o"],
            ["fo#o"],
            ["fo%o"],
            ["fo\\o"],
        ];
    }

    /**
     * @param string $binding
     * @dataProvider validBindings
     */
    public function testIsValidBinding($binding)
    {
        $this->assertTrue($this->invokeStaticMethod(Segment::class, 'isValidBinding', [$binding]));
    }

    public function validBindings()
    {
        return array_merge(
            $this->validLiterals(),
            [
                ["fo#o"],
                ["fo%o"],
                ["fo!o"],
                ["fo@o"],
                ["fo#o"],
                ["fo\$o"],
                ["fo%o"],
                ["fo^o"],
                ["fo&o"],
                ["fo*o"],
                ["fo(o"],
                ["fo)o"],
                ["fo{o"],
                ["fo}o"],
                ["fo+o"],
                ["fo=o"],
            ]
        );
    }

    /**
     * @param string $binding
     * @dataProvider invalidBindings
     */
    public function testFailIsValidBinding($binding)
    {
        $this->assertFalse($this->invokeStaticMethod(Segment::class, 'isValidBinding', [$binding]));
    }

    public function invalidBindings()
    {
        return [
            [null],
            [""],
            ["fo/o"],
        ];
    }

    /**
     * @param string $binding
     * @dataProvider validDoubleWildcardBindings
     */
    public function testIsValidDoubleWildcardBinding($binding)
    {
        $this->assertTrue($this->invokeStaticMethod(Segment::class, 'isValidDoubleWildcardBinding', [$binding]));
    }

    public function validDoubleWildcardBindings()
    {
        return array_merge(
            $this->validBindings(),
            [
                ["fo/o"]
            ]
        );
    }

    /**
     * @param string $binding
     * @dataProvider invalidDoubleWildcardBindings
     */
    public function testFailIsValidDoubleWildcardBinding($binding)
    {
        $this->assertFalse($this->invokeStaticMethod(Segment::class, 'isValidDoubleWildcardBinding', [$binding]));
    }

    public function invalidDoubleWildcardBindings()
    {
        return [
            [null],
            [""],
        ];
    }

    private function invokeStaticMethod($class, $methodName, array $parameters)
    {
        $reflection = new \ReflectionClass($class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $parameters);
    }
}
