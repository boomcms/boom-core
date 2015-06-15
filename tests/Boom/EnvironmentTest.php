<?php

class EnvironmentTest extends TestCase
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

    private function getEnvClass($env)
    {
        $className = "BoomCMS\Core\Environment\\" . ucfirst($env);
        $class = new $className;

        return $class;
    }
}