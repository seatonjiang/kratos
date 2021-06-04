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

use Google\ApiCore\PathTemplate;
use PHPUnit\Framework\TestCase;

class PathTemplateTest extends TestCase
{
    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Unexpected characters in literal segment
     */
    public function testFailInvalidToken()
    {
        new PathTemplate('hello/wor*ld');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Could not match path
     */
    public function testFailWhenImpossibleMatch01()
    {
        $template = new PathTemplate('hello/world');
        $template->match('hello');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Could not match path
     */
    public function testFailWhenImpossibleMatch02()
    {
        $template = new PathTemplate('hello/world');
        $template->match('hello/world/fail');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Could not match path
     */
    public function testFailMismatchedLiteral()
    {
        $template = new PathTemplate('hello/world');
        $template->match('hello/world2');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Cannot parse
     */
    public function testFailWhenMultiplePathWildcards()
    {
        new PathTemplate('buckets/*/**/**/objects/*');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Unexpected '{'
     */
    public function testFailIfInnerBinding()
    {
        new PathTemplate('buckets/{hello={world}}');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Expected '}'
     */
    public function testFailUnexpectedEof()
    {
        new PathTemplate('a/{hello=world');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Cannot construct PathTemplate from empty string
     */
    public function testFailNullString()
    {
        new PathTemplate(null);
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Cannot construct PathTemplate from empty string
     */
    public function testFailEmptyString()
    {
        new PathTemplate("");
    }

    public function testMatchAtomicResourceName()
    {
        $template = new PathTemplate('buckets/*/*/objects/*');
        $this->assertEquals(
            ['$0' => 'f', '$1' => 'o', '$2' => 'bar'],
            $template->match('buckets/f/o/objects/bar')
        );
        $template = new PathTemplate('/buckets/{hello}');
        $this->assertEquals(
            ['hello' => 'world'],
            $template->match('/buckets/world')
        );
        $template = new PathTemplate('/buckets/{hello=*}');
        $this->assertEquals(
            ['hello' => 'world'],
            $template->match('/buckets/world')
        );
    }

    public function testMatchEscapedChars()
    {
        $template = new PathTemplate('buckets/*/objects');
        $this->assertEquals(
            ['$0' => 'hello%2F%2Bworld'],
            $template->match('buckets/hello%2F%2Bworld/objects')
        );
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testFailMatchWildcardWithColonInMiddle()
    {
        new PathTemplate('/buckets/*:action/objects');
    }

    public function testMatchWildcardWithColon()
    {
        $template = new PathTemplate('/buckets/*:action');
        $this->assertEquals(
            ['$0' => 'foo'],
            $template->match('/buckets/foo:action')
        );
    }

    public function testMatchColonInWildcardAndTemplate()
    {
        $template = new PathTemplate('/buckets/*/*/*/objects/*:action');
        $url = $template->render(
            ['$0' => 'f', '$1' => 'o', '$2' => 'o', '$3' => 'google.com:a-b']
        );
        $this->assertEquals($url, '/buckets/f/o/o/objects/google.com:a-b:action');
    }

    public function testMatchUnboundedWildcardWithColon()
    {
        $template = new PathTemplate('/buckets/*/objects/**:action');
        $this->assertEquals(
            ['$0' => 'foo', '$1' => 'bar/baz'],
            $template->match('/buckets/foo/objects/bar/baz:action')
        );
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testFailMatchUnboundedWildcardWithColonInMiddle()
    {
        new PathTemplate('/buckets/*/objects/**:action/path');
    }

    public function testMatchTemplateWithUnboundedWildcard()
    {
        $template = new PathTemplate('buckets/*/objects/**');
        $this->assertEquals(
            ['$0' => 'foo', '$1' => 'bar/baz'],
            $template->match('buckets/foo/objects/bar/baz')
        );
    }

    public function testMatchWithUnboundInMiddle()
    {
        $template = new PathTemplate('bar/**/foo/*');
        $this->assertEquals(
            ['$0' => 'foo/foo', '$1' => 'bar'],
            $template->match('bar/foo/foo/foo/bar')
        );
    }

    public function testRenderAtomicResource()
    {
        $template = new PathTemplate('buckets/*/*/*/objects/*');
        $url = $template->render(
            ['$0' => 'f', '$1' => 'o', '$2' => 'o', '$3' => 'google.com:a-b']
        );
        $this->assertEquals($url, 'buckets/f/o/o/objects/google.com:a-b');
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Error rendering
     */
    public function testRenderFailWhenTooFewVariables()
    {
        $template = new PathTemplate('buckets/*/*/*/objects/*');
        $template->render(['$0' => 'f', '$1' => 'l', '$2' => 'o']);
    }

    public function testRenderWithUnboundInMiddle()
    {
        $template = new PathTemplate('bar/**/foo/*');
        $url = $template->render(['$0' => '1/2', '$1' => '3']);
        $this->assertEquals($url, 'bar/1/2/foo/3');
    }

    public function testToString()
    {
        $template = new PathTemplate('bar/**/foo/*');
        $this->assertEquals((string) $template, 'bar/**/foo/*');
        $template = new PathTemplate('buckets/*/objects/*');
        $this->assertEquals(
            (string) ($template),
            'buckets/*/objects/*'
        );
        $template = new PathTemplate('/buckets/{hello}');
        $this->assertEquals((string) ($template), '/buckets/{hello=*}');
        $template = new PathTemplate('/buckets/{hello=what}/{world}');
        $this->assertEquals(
            (string) ($template),
            '/buckets/{hello=what}/{world=*}'
        );
        $template = new PathTemplate('/buckets/helloazAZ09-.~_what');
        $this->assertEquals(
            (string) ($template),
            '/buckets/helloazAZ09-.~_what'
        );
    }

    public function testSubstitutionOddChars()
    {
        $template = new PathTemplate('projects/{project}/topics/{topic}');
        $url = $template->render(
            ['project' => 'google.com:proj-test', 'topic' => 'some-topic']
        );
        $this->assertEquals(
            $url,
            'projects/google.com:proj-test/topics/some-topic'
        );
        $template = new PathTemplate('projects/{project}/topics/{topic}');
        $url = $template->render(
            ['project' => 'g.,;:~`!@#$%^&()+-', 'topic' => 'sdf<>,.?[]']
        );
        $this->assertEquals(
            $url,
            'projects/g.,;:~`!@#$%^&()+-/topics/sdf<>,.?[]'
        );
    }
}
