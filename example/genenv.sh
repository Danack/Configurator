set -x

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

path_to_bin="${DIR}/../bin"



environment=dev,uxtesting

result=`"${path_to_bin}/genenv" -p "${DIR}/environment/config.php" "${DIR}/environment/envRequired.php" env.php "${environment}"`



