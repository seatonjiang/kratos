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
#
# This script expects to be invoked from the common-protos-php root

set -e

function pushDocs () {
  git submodule add -q -f -b gh-pages https://${GH_OAUTH_TOKEN}@github.com/${TRAVIS_REPO_SLUG} ghpages

  rsync -aP tmp_gh-pages/* ghpages/

  cd ghpages
  git add .
  if [[ -n "$(git status --porcelain)" ]]; then
    git config user.name "travis-ci"
    git config user.email "travis@travis-ci.org"
    git commit -m "Updating docs for ${1}"
    git status
    git push -q https://${GH_OAUTH_TOKEN}@github.com/${TRAVIS_REPO_SLUG} HEAD:gh-pages
  else
    echo "Nothing to commit."
  fi
}

if [[ ! -z ${TRAVIS_TAG} ]]; then
  pushDocs ${TRAVIS_TAG}
fi
