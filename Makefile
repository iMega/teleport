IMAGES = imega/teleport-test imega/teleport
CONTAINERS = teleport_db teleport teleport_nginx
DBHOST = localhost
PORT = -p 127.0.0.1:80:80
BASEURL = localhost
MYSQL_PORTS =

help:
	@echo "USAGE: make COMMAND [OPTIONS]\n\n" \
		"COMMANDS:\n\n" \
		"\033[1mbuild\033[0m\t Build container for tests.\n\t Install php's dependency. Build container teleport.\n\n" \
		"\033[1mquick\033[0m\t Start commands build + tests + start.\n\n" \
		"\033[1mstart\033[0m\t Run all containers.\n\n" \
		"\033[1mstop\033[0m\t Stop all containers.\n\n" \
		"\033[1mdep\033[0m\t Update php's dependency.\n\n" \
		"\033[1mtest\033[0m\t Start testing.\n\n" \
		"\033[1mclean\033[0m\t Stop and remove containers.\n\n" \
		"\033[1mdestroy\033[0m Stop and remove containers and images.\n\n" \
		"OPTIONS:\n\n" \
		"\033[1mDBHOST\033[0m\t\tFor tests port to teleport_db.\n\t\texample: DBHOST=10.0.3.11\n\n" \
		"\033[1mPORT\033[0m\t\tPublic port teleport_nginx.\n\t\texample: PORT=\"-p 80:80\"\n\n" \
		"\033[1mBASEURL\033[0m\tSite domain or ip without slashes.\n\t\texample: BASEURL=demo.imegateleport.ru\n\n" \
		"\033[1mMYSQL_PORTS\033[0m\tSet port mapping for teleport_db.\n\t\texample: MYSQL_PORTS=\"-p 3307:3306\"\n\n" \
		"EXAMPLES:\n\n" \
		"make destroy build start BASEURL=10.0.3.39 PORT=\"-p 80:80\"\n" \
		"make destroy build start BASEURL=demo-teleport.imega.ru PORT=\"-p 8082:80\"\n\n"

quick: build test start

build:
	@docker build -f "teleport-test.docker" -t imega/teleport-test .
	@docker run --rm -v $(CURDIR):/data imega/composer:1.0.1 update
	@docker build -f "teleport.docker" -t imega/teleport .

start:
	@mkdir -p $(CURDIR)/build/db

	@docker run -d \
		--name "teleport_db" \
		-v $(CURDIR)/build/db:/var/lib/mysql \
		$(MYSQL_PORTS) \
		imega/mysql

	@docker run --rm \
		-v $(CURDIR)/build/sql:/sql \
		--link teleport_db:teleport_db \
		imega/mysql-client:1.1.0 \
		mysqladmin --silent --host=teleport_db --wait=5 ping

	@docker run --rm \
		-v $(CURDIR)/build/sql:/sql \
		--link teleport_db:teleport_db \
		--log-driver=syslog \
		imega/mysql-client:1.1.0 \
		mysql --host=teleport_db -e "source /sql/teleport.sql"

	@docker run --rm \
		--link teleport_db:teleport_db \
		--log-driver=syslog \
		imega/mysql-client:1.1.0 \
		mysql --host=teleport_db --database=teleport -e "update wp_options set option_value='http://$(BASEURL)' where option_id in (1,2);"

	@docker run --rm \
		-v $(CURDIR)/build/sql:/sql \
		--link teleport_db:teleport_db \
		--log-driver=syslog \
		imega/mysql-client:1.1.0 \
		mysql --host=teleport_db --database=teleport -e "source /sql/teleport_enable.sql"

	@docker run -d \
		--name "teleport" \
		--link teleport_db:teleport_db \
		--log-driver=syslog \
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
		--log-driver=syslog \
		-v $(CURDIR)/build/sites-enabled:/etc/nginx/sites-enabled \
		-v $(CURDIR)/build/conf.d:/etc/nginx/conf.d \
		-v $(CURDIR)/build/entrypoints/teleport-nginx.sh:/teleport-nginx.sh \
		$(PORT) \
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
	@docker run --rm \
		-v $(CURDIR):/data \
		--env="DB_HOST=$(DBHOST)" \
		--env="BASE_URL=$(BASEURL)" \
		imega/teleport-test vendor/bin/phpunit

destroy: clean
	-docker rmi -f $(IMAGES)

.PHONY: help build start test dep
