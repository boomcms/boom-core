<?php

class Task_Db_Dump extends Minion_Task
{
    protected $_options = [
        'outfile' => null,
    ];

    /**
	 * Execute the task
	 *
	 * @param array Config for the task
	 */
    protected function _execute(array $options)
    {
        // TODO: DB group is hard coded across Boom code, needs to be a variable.
        $db_config = Arr::get(Kohana::$config->load('database')->get('default'), 'connection');

        $command = "mysqldump {$db_config['database']}";

        if (isset($db_config['username'])) {
            $command .= " -u {$db_config['username']}";
        }

        if (isset($db_config['password'])) {
            $command .= " -p{$db_config['password']}";
        }

        if (isset($db_config['hostname'])) {
            $command .= " -h {$db_config['hostname']}";
        }

        if (isset($options['outfile'])) {
            $command .= " -r {$options['outfile']}";
        }

        exec($command);
    }
}
