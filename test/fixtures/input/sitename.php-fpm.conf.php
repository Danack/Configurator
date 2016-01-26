<?php

$config = <<< END


;;;;;;;;;;;;;;;;;;;;
; Pool Definitions ;
;;;;;;;;;;;;;;;;;;;;

; Start a new pool named 'www'.
[www]

; Unix user/group of processes
user = ${'phpfpm.user'}
group = ${'phpfpm.group'}

listen = ${'phpfpm.socket.directory'}/php-fpm-www.sock

catch_workers_output = yes

; List of ipv4 addresses of FastCGI clients which are allowed to connect.
listen.allowed_clients = 127.0.0.1

listen.owner = ${'phpfpm.user'}
listen.group = ${'phpfpm.group'}
listen.mode = 0664

; Per pool prefix
;prefix = /path/to/pools/\$pool
;prefix = \$pool

request_slowlog_timeout = 10
slowlog = ${'php.log.directory'}/slow.\$pool.log

request_terminate_timeout=1500

pm = dynamic

pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 10
pm.max_requests = 5000

; The URI to view the FPM status page.
pm.status_path = /www-status

; The access log file
; Default: not set
;access.log = log/\$pool.access.log
;access.format = "%R - %u %t \"%m %r%Q%q\" %s %f %{mili}d %{kilo}M %C%%"

; The log file for slow requests
;slowlog = log/\$pool.log.slow
;request_slowlog_timeout = 0

; Additional php.ini defines
php_admin_value[memory_limit] = ${'phpfpm.www.maxmemory'}
php_admin_value[error_log] = ${'php.errorlog.directory'}/www-error.log



;php_value[session.save_handler] = files
;php_value[session.save_path] = ${'php.session.directory'}

; Limits the extensions of the main script FPM will allow to parse.
security.limit_extensions = .php


#php_value[auto_prepend_file]=${'sitename.root.directory'}/php_shared/prepend.php
#php_value[auto_append_file]=${'sitename.root.directory'}/php_shared/postpend.php

#php_value[auto_prepend_file]=/php_shared/prepend.php
#php_value[auto_append_file]=/php_shared/postpend.php


; Start a new pool named 'www'.
[www-images]

; Unix user/group of processes
user = ${'phpfpm.user'}
group = ${'phpfpm.group'}

listen = ${'phpfpm.socket.directory'}/php-fpm-images.sock

catch_workers_output = yes

; List of ipv4 addresses of FastCGI clients which are allowed to connect.
listen.allowed_clients = 127.0.0.1


listen.owner = ${'phpfpm.user'}
listen.group = ${'phpfpm.group'}
listen.mode = 0664

pm = dynamic

pm.max_children = 7
pm.start_servers = 2
pm.min_spare_servers = 2
pm.max_spare_servers = 4
pm.max_requests = 200

pm.status_path = /images-status

; The access log file
; Default: not set
;access.log = log/\$pool.access.log
;access.format = "%R - %u %t \"%m %r%Q%q\" %s %f %{mili}d %{kilo}M %C%%"

request_terminate_timeout=25
; The log file for slow requests
;slowlog = log/\$pool.log.slow
;request_slowlog_timeout = 20

; Additional php.ini defines
php_admin_value[memory_limit] = ${'phpfpm.images.maxmemory'}

; Limits the extensions of the main script FPM will allow to parse.
security.limit_extensions = .php


# php_value[auto_prepend_file]=${'sitename.root.directory'}/php_shared/prepend.php
# php_value[auto_append_file]=${'sitename.root.directory'}/php_shared/postpend.php

# php_value[auto_prepend_file]=/php_shared/prepend.php
# php_value[auto_append_file]=/php_shared/postpend.php


END;

return $config;
