<?php

$config = <<< END

server {

    # rewrite_log on;

    gzip  on;
    gzip_http_version 1.0;
    gzip_vary on;
    gzip_comp_level 6;
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript application/x-javascript text/javascript text/xml application/xml application/rss+xml application/atom+xml application/rdf+xml;

    gzip_buffers 16 8k;

    # block hack attempts
    if (\$args ~* "([a-z0-9]{12,})=([a-z0-9]{12,})" ){
        rewrite / /444_dosattack;
    }

    fastcgi_buffers 8 16k;
    fastcgi_buffer_size 16k;

    #Disable buffering responses that are over CGI buffers size
    fastcgi_max_temp_file_size 0;

    #If this aren't present, don't fill up the log file
    location = /robots.txt  { access_log off; log_not_found off; }
    location = /favicon.ico { access_log off; log_not_found off; }
    # this prevents hidden files (beginning with a period) from being served
    location ~ /\.          { access_log off; log_not_found off; deny all; }
    #Prevent any temp files created by Vim from being accessible.
    location ~ ~$           { access_log off; log_not_found off; deny all; }

    # ~ nginx performs a regular expression match.
    # ~* removes case sensitivity from the matches
    # ^~) matches literal string and stops processing
    # =) as an argument to location forces an exact match with the path requested and then stops searching for more specific matches. will only match http://ducklington.org/ but not http://ducklington.org/index.html

    listen 80;
    server_name ${sitename}.com *.${sitename}.com *.test.${sitename}.com ${sitename}.test *.${sitename}.test;

    access_log  ${'nginx.log.directory'}/${sitename}.access.log requestTime;
    error_log  ${'nginx.log.directory'}/${sitename}.error.log notice;

    root	${'sitename.root.directory'}/${sitename};

    client_max_body_size 8m;
    
    fastcgi_intercept_errors off;
    
    error_page   404  /404_static.html;
    error_page   500 502 503 504  /50x_static.html;
    
    location = /444_dosattack {
        return 444;
        #internal;
    }

    location = /404_static.html {
        root   ${'sitename.root.directory'}/data/html/;
        internal;
    }

    location = /50x_static.html {
        root   ${'sitename.root.directory'}/data/html/;
        internal;
    }

    location ~ ^/(www-status)$ {
        include       ${'sitename.root.directory'}/conf/fastcgi.conf;
        fastcgi_pass   unix:${'phpfpm.socket'}/php-fpm-www.sock;
        allow 127.0.0.1;
        #allow watchdog.localdomain;
        deny all;
    }

    location ~ ^/(images-status)$ {
        include       ${'sitename.root.directory'}/conf/fastcgi.conf;
        fastcgi_pass   unix:${'phpfpm.socket'}/php-fpm-images.sock;
        allow 127.0.0.1;
        #allow watchdog.localdomain;
        deny all;
    }

    # Will serve /documents/projects/intahwebz/intahwebz/var/cache/myfile.tar.gz
    # When passed URI /protected_files/myfile.tar.gz
    location ^~ /protected_files {
        internal;
        alias ${'sitename.cache.directory'};
    }

    location ~* ^[^\?\&]+\.(html|jpg|jpeg|gif|png|ico|css|zip|tgz|gz|rar|bz2|doc|xls|pdf|ppt|txt|tar|mid|midi|wav|bmp|rtf|js)$ {
        try_files \$uri /routing.php?file=$1;

        #access_log off;
        expires 24h;
        add_header Pragma public;
        add_header Cache-Control "public, must-revalidate, proxy-revalidate";
   }

    location ~* /(proxy|image|file) {
        set \$originalURI  \$uri;
        try_files \$uri /routing.php /50x_static.html;
        fastcgi_param  QUERY_STRING  q=\$originalURI&\$query_string;

        fastcgi_pass   unix:${'phpfpm.socket'}/php-fpm-images.sock;
        include       ${'sitename.root.directory'}/conf/fastcgi.conf;
    }

    location  / {
        set \$originalURI  \$uri;
        try_files \$uri /routing.php /50x_static.html;
        fastcgi_param  QUERY_STRING  q=\$originalURI&\$query_string;
    
        fastcgi_pass   unix:${'phpfpm.socket'}/php-fpm-www.sock;
        include       ${'sitename.root.directory'}/conf/fastcgi.conf;
    }
}



END;

return $config;
