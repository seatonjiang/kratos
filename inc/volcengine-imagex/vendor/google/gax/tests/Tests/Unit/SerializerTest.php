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

use Google\Api\HttpRule;
use Google\ApiCore\Serializer;
use Google\Protobuf\Any;
use Google\Protobuf\FieldMask;
use Google\Protobuf\ListValue;
use Google\Protobuf\Struct;
use Google\Protobuf\Value;
use Google\Rpc\Status;
use Google\Type\Color;
use PHPUnit\Framework\TestCase;

/**
 * @group core
 */
class SerializerTest extends TestCase
{
    /**
     * @param \Google\Protobuf\Internal\Message $message A protobuf message
     * @param array $arrayStructure An array structure corresponding the expected encoding of $message
     */
    private function verifySerializeAndDeserialize($message, $arrayStructure)
    {
        $serializer = new Serializer();
        $klass = get_class($message);

        // Check that $message when encoded is equal to $arrayStructure
        $serializedMessage = $serializer->encodeMessage($message);
        $this->assertEquals($arrayStructure, $serializedMessage);

        // Check that $message when encoded and decoded is unchanged
        $deserializedMessage = $serializer->decodeMessage(new $klass(), $serializedMessage);
        $this->assertEquals($message, $deserializedMessage);

        // Check that $arrayStructure when decoded is equal to $message
        $deserializedStructure = $serializer->decodeMessage(new $klass(), $arrayStructure);
        $this->assertEquals($message, $deserializedStructure);

        // Check that $arrayStructure when decoded and encoded is unchanged
        $reserializedStructure = $serializer->encodeMessage($deserializedStructure);
        $this->assertEquals($arrayStructure, $reserializedStructure);
    }

    public function testStatusMessage()
    {
        $details = [new Any()];
        $message = new Status();
        $message->setMessage("message");
        $message->setCode(0);
        $message->setDetails($details);

        $encodedMessage = [
            'message' => 'message',
            'code' => 0,
            'details' => [
                [
                    'typeUrl' => '',
                    'value' => '',
                ],
            ]
        ];

        $this->verifySerializeAndDeserialize($message, $encodedMessage);
    }

    public function testHttpRule()
    {
        $message = new HttpRule();

        $encodedMessage = [
            'selector' => '',
            'body' => '',
            'responseBody' => '',
            'additionalBindings' => [],
        ];

        $this->verifySerializeAndDeserialize($message, $encodedMessage);
    }

    public function testHttpRuleSetOneof()
    {
        $message = new HttpRule();
        $message->setPatch('');

        $encodedMessage = [
            'selector' => '',
            'patch' => '',
            'body' => '',
            'responseBody' => '',
            'additionalBindings' => [],
        ];

        $this->verifySerializeAndDeserialize($message, $encodedMessage);
    }

    public function testHttpRuleSetOneofToValue()
    {
        $message = new HttpRule();
        $message->setPatch('test');

        $encodedMessage = [
            'selector' => '',
            'patch' => 'test',
            'body' => '',
            'responseBody' => '',
            'additionalBindings' => [],
        ];

        $this->verifySerializeAndDeserialize($message, $encodedMessage);
    }

    public function testFieldMask()
    {
        $message = new FieldMask();

        $encodedMessage = [
            'paths' => []
        ];

        $this->verifySerializeAndDeserialize($message, $encodedMessage);
    }

    public function testProperlyHandlesMessage()
    {
        $value = 'test';

        // Using this class because it contains maps, oneofs and structs
        $message = new \Google\Protobuf\Struct();

        $innerValue1 = new Value();
        $innerValue1->setStringValue($value);

        $innerValue2 = new Value();
        $innerValue2->setBoolValue(true);

        $structValue1 = new Value();
        $structValue1->setStringValue(strtoupper($value));
        $structValue2 = new Value();
        $structValue2->setStringValue($value);
        $labels = [
            strtoupper($value) => $structValue1,
            $value => $structValue2,
        ];
        $innerStruct = new Struct();
        $innerStruct->setFields($labels);
        $innerValue3 = new Value();
        $innerValue3->setStructValue($innerStruct);

        $innerValues = [$innerValue1, $innerValue2, $innerValue3];
        $listValue = new ListValue();
        $listValue->setValues($innerValues);
        $fieldValue = new Value();
        $fieldValue->setListValue($listValue);

        $fields = [
            'listField' => $fieldValue,
        ];
        $message->setFields($fields);

        $encodedMessage = [
            'fields' => [
                'listField' => [
                    'listValue' => [
                        'values' => [
                            [
                                'stringValue' => $value,
                            ],
                            [
                                'boolValue' => true,
                            ],
                            [
                                'structValue' => [
                                    'fields' => [
                                        strtoupper($value) => [
                                            'stringValue' => strtoupper($value),
                                        ],
                                        $value => [
                                            'stringValue' => $value,
                                        ]
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $this->verifySerializeAndDeserialize($message, $encodedMessage);
    }

    public function testSpecialEncodingDecodingByFieldName()
    {
        $serializer = new Serializer([
            'red' => function ($v) {
                return $v * 2;
            }
        ], [], [
            'red' => function ($v) {
                return $v / 2;
            }
        ]);
        $data = [
            'red' => 0.2,
            'green' => 0.3,
            'blue' => 0.4,
            'alpha' => [
                'value' => 1.0
            ]
        ];
        $color = $serializer->decodeMessage(new Color(), $data);
        $this->assertEquals(0.1, $color->getRed(), '', 0.0000001);
        $this->assertEquals(0.3, $color->getGreen(), '', 0.0000001);
        $this->assertEquals(0.4, $color->getBlue(), '', 0.0000001);
        $alpha = $color->getAlpha();
        $this->assertEquals(1.0, $alpha->getValue(), '', 0.0000001);

        $array = $serializer->encodeMessage($color);
        $this->assertEquals($data['red'], $array['red'], '', 0.0000001);
        $this->assertEquals($data['green'], $array['green'], '', 0.0000001);
        $this->assertEquals($data['blue'], $array['blue'], '', 0.0000001);
        $this->assertEquals($data['alpha']['value'], $array['alpha']['value'], '', 0.0000001);
    }

    public function testSpecialEncodingDecodingByFieldType()
    {
        $serializer = new Serializer([], [
            'google.protobuf.FloatValue' => function ($v) {
                return [
                    'value' => $v['value'] * 2
                ];
            }
        ], [], [
            'google.protobuf.FloatValue' => function ($v) {
                return [
                    'value' => $v['value'] / 2
                ];
            }
        ]);
        $data = [
            'red' => 0.2,
            'green' => 0.3,
            'blue' => 0.4,
            'alpha' => [
                'value' => 1.0
            ]
        ];

        $color = $serializer->decodeMessage(new Color(), $data);
        $this->assertEquals(0.2, $color->getRed(), '', 0.0000001);
        $this->assertEquals(0.3, $color->getGreen(), '', 0.0000001);
        $this->assertEquals(0.4, $color->getBlue(), '', 0.0000001);
        $alpha = $color->getAlpha();
        $this->assertEquals(0.5, $alpha->getValue(), '', 0.0000001);

        $array = $serializer->encodeMessage($color);
        $this->assertEquals($data['red'], $array['red'], '', 0.0000001);
        $this->assertEquals($data['green'], $array['green'], '', 0.0000001);
        $this->assertEquals($data['blue'], $array['blue'], '', 0.0000001);
        $this->assertEquals($data['alpha']['value'], $array['alpha']['value'], '', 0.0000001);
    }
}
