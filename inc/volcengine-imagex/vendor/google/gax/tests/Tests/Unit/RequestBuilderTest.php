<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\ApiCore\Tests\Unit;

use Google\ApiCore\RequestBuilder;
use Google\ApiCore\Testing\MockRequestBody;
use Google\Protobuf\BytesValue;
use Google\Protobuf\Duration;
use Google\Protobuf\FieldMask;
use Google\Protobuf\Int64Value;
use Google\Protobuf\ListValue;
use Google\Protobuf\StringValue;
use Google\Protobuf\Struct;
use Google\Protobuf\Timestamp;
use Google\Protobuf\Value;
use GuzzleHttp\Psr7;
use PHPUnit\Framework\TestCase;

/**
 * @group core
 */
class RequestBuilderTest extends TestCase
{
    const SERVICE_NAME = 'test.interface.v1.api';

    public function setUp()
    {
        $this->builder = new RequestBuilder(
            'www.example.com',
            __DIR__ . '/testdata/test_service_rest_client_config.php'
        );
    }

    public function testMethodWithUrlPlaceholder()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithUrlPlaceholder', $message);
        $uri = $request->getUri();

        $this->assertEmpty($uri->getQuery());
        $this->assertEmpty((string) $request->getBody());
        $this->assertEquals('/v1/message/foo', $uri->getPath());
    }

    public function testMethodWithBody()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');
        $nestedMessage = new MockRequestBody();
        $nestedMessage->setName('nested/foo');
        $message->setNestedMessage($nestedMessage);

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithBodyAndUrlPlaceholder', $message);
        $uri = $request->getUri();

        $this->assertEmpty($uri->getQuery());
        $this->assertEquals('/v1/message/foo', $uri->getPath());
        $this->assertEquals(
            ['name' => 'message/foo', 'nestedMessage' => ['name' => 'nested/foo']],
            json_decode($request->getBody(), true)
        );
    }

    public function testMethodWithNestedMessageAsBody()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');
        $nestedMessage = new MockRequestBody();
        $nestedMessage->setName('nested/foo');
        $message->setNestedMessage($nestedMessage);

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithNestedMessageAsBody', $message);
        $uri = $request->getUri();

        $this->assertEmpty($uri->getQuery());
        $this->assertEquals('/v1/message/foo', $uri->getPath());
        $this->assertEquals(
            ['name' => 'nested/foo'],
            json_decode($request->getBody(), true)
        );
    }

    public function testMethodWithScalarBody()
    {
        $message = new MockRequestBody();
        $message->setName('foo');

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithScalarBody', $message);

        $this->assertEquals(
            '"foo"',
            (string) $request->getBody()
        );
    }

    public function testMethodWithEmptyMessageInBody()
    {
        $message = new MockRequestBody();
        $nestedMessage = new MockRequestBody();
        $message->setNestedMessage($nestedMessage);

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithBody', $message);

        $this->assertEquals(
            '{"nestedMessage":{}}',
            $request->getBody()
        );
    }

    public function testMethodWithEmptyMessageInNestedMessageBody()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');
        $nestedMessage = new MockRequestBody();
        $message->setNestedMessage($nestedMessage);
        $emptyMessage = new MockRequestBody();
        $nestedMessage->setNestedMessage($emptyMessage);


        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithNestedMessageAsBody', $message);

        $this->assertEquals(
            '{"nestedMessage":{}}',
            $request->getBody()
        );
    }

    public function testMethodWithNestedUrlPlaceholder()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');
        $nestedMessage = new MockRequestBody();
        $nestedMessage->setName('nested/foo');
        $message->setNestedMessage($nestedMessage);

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithNestedUrlPlaceholder', $message);
        $uri = $request->getUri();

        $this->assertEmpty($uri->getQuery());
        $this->assertEquals('/v1/nested/foo', $uri->getPath());
        $this->assertEquals(
            ['name' => 'message/foo', 'nestedMessage' => ['name' => 'nested/foo']],
            json_decode($request->getBody(), true)
        );
    }

    public function testMethodWithUrlRepeatedField()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');
        $message->setRepeatedField(['bar1', 'bar2']);

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithUrlPlaceholder', $message);
        $uri = $request->getUri();

        $this->assertEmpty((string) $request->getBody());
        $this->assertEquals('/v1/message/foo', $uri->getPath());
        $this->assertEquals('repeatedField=bar1&repeatedField=bar2', $uri->getQuery());
    }

    public function testMethodWithHeaders()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithUrlPlaceholder', $message, [
            'header1' => 'value1',
            'header2' => 'value2'
        ]);

        $this->assertEquals('value1', $request->getHeaderLine('header1'));
        $this->assertEquals('value2', $request->getHeaderLine('header2'));
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
    }

    public function testMethodWithColon()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithColonInUrl', $message);
        $uri = $request->getUri();

        $this->assertEmpty($uri->getQuery());
        $this->assertEquals('/v1/message/foo:action', $uri->getPath());
    }

    public function testMethodWithMultipleWildcardsAndColonInUrl()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');
        $message->setNumber(10);

        $request = $this->builder->build(
            self::SERVICE_NAME . '/MethodWithMultipleWildcardsAndColonInUrl',
            $message
        );
        $uri = $request->getUri();

        $this->assertEmpty($uri->getQuery());
        $this->assertEquals('/v1/message/foo/number/10:action', $uri->getPath());
    }

    public function testMethodWithSimplePlaceholder()
    {
        $message = new MockRequestBody();
        $message->setName('message-name');

        $request = $this->builder->build(
            self::SERVICE_NAME . '/MethodWithSimplePlaceholder',
            $message
        );
        $uri = $request->getUri();

        $this->assertEquals('/v1/message-name', $uri->getPath());
    }

    public function testMethodWithAdditionalBindings()
    {
        $message = new MockRequestBody();
        $message->setName('message/foo');
        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithAdditionalBindings', $message);

        $this->assertEquals('/v1/message/foo/additional/bindings', $request->getUri()->getPath());

        $message->setName('different/format/foo');
        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithAdditionalBindings', $message);

        $this->assertEquals('/v1/different/format/foo/additional/bindings', $request->getUri()->getPath());

        $nestedMessage = new MockRequestBody();
        $nestedMessage->setName('nested/foo');
        $message->setNestedMessage($nestedMessage);
        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithAdditionalBindings', $message);

        $this->assertEquals('/v2/nested/foo/additional/bindings', $request->getUri()->getPath());
    }

    public function testMethodWithSpecialJsonMapping()
    {
        $bytesValue = (new BytesValue)
            ->setValue('\000');
        $durationValue = (new Duration)
            ->setSeconds(9001)
            ->setNanos(500000);

        $fieldMask = (new FieldMask)
            ->setPaths(['path1', 'path2']);
        $int64Value = (new Int64Value)
            ->setValue(100);
        $listValue = (new ListValue)
            ->setValues([
                (new Value)->setStringValue('val1'),
                (new Value)->setStringValue('val2')
            ]);
        $stringValue = (new StringValue)
            ->setValue('some-value');
        $structValue = (new Struct)
            ->setFields([
                'test' => (new Value)->setStringValue('val5')
            ]);
        $timestampValue = (new Timestamp)
            ->setSeconds(9001);
        $valueValue = (new Value)
            ->setStringValue('some-value');

        $message = (new MockRequestBody())
            ->setBytesValue($bytesValue)
            ->setDurationValue($durationValue)
            ->setFieldMask($fieldMask)
            ->setInt64Value($int64Value)
            ->setListValue($listValue)
            ->setStringValue($stringValue)
            ->setStructValue($structValue)
            ->setTimestampValue($timestampValue)
            ->setValueValue($valueValue);

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithSpecialJsonMapping', $message);
        $uri = $request->getUri();

        $this->assertContains('listValue=val1&listValue=val2', (string) $uri);

        $query = Psr7\parse_query($uri->getQuery());


        $this->assertEquals('XDAwMA==', $query['bytesValue']);
        // @todo (dwsupplee) Investigate differences in native protobuf implementation
        // between v3.7.0 and v3.9.0 - this passed previously with the value
        // "9001.000500000s".
        if (extension_loaded('protobuf')) {
            $this->assertEquals('9001.000500000s', $query['durationValue']);
        } else {
            $this->assertEquals('9001.000500s', $query['durationValue']);
        }
        $this->assertEquals('path1,path2', $query['fieldMask']);
        $this->assertEquals(100, $query['int64Value']);
        $this->assertEquals(['val1', 'val2'], $query['listValue']);
        $this->assertEquals('some-value', $query['stringValue']);
        $this->assertEquals('val5', $query['structValue.test']);
        $this->assertEquals('1970-01-01T02:30:01Z', $query['timestampValue']);
        $this->assertEquals('some-value', $query['valueValue']);
    }

    public function testMethodWithoutPlaceholders()
    {
        $stringValue = (new StringValue)
            ->setValue('some-value');

        $fieldMask = (new FieldMask)
            ->setPaths(['path1', 'path2']);

        $message = (new MockRequestBody())
            ->setStringValue($stringValue)
            ->setFieldMask($fieldMask);

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithoutPlaceholders', $message);
        $query = Psr7\parse_query($request->getUri()->getQuery());

        $this->assertEquals('path1,path2', $query['fieldMask']);
        $this->assertEquals('some-value', $query['stringValue']);
    }

    public function testMethodWithComplexMessageInQueryString()
    {
        $message = (new MockRequestBody())
            ->setNestedMessage(
                (new MockRequestBody)
                    ->setName('some-name')
                    ->setNumber(10)
            );

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithoutPlaceholders', $message);
        $query = Psr7\parse_query($request->getUri()->getQuery());

        $this->assertEquals('some-name', $query['nestedMessage.name']);
        $this->assertEquals(10, $query['nestedMessage.number']);
    }

    public function testMethodWithOneOfInQueryString()
    {
        $message = (new MockRequestBody())
            ->setField1('some-value');

        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithoutPlaceholders', $message);
        $query = Psr7\parse_query($request->getUri()->getQuery());

        $this->assertEquals('some-value', $query['field1']);
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Could not map bindings for test.interface.v1.api/MethodWithAdditionalBindings to any Uri template.
     */
    public function testThrowsExceptionWithNonMatchingFormat()
    {
        $message = new MockRequestBody();
        $message->setName('invalid/name/format');
        $request = $this->builder->build(self::SERVICE_NAME . '/MethodWithAdditionalBindings', $message);
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Failed to build request, as the provided path (myResource/doesntExist) was not found in the configuration.
     */
    public function testThrowsExceptionWithNonExistantMethod()
    {
        $message = new MockRequestBody();
        $this->builder->build('myResource/doesntExist', $message);
    }
}
