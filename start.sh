#!/bin/bash
git pull origin
git checkout dev
docker stop php && docker rm php && docker rmi phpimageresize:latest
docker build -t phpimageresize .
docker run -d -p 8090:8080 --name php phpimageresize:latest


