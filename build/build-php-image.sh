#!/bin/bash
docker image build -f Dockerfile -t planereservation:php8.3 .
docker image build -f app.Dockerfile -t planereservation:dev --target dev .
