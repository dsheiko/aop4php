<?php
/*
 * @package Aspect-Oriented Programming PHP API
 * @author sheiko
 * @license MIT
 * @copyright (c) Dmitry Sheiko http://www.dsheiko.com
 */
namespace Lib\Aop;

include_once __DIR__ . "/Aspect.php";
include_once __DIR__ . "/Advice.php";
include_once __DIR__ . "/Exception.php";
/**
 * Pointcut class, which declare an pointcut of the aspect (http://en.wikipedia.org/wiki/Advice_in_aspect-oriented_programming)
 */
class Pointcut
{
    static private $_enabled = array();
    private $_callers = array();
    private $_before = array();
    private $_after = array();
    private $_event = array();

    public function __construct($binding)
    {
        if (empty($binding)) {
            throw new Exception("The argument with binding instructions must not be empty");
        }

        $binding = explode(" or ", $binding);
        $binding = array_map(function($item){
            return trim(preg_replace("/\scall\s/", "", " " . $item));
        }, $binding);
        $this->parse($binding);
    }
    public function parse(array $binding)
    {
        foreach ($binding as $point) {
            list($class, $method) = explode("::", $point);
            if (empty($method)) {
                throw new Exception("Binding instructions do not contain method name");
            }
            $this->_callers[] = array(
                "class" => trim($class),
                "method" => trim($method)
            );
        }
    }
    public function before($fn)
    {
        $this->_before[] = $fn;
    }
    public function after($fn)
    {
        $this->_after[] = $fn;
    }
    public function event($event, $fn)
    {
        $this->_event[$event] = $fn;
    }
    public function apply()
    {
        foreach ($this->_callers as $caller) {
            static::$_enabled[] = array(
                "before" => $this->_before,
                "after" => $this->_after,
                "event" => $this->_event,
                "callers" => $caller,
            );
        }
    }
    public function getEnabled()
    {
        return static::$_enabled;
    }
}