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

    /**
     * Should return a public exception handler for development,
     * Private for staging or production.
     */
    public function testGetExceptionHandler()
    {
        $e = new Exception;

        foreach($this->environments as $env) {
            $class = $this->getEnvClass($env);

            if ($env === 'development') {
                $this->assertInstanceOf('Boom\\Exception\\Handler\\Pub', $class->getExceptionHandler($e), $env);
            } else {
                $this->assertInstanceOf('Boom\\Exception\\Handler\\Priv', $class->getExceptionHandler($e), $env);
            }
        }
    }

    private function getEnvClass($env)
    {
        $className = "Boom\\Environment\\" . ucfirst($env);
        $class = new $className;

        return $class;
    }
}