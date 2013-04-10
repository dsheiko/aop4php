<?php
/*
 * @package Aspect-Oriented Programming PHP API
 * @author sheiko
 * @license MIT
 * @copyright (c) Dmitry Sheiko http://www.dsheiko.com
 */
namespace Lib\Aop\Dto;


class Trace
{
    public $file = "";
    public $line = 0;
    public $function = "";
    public $class = "";
    public $args = array();


    public function __construct(array $data = array()) 
    {
        if (!$data) {
            return;
        }
        
        foreach ($data as $key => $val) {
            isset ($this->$key) && $this->$key = $val;
        }
    }
    
    public function getHash()
    {
        return $this->class . "::" . $this->function;
    }
}