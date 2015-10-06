set -x

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

path_to_bin="${DIR}/../bin"

result=`"${path_to_bin}/fpmconv" "${DIR}/example.php.ini" "${DIR}/example.phpfpm.ini"`