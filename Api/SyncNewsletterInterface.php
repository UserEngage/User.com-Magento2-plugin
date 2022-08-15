<?php 
namespace Usercom\Analytics\Api;
 
 
interface SyncNewsletterInterface {


	/**
	 * @param string $data
	 * @return string
	 */
	
	public function sync($data);
}
