#!/bin/bash
git pull origin
git checkout dev
<<<<<<< HEAD
docker stop teamflash && docker rm teamflash && docker rmi teamflash:latest
docker build -t teamflash .
docker run -d -p 8090:8080 --name teamflash teamflash:latest

=======
docker stop php && docker rm php && docker rmi phpimageresize:latest
docker build -t phpimageresize .
docker run -d -p 8090:8080 --name php phpimageresize:latest
>>>>>>> 7608ba3fc5282e599d810642e535585f1e563222


