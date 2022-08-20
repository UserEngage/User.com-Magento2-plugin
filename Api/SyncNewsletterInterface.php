<?php 
namespace Usercom\Analytics\Api;
 
 
interface SyncNewsletterInterface {


	/**
	 * @param mixed $user
	 * @return string
	 */
	
	public function sync($user);
}
