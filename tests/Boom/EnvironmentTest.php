<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    private $environments = ['development', 'staging', 'production'];

    public function testIsMethods()
    {
        foreach($this->environments as $env) {
            $className = "Boom\\Environment\\" . ucfirst($env);
            $class = new $className;

            foreach($this->environments as $env2) {
                $method = "is" . ucfirst($env2);

                if ($env === $env2) {
                    $this->assertTrue($class->$method(), "$className->$method())");
                } else {
                    $this->assertFalse($class->$method(), "$className->$method())");
                }
            }
        }
    }

    public function testRequireLoginForDevelopmentOnly()
    {
        foreach($this->environments as $env) {
            $className = "Boom\\Environment\\" . ucfirst($env);
            $class = new $className;

            if ($env === 'development') {
                $this->assertTrue($class->requiresLogin(), $className);
            } else {
                $this->assertFalse($class->requiresLogin(), $className);
            }
        }
    }
}