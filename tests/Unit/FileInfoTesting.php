<?php
use PHPUnit\Framework\TestCase;
use App\Repository\DeviceRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Contracts\Foundation\Application;


final class FileInfoTesting extends TestCase
{
    public function testValidControllerPath()
    {
        $controllerPath = 'app/Http/Controllers';
        $actualControllerPath = realpath(__DIR__.'/../../'.$controllerPath);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__.'/../../app')
        );
        $regexIterator = new RegexIterator($iterator, '/^.+Controller\.php$/i', RegexIterator::MATCH);

        foreach ($regexIterator as $file) {
            $expectedControllerPath = dirname(realpath($file));
            $this->assertEquals($expectedControllerPath, $actualControllerPath);
        }
    }

    public function testValidRepositoryPath()
    {
        $repositoryPath = 'app/Repository';
        $actualRepositoryPath = realpath(__DIR__.'/../../'.$repositoryPath);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__.'/../../app')
        );
        $regexIterator = new RegexIterator($iterator, '/^.+Repository\.php$/i', RegexIterator::MATCH);

        foreach ($regexIterator as $file) {
            $expectedRepositoryPath = dirname(realpath($file));
            $this->assertEquals($expectedRepositoryPath, $actualRepositoryPath);
        }
    }

    public function testValidControllerFileName() {
        $actualControllerPath = realpath(__DIR__.'/../../app/Http/Controllers');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($actualControllerPath)
        );

        $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i',
            RegexIterator::MATCH);

        foreach ($regexIterator as $file) {
            $namespace = getNameSpace(token_get_all(file_get_contents($file)));
            $className = $namespace .'\\'.basename($file, '.php');
            
            try {
                include_once($file);
                $classObject = (new ReflectionClass($className))->newInstanceWithoutConstructor(); //That's it!
                if ($classObject instanceof App\Http\Controllers\Controller) {
                    $this->assertTrue(true, "class is instance of ...");
                } else {
                    $this->assertTrue(false, "class is not instance of ...");
                }
            } catch (Exception $e) {
                $this->assertTrue(false, "Controller name is not Classname!");
            }
        }
    }

    public function testValidRepositoryFileName() {
        $actualRepositoryPath = realpath(__DIR__.'/../../app/Repository');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($actualRepositoryPath)
        );

        $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i',
            RegexIterator::MATCH);

        foreach ($regexIterator as $file) {
            $namespace = getNameSpace(token_get_all(file_get_contents($file)));
            $className = $namespace .'\\'.basename($file, '.php');
            
            try {
                include_once($file);
                $classObject = (new ReflectionClass($className))->newInstanceWithoutConstructor(); //That's it!
                if ($classObject instanceof App\Repository\BaseRepository) {
                    $this->assertTrue(true, "class is instance of ...");
                } else {
                    $this->assertTrue(false, "class is not instance of ...");
                }
            } catch (Exception $e) {
                $this->assertTrue(false, "Repository name is not Classname!");
            }
        }
    }
}

function getNameSpace($tokens) {
    for ($i = 0; $i < count($tokens); $i++) {
        if (is_array($tokens[$i]) && $tokens[$i][1] == "namespace") {
            while (++$i < count($tokens) && token_name($tokens[$i][0]) == 'T_WHITESPACE');
            return ($i < count($tokens)) ? $tokens[$i][1]: null;
        }
    }
}

