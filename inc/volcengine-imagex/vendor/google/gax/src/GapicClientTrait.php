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

namespace Google\ApiCore;

use Google\ApiCore\LongRunning\OperationsClient;
use Google\ApiCore\Middleware\CredentialsWrapperMiddleware;
use Google\ApiCore\Middleware\FixedHeaderMiddleware;
use Google\ApiCore\Middleware\OperationsMiddleware;
use Google\ApiCore\Middleware\OptionsFilterMiddleware;
use Google\ApiCore\Middleware\PagedMiddleware;
use Google\ApiCore\Middleware\RetryMiddleware;
use Google\ApiCore\Transport\GrpcFallbackTransport;
use Google\ApiCore\Transport\GrpcTransport;
use Google\ApiCore\Transport\RestTransport;
use Google\ApiCore\Transport\TransportInterface;
use Google\Auth\FetchAuthTokenInterface;
use Google\LongRunning\Operation;
use Google\Protobuf\Internal\Message;
use Grpc\Gcp\ApiConfig;
use Grpc\Gcp\Config;
use GuzzleHttp\Promise\PromiseInterface;
use LogicException;

/**
 * Common functions used to work with various clients.
 */
trait GapicClientTrait
{
    use ArrayTrait;
    use ValidationTrait {
        ValidationTrait::validate as traitValidate;
    }
    use GrpcSupportTrait;

    /** @var TransportInterface */
    private $transport;
    private $credentialsWrapper;

    private static $gapicVersionFromFile;
    /** @var RetrySettings[] $retrySettings */
    private $retrySettings;
    private $serviceName;
    private $agentHeader;
    private $descriptors;
    private $transportCallMethods = [
        Call::UNARY_CALL => 'startUnaryCall',
        Call::BIDI_STREAMING_CALL => 'startBidiStreamingCall',
        Call::CLIENT_STREAMING_CALL => 'startClientStreamingCall',
        Call::SERVER_STREAMING_CALL => 'startServerStreamingCall',
    ];

    /**
     * Initiates an orderly shutdown in which preexisting calls continue but new
     * calls are immediately cancelled.
     *
     * @experimental
     */
    public function close()
    {
        $this->transport->close();
    }

    /**
     * Get the transport for the client. This method is protected to support
     * use by customized clients.
     *
     * @access private
     * @return TransportInterface
     */
    protected function getTransport()
    {
        return $this->transport;
    }

    /**
     * Get the credentials for the client. This method is protected to support
     * use by customized clients.
     *
     * @access private
     * @return CredentialsWrapper
     */
    protected function getCredentialsWrapper()
    {
        return $this->credentialsWrapper;
    }

    private static function getGapicVersion(array $options)
    {
        if (isset($options['libVersion'])) {
            return $options['libVersion'];
        } else {
            if (!isset(self::$gapicVersionFromFile)) {
                self::$gapicVersionFromFile = AgentHeader::readGapicVersionFromFile(__CLASS__);
            }
            return self::$gapicVersionFromFile;
        }
    }

    private static function initGrpcGcpConfig($hostName, $confPath)
    {
        $apiConfig = new ApiConfig();
        $apiConfig->mergeFromJsonString(file_get_contents($confPath));
        $config = new Config($hostName, $apiConfig);
        return $config;
    }

    /**
     * Get default options. This function should be "overridden" by clients using late static
     * binding to provide default options to the client.
     *
     * @return array
     * @access private
     */
    private static function getClientDefaults()
    {
        return [];
    }

    private function buildClientOptions(array $options)
    {
        // Build $defaultOptions starting from top level
        // variables, then going into deeper nesting, so that
        // we will not encounter missing keys
        $defaultOptions = self::getClientDefaults();
        $defaultOptions += [
            'disableRetries' => false,
            'credentials' => null,
            'credentialsConfig' => [],
            'transport' => null,
            'transportConfig' => [],
            'gapicVersion' => self::getGapicVersion($options),
            'libName' => null,
            'libVersion' => null,
            'apiEndpoint' => null,
        ];
        $defaultOptions['transportConfig'] += [
            'grpc' => ['stubOpts' => ['grpc.service_config_disable_resolution' => 1]],
            'rest' => [],
            'grpc-fallback' => [],
        ];

        // Merge defaults into $options starting from top level
        // variables, then going into deeper nesting, so that
        // we will not encounter missing keys
        $options += $defaultOptions;
        $options['credentialsConfig'] += $defaultOptions['credentialsConfig'];
        $options['transportConfig'] += $defaultOptions['transportConfig'];
        $options['transportConfig']['grpc'] += $defaultOptions['transportConfig']['grpc'];
        $options['transportConfig']['grpc']['stubOpts'] += $defaultOptions['transportConfig']['grpc']['stubOpts'];
        $options['transportConfig']['rest'] += $defaultOptions['transportConfig']['rest'];
        $options['transportConfig']['grpc-fallback'] += $defaultOptions['transportConfig']['grpc-fallback'];

        $this->modifyClientOptions($options);

        // serviceAddress is now deprecated and acts as an alias for apiEndpoint
        if (isset($options['serviceAddress'])) {
            $options['apiEndpoint'] = $this->pluck('serviceAddress', $options, false);
        }

        // If an API endpoint is set, ensure the "audience" does not conflict
        // with the custom endpoint by setting "user defined" scopes.
        if ($options['apiEndpoint'] != $defaultOptions['apiEndpoint']
            && empty($options['credentialsConfig']['scopes'])
            && !empty($options['credentialsConfig']['defaultScopes'])
        ) {
            $options['credentialsConfig']['scopes'] = $options['credentialsConfig']['defaultScopes'];
        }

        if (extension_loaded('sysvshm')
                && isset($options['gcpApiConfigPath'])
                && file_exists($options['gcpApiConfigPath'])
                && isset($options['apiEndpoint'])) {
            $grpcGcpConfig = self::initGrpcGcpConfig(
                $options['apiEndpoint'],
                $options['gcpApiConfigPath']
            );

            if (array_key_exists('stubOpts', $options['transportConfig']['grpc'])) {
                $options['transportConfig']['grpc']['stubOpts'] += [
                    'grpc_call_invoker' => $grpcGcpConfig->callInvoker()
                ];
            } else {
                $options['transportConfig']['grpc'] += [
                    'stubOpts' => [
                        'grpc_call_invoker' => $grpcGcpConfig->callInvoker()
                    ]
                ];
            }
        }

        return $options;
    }

    /**
     * Configures the GAPIC client based on an array of options.
     *
     * @param array $options {
     *     An array of required and optional arguments.
     *
     *     @type string $apiEndpoint
     *           The address of the API remote host, for example "example.googleapis.com. May also
     *           include the port, for example "example.googleapis.com:443"
     *     @type string $serviceAddress
     *           **Deprecated**. This option will be removed in the next major release. Please
     *           utilize the `$apiEndpoint` option instead.
     *     @type bool $disableRetries
     *           Determines whether or not retries defined by the client configuration should be
     *           disabled. Defaults to `false`.
     *     @type string|array $clientConfig
     *           Client method configuration, including retry settings. This option can be either a
     *           path to a JSON file, or a PHP array containing the decoded JSON data.
     *           By default this settings points to the default client config file, which is provided
     *           in the resources folder.
     *     @type string|array|FetchAuthTokenInterface|CredentialsWrapper $credentials
     *           The credentials to be used by the client to authorize API calls. This option
     *           accepts either a path to a credentials file, or a decoded credentials file as a
     *           PHP array.
     *           *Advanced usage*: In addition, this option can also accept a pre-constructed
     *           \Google\Auth\FetchAuthTokenInterface object or \Google\ApiCore\CredentialsWrapper
     *           object. Note that when one of these objects are provided, any settings in
     *           $authConfig will be ignored.
     *     @type array $credentialsConfig
     *           Options used to configure credentials, including auth token caching, for the client.
     *           For a full list of supporting configuration options, see
     *           \Google\ApiCore\CredentialsWrapper::build.
     *     @type string|TransportInterface $transport
     *           The transport used for executing network requests. May be either the string `rest`,
     *           `grpc`, or 'grpc-fallback'. Defaults to `grpc` if gRPC support is detected on the system.
     *           *Advanced usage*: Additionally, it is possible to pass in an already instantiated
     *           TransportInterface object. Note that when this objects is provided, any settings in
     *           $transportConfig, and any `$apiEndpoint` setting, will be ignored.
     *     @type array $transportConfig
     *           Configuration options that will be used to construct the transport. Options for
     *           each supported transport type should be passed in a key for that transport. For
     *           example:
     *           $transportConfig = [
     *               'grpc' => [...],
     *               'rest' => [...],
     *               'grpc-fallback' => [...],
     *           ];
     *           See the GrpcTransport::build and RestTransport::build
     *           methods for the supported options.
     *     @type string $versionFile
     *           The path to a file which contains the current version of the client.
     *     @type string $descriptorsConfigPath
     *           The path to a descriptor configuration file.
     *     @type string $serviceName
     *           The name of the service.
     *     @type string $libName
     *           The name of the client application.
     *     @type string $libVersion
     *           The version of the client application.
     *     @type string $gapicVersion
     *           The code generator version of the GAPIC library.
     * }
     * @throws ValidationException
     */
    private function setClientOptions(array $options)
    {
        // serviceAddress is now deprecated and acts as an alias for apiEndpoint
        if (isset($options['serviceAddress'])) {
            $options['apiEndpoint'] = $this->pluck('serviceAddress', $options, false);
        }
        $this->validateNotNull($options, [
            'apiEndpoint',
            'serviceName',
            'descriptorsConfigPath',
            'clientConfig',
            'disableRetries',
            'credentialsConfig',
            'transportConfig',
        ]);
        $this->traitValidate($options, [
            'credentials',
            'transport',
            'gapicVersion',
            'libName',
            'libVersion',
        ]);

        $clientConfig = $options['clientConfig'];
        if (is_string($clientConfig)) {
            $clientConfig = json_decode(file_get_contents($clientConfig), true);
        }
        $this->serviceName = $options['serviceName'];
        $this->retrySettings = RetrySettings::load(
            $this->serviceName,
            $clientConfig,
            $options['disableRetries']
        );
        $this->agentHeader = AgentHeader::buildAgentHeader(
            $this->pluckArray([
                'libName',
                'libVersion',
                'gapicVersion'
            ], $options)
        );
        self::validateFileExists($options['descriptorsConfigPath']);
        $descriptors = require($options['descriptorsConfigPath']);
        $this->descriptors = $descriptors['interfaces'][$this->serviceName];

        $this->credentialsWrapper = $this->createCredentialsWrapper(
            $options['credentials'],
            $options['credentialsConfig']
        );

        $transport = $options['transport'] ?: self::defaultTransport();
        $this->transport = $transport instanceof TransportInterface
            ? $transport
            : $this->createTransport($options['apiEndpoint'], $transport, $options['transportConfig']);
    }

    /**
     * @param mixed $credentials
     * @param array $credentialsConfig
     * @return CredentialsWrapper
     * @throws ValidationException
     */
    private function createCredentialsWrapper($credentials, array $credentialsConfig)
    {
        if (is_null($credentials)) {
            return CredentialsWrapper::build($credentialsConfig);
        } elseif (is_string($credentials) || is_array($credentials)) {
            return CredentialsWrapper::build(['keyFile' => $credentials] + $credentialsConfig);
        } elseif ($credentials instanceof FetchAuthTokenInterface) {
            $authHttpHandler = isset($credentialsConfig['authHttpHandler'])
                ? $credentialsConfig['authHttpHandler']
                : null;
            return new CredentialsWrapper($credentials, $authHttpHandler);
        } elseif ($credentials instanceof CredentialsWrapper) {
            return $credentials;
        } else {
            throw new ValidationException(
                'Unexpected value in $auth option, got: ' .
                print_r($credentials, true)
            );
        }
    }

    /**
     * @param string $apiEndpoint
     * @param string $transport
     * @param array $transportConfig
     * @return TransportInterface
     * @throws ValidationException
     */
    private function createTransport($apiEndpoint, $transport, array $transportConfig)
    {
        if (!is_string($transport)) {
            throw new ValidationException(
                "'transport' must be a string, instead got:" .
                print_r($transport, true)
            );
        }
        $supportedTransports = self::supportedTransports();
        if (!in_array($transport, $supportedTransports)) {
            throw new ValidationException(sprintf(
                'Unexpected transport option "%s". Supported transports: %s',
                $transport,
                implode(', ', $supportedTransports)
            ));
        }
        $configForSpecifiedTransport = isset($transportConfig[$transport])
            ? $transportConfig[$transport]
            : [];
        switch ($transport) {
            case 'grpc':
                return GrpcTransport::build($apiEndpoint, $configForSpecifiedTransport);
            case 'grpc-fallback':
                return GrpcFallbackTransport::build($apiEndpoint, $configForSpecifiedTransport);
            case 'rest':
                if (!isset($configForSpecifiedTransport['restClientConfigPath'])) {
                    throw new ValidationException(
                        "The 'restClientConfigPath' config is required for 'rest' transport."
                    );
                }
                $restConfigPath = $configForSpecifiedTransport['restClientConfigPath'];
                return RestTransport::build($apiEndpoint, $restConfigPath, $configForSpecifiedTransport);
            default:
                throw new ValidationException(
                    "Unexpected 'transport' option: $transport. " .
                    "Supported values: ['grpc', 'rest', 'grpc-fallback']"
                );
        }
    }

    /**
     * @param array $options
     * @return OperationsClient
     */
    private function createOperationsClient(array $options)
    {
        $this->pluckArray([
            'serviceName',
            'clientConfig',
            'descriptorsConfigPath',
        ], $options);

        return $this->pluck('operationsClient', $options, false)
            ?: new OperationsClient($options);
    }

    /**
     * @return string
     */
    private static function defaultTransport()
    {
        return self::getGrpcDependencyStatus()
            ? 'grpc'
            : 'rest';
    }

    /**
     * @param string $methodName
     * @param string $decodeType
     * @param array $optionalArgs {
     *     Call Options
     *
     *     @type array $headers [optional] key-value array containing headers
     *     @type int $timeoutMillis [optional] the timeout in milliseconds for the call
     *     @type array $transportOptions [optional] transport-specific call options
     *     @type RetrySettings|array $retrySettings [optional] A retry settings
     *           override for the call.
     * }
     * @param Message $request
     * @param int $callType
     * @param string $interfaceName
     *
     * @return PromiseInterface|BidiStream|ClientStream|ServerStream
     */
    private function startCall(
        $methodName,
        $decodeType,
        array $optionalArgs = [],
        Message $request = null,
        $callType = Call::UNARY_CALL,
        $interfaceName = null
    ) {
        $callStack = $this->createCallStack(
            $this->configureCallConstructionOptions($methodName, $optionalArgs)
        );

        $descriptor = isset($this->descriptors[$methodName]['grpcStreaming'])
            ? $this->descriptors[$methodName]['grpcStreaming']
            : null;

        $call = new Call(
            $this->buildMethod($interfaceName, $methodName),
            $decodeType,
            $request,
            $descriptor,
            $callType
        );
        switch ($callType) {
            case Call::UNARY_CALL:
                $this->modifyUnaryCallable($callStack);
                break;
            case Call::BIDI_STREAMING_CALL:
            case Call::CLIENT_STREAMING_CALL:
            case Call::SERVER_STREAMING_CALL:
                $this->modifyStreamingCallable($callStack);
                break;
        }

        return $callStack($call, $optionalArgs + array_filter([
            'audience' => self::getDefaultAudience()
        ]));
    }

    /**
     * @param array $callConstructionOptions {
     *     Call Construction Options
     *
     *     @type RetrySettings $retrySettings [optional] A retry settings override
     *           For the call.
     * }
     *
     * @return callable
     */
    private function createCallStack(array $callConstructionOptions)
    {
        $quotaProject = $this->credentialsWrapper->getQuotaProject();
        $fixedHeaders = $this->agentHeader;
        if ($quotaProject) {
            $fixedHeaders += [
                'X-Goog-User-Project' => [$quotaProject]
            ];
        }
        $callStack = function (Call $call, array $options) {
            $startCallMethod = $this->transportCallMethods[$call->getCallType()];
            return $this->transport->$startCallMethod($call, $options);
        };
        $callStack = new CredentialsWrapperMiddleware($callStack, $this->credentialsWrapper);
        $callStack = new FixedHeaderMiddleware($callStack, $fixedHeaders, true);
        $callStack = new RetryMiddleware($callStack, $callConstructionOptions['retrySettings']);
        $callStack = new OptionsFilterMiddleware($callStack, [
            'headers',
            'timeoutMillis',
            'transportOptions',
            'metadataCallback',
            'audience',
        ]);

        return $callStack;
    }

    /**
     * @param string $methodName
     * @param array $optionalArgs {
     *     Optional arguments
     *
     *     @type RetrySettings|array $retrySettings [optional] A retry settings
     *           override for the call.
     * }
     *
     * @return array
     */
    private function configureCallConstructionOptions($methodName, array $optionalArgs)
    {
        $retrySettings = $this->retrySettings[$methodName];
        // Allow for retry settings to be changed at call time
        if (isset($optionalArgs['retrySettings'])) {
            if ($optionalArgs['retrySettings'] instanceof RetrySettings) {
                $retrySettings = $optionalArgs['retrySettings'];
            } else {
                $retrySettings = $retrySettings->with(
                    $optionalArgs['retrySettings']
                );
            }
        }
        return [
            'retrySettings' => $retrySettings,
        ];
    }

    /**
     * @param string $methodName
     * @param array $optionalArgs {
     *     Call Options
     *
     *     @type array $headers [optional] key-value array containing headers
     *     @type int $timeoutMillis [optional] the timeout in milliseconds for the call
     *     @type array $transportOptions [optional] transport-specific call options
     * }
     * @param Message $request
     * @param OperationsClient $client
     * @param string $interfaceName
     *
     * @return PromiseInterface
     */
    private function startOperationsCall(
        $methodName,
        array $optionalArgs,
        Message $request,
        OperationsClient $client,
        $interfaceName = null
    ) {
        $callStack = $this->createCallStack(
            $this->configureCallConstructionOptions($methodName, $optionalArgs)
        );
        $descriptor = $this->descriptors[$methodName]['longRunning'];
        $callStack = new OperationsMiddleware($callStack, $client, $descriptor);

        $call = new Call(
            $this->buildMethod($interfaceName, $methodName),
            Operation::class,
            $request,
            [],
            Call::UNARY_CALL
        );

        $this->modifyUnaryCallable($callStack);
        return $callStack($call, $optionalArgs + array_filter([
            'audience' => self::getDefaultAudience()
        ]));
    }

    /**
     * @param string $methodName
     * @param array $optionalArgs
     * @param string $decodeType
     * @param Message $request
     * @param string $interfaceName
     *
     * @return PagedListResponse
     */
    private function getPagedListResponse(
        $methodName,
        array $optionalArgs,
        $decodeType,
        Message $request,
        $interfaceName = null
    ) {
        $callStack = $this->createCallStack(
            $this->configureCallConstructionOptions($methodName, $optionalArgs)
        );
        $descriptor = new PageStreamingDescriptor(
            $this->descriptors[$methodName]['pageStreaming']
        );
        $callStack = new PagedMiddleware($callStack, $descriptor);

        $call = new Call(
            $this->buildMethod($interfaceName, $methodName),
            $decodeType,
            $request,
            [],
            Call::UNARY_CALL
        );

        $this->modifyUnaryCallable($callStack);
        return $callStack($call, $optionalArgs + array_filter([
            'audience' => self::getDefaultAudience()
        ]))->wait();
    }

    /**
     * @param string $interfaceName
     * @param string $methodName
     *
     * @return string
     */
    private function buildMethod($interfaceName, $methodName)
    {
        return sprintf(
            '%s/%s',
            $interfaceName ?: $this->serviceName,
            $methodName
        );
    }

    /**
     * The SERVICE_ADDRESS constant is set by GAPIC clients
     */
    private static function getDefaultAudience()
    {
        if (!defined('self::SERVICE_ADDRESS')) {
            return null;
        }
        return 'https://' . self::SERVICE_ADDRESS . '/';
    }

    /**
     * This defaults to all three transports, which One-Platform supports.
     * Discovery clients should define this function and only return ['rest'].
     */
    private static function supportedTransports()
    {
        return ['grpc', 'grpc-fallback', 'rest'];
    }

    // Gapic Client Extension Points
    // The methods below provide extension points that can be used to customize client
    // functionality. These extension points are currently considered
    // private and may change at any time.

    /**
     * Modify options passed to the client before calling setClientOptions.
     *
     * @param array $options
     * @access private
     */
    protected function modifyClientOptions(array &$options)
    {
        // Do nothing - this method exists to allow option modification by partial veneers.
    }

    /**
     * Modify the unary callable.
     *
     * @param callable $callable
     * @access private
     */
    protected function modifyUnaryCallable(callable &$callable)
    {
        // Do nothing - this method exists to allow callable modification by partial veneers.
    }

    /**
     * Modify the streaming callable.
     *
     * @param callable $callable
     * @access private
     */
    protected function modifyStreamingCallable(callable &$callable)
    {
        // Do nothing - this method exists to allow callable modification by partial veneers.
    }
}
