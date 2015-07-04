IMAGES = imega/teleport-test imega/teleport
CONTAINERS = teleport_db teleport teleport_nginx
DBHOST = localhost
PORT = 80
BASEURL = localhost

quick: build test start

build:
	@docker build -f "teleport-test.docker" -t imega/teleport-test .
	@docker run --rm -v $(CURDIR):/data imega/composer:1.0.1 update
	@docker build -f "teleport.docker" -t imega/teleport .

start:
	@mkdir -p $(CURDIR)/build/db

	@docker run -d --name "teleport_db" -v $(CURDIR)/build/db:/var/lib/mysql -p 3306:3306 imega/mysql

	@docker run --rm \
		-v $(CURDIR)/build/sql:/sql \
		--link teleport_db:teleport_db \
		imega/mysql-client:1.1.0 \
		mysqladmin --silent --host=teleport_db --wait=5 ping

	@docker run --rm \
		-v $(CURDIR)/build/sql:/sql \
		--link teleport_db:teleport_db \
		imega/mysql-client:1.1.0 \
		mysql --host=teleport_db -e "source /sql/teleport.sql"

	@docker run -d \
		--name "teleport" \
		--link teleport_db:teleport_db \
		-v $(CURDIR):/app \
		-p 9001:9001 \
		imega/teleport \
		php-fpm -F \
			-d error_reporting=E_ALL \
			-d log_errors=On \
			-d error_log=/dev/stdout \
			-d display_errors=On \
			-d always_populate_raw_post_data=-1

	@docker run -d \
		--name teleport_nginx \
		--link teleport:service \
		-v $(CURDIR)/build/sites-enabled:/etc/nginx/sites-enabled \
		-v $(CURDIR)/build/conf.d:/etc/nginx/conf.d \
		-v $(CURDIR)/build/entrypoints/teleport-nginx.sh:/teleport-nginx.sh \
		-p $(PORT):80 \
		--entrypoint=/teleport-nginx.sh \
		leanlabs/nginx

stop:
	-docker stop $(CONTAINERS)

dep:
	@docker run --rm -v $(CURDIR):/data imega/composer:1.0.1 update

clean: stop
	@rm -rf $(CURDIR)/build/db
	-docker rm -fv $(CONTAINERS)

test:
	@docker run --rm -v $(CURDIR):/data --env="DB_HOST=$(DBHOST)" --env="BASE_URL=$(BASEURL)" imega/teleport-test vendor/bin/phpunit

destroy: clean
	@docker rmi -f $(IMAGES)

.PHONY: build start test dep
