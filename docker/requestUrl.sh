#!/bin/ash

export REQUEST_METHOD=$1
export REQUEST_URI=$2
export QUERY_STRING=$3
export SCRIPT_FILENAME=/var/www/html/public/index.php
# connect to fpm and discard all headers to just display the content
cgi-fcgi -bind -connect 127.0.0.1:9000 | awk '{while(getline && $0 != ""){}}1'
