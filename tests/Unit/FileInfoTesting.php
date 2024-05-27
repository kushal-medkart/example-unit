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

    public function testValidFileName() {
        $actualControllerPath = realpath(__DIR__.'/../../app/Http/Controllers');


        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($actualControllerPath)
        );

        $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i',
            RegexIterator::MATCH);

        $fTokens = ['T_CLASS', 'T_WHITESPACE', 'T_STRING'];
        foreach ($regexIterator as $file) {
            $Compiler = new CompilerCheck($file);
            $namespace = $Compiler->getNameSpace();
            $className = $namespace .'\\'.basename($file, '.php');
        
            
            try {
                include_once($file);
                $classObject = new $className;
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
}


class CompilerCheck {
    function __construct($filename) {
        $this->tokens = token_get_all(file_get_contents($filename));
        $this->filename = $filename;
    }

    function HasValidClassName($fTokens) {
        for ($i = 0; $i < count($this->tokens); $i++) {
            /// The Design in It self is so complex
            if (is_array($this->tokens[$i]) && token_name($this->tokens[$i][0]) == $fTokens[0]) {

                for ($j = $i+1; $j < count($this->tokens); $j++)
                {
                    // break on invalid code
                    // continue on Whitespace
                    if (!is_array($this->tokens[$j])) { break; }
                    else if (token_name($this->tokens[$j][0]) == $fTokens[1]) { continue; }
                    else { 
                        if(token_name($this->tokens[$j][0]) == $fTokens[2] && 
                            basename($this->filename, '.php') == $this->tokens[$j][1]) 
                            return true;
                        break;
                    }
                }
            } 
        }
        return false;
    }

    function getNameSpace() {
        for ($i = 0; $i < count($this->tokens); $i++) {
            if (is_array($this->tokens[$i]) && $this->tokens[$i][1] == "namespace") {
                while (++$i < count($this->tokens) && token_name($this->tokens[$i][0]) == 'T_WHITESPACE');
                return ($i < count($this->tokens)) ? $this->tokens[$i][1]: null;
            }
        }
    }
}