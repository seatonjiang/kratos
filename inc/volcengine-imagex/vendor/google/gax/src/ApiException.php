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

use Exception;
use Google\Protobuf\Internal\RepeatedField;
use Google\Rpc\Status;

/**
 * Represents an exception thrown during an RPC.
 */
class ApiException extends Exception
{
    private $status;
    private $metadata;
    private $basicMessage;

    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param string $status
     * @param array $optionalArgs {
     *     @type Exception|null $previous
     *     @type array|null $metadata
     *     @type string|null $basicMessage
     * }
     */
    public function __construct(
        $message,
        $code,
        $status,
        array $optionalArgs = []
    ) {
        $optionalArgs += [
            'previous' => null,
            'metadata' => null,
            'basicMessage' => $message,
        ];
        parent::__construct($message, $code, $optionalArgs['previous']);
        $this->status = $status;
        $this->metadata = $optionalArgs['metadata'];
        $this->basicMessage = $optionalArgs['basicMessage'];
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \stdClass $status
     * @return ApiException
     */
    public static function createFromStdClass($status)
    {
        $metadata = property_exists($status, 'metadata') ? $status->metadata : null;
        return self::create(
            $status->details,
            $status->code,
            $metadata,
            Serializer::decodeMetadata($metadata)
        );
    }

    /**
     * @param string $basicMessage
     * @param int $rpcCode
     * @param array|null $metadata
     * @param \Exception $previous
     * @return ApiException
     */
    public static function createFromApiResponse(
        $basicMessage,
        $rpcCode,
        array $metadata = null,
        \Exception $previous = null
    ) {
        return self::create(
            $basicMessage,
            $rpcCode,
            $metadata,
            Serializer::decodeMetadata($metadata),
            $previous
        );
    }

    /**
     * Construct an ApiException with a useful message, including decoded metadata.
     *
     * @param string $basicMessage
     * @param int $rpcCode
     * @param mixed[]|RepeatedField $metadata
     * @param array $decodedMetadata
     * @param \Exception|null $previous
     * @return ApiException
     */
    private static function create($basicMessage, $rpcCode, $metadata, array $decodedMetadata, $previous = null)
    {
        $rpcStatus = ApiStatus::statusFromRpcCode($rpcCode);
        $messageData = [
            'message' => $basicMessage,
            'code' => $rpcCode,
            'status' => $rpcStatus,
            'details' => $decodedMetadata
        ];

        $message = json_encode($messageData, JSON_PRETTY_PRINT);

        return new ApiException($message, $rpcCode, $rpcStatus, [
            'previous' => $previous,
            'metadata' => $metadata,
            'basicMessage' => $basicMessage,
        ]);
    }

    /**
     * @param Status $status
     * @return ApiException
     */
    public static function createFromRpcStatus(Status $status)
    {
        return self::create(
            $status->getMessage(),
            $status->getCode(),
            $status->getDetails(),
            Serializer::decodeAnyMessages($status->getDetails())
        );
    }

    /**
     * @return null|string
     */
    public function getBasicMessage()
    {
        return $this->basicMessage;
    }

    /**
     * @return mixed[]
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * String representation of ApiException
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": $this->message\n";
    }
}
