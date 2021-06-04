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

use Google\ApiCore\GrpcSupportTrait;
use PHPUnit\Framework\TestCase;

class GrpcSupportTraitTest extends TestCase
{
    use GrpcSupportTrait;

    private static $hasGrpc;

    public function testValidateGrpcSupportSuccess()
    {
        self::$hasGrpc = true;
        self::validateGrpcSupport();
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage gRPC support has been requested
     */
    public function testValidateGrpcSupportFailure()
    {
        self::$hasGrpc = false;
        self::validateGrpcSupport();
    }

    /**
     * "Override" existing trait method using late static binding
     */
    private static function getGrpcDependencyStatus()
    {
        return self::$hasGrpc;
    }
}