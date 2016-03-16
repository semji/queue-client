USER_UID = $(shell id -u $(USER))

web_test_start:
	@mkdir -p codeCoverage
	docker-compose up -d web_test

web_test_stop:
	$(eval DOCKER_ID := $(shell docker-compose ps -q web_test))
	@if [ $(DOCKER_ID) ]; then /bin/bash -c 'echo "stop container" && docker stop $(DOCKER_ID)'; fi
	@if [ $(DOCKER_ID) ]; then /bin/bash -c 'echo "rm container" && docker rm $(DOCKER_ID)'; fi

test:
	@mkdir -p codeCoverage
	@docker-compose build test &&  \
	docker-compose run --rm test /bin/bash -c \
	'(composer install --prefer-dist --no-interaction && \
	php vendor/atoum/atoum/bin/atoum -c coverage.php -d tests/units/)' ; \
	docker-compose run --rm test /bin/bash -c \
	'chown $(USER_UID):$(USER_UID) . -R'
