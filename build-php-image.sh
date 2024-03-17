#!/bin/bash
cd ./build/
docker image build -t planereservation-system:php8.3 .
cd -

