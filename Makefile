IMAGES = imega/teleport-test imega/teleport
CONTAINERS = teleport_nginx teleport teleport_db
DBHOST = localhost
PORT = -p 127.0.0.1:80:80
BASEURL = localhost
MYSQL_PORTS =
BUILD = $(CURDIR)/build

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
		"make destroy build start BASEURL=10.0.3.39 PORT=\"-p 80:80\" MYSQL_PORTS=\"-p 3306:3306\"\n" \
		"make destroy build start BASEURL=demo-teleport.imega.ru PORT=\"-p 8082:80\"\n" \
		"make test BASEURL=10.0.3.39 DBHOST=10.0.3.39\n"

quick: build test start

build: build/packages build/composer
	@docker build -f "teleport-test.docker" -t imega/teleport-test .
	@docker build -t imega/teleport .

build/packages:
	@mkdir -p $(CURDIR)/build
	@curl -L0sS http://wordpress.org/wordpress-4.2.2.zip -o $(BUILD)/wordpress.zip
	@curl -L0sS http://downloads.wordpress.org/plugin/woocommerce.2.5.5.zip -o $(BUILD)/woocommerce.zip
	@curl -L0sS https://downloads.wordpress.org/theme/omega.1.2.3.zip -o $(BUILD)/omega.zip
	@curl -L0sS https://downloads.wordpress.org/theme/shopping.0.4.0.zip -o $(BUILD)/shopping.zip
	@docker run --rm -v $(BUILD):/data imega/unzip
	@mv $(BUILD)/wordpress/wordpress/* $(BUILD)/wordpress/
	@mv $(BUILD)/woocommerce/woocommerce $(BUILD)/wordpress/wp-content/plugins/
	@mv $(BUILD)/omega/omega $(BUILD)/wordpress/wp-content/themes/
	@mv $(BUILD)/shopping/shopping $(BUILD)/wordpress/wp-content/themes/
	@touch $(CURDIR)/build/packages


build/composer:
	@docker run --rm -v $(CURDIR):/data imega/composer update --ignore-platform-reqs --no-interaction

start:
	@mkdir -m 777 -p $(CURDIR)/build/storage
	@touch $(CURDIR)/mysql.log
	@docker run -d \
		--name "teleport_db" \
		-v $(CURDIR)/images/cnf:/etc/mysql/conf.d \
		-v $(CURDIR)/mysql.log:/var/log/mysql/mysql.log \
		$(MYSQL_PORTS) \
		imega/mysql

	@docker run --rm \
		-v $(CURDIR)/images/teleport_db/sql:/sql \
		--link teleport_db:teleport_db \
		imega/mysql-client \
		mysql --host=teleport_db -e "source /sql/teleport.sql"

	@docker run --rm \
		--link teleport_db:teleport_db \
		imega/mysql-client \
		mysql --host=teleport_db --database=teleport -e "update wp_options set option_value='http://$(BASEURL)' where option_id in (1,2);"

	@docker run --rm \
		-v $(CURDIR)/images/teleport_db/sql:/sql \
		--link teleport_db:teleport_db \
		imega/mysql-client \
		mysql --host=teleport_db --database=teleport -e "source /sql/teleport_enable.sql"

	@docker run -d \
		--name "teleport" \
		--link teleport_db:teleport_db \
		-v $(CURDIR)/build/wordpress:/wordpress \
		-v $(CURDIR)/build/storage:/storage \
		-v $(CURDIR):/app \
		-p 9005:9005 \
		--memory="300M" \
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
		-v $(CURDIR)/build/wordpress:/wordpress \
		-v $(CURDIR)/build/storage:/storage \
		-v $(CURDIR)/images/teleport_nginx/sites-enabled:/etc/nginx/sites-enabled \
		-v $(CURDIR)/images/teleport_nginx/conf.d:/etc/nginx/conf.d \
		-v $(CURDIR)/images/teleport_nginx/entrypoints/teleport-nginx.sh:/teleport-nginx.sh \
		$(PORT) \
		--entrypoint=/teleport-nginx.sh \
		leanlabs/nginx

stop:
	-docker stop $(CONTAINERS)

dep:
	@docker run --rm -v $(CURDIR):/data imega/composer:1.0.1 update

clean: stop
	@rm -rf $(CURDIR)/build/storage
	-docker rm -fv $(CONTAINERS)

test:
	@docker run --rm \
		-v $(CURDIR):/data \
		--env="DB_HOST=$(DBHOST)" \
		--env="BASE_URL=$(BASEURL)" \
		--memory="300M" \
		imega/teleport-test vendor/bin/phpunit tests/iMega/Teleport/Service/AcceptFileServiceTest.php

destroy: clean
	-docker rmi -f $(IMAGES)

.PHONY: build
