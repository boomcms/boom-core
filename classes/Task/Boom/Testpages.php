<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests that all pages on the site return a HTTP status code 200.
 *
 * This task takes no config options
 *
 */
class Task_Boom_Testpages extends Minion_Task
{
	protected $_page_count;
	protected $_test_run_times = array();

	/**
	 * Execute the task
	 *
	 * @param array Config for the task
	 */
	protected function _execute(array $options)
	{
		// Calling URL::site() when Kohana is running from CLI crashes because the server name isn't set.
		$_SERVER['SERVER_NAME'] = '';

		$start_time = microtime(true);

		$this->_page_count = $this->_how_many_pages_are_being_tested();
		$pages = $this->_get_testable_pages();
		$errors = $this->_test_pages($pages);

		if ($errors)
		{
			$this->_report_errors($errors);
		}

		$run_time = microtime(true) - $start_time;

		echo "\n\n".$this->_page_count." tests completed in ".round($run_time, 2)."s\n";
		echo "Mean test run time: ".round(array_sum($this->_test_run_times) / $this->_page_count, 2)."s";

	}

	protected function _get_testable_pages()
	{
		return ORM::factory('Page')
			->where('primary_uri', '!=', null)
			->with_current_version(\Boom\Editor::instance())
			->find_all();
	}

	protected function _how_many_pages_are_being_tested()
	{
		return ORM::factory('Page')
			->with_current_version(\Boom\Editor::instance())
			->where('primary_uri', '!=', null)
			->count_all();
	}

	protected function _test_pages($pages)
	{
		$errors = array();

		foreach ($pages as $i => $page)
		{
			$result = $this->_test_single_page($page);

			if ($result == 200)
			{
				$this->_report_test_passed();
			}
			else
			{
				$this->_report_test_failed();
				$errors[] = array('page' => $page->primary_uri, 'status' => $result);
			}

			$this->_report_testsuite_progress($i);

			ob_flush();
		}

		return $errors;
	}

	protected function _test_single_page($page)
	{
		$start_time = microtime(true);

		try
		{
			$status = Request::factory($page->primary_uri)
				->method(Request::HEAD)
				->execute()
				->status();
		}
		catch (Exception $e)
		{
			$status = ($e instanceof HTTP_Exception)? $e->getCode() : '500';
		}

		$run_time = microtime(true) - $start_time;
		$this->_test_run_times[] = $run_time;

		return $status;
	}

	protected function _report_test_passed()
	{
		echo ".";
	}

	protected function _report_test_failed()
	{
		echo "x";
	}

	protected function _report_testsuite_progress($i)
	{
		if (++$i % 100 === 0)
		{
			echo " ($i / $this->_page_count)\n";
		}
	}

	protected function _report_errors($errors)
	{
		if (count($errors))
		{
			echo "\n\nThe following errors occurred:\n";

			foreach ($errors as $error)
			{
				echo $error['page']." : ".$error['status']."\n";
			}
		}
	}
}