set -x

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

path_to_bin="${DIR}/../bin"

environment=centos,dev

result=`"${path_to_bin}/configurate" -p "${DIR}/config/config.php" "${DIR}/config/example.nginx.conf.php" example.nginx.conf "${environment}"`

`"${path_to_bin}/configurate" -p "${DIR}/config/config.php" "${DIR}/config/example.php-fpm.conf.php" example.php-fpm.conf "${environment}"`


