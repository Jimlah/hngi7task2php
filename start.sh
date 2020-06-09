#!/bin/bash
docker build -t phpimageresize .
docker run -d -p 8090:8080 --name php phpimageresize:latest


