<?php
/**
 * Copyright 2018 Google LLC
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

use Google\ApiCore\Testing\GeneratedTest;

class ProtobufBandaidTest extends GeneratedTest
{
    /**
     * @dataProvider protobufMessageProvider
     */
    public function testCompare($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
    }

    public function protobufMessageProvider()
    {
        $msg1 = new MyMessage();
        $msg2 = new Mymessage();
        return [
            [$msg1, $msg2],
            [[$msg1, $msg2], [$msg1, $msg2]]
        ];
    }
}
