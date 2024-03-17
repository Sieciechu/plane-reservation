#!/bin/bash

# Script is designed to mount whole git-repo and keep working dir
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
echo "Script dir: $SCRIPT_DIR"
cd $SCRIPT_DIR
docker image build -f Dockerfile -t planereservation:php8.3 .
docker image build -f app.Dockerfile -t planereservation:dev --target dev .

cd -
