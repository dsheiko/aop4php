<?php
/*
 * @package Aspect-Oriented Programming PHP API
 * @author sheiko
 * @license MIT
 * @copyright (c) Dmitry Sheiko http://www.dsheiko.com
 */
namespace Lib\Aop;

include_once __DIR__ . "/Advice.php";
include_once __DIR__ . "/Pointcut.php";
include_once __DIR__ . "/Exception.php";
/**
 * Aspect class, which declare an aspect
 * @package Aspect-Oriented Programming Library
 * @author $Author: sheiko $
 */
class Aspect
{
    private $_pointcuts = array();
    /**
     * Add a set of join points
     * @param string $binding binding statement (e.g. "call classA::MethodName1 or call *::MethodName2")
     * @return Pointcut
     */
    public function pointcut($binding)
    {
        $this->_pointcuts[] = $pointcut = new Pointcut($binding);
        return $pointcut;
    }

    public function apply()
    {
        array_walk($this->_pointcuts, function($pointcut)
        {
            $pointcut->apply();
        });
    }
}