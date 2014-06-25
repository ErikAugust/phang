<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Tests;

use Psr\Log\LogLevel;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\Exception\ContextErrorException;

/**
 * ErrorHandlerTest
 *
 * @author Robert Schönthal <seroscho@googlemail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int Error reporting level before running tests.
     */
    protected $errorReporting;

    /**
     * @var string Display errors setting before running tests.
     */
    protected $displayErrors;

    public function setUp()
    {
        $this->errorReporting = error_reporting(E_ALL | E_STRICT);
        $this->displayErrors = ini_get('display_errors');
        ini_set('display_errors', '1');
    }

    public function tearDown()
    {
        ini_set('display_errors', $this->displayErrors);
        error_reporting($this->errorReporting);
    }

    public function testNotice()
    {
        ErrorHandler::register();

        try {
            self::triggerNotice($this);
            $this->fail('ContextErrorException expected');
        } catch (ContextErrorException $exception) {
            // if an exception is thrown, the test passed
            restore_error_handler();
            restore_exception_handler();

            $this->assertEquals(E_NOTICE, $exception->getSeverity());
            $this->assertEquals(__FILE__, $exception->getFile());
            $this->assertRegexp('/^Notice: Undefined variable: (foo|bar)/', $exception->getMessage());
            $this->assertArrayHasKey('foobar', $exception->getContext());

            $trace = $exception->getTrace();
            $this->assertEquals(__FILE__, $trace[0]['file']);
            $this->assertEquals('Symfony\Component\Debug\ErrorHandler', $trace[0]['class']);
            $this->assertEquals('handleError', $trace[0]['function']);
            $this->assertEquals('->', $trace[0]['type']);

            $this->assertEquals(__FILE__, $trace[1]['file']);
            $this->assertEquals(__CLASS__, $trace[1]['class']);
            $this->assertEquals('triggerNotice', $trace[1]['function']);
            $this->assertEquals('::', $trace[1]['type']);

            $this->assertEquals(__FILE__, $trace[1]['file']);
            $this->assertEquals(__CLASS__, $trace[2]['class']);
            $this->assertEquals(__FUNCTION__, $trace[2]['function']);
            $this->assertEquals('->', $trace[2]['type']);
        } catch (\Exception $e) {
            restore_error_handler();
            restore_exception_handler();

            throw $e;
        }
    }

    // dummy function to test trace in error handler.
    private static function triggerNotice($that)
    {
        // dummy variable to check for in error handler.
        $foobar = 123;
        $that->assertSame('', $foo.$foo.$bar);
    }

    public function testConstruct()
    {
        try {
            $this->assertEquals(3 | E_RECOVERABLE_ERROR | E_USER_ERROR, ErrorHandler::register(3)->throwAt(0));

            restore_error_handler();
            restore_exception_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            restore_exception_handler();

            throw $e;
        }
    }

    public function testDefaultLogger()
    {
        try {
            $handler = ErrorHandler::register();

            $logger = $this->getMock('Psr\Log\LoggerInterface');

            $handler->setDefaultLogger($logger, E_NOTICE);
            $handler->setDefaultLogger($logger, array(E_USER_NOTICE => LogLevel::CRITICAL));

            $loggers = array(
                E_DEPRECATED        => array(null, LogLevel::INFO),
                E_USER_DEPRECATED   => array(null, LogLevel::INFO),
                E_NOTICE            => array($logger, LogLevel::NOTICE),
                E_USER_NOTICE       => array($logger, LogLevel::CRITICAL),
                E_STRICT            => array(null, LogLevel::NOTICE),
                E_WARNING           => array(null, LogLevel::WARNING),
                E_USER_WARNING      => array(null, LogLevel::WARNING),
                E_COMPILE_WARNING   => array(null, LogLevel::WARNING),
                E_CORE_WARNING      => array(null, LogLevel::WARNING),
                E_USER_ERROR        => array(null, LogLevel::ERROR),
                E_RECOVERABLE_ERROR => array(null, LogLevel::ERROR),
                E_COMPILE_ERROR     => array(null, LogLevel::EMERGENCY),
                E_PARSE             => array(null, LogLevel::EMERGENCY),
                E_ERROR             => array(null, LogLevel::EMERGENCY),
                E_CORE_ERROR        => array(null, LogLevel::EMERGENCY),
            );
            $this->assertSame($loggers, $handler->setLoggers(array()));

            restore_error_handler();
            restore_exception_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            restore_exception_handler();

            throw $e;
        }
    }

    public function testHandleError()
    {
        try {
            $handler = ErrorHandler::register(0);
            $this->assertFalse($handler->handleError(0, 'foo', 'foo.php', 12, array()));

            restore_error_handler();
            restore_exception_handler();

            $handler = ErrorHandler::register(3);
            $this->assertFalse($handler->handleError(4, 'foo', 'foo.php', 12, array()));

            restore_error_handler();
            restore_exception_handler();

            $handler = ErrorHandler::register(3);
            try {
                $handler->handleError(4, 'foo', 'foo.php', 12, array());
            } catch (\ErrorException $e) {
                $this->assertSame('Parse Error: foo', $e->getMessage());
                $this->assertSame(4, $e->getSeverity());
                $this->assertSame('foo.php', $e->getFile());
                $this->assertSame(12, $e->getLine());
            }

            restore_error_handler();
            restore_exception_handler();

            $handler = ErrorHandler::register(E_USER_DEPRECATED);
            $this->assertFalse($handler->handleError(E_USER_DEPRECATED, 'foo', 'foo.php', 12, array()));

            restore_error_handler();
            restore_exception_handler();

            $handler = ErrorHandler::register(E_DEPRECATED);
            $this->assertFalse($handler->handleError(E_DEPRECATED, 'foo', 'foo.php', 12, array()));

            restore_error_handler();
            restore_exception_handler();

            $logger = $this->getMock('Psr\Log\LoggerInterface');

            $that = $this;
            $warnArgCheck = function ($logLevel, $message, $context) use ($that) {
                $that->assertEquals('info', $logLevel);
                $that->assertEquals('foo', $message);
                $that->assertArrayHasKey('type', $context);
                $that->assertEquals($context['type'], E_USER_DEPRECATED);
                $that->assertArrayHasKey('stack', $context);
                $that->assertInternalType('array', $context['stack']);
            };

            $logger
                ->expects($this->once())
                ->method('log')
                ->will($this->returnCallback($warnArgCheck))
            ;

            $handler = ErrorHandler::register(E_USER_DEPRECATED);
            $handler->setDefaultLogger($logger, E_USER_DEPRECATED);
            $this->assertTrue($handler->handleError(E_USER_DEPRECATED, 'foo', 'foo.php', 12, array()));

            restore_error_handler();
            restore_exception_handler();

            $logger = $this->getMock('Psr\Log\LoggerInterface');

            $that = $this;
            $logArgCheck = function ($level, $message, $context) use ($that) {
                $that->assertEquals('Undefined variable: undefVar', $message);
                $that->assertArrayHasKey('type', $context);
                $that->assertEquals($context['type'], E_NOTICE);
            };

            $logger
                ->expects($this->once())
                ->method('log')
                ->will($this->returnCallback($logArgCheck))
            ;

            $handler = ErrorHandler::register(E_NOTICE);
            $handler->setDefaultLogger($logger, E_NOTICE);
            $handler->screamAt(E_NOTICE);
            unset($undefVar);
            @$undefVar++;

            restore_error_handler();
            restore_exception_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            restore_exception_handler();

            throw $e;
        }
    }

    public function testHandleException()
    {
        try {
            $handler = ErrorHandler::register();

            $exception = new \Exception('foo');

            $logger = $this->getMock('Psr\Log\LoggerInterface');

            $that = $this;
            $logArgCheck = function ($level, $message, $context) use ($that) {
                $that->assertEquals('Uncaught Exception: foo', $message);
                $that->assertArrayHasKey('type', $context);
                $that->assertEquals($context['type'], E_ERROR);
            };

            $logger
                ->expects($this->exactly(2))
                ->method('log')
                ->will($this->returnCallback($logArgCheck))
            ;

            $handler->setDefaultLogger($logger, E_ERROR);

            try {
                $handler->handleException($exception);
                $this->fail('Exception expected');
            } catch (\Exception $e) {
                $this->assertSame($exception, $e);
            }

            $that = $this;
            $handler->setExceptionHandler(function ($e) use ($exception, $that) {
                $that->assertSame($exception, $e);
            });

            $handler->handleException($exception);

            restore_error_handler();
            restore_exception_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            restore_exception_handler();

            throw $e;
        }
    }

    public function testHandleFatalError()
    {
        try {
            $handler = ErrorHandler::register();

            $error = array(
                'type' => E_PARSE,
                'message' => 'foo',
                'file' => 'bar',
                'line' => 123,
            );

            $logger = $this->getMock('Psr\Log\LoggerInterface');

            $that = $this;
            $logArgCheck = function ($level, $message, $context) use ($that) {
                $that->assertEquals('Fatal Parse Error: foo', $message);
                $that->assertArrayHasKey('type', $context);
                $that->assertEquals($context['type'], E_ERROR);
            };

            $logger
                ->expects($this->once())
                ->method('log')
                ->will($this->returnCallback($logArgCheck))
            ;

            $handler->setDefaultLogger($logger, E_ERROR);

            $handler->handleFatalError($error);

            restore_error_handler();
            restore_exception_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            restore_exception_handler();

            throw $e;
        }
    }

    public function testDeprecated()
    {
        try {
            $handler = ErrorHandler::register(0);
            $this->assertFalse($handler->handle(0, 'foo', 'foo.php', 12, array()));

            restore_error_handler();
            restore_exception_handler();

            $logger = $this->getMock('Psr\Log\LoggerInterface');

            $that = $this;
            $logArgCheck = function ($level, $message, $context) use ($that) {
                $that->assertEquals('Undefined variable: undefVar', $message);
                $that->assertArrayHasKey('type', $context);
                $that->assertEquals($context['type'], E_NOTICE);
            };

            $logger
                ->expects($this->once())
                ->method('log')
                ->will($this->returnCallback($logArgCheck))
            ;

            $handler = ErrorHandler::register(E_NOTICE);
            $handler->setLogger($logger, 'scream');
            unset($undefVar);
            @$undefVar++;

            restore_error_handler();
            restore_exception_handler();
        } catch (\Exception $e) {
            restore_error_handler();
            restore_exception_handler();

            throw $e;
        }
    }
}
