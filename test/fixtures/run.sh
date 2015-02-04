../bin/configurate \
    -j data/deployConfig.json,data/empty.json \
    input/my.cnf.php output/my.testfromjson.cnf amazonec2
    
    
    
../bin/configurate \
    -p data/deployConfig.php -j data/empty.json \
    input/my.cnf.php output/my.testfromphp.cnf amazonec2

../bin/fpmconv input/site.php.ini output/site.phpfpm.ini 