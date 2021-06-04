<?php
/**
 * Copyright 2018 Google Inc.
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

use Google\ApiCore\ServiceAddressTrait;
use PHPUnit\Framework\TestCase;

/**
 * @todo (dwsupplee) serviceAddress is deprecated now in favor of
 *        apiEndpoint. Rename the tests/variables in our next major release.
 */
class ServiceAddressTraitTest extends TestCase
{
    use ServiceAddressTrait;

    /**
     * @dataProvider normalizeServiceAddressData
     */
    public function testNormalizeServiceAddress($serviceAddressString, $expectedAddress, $expectedPort)
    {
        list($actualAddress, $actualPort) = self::normalizeServiceAddress($serviceAddressString);
        $this->assertSame($expectedAddress, $actualAddress);
        $this->assertSame($expectedPort, $actualPort);
    }

    public function normalizeServiceAddressData()
    {
        return [
            ["simple.com:123", "simple.com", "123"],
            ["really.long.and.dotted:456", "really.long.and.dotted", "456"],
            ["noport.com", "noport.com", self::$defaultPort],
        ];
    }

    /**
     * @dataProvider normalizeServiceAddressInvalidData
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Invalid apiEndpoint
     */
    public function testNormalizeServiceAddressInvalid($serviceAddressString)
    {
        self::normalizeServiceAddress($serviceAddressString);
    }

    public function normalizeServiceAddressInvalidData()
    {
        return [
            ["too.many:colons:123"],
            ["too:many:colons"],
        ];
    }
}
