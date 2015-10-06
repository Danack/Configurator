
set -x

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

path_to_fpmconv="${DIR}/../../bin"

result=`"${path_to_fpmconv}/fpmconv" "${DIR}/example.php.ini" "${DIR}/example.phpfpm.ini"`