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

use Google\ApiCore\RetrySettings;
use PHPUnit\Framework\TestCase;

class RetrySettingsTest extends TestCase
{
    const SERVICE_NAME = 'test.interface.v1.api';

    private static function buildInputConfig()
    {
        $contents = file_get_contents(__DIR__ . '/testdata/test_service_client_config.json');
        return json_decode($contents, true);
    }

    private static function buildInvalidInputConfig()
    {
        $contents = file_get_contents(__DIR__ . '/testdata/test_service_invalid_client_config.json');
        return json_decode($contents, true);
    }


    public function testConstructSettings()
    {
        $inputConfig = RetrySettingsTest::buildInputConfig();

        $defaultRetrySettings = RetrySettings::load(
            RetrySettingsTest::SERVICE_NAME,
            $inputConfig
        );
        $simpleMethod = $defaultRetrySettings['SimpleMethod'];
        $this->assertTrue($simpleMethod->retriesEnabled());
        $this->assertEquals(40000, $simpleMethod->getNoRetriesRpcTimeoutMillis());
        $this->assertEquals(['DEADLINE_EXCEEDED', 'UNAVAILABLE'], $simpleMethod->getRetryableCodes());
        $this->assertEquals(100, $simpleMethod->getInitialRetryDelayMillis());
        $pageStreamingMethod = $defaultRetrySettings['PageStreamingMethod'];
        $this->assertEquals(['INTERNAL'], $pageStreamingMethod->getRetryableCodes());
        $timeoutOnlyMethod = $defaultRetrySettings['TimeoutOnlyMethod'];
        $this->assertFalse($timeoutOnlyMethod->retriesEnabled());
        $this->assertEquals(40000, $timeoutOnlyMethod->getNoRetriesRpcTimeoutMillis());
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testLoadInvalid()
    {
        $inputConfig = RetrySettingsTest::buildInvalidInputConfig();
        RetrySettings::load(
            RetrySettingsTest::SERVICE_NAME,
            $inputConfig
        );
    }

    public function testDisableRetries()
    {
        $inputConfig = RetrySettingsTest::buildInputConfig();

        $defaultRetrySettings = RetrySettings::load(
            RetrySettingsTest::SERVICE_NAME,
            $inputConfig,
            true
        );
        $simpleMethod = $defaultRetrySettings['SimpleMethod'];
        $this->assertFalse($simpleMethod->retriesEnabled());
        $this->assertEquals(40000, $simpleMethod->getNoRetriesRpcTimeoutMillis());
        $pageStreamingMethod = $defaultRetrySettings['PageStreamingMethod'];
        $this->assertEquals(['INTERNAL'], $pageStreamingMethod->getRetryableCodes());
        $timeoutOnlyMethod = $defaultRetrySettings['TimeoutOnlyMethod'];
        $this->assertFalse($timeoutOnlyMethod->retriesEnabled());
        $this->assertEquals(40000, $timeoutOnlyMethod->getNoRetriesRpcTimeoutMillis());
    }

    /**
     * @expectedException \Google\ApiCore\ValidationException
     */
    public function testRetrySettingsMissingFields()
    {
        $retrySettings = new RetrySettings([
            'initialRetryDelayMillis' => 100,
            'retryDelayMultiplier' => 1.3,
            // Missing field:
            //'maxRetryDelayMillis' => 400,
            'initialRpcTimeoutMillis' => 150,
            'rpcTimeoutMultiplier' => 2,
            'maxRpcTimeoutMillis' => 600,
            'totalTimeoutMillis' => 2000
        ]);
    }

    /**
     * @dataProvider retrySettingsProvider
     * @param $settings
     * @param $expectedValues
     */
    public function testRetrySettings($settings, $expectedValues)
    {
        $retrySettings = new RetrySettings($settings);
        $this->compare($retrySettings, $expectedValues);
    }

    /**
     * @dataProvider withRetrySettingsProvider
     * @param $settings
     * @param $withSettings
     * @param $expectedValues
     */
    public function testWith($settings, $withSettings, $expectedValues)
    {
        $retrySettings = new RetrySettings($settings);
        $withRetrySettings = $retrySettings->with($withSettings);
        $this->compare($withRetrySettings, $expectedValues);
    }

    private function compare(RetrySettings $retrySettings, $expectedValues)
    {
        $this->assertSame(
            $expectedValues['initialRetryDelayMillis'],
            $retrySettings->getInitialRetryDelayMillis()
        );
        $this->assertSame(
            $expectedValues['retryDelayMultiplier'],
            $retrySettings->getRetryDelayMultiplier()
        );
        $this->assertSame(
            $expectedValues['maxRetryDelayMillis'],
            $retrySettings->getMaxRetryDelayMillis()
        );
        $this->assertSame(
            $expectedValues['rpcTimeoutMultiplier'],
            $retrySettings->getRpcTimeoutMultiplier()
        );
        $this->assertSame(
            $expectedValues['maxRpcTimeoutMillis'],
            $retrySettings->getMaxRpcTimeoutMillis()
        );
        $this->assertSame(
            $expectedValues['totalTimeoutMillis'],
            $retrySettings->getTotalTimeoutMillis()
        );
        $this->assertSame(
            $expectedValues['retryableCodes'],
            $retrySettings->getRetryableCodes()
        );
        $this->assertSame(
            $expectedValues['retriesEnabled'],
            $retrySettings->retriesEnabled()
        );
        $this->assertSame(
            $expectedValues['noRetriesRpcTimeoutMillis'],
            $retrySettings->getNoRetriesRpcTimeoutMillis()
        );
    }

    public function retrySettingsProvider()
    {
        $defaultSettings = [
            'initialRetryDelayMillis' => 100,
            'retryDelayMultiplier' => 1.3,
            'maxRetryDelayMillis' => 400,
            'initialRpcTimeoutMillis' => 150,
            'rpcTimeoutMultiplier' => 2,
            'maxRpcTimeoutMillis' => 600,
            'totalTimeoutMillis' => 2000,
            'retryableCodes' => [1],
        ];
        $defaultExpectedValues = [
            'initialRetryDelayMillis' => 100,
            'retryDelayMultiplier' => 1.3,
            'maxRetryDelayMillis' => 400,
            'initialRpcTimeoutMillis' => 150,
            'rpcTimeoutMultiplier' => 2,
            'maxRpcTimeoutMillis' => 600,
            'totalTimeoutMillis' => 2000,
            'retryableCodes' => [1],
            'noRetriesRpcTimeoutMillis' => 150,
            'retriesEnabled' => true
        ];
        return [
            [
                // Test with retryableCodes, without retriesEnabled or noRetriesRpcTimeoutMillis
                $defaultSettings,
                $defaultExpectedValues
            ],
            [
                // Test with empty retryableCodes, without retriesEnabled or noRetriesRpcTimeoutMillis
                [
                    'retryableCodes' => [],
                ] + $defaultSettings,
                [
                    'retryableCodes' => [],
                    'retriesEnabled' => false
                ] + $defaultExpectedValues
            ],
            [
                // Test with retryableCodes, with retriesEnabled=false
                [
                    'retriesEnabled' => false
                ] + $defaultSettings,
                [
                    'retriesEnabled' => false
                ] + $defaultExpectedValues
            ],
            [
                // Test with empty retryableCodes, with retriesEnabled=true
                [
                    'retryableCodes' => [],
                    'retriesEnabled' => true
                ] + $defaultSettings,
                [
                    'retryableCodes' => [],
                    'retriesEnabled' => true
                ] + $defaultExpectedValues
            ],
            [
                // Test with noRetriesRpcTimeoutMillis
                [
                    'noRetriesRpcTimeoutMillis' => 151,
                ] + $defaultSettings,
                [
                    'noRetriesRpcTimeoutMillis' => 151,
                ] + $defaultExpectedValues
            ]
        ];
    }

    public function withRetrySettingsProvider()
    {
        $defaultSettings = [
            'initialRetryDelayMillis' => 1,
            'retryDelayMultiplier' => 1,
            'maxRetryDelayMillis' => 1,
            'initialRpcTimeoutMillis' => 1,
            'rpcTimeoutMultiplier' => 1,
            'maxRpcTimeoutMillis' => 1,
            'totalTimeoutMillis' => 1,
            'retryableCodes' => [1],
            'noRetriesRpcTimeoutMillis' => 1,
            'retriesEnabled' => true,
        ];
        $defaultExpectedValues = [
            'initialRetryDelayMillis' => 1,
            'retryDelayMultiplier' => 1,
            'maxRetryDelayMillis' => 1,
            'initialRpcTimeoutMillis' => 1,
            'rpcTimeoutMultiplier' => 1,
            'maxRpcTimeoutMillis' => 1,
            'totalTimeoutMillis' => 1,
            'retryableCodes' => [1],
            'noRetriesRpcTimeoutMillis' => 1,
            'retriesEnabled' => true,
        ];
        return [
            [
                // Test with no changes
                $defaultSettings,
                [],
                $defaultExpectedValues
            ],
            [
                // Test disable retries
                $defaultSettings,
                [
                    'retriesEnabled' => false,
                ],
                [
                    'retriesEnabled' => false,
                ] + $defaultExpectedValues
            ],
            [
                // Test change all settings
                $defaultSettings,
                [
                    'initialRetryDelayMillis' => 2,
                    'retryDelayMultiplier' => 3,
                    'maxRetryDelayMillis' => 4,
                    'initialRpcTimeoutMillis' => 5,
                    'rpcTimeoutMultiplier' => 6,
                    'maxRpcTimeoutMillis' => 7,
                    'totalTimeoutMillis' => 8,
                    'retryableCodes' => [9],
                    'noRetriesRpcTimeoutMillis' => 10,
                    'retriesEnabled' => false,
                ],
                [
                    'initialRetryDelayMillis' => 2,
                    'retryDelayMultiplier' => 3,
                    'maxRetryDelayMillis' => 4,
                    'initialRpcTimeoutMillis' => 5,
                    'rpcTimeoutMultiplier' => 6,
                    'maxRpcTimeoutMillis' => 7,
                    'totalTimeoutMillis' => 8,
                    'retryableCodes' => [9],
                    'noRetriesRpcTimeoutMillis' => 10,
                    'retriesEnabled' => false,
                ]
            ]
        ];
    }
}
