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

# Script to build doc site.
# This script expects to be invoked from the common-protos-php root.
#
# This script will look for the TRAVIS_TAG environment variable, and
# use that as the version number. If no environment variable is found,
# it will use the first command line argument. If no command line
# argument is specified, default to 'master'.

set -ev

ROOT_DIR=$(pwd)
DOC_OUTPUT_DIR=${ROOT_DIR}/tmp_gh-pages
INDEX_FILE=${DOC_OUTPUT_DIR}/index.html
SAMI_EXECUTABLE=${ROOT_DIR}/vendor/sami/sami/sami.php
SAMI_CONFIG=${ROOT_DIR}/dev/sami-config.php

# Construct the base index file that redirects to the latest version
# of the docs. This will only be generated when TRAVIS_TAG is set.
UPDATED_INDEX_FILE=$(cat << EndOfMessage
<html><head><script>window.location.replace('/common-protos-php/${TRAVIS_TAG}/' + location.hash.substring(1))</script></head><body></body></html>
EndOfMessage
)

function buildDocs() {
  DOCS_VERSION_TO_BUILD=${1}
  COMMON_PROTOS_DOCS_VERSION=${DOCS_VERSION_TO_BUILD} php ${SAMI_EXECUTABLE} update ${SAMI_CONFIG} -v
}

if [[ ! -z ${TRAVIS_TAG} ]]; then
  buildDocs ${TRAVIS_TAG}
  # Update the redirect index file only for builds that use the
  # TRAVIS_TAG env variable.
  echo ${UPDATED_INDEX_FILE} > ${INDEX_FILE}
elif [[ ! -z ${1} ]]; then
  buildDocs ${1}
else
  echo ERROR: TRAVIS_TAG not found, and no command line version specified
  exit 1
fi
