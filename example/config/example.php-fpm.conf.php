<?php

$config = <<< END


;;;;;;;;;;;;;;;;;;;;
; Pool Definitions ; 
;;;;;;;;;;;;;;;;;;;;

; Start a new pool named 'www'.
[example]

; Unix user/group of processes
user = exampleuser
group = ${'phpfpm.group'}

listen = ${'phpfpm.fullsocketpath'}

; List of ipv4 addresses of FastCGI clients which are allowed to connect.
listen.allowed_clients = 127.0.0.1

listen.owner = exampleuser
listen.group = ${'phpfpm.group'}
listen.mode = 0664

; Per pool prefix
;prefix = /path/to/pools/\$pool
;prefix = \$pool

request_slowlog_timeout = 10
slowlog = ${'php.log.directory'}/slow.\$pool.log

request_terminate_timeout=500

catch_workers_output = yes

pm = dynamic

pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 10
pm.max_requests = 5000

; The URI to view the FPM status page.
pm.status_path = /www-status

; Additional php.ini defines
php_admin_value[memory_limit] = ${'phpfpm.www.maxmemory'}
php_admin_value[error_log] = ${'php.errorlog.directory'}/\$pool-error.log

security.limit_extensions = .php

include = ${'example.root.directory'}/autogen/example.php.fpm.ini

END;

return $config;
