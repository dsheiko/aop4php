# Aspect-Oriented Programming for PHP

* Version 2.0.0 - April 2013
* Version 1.2.0 - Febrary 2006

## Introducion

Aspect-oriented programming allows developers to organize cross-cutting concerns into individual declarations - aspects. It provides possibilities to define functionality for specified program execution points JoinPoints (method activation , class construction, access to a class field etc.). Languages that support aspect-oriented programming (AOP) more commonly employ functions for a set of points - Pointcut. The functionality at those points is determined by the program code which is politely referred to as Advice (AspectJ). Thus, aspects describe crosscutting concerns for a specific set of system components. The components themselves include only business logic that they are supposed to implement. During compilation of the program the components are associated with aspects (Weave).

To better grasp the basics of aspect-oriented programming, let us once again consider the example, which involves defining the productivity monitoring aspect. (see Figure 2). Suppose, we want to take the timer readings at entry and exit points of all the methods within such classes as Model, Document, Record and Dispatcher. So, we have to introduce into the Logging aspect a Pointcut with the listing of all the required functions. To cover all of the methods of a certain class, most AOP-supporting languages employ a special mask. Now it is possible to define the needed Pointcut. We set up Advice at the entry (Before) and exit (After) points to the methods listed in the Pointcut. Usually, Advice in AOP-supporting languages is available for such events as Before, After and Around, though sometimes other events are also present.

Thus, the following basic AOP declarations may be singled out:

* Aspect is a definition of a certain set of cross-cutting concerns performing a specific task;
* Pointcut is a code of an Aspect's applicability: defines when and where the functionality of a given aspect may be applied
* Advice is a code of an object's functionality: a direct functionality code for the specified events. In other words, this is what is going to be executed for the objects listed in the Pointcut.

Still too complicated? Well, I think everything will become clear once we introduce some practical examples. Let us begin with the simplest of them. I wrote this small library with a view to demonstrate both the advantages and availability of AOSD.

It is possible to define a certain aspect for crosscutting concerns (let's say, for keeping transaction log), through initiating the Aspect class:
```php
$aspect1 = new \Lib\Aop\Aspect();
```
Then we set up a Pointcut and specify the methods it affects:
```php
$pc1 = $aspect1->pointcut("call Sample::Sample  or call Sample::Sample2");
```
The only thing remaining is to specify the program code for entry and exit points for the methods of the current pointcut:
```php
$pc1->before(function()
{
    print 'Aspect1 preprocessor<br />';
});
$pc1->after(function()
{
    print 'Aspect1 postprocessor<br />';
});
```
In the same way we can define an additional aspect, for example:
```php
$aspect2 = new \Lib\Aop\Aspect();
$pc2 = $aspect2->pointcut("call *::Sample2");
$pc2->before(function()
{
    print 'Aspect2 preprocessor<br />';
});
$pc2->after(function()
{
    print 'Aspect1 postprocessor<br />';
});
```
In order to enable one or several aspects just use the apply method
```php
$aspect1->apply();
$aspect2->apply();
```
Further we need to specify the points of our concern using before, after and event static method of \Lib\Aop\Advice:
```php
class Sample
{
    public function sample()
	{
        \Lib\Aop\Advice::before();
        print 'Class initilization<br />';
        \Lib\Aop\Advice::after();
        return $this;
    }
    public function sample2()
	{
        \Lib\Aop\Advice::before();
        print 'Business logic of Sample2<br />';
        \Lib\Aop\Advice::after();
        return $this;
    }
}
```


Mark that event triggers \Lib\Aop\Advice::* may have any number of arguments. Specified with pointcut handler then receive all these arguments preceding by mandatory \Lib\Aop\Dto\Trace object.
```php
$pc->before(function($trace, $arg1, $arg2)
{
     echo "Entry point of ", $trace->class, "::",
        $trace->function, " with passed arguments ", $arg1, ", ", $arg2, PHP_EOL;
});
//...
function sample() {
	\Lib\Aop\Advice::before($arg1, $arg2);
}
```

Now during the run of these marked methods PHP fires corresponding events. It checks if the caller function matches pointcuts of any of enabled aspects and if it' so invokes handlers respectively.

An example of  practical use? Suppose, we have wrapped our code with advices (\Lib\Aop\Advice methods) . Then one day a need arises to obtain a detailed report on the distribution of workload among the functions of our project. We set up an aspect and define its Pointcut, which includes all functions within the project:
```php
$Monitoring = new \Lib\Aop\Aspect();
$pc3 = $Monitoring->pointcut("call *::*");
```
Now we can attach before and after event handlers which will store timestamp on entry point and on exit point of very function affected. Having that log it's not a big deal to make a report on functions performance.

[![Analytics](https://ga-beacon.appspot.com/UA-1150677-13/dsheiko/aop4php)](http://githalytics.com/dsheiko/aop4php)
