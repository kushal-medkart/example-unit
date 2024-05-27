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
            $tokens = token_get_all(file_get_contents($file));

            // this pure custom code
            // only allowed by company way
            for ($i = 0; $i < count($tokens); $i++) {
                /// The Design in It self is so complex

                if (is_array($tokens[$i]) &&
                    is_array($tokens[$i+1]) &&
                    is_array($tokens[$i+2]) &&
                    token_name($tokens[$i][0]) == $fTokens[0] &&
                    token_name($tokens[$i+1][0]) == $fTokens[1] &&
                    token_name($tokens[$i+2][0]) == $fTokens[2] &&
                    basename($file, '.php') == $tokens[$i+2][1]
                ) {
                    $this->assertTrue(true, $file);
                }
            }
            $this->assertTrue(false, $file);
        }
    }
}


class Tokenisation {
    function __construct($filename) {
        $this->tokens = token_get_all($filename);
        $this->filename = $filename;
    }

    function Token() {
        for ($i = 0; $i < count($tokens); $i++) {
            /// The Design in It self is so complex
            if (is_array($tokens[$i]) && token_name($tokens[$i]) == $fTokens[0]) {
                while (++$i < count($tokens) )
                {
                    if (!is_array($tokens[$i][0])) { return false; }
                    else if (token_name($tokens[$i][0]) == $fTokens[1]) { continue; }
                    else { return token_name($tokens[$i][0]) == $fTokens[2] && basename($this->filename, '.php') == $tokens[$i][1] ? true : false; }
                }
            } else {
                return false;
            }
        }
    }
}