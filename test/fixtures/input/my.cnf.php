<?php

$config = <<< END
[mysql]
# no-auto-rehash
default-character-set=${'mysql.charset'}




# The following options will be passed to all MySQL clients
[client]
port		= 3306
socket		= ${'mysql.socket'}
default-character-set=${'mysql.charset'}

# The MySQL server
[mysqld]

# Do not store invalid data e.g. values too big to fit in int
# or strings that are too long to fit in varchar(256)
sql-mode = STRICT_ALL_TABLES

port		= 3306
socket		= ${'mysql.socket'}
init-connect='SET NAMES ${'mysql.charset'}'
character-set-server=${'mysql.charset'}
collation-server=${'mysql.collation'}
log-error=${'mysql.log.directory'}/mysqld.log
general-log=1
general_log_file=${'mysql.log.directory'}/mysql.log


long_query_time=1
slow_query_log=1
slow_query_log_file=${'mysql.log.directory'}/mysql_slow_query.log


# skip-locking
skip-networking
key_buffer = 16K
max_allowed_packet = 1M
table_cache = 48
sort_buffer_size = 64K
read_buffer_size = 256K
read_rnd_buffer_size = 256K
net_buffer_length = 2K
thread_stack = 128K

innodb_file_per_table


#0	Table and database names are stored on disk using the lettercase specified in the CREATE TABLE or CREATE DATABASE statement. Name comparisons are case sensitive. You should not set this variable to 0 if you are running MySQL on a system that has case-insensitive file names (such as Windows or Mac OS X). If you force this variable to 0 with --lower-case-table-names=0 on a case-insensitive file system and access MyISAM tablenames using different lettercases, index corruption may result.
#1	Table names are stored in lowercase on disk and name comparisons are not case sensitive. MySQL converts all table names to lowercase on storage and lookup. This behavior also applies to database names and table aliases.
#2	Table and database names are stored on disk using the lettercase specified in the CREATE TABLE or CREATE DATABASE statement, but MySQL converts them to lowercase on lookup. Name comparisons are not case sensitive. This works only on file systems that are not case sensitive! InnoDB table names are stored in lowercase, as for lower_case_table_names=1.

lower_case_table_names=${'mysql.casetablenames'}

# Don't listen on a TCP/IP port at all. This can be a security enhancement,
# if all processes that need to connect to mysqld run on the same host.
# All interaction with mysqld must be made via Unix sockets or named pipes.
# Note that using this option without enabling named pipes on Windows
# (using the "enable-named-pipe" option) will render mysqld useless!
server-id	= 1

# Uncomment the following if you want to log updates
#log-bin=mysql-bin

# Uncomment the following if you are NOT using BDB tables
#skip-bdb
innodb_strict_mode=on
# Uncomment the following if you are using InnoDB tables
#innodb_data_home_dir = /Applications/XAMPP/xamppfiles/var/mysql/
#innodb_data_file_path = ibdata1:10M:autoextend
#innodb_log_group_home_dir = /Applications/XAMPP/xamppfiles/var/mysql/
#innodb_log_arch_dir = /Applications/XAMPP/xamppfiles/var/mysql/
# You can set .._buffer_pool_size up to 50 - 80 %
# of RAM but beware of setting memory usage too high
#innodb_buffer_pool_size = 16M
#innodb_additional_mem_pool_size = 2M
# Set .._log_file_size to 25 % of buffer pool size
#innodb_log_file_size = 5M
#innodb_log_buffer_size = 8M
#innodb_flush_log_at_trx_commit = 1
#innodb_lock_wait_timeout = 50

innodb_buffer_pool_size=128M
innodb_additional_mem_pool_size = 16M
innodb_flush_method=O_DIRECT

[mysqldump]
quick
max_allowed_packet = 16M


[isamchk]
key_buffer = 8M
sort_buffer_size = 8M

[myisamchk]
key_buffer = 8M
sort_buffer_size = 8M

[mysqlhotcopy]
interactive-timeout

END;

return $config;
