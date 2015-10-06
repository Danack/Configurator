<?php

use Configurator\TestBase\BaseTestCase;
use Configurator\Configurator;
use org\bovigo\vfs\vfsStream;


class ConvertTest extends BaseTestCase {

    function testJsonSourceData()
    {
        $pathToFixturesDir = realpath(dirname(__FILE__)."/../../fixtures");

        
        $command = "configurate -j $pathToFixturesDir/data/config.json,$pathToFixturesDir/data/empty.json  $pathToFixturesDir/input/my.cnf.php $pathToFixturesDir/output/my.testfromjson.cnf amazonec2 ";
        $this->runCommand($command);
    }
    
    function testMixedSourceData()
    {
        $command = "configurate -p test/fixtures/data/config.php -j test/fixtures/data/empty.json test/fixtures/input/site.ini.php test/fixtures/output/site.generated.ini amazonec2";
        $this->runCommand($command);
        $contents = file_get_contents('test/fixtures/output/site.generated.ini');
        $this->assertContains('memory_limit=16M', $contents);

        $command = "configurate -p test/fixtures/data/config.php -j test/fixtures/data/empty.json,test/fixtures/data/memory256.json test/fixtures/input/site.ini.php  test/fixtures/output/site.generated.ini amazonec2";
        $this->runCommand($command);
        $contents = file_get_contents('test/fixtures/output/site.generated.ini');
        $this->assertContains('memory_limit=256M', $contents);
    }

    function testConvertIniToFPM()
    {
        $command = "fpmconv test/fixtures/input/site.ini test/fixtures/output/site.phpfpm.ini";
        $this->runCommand($command);
        $result = parse_ini_file("test/fixtures/output/site.phpfpm.ini");
        $this->assertArrayHasKey('php_admin_value', $result, "Failed to put values into 'php_admin_value' array.");
    }
    
    function runCommand($command)
    {
        $returnValue = null;
        $output = [];
        chdir(__DIR__.'/../../../');

        exec("php ./bin/".$command, $output, $returnValue);
        $outputString = implode("\n", $output);
        $this->assertEquals(0, $returnValue, "Conversion returned non zero value, output was: ".$outputString);
    }

    
    function testConfigTemplateFile()
    {
        vfsStream::setup('exampleDir');
        $path = vfsStream::url("exampleDir/site.ini");

        $configurator = new Configurator(
            'amazonec2',
            'test/fixtures/data/empty.json',
            'test/fixtures/data/config.php'
        );

        $configurator->writeConfigFile('test/fixtures/input/site.ini.php', $path);
        $contents = file_get_contents($path);
        $this->assertContains('memory_limit=16M', $contents);

        $configurator = new Configurator(
            'amazonec2',
            'test/fixtures/data/empty.json,test/fixtures/data/memory256.json',
            'test/fixtures/data/config.php'
        );
                    
        $configurator->writeConfigFile('test/fixtures/input/site.ini.php', $path);
        
        $contents = file_get_contents($path);
        $this->assertContains('memory_limit=256M', $contents);
     }
    
    function testGenerateEnvFile()
    {
        vfsStream::setup('exampleDir');
        $path = vfsStream::url("exampleDir/env.php");

        $configurator = new Configurator(
            'amazonec2',
            'test/fixtures/data/empty.json',
            'test/fixtures/data/config.php'
        );
        
        $namespace = "test12345";

        $configurator->writeEnvironmentFile(
            'test/fixtures/input/envRequired.php',
            $path,
            $namespace
        );

        $contents = file_get_contents($path);

        if (strpos($contents, "<?php") !== 0) {
            $this->fail("Generated code does not start with '<?php'.\n");
            return;
        }
        
        $contents = substr($contents, strlen("<?php"));
        eval($contents);

        if (function_exists('test12345\getAppEnv') == false) {
            $this->fail("Function test12345\\getAppEnv was not in generated code.\n");
            return;
        }

        $vars = \test12345\getAppEnv();
        $this->assertArrayHasKey('cache_setting', $vars);
        $this->assertEquals('cache_time', $vars['cache_setting']);
    }
}