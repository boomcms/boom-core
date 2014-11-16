<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    public function testIsMethods()
    {
        $environments = ['development', 'staging', 'production'];

        foreach($environments as $env) {
            $className = "Boom\\Environment\\" . ucfirst($env);
            $class = new $className;

            foreach($environments as $env2) {
                $method = "is" . ucfirst($env2);

                if ($env === $env2) {
                    $this->assertTrue($class->$method(), "$className->$method())");
                } else {
                    $this->assertFalse($class->$method(), "$className->$method())");
                }
            }
        }
    }
}