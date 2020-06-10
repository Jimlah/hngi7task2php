#!/bin/bash
git pull origin
git checkout dev
docker stop teamflash && docker rm teamflash && docker rmi teamflash:latest
docker build -t teamflash .
docker run -d -p 8090:8080 --name teamflash teamflash:latest



