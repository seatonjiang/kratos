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

namespace Google\ApiCore\Tests\Unit;

use Google\ApiCore\CredentialsWrapper;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Cache\MemoryCacheItemPool;
use Google\Auth\Cache\SysVCacheItemPool;
use Google\Auth\CredentialsLoader;
use Google\Auth\FetchAuthTokenCache;
use Google\Auth\FetchAuthTokenInterface;
use Google\Auth\UpdateMetadataInterface;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use GPBMetadata\Google\Api\Auth;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CredentialsWrapperTest extends TestCase
{
    private static $appDefaultCreds;

    public static function setUpBeforeClass()
    {
        self::$appDefaultCreds = getenv('GOOGLE_APPLICATION_CREDENTIALS');
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/testdata/json-key-file.json');
    }

    public static function tearDownAfterClass()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . self::$appDefaultCreds);
    }

    /**
     * @dataProvider buildDataWithoutExplicitKeyFile
     */
    public function testBuildWithoutExplicitKeyFile($args, $expectedCredentialsWrapper)
    {
        $actualCredentialsWrapper = CredentialsWrapper::build($args);
        $this->assertEquals($expectedCredentialsWrapper, $actualCredentialsWrapper);
    }

    /**
     * @dataProvider buildDataWithKeyFile
     */
    public function testBuildWithKeyFile($args, $expectedCredentialsWrapper)
    {
        $actualCredentialsWrapper = CredentialsWrapper::build($args);
        $this->assertEquals($expectedCredentialsWrapper, $actualCredentialsWrapper);
    }

    public function buildDataWithoutExplicitKeyFile()
    {
        $scopes = ['myscope'];
        $defaultAuthHttpHandler = HttpHandlerFactory::build();
        $authHttpHandler = HttpHandlerFactory::build();
        $asyncAuthHttpHandler = function ($request, $options) use ($authHttpHandler) {
            return $authHttpHandler->async($request, $options)->wait();
        };
        $defaultAuthCache = new MemoryCacheItemPool();
        $authCache = new SysVCacheItemPool();
        $authCacheOptions = ['lifetime' => 600];
        $quotaProject = 'my-quota-project';

        $testData = [
            [
                [],
                new CredentialsWrapper(ApplicationDefaultCredentials::getCredentials(null, $defaultAuthHttpHandler, null, $defaultAuthCache), $defaultAuthHttpHandler),
            ],
            [
                ['scopes' => $scopes],
                new CredentialsWrapper(ApplicationDefaultCredentials::getCredentials($scopes, $defaultAuthHttpHandler, null, $defaultAuthCache), $defaultAuthHttpHandler),
            ],
            [
                ['scopes' => $scopes, 'authHttpHandler' => $asyncAuthHttpHandler],
                new CredentialsWrapper(ApplicationDefaultCredentials::getCredentials($scopes, $asyncAuthHttpHandler, null, $defaultAuthCache), $asyncAuthHttpHandler),
            ],
            [
                ['enableCaching' => false],
                new CredentialsWrapper(ApplicationDefaultCredentials::getCredentials(null, $defaultAuthHttpHandler, null, null), $defaultAuthHttpHandler),
            ],
            [
                ['authCacheOptions' => $authCacheOptions],
                new CredentialsWrapper(ApplicationDefaultCredentials::getCredentials(null, $defaultAuthHttpHandler, $authCacheOptions, $defaultAuthCache), $defaultAuthHttpHandler),
            ],
            [
                ['authCache' => $authCache],
                new CredentialsWrapper(ApplicationDefaultCredentials::getCredentials(null, $defaultAuthHttpHandler, null, $authCache), $defaultAuthHttpHandler),
            ],
            [
                ['quotaProject' => $quotaProject],
                new CredentialsWrapper(ApplicationDefaultCredentials::getCredentials(null, $defaultAuthHttpHandler, null, $defaultAuthCache, $quotaProject), $defaultAuthHttpHandler),
            ],
        ];

        return $testData;
    }

    public function buildDataWithKeyFile()
    {
        $keyFilePath = __DIR__ . '/testdata/json-key-file.json';
        $keyFile = json_decode(file_get_contents($keyFilePath), true);

        $scopes = ['myscope'];
        $authHttpHandler = function () {};
        $defaultAuthCache = new MemoryCacheItemPool();
        $authCache = new SysVCacheItemPool();
        $authCacheOptions = ['lifetime' => 600];
        $quotaProject = 'my-quota-project';
        return [
            [
                ['keyFile' => $keyFile],
                $this->makeExpectedKeyFileCreds($keyFile, null, $defaultAuthCache, null, null),
            ],
            [
                ['keyFile' => $keyFilePath],
                $this->makeExpectedKeyFileCreds($keyFile, null, $defaultAuthCache, null, null),
            ],
            [
                ['keyFile' => $keyFile, 'scopes' => $scopes],
                $this->makeExpectedKeyFileCreds($keyFile, $scopes, $defaultAuthCache, null, null),
            ],
            [
                ['keyFile' => $keyFile, 'scopes' => $scopes, 'authHttpHandler' => $authHttpHandler],
                $this->makeExpectedKeyFileCreds($keyFile, $scopes, $defaultAuthCache, null, $authHttpHandler),
            ],
            [
                ['keyFile' => $keyFile, 'enableCaching' => false],
                $this->makeExpectedKeyFileCreds($keyFile, null, null, null, null),
            ],
            [
                ['keyFile' => $keyFile, 'authCacheOptions' => $authCacheOptions],
                $this->makeExpectedKeyFileCreds($keyFile, null, $defaultAuthCache, $authCacheOptions, null),
            ],
            [
                ['keyFile' => $keyFile, 'authCache' => $authCache],
                $this->makeExpectedKeyFileCreds($keyFile, null, $authCache, null, null),
            ],
            [
                ['keyFile' => $keyFile, 'quotaProject' => $quotaProject],
                $this->makeExpectedKeyFileCreds(
                    $keyFile + ['quota_project_id' => $quotaProject],
                    null,
                    $defaultAuthCache,
                    null,
                    null
                ),
            ],
        ];
    }

    private function makeExpectedKeyFileCreds($keyFile, $scopes, $cache, $cacheConfig, $httpHandler)
    {
        $loader = CredentialsLoader::makeCredentials($scopes, $keyFile);
        if ($cache) {
            $loader = new FetchAuthTokenCache($loader, $cacheConfig, $cache);
        }
        return new CredentialsWrapper($loader, $httpHandler);
    }

    /**
     * @dataProvider getBearerStringData
     */
    public function testGetBearerString($fetcher, $expectedBearerString)
    {
        $credentialsWrapper = new CredentialsWrapper($fetcher);
        $bearerString = $credentialsWrapper->getBearerString();
        $this->assertSame($expectedBearerString, $bearerString);
    }

    public function getBearerStringData()
    {
        $expiredFetcher = $this->prophesize(FetchAuthTokenInterface::class);
        $expiredFetcher->getLastReceivedToken()
            ->willReturn([
                'access_token' => 123,
                'expires_at' => time() - 1
            ]);
        $expiredFetcher->fetchAuthToken(Argument::any())
            ->willReturn([
                'access_token' => 456,
                'expires_at' => time() + 1000
            ]);
        $unexpiredFetcher = $this->prophesize(FetchAuthTokenInterface::class);
        $unexpiredFetcher->getLastReceivedToken()
            ->willReturn([
                'access_token' => 123,
                'expires_at' => time() + 100,
            ]);
        $insecureFetcher = $this->prophesize(FetchAuthTokenInterface::class);
        $insecureFetcher->getLastReceivedToken()->willReturn(null);
        $insecureFetcher->fetchAuthToken(Argument::any())
            ->willReturn([
                'access_token' => '',
            ]);
        $nullFetcher = $this->prophesize(FetchAuthTokenInterface::class);
        $nullFetcher->getLastReceivedToken()->willReturn(null);
        $nullFetcher->fetchAuthToken(Argument::any())
            ->willReturn([
                'access_token' => null,
            ]);
        return [
            [$expiredFetcher->reveal(), 'Bearer 456'],
            [$unexpiredFetcher->reveal(), 'Bearer 123'],
            [$insecureFetcher->reveal(), ''],
            [$nullFetcher->reveal(), '']
        ];
    }

    /**
     * @dataProvider getAuthorizationHeaderCallbackData
     */
    public function testGetAuthorizationHeaderCallback($fetcher, $expectedCallbackResponse)
    {
        $credentialsWrapper = new CredentialsWrapper($fetcher);
        $callback = $credentialsWrapper->getAuthorizationHeaderCallback('audience');
        $actualResponse = $callback();
        $this->assertSame($expectedCallbackResponse, $actualResponse);
    }

    public function getAuthorizationHeaderCallbackData()
    {
        $expiredFetcher = $this->prophesize();
        $expiredFetcher->willImplement(FetchAuthTokenInterface::class);
        $expiredFetcher->willImplement(UpdateMetadataInterface::class);
        $expiredFetcher->getLastReceivedToken()
            ->willReturn([
                'access_token' => 123,
                'expires_at' => time() - 1
            ]);
        $expiredFetcher->updateMetadata(Argument::any(), 'audience')
            ->willReturn(['authorization' => ['Bearer 456']]);
        $expiredInvalidFetcher = $this->prophesize(FetchAuthTokenInterface::class);
        $expiredInvalidFetcher->getLastReceivedToken()
            ->willReturn([
                'access_token' => 123,
                'expires_at' => time() - 1
            ]);
        $expiredInvalidFetcher->fetchAuthToken(Argument::any())
            ->willReturn(['not-a' => 'valid-token']);
        $unexpiredFetcher = $this->prophesize();
        $unexpiredFetcher->willImplement(FetchAuthTokenInterface::class);
        $unexpiredFetcher->getLastReceivedToken()
            ->willReturn([
                'access_token' => 123,
                'expires_at' => time() + 100,
            ]);

        $insecureFetcher = $this->prophesize(FetchAuthTokenInterface::class);
        $insecureFetcher->getLastReceivedToken()->willReturn(null);
        $insecureFetcher->fetchAuthToken(Argument::any())
            ->willReturn([
                'access_token' => '',
            ]);
        $nullFetcher = $this->prophesize(FetchAuthTokenInterface::class);
        $nullFetcher->getLastReceivedToken()->willReturn(null);
        $nullFetcher->fetchAuthToken(Argument::any())
            ->willReturn([
                'access_token' => null,
            ]);

        $customFetcher = $this->prophesize();
        $customFetcher->willImplement(FetchAuthTokenInterface::class);
        $customFetcher->getLastReceivedToken()->willReturn(null);
        $customFetcher->fetchAuthToken(Argument::any())
            ->willReturn([
                'access_token' => 123,
                'expires_at' => time() + 100,
            ]);

        return [
            [$expiredFetcher->reveal(), ['authorization' => ['Bearer 456']]],
            [$expiredInvalidFetcher->reveal(), []],
            [$unexpiredFetcher->reveal(), ['authorization' => ['Bearer 123']]],
            [$insecureFetcher->reveal(), []],
            [$nullFetcher->reveal(), []],
            [$customFetcher->reveal(), ['authorization' => ['Bearer 123']]],
        ];
    }
}
