<?php
namespace Aura\Framework\Cli;
use Aura\Cli\ExceptionFactory;
use Aura\Cli\Getopt;
use Aura\Cli\Stdio;
use Aura\Cli\StdioResource;
use Aura\Cli\Vt100;
use Aura\Cli\Context;
use Aura\Cli\OptionFactory;
use Aura\Framework\Signal\Manager;
use Aura\Signal\HandlerFactory;
use Aura\Signal\ResultFactory;
use Aura\Signal\ResultCollection;
use Aura\Framework\Mock\System;
use Aura\Framework\Intl\Translator;
use Aura\Intl\BasicFormatter;

/**
 * Test class for Command.
 * Generated by PHPUnit on 2011-05-27 at 11:01:31.
 */
abstract class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $command_name;
    
    protected $stdio;
    
    protected $getopt;
    
    protected $system;
    
    protected $tmp_dir;
    
    protected $context;
    
    protected $signal;
    
    protected $outfile;
    
    protected $errfile;
    
    public function setUp()
    {
        $root = dirname(dirname(dirname(__DIR__)));
        $this->system = System::newInstance($root);
        $this->system->create();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->stdio);
        unlink($this->outfile);
        unlink($this->errfile);
        $this->system->remove();
    }
    
    protected function newCommand($argv = [])
    {
        $_SERVER['argv'] = $argv;
        
        $this->context = new Context($GLOBALS);
        
        $sub = "test/Aura.Framework/Cli/{$this->command_name}/Command";
        $this->tmp_dir =  $this->system->getTmpPath();
        
        // use files because we can't use php://memory in proc_open() calls
        $this->outfile = tempnam($this->tmp_dir, '');
        $this->errfile = tempnam($this->tmp_dir, '');
        
        $stdin = new StdioResource('php://stdin', 'r');
        $stdout = new StdioResource($this->outfile, 'w+');
        $stderr = new StdioResource($this->errfile, 'w+');
        $vt100 = new Vt100;
        
        $this->stdio = new Stdio($stdin, $stdout, $stderr, $vt100);
        
        $option_factory = new OptionFactory;
        $exception_factory = new ExceptionFactory(new Translator(
            'en_US',
            [],
            new BasicFormatter,
            null
        ));
        $this->getopt = new Getopt($option_factory, $exception_factory);
        
        $this->signal = new Manager(new HandlerFactory, new ResultFactory, new ResultCollection);
        
        $class = "\Aura\Framework\Cli\\{$this->command_name}\Command";
        $command = new $class(
            $this->context,
            $this->stdio,
            $this->getopt,
            $this->signal
        );
        return $command;
    }
}
