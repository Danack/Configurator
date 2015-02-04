<?php

$config = <<< END
extension=imagick.so
default_charset = "utf-8";

memory_limit=${'php.memory.limit'}

END;

return $config;