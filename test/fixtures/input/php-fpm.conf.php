<?php

$config = <<< END


;;;;;;;;;;;;;;;;;;
; Global Options ;
;;;;;;;;;;;;;;;;;;

[global]
; Pid file
; Default Value: none
; pid = /var/run/php-fpm/php-fpm.pid
pid = ${'phpfpm.pid.directory'}/php-fpm.pid



error_log = ${'php.log.directory'}/php-fpm.log

#Log level. Default: "notice" (levels: "DEBUG", "NOTICE", "WARNING", "ERROR", "ALERT")
#log_level = NOTICE

; syslog_ident is prepended to every message.
syslog.ident = php-fpm

; Send FPM to background. Set to 'no' to keep FPM in foreground for debugging
; or for use with launchd.
; Default Value: yes
daemonize = yes

; - select (any POSIX os)
; - poll (any POSIX os)
; - epoll (linux >= 2.5.44)
; - kqueue (FreeBSD >= 4.1, OpenBSD >= 2.9, NetBSD >= 2.0)
; - /dev/poll (Solaris >= 7)
; - port (Solaris >= 10)
; Default Value: not set (auto detection)
# events.mechanism = select

include=${'phpfpm.conf.directory'}/*.conf

END;

return $config;
