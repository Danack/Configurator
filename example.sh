
# Turn on echoing, error-reporting.
set -eux -o pipefail


dev_environment="centos,dev"
environment="${dev_environment}"

if [ "$#" -ge 1 ]; then
    environment=$1
fi


#DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
#path_to_bin="${DIR}/./bin"

mkdir -p autogen



#Generate config templates for the set environment
bin/configurate -p example/config.php example/config_template/nginx.conf.php autogen/nginx.conf $environment
bin/configurate -p example/config.php example/config_template/php-fpm.conf.php autogen/php-fpm.conf $environment
bin/configurate -p example/config.php example/config_template/php.ini.php autogen/php.ini $environment
bin/configurate -p example/config.php example/config_template/addConfig.sh.php autogen/addConfig.sh $environment

# Convert the ini file to be in the PHP-FPM format
bin/fpmconv autogen/php.ini autogen/php.fpm.ini

# Link the generated files to where they need to be on the system.
# Not needed for example
# sh autogen/addConfig.sh
echo "skipped running autogen/addConfig.sh as this is an example."

bin/genenv -p example/config.php example/envRequired.php autogen/appEnv.php $environment

echo "Done. Generated files are in autogen directory"