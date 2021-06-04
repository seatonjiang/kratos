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
namespace Google\ApiCore\Transport;

use Google\ApiCore\ApiException;
use Google\ApiCore\ApiStatus;
use Google\ApiCore\Call;
use Google\ApiCore\RequestBuilder;
use Google\ApiCore\ServiceAddressTrait;
use Google\ApiCore\ValidationException;
use Google\ApiCore\ValidationTrait;
use Google\Protobuf\Internal\Message;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * A REST based transport implementation.
 */
class RestTransport implements TransportInterface
{
    use ValidationTrait;
    use ServiceAddressTrait;
    use HttpUnaryTransportTrait;

    private $requestBuilder;

    /**
     * @param RequestBuilder $requestBuilder A builder responsible for creating
     *        a PSR-7 request from a set of request information.
     * @param callable $httpHandler A handler used to deliver PSR-7 requests.
     */
    public function __construct(
        RequestBuilder $requestBuilder,
        callable $httpHandler
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->httpHandler = $httpHandler;
        $this->transportName = 'REST';
    }

    /**
     * Builds a RestTransport.
     *
     * @param string $apiEndpoint
     *        The address of the API remote host, for example "example.googleapis.com".
     * @param string $restConfigPath
     *        Path to rest config file.
     * @param array $config {
     *    Config options used to construct the gRPC transport.
     *
     *    @type callable $httpHandler A handler used to deliver PSR-7 requests.
     * }
     * @return RestTransport
     * @throws ValidationException
     */
    public static function build($apiEndpoint, $restConfigPath, array $config = [])
    {
        $config += [
            'httpHandler'  => null,
        ];
        list($baseUri, $port) = self::normalizeServiceAddress($apiEndpoint);
        $requestBuilder = new RequestBuilder("$baseUri:$port", $restConfigPath);
        $httpHandler = $config['httpHandler'] ?: self::buildHttpHandlerAsync();
        return new RestTransport($requestBuilder, $httpHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function startUnaryCall(Call $call, array $options)
    {
        $headers = self::buildCommonHeaders($options);

        // call the HTTP handler
        $httpHandler = $this->httpHandler;
        return $httpHandler(
            $this->requestBuilder->build(
                $call->getMethod(),
                $call->getMessage(),
                $headers
            ),
            $this->getCallOptions($options)
        )->then(
            function (ResponseInterface $response) use ($call, $options) {
                $decodeType = $call->getDecodeType();
                /** @var Message $return */
                $return = new $decodeType;
                $return->mergeFromJsonString(
                    (string) $response->getBody(),
                    true
                );

                if (isset($options['metadataCallback'])) {
                    $metadataCallback = $options['metadataCallback'];
                    $metadataCallback($response->getHeaders());
                }

                return $return;
            },
            function (\Exception $ex) {
                if ($ex instanceof RequestException && $ex->hasResponse()) {
                    throw $this->convertToApiException($ex);
                }

                throw $ex;
            }
        );
    }

    private function getCallOptions(array $options)
    {
        $callOptions = isset($options['transportOptions']['restOptions'])
            ? $options['transportOptions']['restOptions']
            : [];

        if (isset($options['timeoutMillis'])) {
            $callOptions['timeout'] = $options['timeoutMillis'] / 1000;
        }

        return $callOptions;
    }

    /**
     * @param RequestException $ex
     * @return ApiException
     * @throws ValidationException
     */
    private function convertToApiException(RequestException $ex)
    {
        $res = $ex->getResponse();
        $body = (string) $res->getBody();
        if ($error = json_decode($body, true)['error']) {
            $basicMessage = $error['message'];
            $code = isset($error['status'])
                ? ApiStatus::rpcCodeFromStatus($error['status'])
                : $ex->getCode();
            $metadata = isset($error['details']) ? $error['details'] : null;
            return ApiException::createFromApiResponse($basicMessage, $code, $metadata);
        }
        // Use the RPC code instead of the HTTP Status Code.
        $code = ApiStatus::rpcCodeFromHttpStatusCode($res->getStatusCode());
        return ApiException::createFromApiResponse($body, $code);
    }
}
