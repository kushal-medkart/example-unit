<?php
require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Repository\DeviceRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverser;



final class FileInfoTesting extends TestCase
{
///
/// Validate Controller Path
/// 
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

///
/// Validate Repository Path
/// 
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


/// 
/// Check for existence of class and also check for whether
/// Controller extends ApiController
/// 
    public function testValidControllerFileName() {
        $actualControllerPath = realpath(__DIR__.'/../../app/Http/Controllers');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($actualControllerPath)
        );

        $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i',
            RegexIterator::MATCH);

        // loop through file
        foreach ($regexIterator as $file) {
            $namespace = NameSpaceExtractor::getNameSpace($file);
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

/// 
/// Check for existence of class and also check for whether
/// repository extends BaseRepository
/// 
    public function testValidRepositoryFileName() {
        $actualRepositoryPath = realpath(__DIR__.'/../../app/Repository');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($actualRepositoryPath)
        );

        $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i',
            RegexIterator::MATCH);

        foreach ($regexIterator as $file) {
            $namespace = NameSpaceExtractor::getNameSpace($file);
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

///
/// returns are only allowed through $this->catch method
/// in controller
///

    public function testCheckReturnValue() {
        $actualControllerPath = realpath(__DIR__.'/../../app/Http/Controllers');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($actualControllerPath)
        );

        $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i',
            RegexIterator::MATCH);

        foreach ($regexIterator as $file) {
            $parserFactory = new ParserFactory();
            $parser = $parserFactory->createForNewestSupportedVersion();
            $traverser = new NodeTraverser;
            $checker = new ReturnValueChecker(basename($file, '.php'), "catch");
            $traverser->addVisitor($checker);

            try {
                $ast = $parser->parse(file_get_contents($file));
                $traverser->traverse($ast);
                $invalidMethods = $checker->getInvalidMethods();
                if (count($invalidMethods) > 0)
                {
                    var_dump($invalidMethods);
                    $this->assertTrue(false, '');
                }
                $this->assertTrue(true, "All Methods are valid");
            } catch (Error $e) {
                echo 'Parse error: ', $e->getMessage();
                return [];
            }
        }
    }
}

class ReturnValueChecker extends NodeVisitorAbstract
{
    private $className;
    private $customMethodName;
    private $invalidMethods = [];

    public function __construct($className, $customMethodName)
    {
        $this->className = $className;
        $this->customMethodName = $customMethodName;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->className = $node->name->toString();
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            $methodName = $node->name->toString();
            if (!$this->checkReturnStatements($node->stmts)) {
                if ($methodName != $this->customMethodName)
                    $this->invalidMethods[] = "$this->className::$methodName";
            }
        }
    }

///
/// if source code had return statement in function block
/// then it must only return through $this->catch method i.e. return $this->catch; 
/// or return nothing i.e. return;
/// 
    private function checkReturnStatements(array $statements)
    {
        $success = true;
        foreach ($statements as $stmt) {
            if ($stmt instanceof Node\Stmt\Return_) {
                if  ($stmt->expr instanceof Node\Expr\MethodCall && 
                    $stmt->expr->name instanceof Node\Identifier && 
                    $stmt->expr->name->name === $this->customMethodName) {
                        // do nothing
                }
                else if ($stmt->expr == NULL) {
                    // do nothing
                }
                else {
                    $success = false;
                }
            }
        }
        return $success;
    }

    public function getInvalidMethods()
    {
        return $this->invalidMethods;
    }
}

///
/// Extract NameSpace by parsing source code in 
/// Abstract Syntax Tree
/// 
class NameSpaceExtractor extends NodeVisitorAbstract {
    public $namespace;
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = $node->name->toString();
        }
    }

    public static function getNameSpace($file) {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->createForNewestSupportedVersion();
        $traverser = new NodeTraverser;
        $checker = new NameSpaceExtractor;
        $traverser->addVisitor($checker);

        try {
            $ast = $parser->parse(file_get_contents($file));
            $traverser->traverse($ast);

            return $checker->namespace;
        } catch (Error $e) {
            echo 'Parse error: ', $e->getMessage();
            return null;
        }
    }
}
