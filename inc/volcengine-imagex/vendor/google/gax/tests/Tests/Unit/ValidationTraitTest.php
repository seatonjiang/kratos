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

use Google\ApiCore\ValidationTrait;
use PHPUnit\Framework\TestCase;

class ValidationTraitTest extends TestCase
{
    private $stub;

    public function setUp()
    {
        $this->stub = new ValidationTraitStub;
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Missing required argument
     */
    public function testValidateMissingRequiredKey()
    {
        $input = [
            'foo' => 1,
            'bar' => 2
        ];

        $this->stub->validate($input, ['bar', 'baz']);
    }

    public function testValidateValidArray()
    {
        $input = [
            'foo' => 1,
            'bar' => 2
        ];

        $arr = $this->stub->validate($input, ['foo', 'bar']);

        $this->assertEquals($input, $arr);
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     * @expectedExceptionMessage Missing required argument
     */
    public function testValidateNotNullWithNullRequiredKey()
    {
        $input = [
            'foo' => 1,
            'bar' => null
        ];

        $this->stub->validateNotNull($input, ['foo', 'bar']);
    }

    public function testValidateValidArrayWithNotNull()
    {
        $input = [
            'foo' => 1,
            'bar' => 2
        ];

        $arr = $this->stub->validateNotNull($input, ['foo', 'bar']);

        $this->assertEquals($input, $arr);
    }
}

class ValidationTraitStub
{
    use ValidationTrait;
}
