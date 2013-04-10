<?php
/*
 * Sample of perfomance logging cross-cutting concern decomposition
 * @package Aspect-Oriented Programming PHP API
 * @author sheiko
 * @license MIT
 * @copyright (c) Dmitry Sheiko http://www.dsheiko.com
 */

include_once "../src/Lib/Aop/Aspect.php";

/**
 * Benchmark Logger
 */
class BmLogger
{
    static private $_log = array();
    /**
     * Store timestamp for the start of the process
     * @param \Lib\Aop\Dto\Trace $trace
     */
    static public function logStart(\Lib\Aop\Dto\Trace $trace)
    {
        $id = $trace->getHash();
        isset(static::$_log[$id]) ||
            static::$_log[$id] = array();
        static::$_log[$id]["tsStart"] = microtime(true);
    }
    /**
     * Store timestamp for the end of the process
     * @param \Lib\Aop\Dto\Trace $trace
     */
    static public function logEnd(\Lib\Aop\Dto\Trace $trace)
    {
        $id = $trace->getHash();
        isset(static::$_log[$id]) ||
            static::$_log[$id] = array();
        static::$_log[$id]["tsEnd"] = microtime(true);
    }
    /**
     * Display report information
     * @return void
     */
    static public function printReport() {
        if (!static::$_log) {
            return;
        }
        foreach (static::$_log as $function => $entry) {
            echo $function, ", execution time: ",
                (int)(($entry["tsEnd"] - $entry["tsStart"]) * 1000), "ms" , PHP_EOL;
        }
    }

}

/**
 * Example class
 */
class Foo
{
    /**
     * Method taking 20ms on run
     * @return \Foo self
     */
    public function bar1() {
        \Lib\Aop\Advice::before();
        usleep(20000);
        echo 'Some business logic of Sample' . PHP_EOL;
        \Lib\Aop\Advice::after();
        return $this;
    }
    /**
     * Method taking 30ms on run
     * @return \Foo self
     */
    public function bar2() {
        \Lib\Aop\Advice::before();
        usleep(30000);
        echo 'Some business logic of Sample2' . PHP_EOL;
        \Lib\Aop\Advice::after();
        return $this;
    }

}

/** Usage */

$perfAspect = new \Lib\Aop\Aspect();
$pc = $perfAspect->pointcut("call Foo::bar1  or call Foo::bar2");
$pc->before(function($trace)
{
    \BmLogger::logStart($trace);
});
$pc->after(function($trace)
{
    \BmLogger::logEnd($trace);
});
$perfAspect->apply();

$foo = new Foo();
$foo->bar1()->bar2();
echo "Perfomance log:", PHP_EOL;
\BmLogger::printReport();

/** Output */

// Some business logic of Sample
// Some business logic of Sample2
// Perfomance log:
// Foo::bar1, execution time: 20ms
// Foo::bar2, execution time: 30ms