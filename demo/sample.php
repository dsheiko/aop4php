<?php
/*
 * This package can be used to implement Aspect Oriented Programming (AOP,
 * http://en.wikipedia.org/wiki/Aspect-oriented_programming) by executing
 * the code of classes that enable orthogonal aspects at run-time.
 *
 * The intention is to provide a means implement orthogonal aspects in separate
 * classes that may be interesting add to the application, like logging, caching,
 * transaction control, etc., without affecting the main business logic.
 * The package provides base classes for implementing defining point cuts where
 * the code of advice class is called to implement actions of the orthogonal aspects
 * that an application may need to enable.
 *
 * @package Aspect-Oriented Programming PHP API
 * @author sheiko
 * @license MIT
 * @copyright (c) Dmitry Sheiko http://www.dsheiko.com
 */

include_once "../src/Lib/Aop/Aspect.php";

/**
 * Example class
 */
class Foo
{
    public function bar1()
    {
        // Subscribe for entry point events
        \Lib\Aop\Advice::before("arg1", "arg2");
        echo "Some business logic in Foo::bar1", PHP_EOL;
        // Subscribe for exit point events
        \Lib\Aop\Advice::after("a return value");
        return $this;
    }
    public function bar2()
    {
        // Subscribe for entry point events
        \Lib\Aop\Advice::before("arg1");
        // Subscribe for custom events
        \Lib\Aop\Advice::event("onOpen", "arg");
        echo "Some business logic in Foo::bar2", PHP_EOL;
        return $this;
    }
}

/** Usage */

$aspectA = new \Lib\Aop\Aspect();

$pcA = $aspectA->pointcut("call Foo::bar1 or call *::bar2");
// Pointcut's entry point handler
$pcA->before(function($trace, $arg1 = null, $arg2 = null)
{
    echo "AspectA, PointcutA, entry point of ", $trace->class, "::",
        $trace->function, " with passed arguments ", $arg1, ", ", $arg2, PHP_EOL;
});
// Pointcut's exit point handler
$pcA->after(function($trace, $arg)
{
    echo "AspectA, PointcutA, exit point of ", $trace->class, "::",
        $trace->function, " with passed argument ", $arg, PHP_EOL;
});

$pcB = $aspectA->pointcut("call Foo::bar2");
// Pointcut's exit point handler
$pcA->after(function($trace, $arg)
{
    echo "AspectA, PointcutB, exit point of ", $trace->class, "::",
        $trace->function, " with passed argument ", $arg, PHP_EOL;
});
// Pointcut's custom event handler
$pcA->event("onOpen", function($trace, $arg)
{
    echo "AspectA, PointcutB, custom event point of ", $trace->class, "::",
        $trace->function, " with passed argument ", $arg, PHP_EOL;
});

$aspectA->apply();

$foo = new Foo();
$foo->bar1()->bar2();

/** Output */

// AspectA, PointcutA, entry point of Foo::bar1 with passed arguments arg1, arg2
// Some business logic in Foo::bar1
// AspectA, PointcutA, exit point of Foo::bar1 with passed argument a return value
// AspectA, PointcutB, exit point of Foo::bar1 with passed argument a return value
// AspectA, PointcutA, entry point of Foo::bar2 with passed arguments arg1,
// AspectA, PointcutB, custom event point of Foo::bar2 with passed argument arg
// Some business logic in Foo::bar2
