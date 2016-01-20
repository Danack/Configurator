<?php

// This is a template file to generate a script that will
// symlink the co

$config = <<< END

rm -f /etc/nginx/sites-enabled/blog.nginx.conf
rm -f /etc/php-fpm.d/blog.php-fpm.conf
rm -f /etc/php-fpm.d/blog.php.fpm.ini

ln -sfn ${'app_root_directory'}/autogen/nginx.conf /etc/nginx/sites-enabled/${'app_name'}.nginx.conf
ln -sfn ${'app_root_directory'}/autogen/php-fpm.conf /etc/php-fpm.d/${'app_name'}.php-fpm.conf
ln -sfn ${'app_root_directory'}/autogen/php.fpm.ini /etc/php-fpm.d/${'app_name'}.php.fpm.ini

END;

return $config;
