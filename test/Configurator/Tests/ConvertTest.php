<?php

use Configurator\TestBase\BaseTestCase;
use Configurator\Configurator;
use org\bovigo\vfs\vfsStream;


class ConvertTest extends BaseTestCase {

    function testJsonSourceData() {
        $command = "configurate -j test/fixtures/data/config.json,test/fixtures/data/empty.json  test/fixtures/input/my.cnf.php test/fixtures/output/my.testfromjson.cnf amazonec2 ";
        $this->runCommand($command);
    }

    
    function testMixedSourceData() {
        $command = "configurate -p test/fixtures/data/config.php -j test/fixtures/data/empty.json test/fixtures/input/site.ini.php test/fixtures/output/site.generated.ini amazonec2";
        $this->runCommand($command);
        $contents = file_get_contents('test/fixtures/output/site.generated.ini');
        $this->assertContains('memory_limit=16M', $contents);

        $command = "configurate -p test/fixtures/data/config.php -j test/fixtures/data/empty.json,test/fixtures/data/memory256.json test/fixtures/input/site.ini.php  test/fixtures/output/site.generated.ini amazonec2";
        $this->runCommand($command);
        $contents = file_get_contents('test/fixtures/output/site.generated.ini');
        $this->assertContains('memory_limit=256M', $contents);
    }

    function testConvertIniToFPM() {
        $command = "fpmconv test/fixtures/input/site.ini test/fixtures/output/site.phpfpm.ini";
        $this->runCommand($command);
        $result = parse_ini_file("test/fixtures/output/site.phpfpm.ini");
        $this->assertArrayHasKey('php_admin_value', $result, "Failed to put values into 'php_admin_value' array.");
    }
    
    function runCommand($command) {
        $returnValue = null;
        $output = [];
        chdir(__DIR__.'/../../../');

        exec("php ./bin/".$command, $output, $returnValue);
        $this->assertEquals(0, $returnValue, "Conversion returned non zero value.");
    }

    
    function testAsMethods() {
        vfsStream::setup('exampleDir');
        $path = vfsStream::url("exampleDir/site.ini");


        $configurator = new Configurator();
        $configurator-> run(
            'test/fixtures/input/site.ini.php',
            $path,
            'amazonec2',
            'test/fixtures/data/empty.json',
            'test/fixtures/data/config.php'
        );

        $contents = file_get_contents($path);
        $this->assertContains('memory_limit=16M', $contents);
        
        
        
        $configurator = new Configurator();
        $configurator-> run(
            'test/fixtures/input/site.ini.php',
            $path,
            'amazonec2',
            'test/fixtures/data/empty.json,test/fixtures/data/memory256.json',
            'test/fixtures/data/config.php'
        );
        
        $contents = file_get_contents($path);
        $this->assertContains('memory_limit=256M', $contents);
     }
    
}