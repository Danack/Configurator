<?php

namespace ConfiguratorTests;

use Configurator\TestBase\BaseTestCase;
use Configurator\Configurator;
use Configurator\Writer\TestWriter;
use org\bovigo\vfs\vfsStream;

class ConvertTest extends BaseTestCase
{
    /**
     * @group yaml
     */
    public function testConfigYamlTemplateFile()
    {
        vfsStream::setup('exampleDir');
        $writer = new TestWriter();

        $configurator = new Configurator(
            $writer,
            'amazonec2',
            ['phpunit'],
            '',
            '',
            'test/fixtures/data/config.yaml'
        );
        
        $outputFilename = 'site.ini.php';

        $configurator->writeConfigFile('test/fixtures/input/site.ini.php', $outputFilename);
        $contents = $writer->getDataForFile($outputFilename);
        $this->assertContains('memory_limit=16M', $contents);
    }
    
    
    public function testJsonSourceData()
    {
        $pathToFixturesDir = realpath(dirname(__FILE__)."/../../fixtures");

        $command = "configurate -j $pathToFixturesDir/data/config.json,$pathToFixturesDir/data/empty.json  $pathToFixturesDir/input/my.cnf.php $pathToFixturesDir/output/my.testfromjson.cnf amazonec2 ";
        $this->runCommand($command);
    }
    
    public function testYamlSourceData()
    {
        $pathToFixturesDir = realpath(dirname(__FILE__)."/../../fixtures");
        $outputFilename = "$pathToFixturesDir/output/my.testfromyaml.cnf";
        $command = "configurate -y $pathToFixturesDir/data/config.yaml $pathToFixturesDir/input/my.cnf.php $outputFilename amazonec2 ";
        $this->runCommand($command);
        
        $contents = file_get_contents($outputFilename);
        $this->assertContains('default-character-set=utf8mb4', $contents);
    }

    
    public function testMixedSourceData()
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

    
    public function testGenEnvCli()
    {
        $outputFilename = "test/fixtures/output/env.php";
        $command = "genenv -p test/fixtures/data/config.php --namespace FooBar test/fixtures/input/envRequired.php $outputFilename amazonec2";

        $this->runCommand($command);
        $contents = file_get_contents($outputFilename);

        $contents = substr($contents, strlen("<?php"));
        eval($contents);
        
        $this->assertTrue(function_exists('FooBar\getAppEnv'), 'Failed to import function.');
        $env = \FooBar\getAppEnv();
        $this->assertArrayHasKey('cache_setting', $env);
        $this->assertEquals('cache_time', $env['cache_setting']);
    }
    
    
    
    public function testConvertIniToFPM()
    {
        $command = "fpmconv test/fixtures/input/site.ini test/fixtures/output/site.phpfpm.ini";
        $this->runCommand($command);
        $result = parse_ini_file("test/fixtures/output/site.phpfpm.ini");
        $this->assertArrayHasKey('php_admin_value', $result, "Failed to put values into 'php_admin_value' array.");
    }
    
    public function runCommand($command)
    {
        $returnValue = null;
        $output = [];
        chdir(__DIR__.'/../../../');

        exec("php ./bin/".$command, $output, $returnValue);
        $outputString = implode("\n", $output);
        $this->assertEquals(0, $returnValue, "Conversion returned non zero value, output was: ".$outputString);
    }

    public function testConfigTemplateFile()
    {
        $path = "output/site.ini";
        $writer = new TestWriter();
        $configurator = new Configurator(
            $writer,
            'amazonec2',
            ['phpunit'],
            'test/fixtures/data/empty.json',
            'test/fixtures/data/config.php'
        );

        $configurator->writeConfigFile('test/fixtures/input/site.ini.php', $path);
        $contents = $writer->getDataForFile($path);
        $this->assertContains('memory_limit=16M', $contents);

        $writer = new TestWriter();
        $configurator = new Configurator(
            $writer,
            'amazonec2',
            ['phpunit'],
            'test/fixtures/data/empty.json,test/fixtures/data/memory256.json',
            'test/fixtures/data/config.php'
        );
                    
        $configurator->writeConfigFile('test/fixtures/input/site.ini.php', $path);
        $contents = $writer->getDataForFile($path);
        $this->assertContains('memory_limit=256M', $contents);
    }

    public function testGenerateEnvFile()
    {
        $writer = new TestWriter();
        $configurator = new Configurator(
            $writer,
            'amazonec2',
            ['phpunit'],
            'test/fixtures/data/empty.json',
            'test/fixtures/data/config.php'
        );
        
        $namespace = "test12345";
        
        $outputFilename = 'test//env.php';

        $configurator->writeEnvironmentFile(
            'test/fixtures/input/envRequired.php',
            $outputFilename,
            $namespace
        );

        $contents = $writer->getDataForFile($outputFilename);

        if (strpos($contents, "<?php") !== 0) {
            $this->fail("Generated code does not start with '<?php'.\n");
            return;
        }
        
        $contents = substr($contents, strlen("<?php"));
        eval($contents);

        if (function_exists('test12345\getAppEnv') === false) {
            $this->fail("Function test12345\\getAppEnv was not in generated code.\n");
            return;
        }

        $vars = \test12345\getAppEnv();
        $this->assertArrayHasKey('cache_setting', $vars);
        $this->assertEquals('cache_time', $vars['cache_setting']);
    }
}
