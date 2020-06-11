#!/bin/bash
git pull origin
git checkout dev
docker stop teamflashphp && docker rm teamflashphp && docker rmi teamflashphp:latest
docker build -t teamflashphp .
docker run -d -p 1060:8080 --name teamflashphp teamflashphp:latest




