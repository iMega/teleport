NAME = imega/composer
TAG = 1.0.1

build:
	@docker run --rm -v `pwd`:/data $(NAME):$(TAG) update

start:
	@docker run -d \
		-v `pwd`:/app \
		--name teleport \
		leanlabs/php:1.1.1 \
		php-fpm -F -d error_reporting=E_ALL -d log_errors=ON -d error_log=/dev/stdout -d display_errors=YES

	@docker run -d \
		--name teleport_nginx \
		--link teleport:service \
		-v `pwd`/build/sites-enabled:/etc/nginx/sites-enabled \
		-v `pwd`/build/conf.d:/etc/nginx/conf.d \
		-p 127.0.0.1:80:80 \
		hub.nethouse.ru/nethouse/nginx

test:
	@docker run --rm -v `pwd`:/data imega/phptest vendor/bin/phpunit --debug

x:
	@docker run --rm -v `pwd`:/data imega/phptest php test.php

.PHONY: build start test x
