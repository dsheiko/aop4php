<?php
/*
 * @package Aspect-Oriented Programming PHP API
 * @author sheiko
 * @license MIT
 * @copyright (c) Dmitry Sheiko http://www.dsheiko.com
 */
namespace Lib\Aop;

include_once __DIR__ . "/Aspect.php";
include_once __DIR__ . "/Pointcut.php";
include_once __DIR__ . "/Exception.php";
include_once __DIR__ . "/Dto/Trace.php";
/**
 * Advice service function package (http://en.wikipedia.org/wiki/Advice_in_aspect-oriented_programming)
 * @package Aspect-Oriented Programming Library
 * @author $Author: sheiko $
 */
class Advice
{

    /**
     * Parent method name getting
     * @return boolean
     */
    static private function _getCallerInfo() {
        $backtrace = debug_backtrace();
        return new Dto\Trace($backtrace[2]);
    }

    static private function _invokeCrosscuttingConcerns($point, 
        Dto\Trace $trace, array $args, $event = null)
    {
        // Make the first argument Dto\Trace
        array_unshift($args, $trace);        
        if ($enabled = Pointcut::getEnabled()) {
            foreach ($enabled as $pointcut) {
                $caller = $pointcut["callers"];
                if (($caller["class"] === $trace->class ||
                    $caller["class"] === "*") &&
                    $caller["method"] === $trace->function) {
                        array_walk($pointcut[$point], function($fn, $ev) use ($args, $event)
                        {
                            if (!$event) {
                                call_user_func_array($fn, $args);
                            } elseif($event === $ev) {
                                call_user_func_array($fn, $args);
                            }
                        });
                }
                
            }
        }
    }
    /**
     * Invoke functions subscribed for entry point
     * @param mixed $arg1
     * @param mixed $argN
     * @return void
     */
    static public function before()
    {
        $args = func_get_args();
        $trace = static::_getCallerInfo();
        static::_invokeCrosscuttingConcerns("before", $trace, $args);
    }
    /**
     * Invoke functions subscribed for exit point
     * @param mixed $arg1
     * @param mixed $argN
     * @return void
     */
    static public function after()
    {
        $args = func_get_args();
        $trace = static::_getCallerInfo();
        static::_invokeCrosscuttingConcerns("after", $trace, $args);
    }
     /**
     * Invoke functions subscribed for specific point
     * @param string $event
     * @param mixed $arg1
     * @param mixed $argN
     * @return void
     */
    static public function event()
    {
        $args = func_get_args();
        $event = array_shift($args);
        $trace = static::_getCallerInfo();
        static::_invokeCrosscuttingConcerns("event", $trace, $args, $event);
    }

}