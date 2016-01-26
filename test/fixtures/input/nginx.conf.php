<?php

$config = <<< END
user  ${'nginx.user'};
worker_processes  1;

#error_log  logs/error.log;
#error_log  logs/error.log  notice;
#error_log  logs/error.log  info;

#pid        logs/nginx.pid;


#Only the "best" location directive gets taken, in this order:
#location =  <path>  (longest match wins)
#location ^~ <path>  (longest match wins)
#location ~  <path>  (first defined match wins)
#location    <path>  (longest match wins)


#pid        %nginx.run.directory'}/nginx.pid;

events {
    worker_connections  1024;
}

http {
    include       mime.types;
    default_type  application/octet-stream;

    sendfile        on;

    #tcp_nopush     on;

    keepalive_timeout  65;

	log_format requestTime '\$remote_addr - \$remote_user [\$time_local]  '
                    '"\$request" \$status \$body_bytes_sent '
                    '"\$http_referer" "\$http_user_agent" '
                    '"\$host" "\$request_time"';
    
    
    error_page   404  /404_static.html;
    error_page   500 502 503 504  /50x_static.html;

    server {

        listen 80;
        # listen 443 default_server ssl;
        # ssl_certificate     /usr/local/nginx/conf/cert.pem;
        # ssl_certificate_key /usr/local/nginx/conf/cert.key;

        access_log  ${'nginx.log.directory'}/catchall.access.log requestTime;
        error_log  ${'nginx.log.directory'}/catchall.error.log;
        #prevents all requests going to first actual server
        return 404;
    }

    include ${'nginx.conf.directory'}/conf.d/*.conf;
    include ${'nginx.conf.directory'}/sites-enabled/*.conf;
}



END;

return $config;
