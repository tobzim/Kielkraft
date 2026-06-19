#!/bin/sh
# Marvento - php-fpm liveness check via the FastCGI ping endpoint.
SCRIPT_NAME=/ping \
SCRIPT_FILENAME=/ping \
REQUEST_METHOD=GET \
cgi-fcgi -bind -connect 127.0.0.1:9000 >/dev/null 2>&1
