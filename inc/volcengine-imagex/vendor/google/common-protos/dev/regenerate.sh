#!/bin/bash
# Copyright 2018 Google LLC
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

# Constants
REQUIRED_PROTOC_VERSION="libprotoc 3.9.0"
COMMON_PROTOS_REPO="https://github.com/googleapis/api-common-protos.git"

# Directories
: "${ROOT_DIR:=$(pwd)}"
echo "root dir: ${ROOT_DIR}"
TEMP_COMMON_PROTOS_DIR="${ROOT_DIR}/tmp_common_protos"
PROTOC_OUT_DIR="${ROOT_DIR}/out"
SRC_DIR="${ROOT_DIR}/src"
METADATA_DIR="${ROOT_DIR}/metadata"

# Protoc bin
: "${PROTOC_BIN:=$(which protoc)}"
echo "protoc bin: ${PROTOC_BIN}"

# Check protoc version
PROTOC_VERSION=$($PROTOC_BIN --version)
echo "protoc version: ${PROTOC_VERSION}"

if [ "${PROTOC_VERSION}" != "${REQUIRED_PROTOC_VERSION}" ]
then
  echo "Invalid protoc version, expected '${REQUIRED_PROTOC_VERSION}', got '${PROTOC_VERSION}'"
  exit 1
fi

rm -rf ${TEMP_COMMON_PROTOS_DIR}
git clone ${COMMON_PROTOS_REPO} ${TEMP_COMMON_PROTOS_DIR}

rm -rf ${PROTOC_OUT_DIR}
mkdir ${PROTOC_OUT_DIR}

PROTOS_TO_GENERATE=$(find ${TEMP_COMMON_PROTOS_DIR} -name "*.proto")

PROTOC_ARGS="--php_out ${PROTOC_OUT_DIR} -I${TEMP_COMMON_PROTOS_DIR} ${PROTOS_TO_GENERATE}"
echo "Calling protoc with args: ${PROTOC_ARGS}"
${PROTOC_BIN} ${PROTOC_ARGS}

rm -rf ${SRC_DIR}
mkdir ${SRC_DIR}

rm -rf ${METADATA_DIR}
mkdir ${METADATA_DIR}

echo "Copy protos to src and metadata locations"
cp -r ${PROTOC_OUT_DIR}/Google/* ${SRC_DIR}/
cp -r ${PROTOC_OUT_DIR}/GPBMetadata/Google/* ${METADATA_DIR}/
