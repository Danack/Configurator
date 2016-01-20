<?php

$config = <<< END

server {

    gzip  on;
    gzip_http_version 1.0;
    gzip_vary on;
    gzip_comp_level 6;
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript application/x-javascript text/javascript text/xml application/xml application/rss+xml application/atom+xml application/rdf+xml;

    gzip_buffers 16 8k;

    # ~ nginx performs a regular expression match.
    # ~* removes case sensitivity from the matches
    # ^~) matches literal string and stops processing
    # =) as an argument to location forces an exact match with the path requested and then stops searching for more specific matches. will only match http://ducklington.org/ but not http://ducklington.org/index.html

    listen 80;
    # listen 8080;
    server_name blog.test blog.basereality.test blog.basereality.com blog.test.basereality.com;

    access_log  ${'nginx_log_directory'}/blog.access.log;
    error_log  ${'nginx_log_directory'}/blog.error.log;

    root ${'app_root_directory'}/public;

    client_max_body_size 1m;
    
    rewrite ^/(.*)/$ /$1 permanent;

    error_page  404  /404.html;
    location = /404.html {
        try_files /html/404.html =500;
    }

    # redirect server error pages to the static page /50x.html
    #error_page   500 502 503 504  /50x.html;

    #location = /50x.html {
    #	root   /usr/share/nginx/html;
    #}

    fastcgi_intercept_errors off;

    # this prevents hidden files (beginning with a period) from being served
    location ~ /\.          { access_log off; log_not_found off; deny all; }

    location = /robots.txt  { access_log off; log_not_found off; }
    location = /favicon.ico { access_log off; log_not_found off; }
    location ~ /\.          { access_log off; log_not_found off; deny all; }
    location ~ ~$           { access_log off; log_not_found off; deny all; }
    
    #This must be the last regular epxression match
    location ~* ^[^\?\&]+\.(bmp|bz2|css|doc|eot|gif|gz|html|ico|jpeg|jpg|js|mid|midi|pdf|png|ppt|psd|rar|rtf|svg|tar|tgz|ttf|txt|wav|woff|woff2|xls|zip)$ {
        try_files \$uri /index.php?file=\$1;

        #access_log off;
        expires 7d;
        add_header Pragma public;
        add_header Cache-Control "public, must-revalidate, proxy-revalidate";
    }
   
    location ~ ^/(www-status|ping)$ {
        if ( \$remote_addr != 127.0.0.1 ) { return 444;} 
        access_log off;
        allow 127.0.0.1;
        deny all;
        fastcgi_param  QUERY_STRING       \$query_string;
        include       ${'app_root_directory'}/data/config_template/fastcgi.conf;
        fastcgi_pass   unix:${'phpfpm_fullsocketpath'};
    }

    location  / {
        try_files \$uri /index.php =404;
        fastcgi_param  QUERY_STRING  \$query_string;
        fastcgi_pass   unix:${'phpfpm_fullsocketpath'};
        include       ${'app_root_directory'}/data/config_template/fastcgi.conf;
    }
}

END;

return $config;
