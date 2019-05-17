#!/bin/sh
today=$(date "+%s")

grep -l 'common.min.js' /var/www/sinclo/socket/webroot/client/* | xargs sed -i.bak -E "s/common.min.js(\?[0-9]*)?/common.min.js?${today}/g"
grep -l 'sinclo.min.js' /var/www/sinclo/socket/webroot/client/* | xargs sed -i.bak -E "s/sinclo.min.js(\?[0-9]*)?/sinclo.min.js?${today}/g"
