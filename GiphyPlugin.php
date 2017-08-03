<?php
/*
Giphy Plugin for posts
Looks at ALL local posts that come in the moment they come in and adds a Image URL to the end of it. (Eventually it will actually attach an image to the notice.)
Built by: Mitchell Urgero (@loki@urgero.org) <info@urgero.org>
*/

if (!defined('STATUSNET')) {
    exit(1);
}
class GiphyPlugin extends Plugin
{
	public function initialize()
    {
    	return true;
    }
    static function settings($setting)
	{
		$settings['hashtag'] = "giphy";
		// config.php settings override the settings in this file
		$configphpsettings = common_config('site','giphy') ?: array();
		foreach($configphpsettings as $configphpsetting=>$value) {
			$settings[$configphpsetting] = $value;
		}
		if(isset($settings[$setting])) {
			return $settings[$setting];
		}
		else {
			return false;
		}
	}
	public function onStartNoticeSave($notice1){
		if ($notice1->isLocal()){
			
			$callname = self::settings("hashtag");
			$orig = $notice1->content;
			preg_match("/#(\w+)/", $orig, $matches);
			$tbool = false;
			foreach($matches as $m){
				if($m == $callname || $m == "#$callname"){
					$tbool = true;
				}
			}
			if(!$tbool){ return true; }
			list(,$gif_req_str) = explode("#$callname ", $orig);
			$image = self::getGIF(trim($gif_req_str)); //Returns Proper URL for GIF image
			$image = explode("|",$image);
			if(count($image) == 2){
				$notice1->content = $orig."\r\n".$image[0]."\r\n".$image[1];
			} else {
				$notice1->content = $orig."\r\n".$image[0];
			}
			
			if($image == "No GIF's found for that tag."){
				$notice1->rendered = $notice1->rendered."<br />".$image."<br />";
				return true;
			}
			$notice1->rendered = $notice1->rendered."<br /><a href=\"".$image[0]."\">".$image[1]."</a><br />";
		}
		return true;
	}
	static function getGIF($tags){
	    $booruapiurl = "http://api.giphy.com/v1/gifs/search?api_key=dc6zaTOxFJmzC&q=";
	    $tags2 = str_replace(" ", "+", $tags);
	    $curlSession = curl_init();
	    curl_setopt($curlSession, CURLOPT_URL, $booruapiurl.$tags2);
	    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	    $jsonData = json_decode(curl_exec($curlSession), true);
	    curl_close($curlSession);
	    $count = count($jsonData['data']);
	    if($count === 0){
		return "No GIF's found for that tag.";
	    }
	    $n = self::getRand($count);
	    $image = $jsonData['data'][$n]['images']['downsized_medium']['url'];
	    $image = explode("?", $image);
	    return $jsonData['data'][$n]['embed_url']."|".$image[0];
	}
	static function getRand($count){
		$t1 = rand(0, 9);
		$t2 = rand(0, $count - 1);
		if($t1 <= 7 && $count >= 7){
			return rand(0, 4);
		} else {
			return $t2;
		}
		return $number;
	}
}
