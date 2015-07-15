<?php

/**
 * Created for php-instagram-feed
 * User: Danny Hearnah
 * Author: ChubbyNinja
 *
 * Date: 15/07/2015
 * Time: 08:09
 */
class InstagramFeed
{

	/**
	 * @var int
	 */
	public $following = 0;
	/**
	 * @var int
	 */
	public $followed_by = 0;
	/**
	 * @var string
	 */
	public $full_name = '';
	/**
	 * @var string
	 */
	public $profile_pic = '';
	/**
	 * @var array
	 */
	public $media = [];
	/**
	 * @var int
	 */
	public $media_count = 0;
	/**
	 * @var string
	 */
	protected $username = '';
	/**
	 * @var array
	 */
	protected $settings = ['cache' => TRUE, 'cache_time' => 3600, 'cache_location' => ''];

	/**
	 * @param null $username
	 * @param array $settings
	 */
	function __construct($username = NULL, $settings = [])
	{
		$this->setUsername($username);
		$this->getFeed();
	}

	/**
	 *
	 */
	private function getFeed()
	{
		$lookup = FALSE;
		if (!$this->getSettings()['cache'] || !file_exists($this->getSettings()['cache_location'] . $this->getFilename()) || (time() - filemtime($this->getSettings()['cache_location'] . $this->getFilename())) > $this->getSettings()['cache_time']) {
			$lookup = TRUE;
		}

		if ($lookup) {
			$feed = $this->getRemoteFeed();
		} else {
			$feed = $this->getLocalFeed();
		}


		$this->parseFeed($feed);
	}

	/**
	 * @return array
	 */
	public function getSettings()
	{
		return $this->settings;
	}

	/**
	 * @param array $settings
	 */
	public function setSettings($settings)
	{
		$this->settings = array_merge($this->getSettings(), $settings);
	}

	private function getFilename()
	{
		return $this->getUsername() . '-instagram.json';
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return array
	 */
	private function getRemoteFeed()
	{
		$res = file_get_contents('https://instagram.com/' . $this->getUsername());

		if (!$res) {
			$this->error = 'remote lookup failed';
		}
		$json = strstr($res, 'window._sharedData = ');
		$json = strstr($json, '</script>', TRUE);
		$json = rtrim($json, ';');
		$json = ltrim($json, 'window._sharedData = ');

		if ($this->getSettings()['cache']) {
			file_put_contents($this->getSettings()['cache_location'] . $this->getFilename(), $json);
		}

		return json_decode($json, TRUE);
	}

	/**
	 * @return array
	 */
	private function getLocalFeed()
	{
		$json = file_get_contents($this->getSettings()['cache_location'] . $this->getFilename());

		return json_decode($json, TRUE);
	}


	/**
	 * @param $feed
	 * @return bool
	 */
	private function parseFeed($feed)
	{
		if (!is_array($feed) || !isset($feed['entry_data']['ProfilePage'])) {
			$this->error = 'not array';

			return FALSE;
		}


		$this->setFollowing($feed['entry_data']['ProfilePage'][0]['user']['follows']['count']);
		$this->setFollowedBy($feed['entry_data']['ProfilePage'][0]['user']['followed_by']['count']);
		$this->setFullName($feed['entry_data']['ProfilePage'][0]['user']['full_name']);
		$this->setMediaCount($feed['entry_data']['ProfilePage'][0]['user']['media']['count']);
		$this->setMedia($feed['entry_data']['ProfilePage'][0]['user']['media']['nodes']);
		$this->setProfilePic($feed['entry_data']['ProfilePage'][0]['user']['profile_pic_url']);


		return TRUE;

	}

	/**
	 * @return int
	 */
	public function getFollowing()
	{
		return $this->following;
	}

	/**
	 * @param int $following
	 */
	public function setFollowing($following)
	{
		$this->following = $following;
	}

	/**
	 * @return int
	 */
	public function getFollowedBy()
	{
		return $this->followed_by;
	}

	/**
	 * @param int $followed_by
	 */
	public function setFollowedBy($followed_by)
	{
		$this->followed_by = $followed_by;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->full_name;
	}

	/**
	 * @param string $full_name
	 */
	public function setFullName($full_name)
	{
		$this->full_name = $full_name;
	}

	/**
	 * @return string
	 */
	public function getProfilePic()
	{
		return $this->profile_pic;
	}

	/**
	 * @param string $profile_pic
	 */
	public function setProfilePic($profile_pic)
	{
		$this->profile_pic = $profile_pic;
	}

	/**
	 * @param int $offset
	 * @param null $limit
	 * @return array
	 */
	public function getMedia($offset = 0, $limit = NULL)
	{
		return array_slice($this->media, $offset, $limit);
	}

	/**
	 * @param array $media
	 */
	public function setMedia($media)
	{
		$this->media = $media;
	}

	/**
	 * @return int
	 */
	public function getMediaCount()
	{
		return $this->media_count;
	}

	/**
	 * @param int $media_count
	 */
	public function setMediaCount($media_count)
	{
		$this->media_count = $media_count;
	}


}