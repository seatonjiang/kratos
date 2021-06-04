<?php
/*
 * Copyright 2016 Google LLC
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
use Google\LongRunning\Operation;
use Google\Protobuf\Any;
use Google\Protobuf\Internal\Message;
use Google\Rpc\Status;

/**
 * Response object from a long running API method.
 *
 * The OperationResponse object is returned by API methods that perform
 * a long running operation. It provides methods that can be used to
 * poll the status of the operation, retrieve the results, and cancel
 * the operation.
 *
 * To support a long running operation, the server must implement the
 * Operations API, which is used by the OperationResponse object. If
 * more control is required, it is possible to make calls against the
 * Operations API directly instead of via the OperationResponse object
 * using an OperationsClient instance.
 */
class OperationResponse
{
    use PollingTrait;

    const DEFAULT_POLLING_INTERVAL = 1000;
    const DEFAULT_POLLING_MULTIPLIER = 2;
    const DEFAULT_MAX_POLLING_INTERVAL = 60000;
    const DEFAULT_MAX_POLLING_DURATION = 0;

    private $operationName;
    private $operationsClient;

    private $operationReturnType;
    private $metadataReturnType;
    private $defaultPollSettings = [
        'initialPollDelayMillis' => self::DEFAULT_POLLING_INTERVAL,
        'pollDelayMultiplier' => self::DEFAULT_POLLING_MULTIPLIER,
        'maxPollDelayMillis' => self::DEFAULT_MAX_POLLING_INTERVAL,
        'totalPollTimeoutMillis' => self::DEFAULT_MAX_POLLING_DURATION,
    ];

    private $lastProtoResponse;
    private $deleted = false;

    /**
     * OperationResponse constructor.
     *
     * @param string $operationName
     * @param OperationsClient $operationsClient
     * @param array $options {
     *                       Optional. Options for configuring the Operation response object.
     *
     *     @type string $operationReturnType The return type of the longrunning operation.
     *     @type string $metadataReturnType The type of the metadata returned in the Operation response.
     *     @type int $initialPollDelayMillis    The initial polling interval to use, in milliseconds.
     *     @type int $pollDelayMultiplier Multiplier applied to the polling interval on each retry.
     *     @type int $maxPollDelayMillis The maximum polling interval to use, in milliseconds.
     *     @type int $totalPollTimeoutMillis The maximum amount of time to continue polling.
     *     @type Operation $lastProtoResponse A response already received from the server.
     * }
     */
    public function __construct($operationName, $operationsClient, $options = [])
    {
        $this->operationName = $operationName;
        $this->operationsClient = $operationsClient;
        if (isset($options['operationReturnType'])) {
            $this->operationReturnType = $options['operationReturnType'];
        }
        if (isset($options['metadataReturnType'])) {
            $this->metadataReturnType = $options['metadataReturnType'];
        }
        if (isset($options['initialPollDelayMillis'])) {
            $this->defaultPollSettings['initialPollDelayMillis'] = $options['initialPollDelayMillis'];
        }
        if (isset($options['pollDelayMultiplier'])) {
            $this->defaultPollSettings['pollDelayMultiplier'] = $options['pollDelayMultiplier'];
        }
        if (isset($options['maxPollDelayMillis'])) {
            $this->defaultPollSettings['maxPollDelayMillis'] = $options['maxPollDelayMillis'];
        }
        if (isset($options['totalPollTimeoutMillis'])) {
            $this->defaultPollSettings['totalPollTimeoutMillis'] = $options['totalPollTimeoutMillis'];
        }
        if (isset($options['lastProtoResponse'])) {
            $this->lastProtoResponse = $options['lastProtoResponse'];
        }
    }

    /**
     * Check whether the operation has completed.
     *
     * @return bool
     */
    public function isDone()
    {
        return (is_null($this->lastProtoResponse) || is_null($this->lastProtoResponse->getDone()))
            ? false
            : $this->lastProtoResponse->getDone();
    }

    /**
     * Check whether the operation completed successfully. If the operation is not complete, or if the operation
     * failed, return false.
     *
     * @return bool
     */
    public function operationSucceeded()
    {
        return !is_null($this->getResult());
    }

    /**
     * Check whether the operation failed. If the operation is not complete, or if the operation
     * succeeded, return false.
     *
     * @return bool
     */
    public function operationFailed()
    {
        return !is_null($this->getError());
    }

    /**
     * Get the formatted name of the operation
     *
     * @return string The formatted name of the operation
     */
    public function getName()
    {
        return $this->operationName;
    }

    /**
     * Poll the server in a loop until the operation is complete.
     *
     * Return true if the operation completed, otherwise return false. If the
     * $options['totalPollTimeoutMillis'] setting is not set (or set <= 0) then
     * pollUntilComplete will continue polling until the operation completes,
     * and therefore will always return true.
     *
     * @param array $options {
     *                       Options for configuring the polling behaviour.
     *
     *     @type int $initialPollDelayMillis The initial polling interval to use, in milliseconds.
     *     @type int $pollDelayMultiplier    Multiplier applied to the polling interval on each retry.
     *     @type int $maxPollDelayMillis     The maximum polling interval to use, in milliseconds.
     *     @type int $totalPollTimeoutMillis The maximum amount of time to continue polling, in milliseconds.
     * }
     * @throws ApiException If an API call fails.
     * @throws ValidationException
     * @return bool Indicates if the operation completed.
     */
    public function pollUntilComplete($options = [])
    {
        if ($this->isDone()) {
            return true;
        }

        $pollSettings = array_merge($this->defaultPollSettings, $options);
        return $this->poll(function () {
            $this->reload();
            return $this->isDone();
        }, $pollSettings);
    }

    /**
     * Reload the status of the operation with a request to the service.
     *
     * @throws ApiException If the API call fails.
     * @throws ValidationException If called on a deleted operation.
     */
    public function reload()
    {
        if ($this->deleted) {
            throw new ValidationException("Cannot call reload() on a deleted operation");
        }
        $name = $this->getName();
        $this->lastProtoResponse = $this->operationsClient->getOperation($name);
    }

    /**
     * Return the result of the operation. If operationSucceeded() is false, return null.
     *
     * @return mixed|null The result of the operation, or null if operationSucceeded() is false
     */
    public function getResult()
    {
        if (!$this->isDone() || is_null($this->lastProtoResponse->getResponse())) {
            return null;
        }

        /** @var Any $anyResponse */
        $anyResponse = $this->lastProtoResponse->getResponse();
        if (is_null($this->operationReturnType)) {
            return $anyResponse;
        }
        $operationReturnType = $this->operationReturnType;
        /** @var Message $response */
        $response = new $operationReturnType();
        $response->mergeFromString($anyResponse->getValue());
        return $response;
    }

    /**
     * If the operation failed, return the status. If operationFailed() is false, return null.
     *
     * @return Status|null The status of the operation in case of failure, or null if
     *                                 operationFailed() is false.
     */
    public function getError()
    {
        if (!$this->isDone() || is_null($this->lastProtoResponse->getError())) {
            return null;
        }
        return $this->lastProtoResponse->getError();
    }

    /**
     * Get an array containing the values of 'operationReturnType', 'metadataReturnType', and
     * the polling options `initialPollDelayMillis`, `pollDelayMultiplier`, `maxPollDelayMillis`,
     * and `totalPollTimeoutMillis`. The array can be passed as the $options argument to the
     * constructor when creating another OperationResponse object.
     *
     * @return array
     */
    public function getDescriptorOptions()
    {
        return [
            'operationReturnType' => $this->operationReturnType,
            'metadataReturnType' => $this->metadataReturnType,
        ] + $this->defaultPollSettings;
    }

    /**
     * @return Operation|null The last Operation object received from the server.
     */
    public function getLastProtoResponse()
    {
        return $this->lastProtoResponse;
    }

    /**
     * @return OperationsClient The OperationsClient object used to make
     * requests to the operations API.
     */
    public function getOperationsClient()
    {
        return $this->operationsClient;
    }

    /**
     * Starts asynchronous cancellation on a long-running operation. The server
     * makes a best effort to cancel the operation, but success is not
     * guaranteed. If the server doesn't support this method, it will throw an
     * ApiException with code \Google\Rpc\Code::UNIMPLEMENTED. Clients can continue
     * to use reload and pollUntilComplete methods to check whether the cancellation
     * succeeded or whether the operation completed despite cancellation.
     * On successful cancellation, the operation is not deleted; instead, it becomes
     * an operation with a getError() value with a \Google\Rpc\Status code of 1,
     * corresponding to \Google\Rpc\Code::CANCELLED.
     *
     * @throws ApiException If the API call fails.
     */
    public function cancel()
    {
        $this->operationsClient->cancelOperation($this->getName());
    }

    /**
     * Delete the long-running operation. This method indicates that the client is
     * no longer interested in the operation result. It does not cancel the operation.
     * If the server doesn't support this method, it will throw an ApiException with
     * code \Google\Rpc\Code::UNIMPLEMENTED.
     *
     * @throws ApiException If the API call fails.
     */
    public function delete()
    {
        $this->operationsClient->deleteOperation($this->getName());
        $this->deleted = true;
    }

    /**
     * Get the metadata returned with the last proto response. If a metadata type was provided, then
     * the return value will be of that type - otherwise, the return value will be of type Any. If
     * no metadata object is available, returns null.
     *
     * @return mixed The metadata returned from the server in the last response.
     */
    public function getMetadata()
    {
        if (is_null($this->lastProtoResponse)) {
            return null;
        }
        /** @var Any $any */
        $any = $this->lastProtoResponse->getMetadata();
        if (is_null($this->metadataReturnType)) {
            return $any;
        }
        if (is_null($any) || is_null($any->getValue())) {
            return null;
        }
        $metadataReturnType = $this->metadataReturnType;
        /** @var Message $metadata */
        $metadata = new $metadataReturnType();
        $metadata->mergeFromString($any->getValue());
        return $metadata;
    }
}
