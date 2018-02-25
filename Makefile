IMG = bestpig-web-compromised
CTN = wcompromised

build:
	docker build -t ${IMG} .

up: build
	docker run -d -p 8080:80 --name ${CTN} ${IMG} && \
	echo "Challenge up sur http://localhost:8080/"

down:
	docker rm -f ${CTN}

reup: down up

logs:
	docker logs -f ${CTN}

clean: down
	docker rmi ${IMG}

.PHONY: build up down logs clean
