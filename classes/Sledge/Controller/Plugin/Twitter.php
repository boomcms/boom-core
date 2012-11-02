<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package Sledge
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
class Sledge_Controller_Plugin_Twitter extends Sledge_Controller
{
	/**
	* Display a feed of tweets from the twitter API.
	* All POST data will be passed on to the twitter API.
	* If no screen name is sent then no content will be returned.
	*
	* @uses Fragment::load()
	* @uses Fragment::save()
	* @link https://dev.twitter.com/docs/api/1/get/statuses/user_timeline
	*/
	public function action_feed()
	{
		// Pop all the post data into a variable to prevent repeated calls to $this->request->post();
		$post = $this->request->post();

		// Only continue if a screen name has been sent, if not don't throw an error, just don't send back any content.
		if ($screen_name = Arr::get($post, 'screen_name'))
		{
			// Create a hash of the POST contents for caching.
			$hash = md5( serialize($post));

			if ( ! Fragment::load("twitter_feed:$hash", 3600))
			{
				// Get the tweets from the twitter API in JSON format.
				$tweets = Request::factory("http://api.twitter.com/1/statuses/user_timeline.json")
					->query($post)
					->execute();

				// Decode the data.
				$tweets = json_decode($tweets);

				if (is_array($tweets) AND ! empty($tweets))
				{
					// Pop it all in the template.
					echo View::factory("sledge/plugin/twitter/feed", array(
						'tweets'		=>	$tweets,
						'screen_name'	=>	$screen_name,
					));
				}

				// Save to cache.
				Fragment::save();
			}
		}
	}
}