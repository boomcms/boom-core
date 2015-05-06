<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    private $environments = ['development', 'staging', 'production'];

    public function testIsMethods()
    {
        foreach($this->environments as $env) {
            $class = $this->getEnvClass($env);

            foreach($this->environments as $env2) {
                $method = "is" . ucfirst($env2);

                if ($env === $env2) {
                    $this->assertTrue($class->$method(), "$env is $env2");
                } else {
                    $this->assertFalse($class->$method(), "$env is not $env2");
                }
            }
        }
    }

    public function testRequireLoginForDevelopmentOnly()
    {
        foreach($this->environments as $env) {
            $class = $this->getEnvClass($env);

            if ($env === 'development') {
                $this->assertTrue($class->requiresLogin(), "$env requires login");
            } else {
                $this->assertFalse($class->requiresLogin(), "$env does not require login");
            }
        }
    }

    private function getEnvClass($env)
    {
        $className = "BoomCMS\Core\Environment\\" . ucfirst($env);
        $class = new $className;

        return $class;
    }
}