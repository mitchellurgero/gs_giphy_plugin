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
			$elements = explode("#$callname", $orig); 
			$tags = trim(str_replace("#","",$elements[1]));
			$image = self::getGIF($tags); //Returns Proper URL for GIF image
			$notice1->rendered = $orig."\r\n".$image;
		}
		return true;
	}
	static function getGIF($tags){
		$booruapiurl = "http://api.giphy.com/v1/gifs/search?api_key=dc6zaTOxFJmzC&q=";
	    $tags2 = str_replace(" ", "+", $tags);
	    $curlSession = curl_init();
	    echo "URL: ".$booruapiurl.$tags2."\r\n";
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
	    return $jsonData['data'][$n]['images']['downsized_medium']['url'];
	}
	static function getRand($count){
		//rand(0, $count - 1);
		$t1 = rand(0,9);
		$t2 = rand(0, $count);
		if($t1 <= 7 && $count >= 7){
			return rand(0, 4);
		} else {
			return $t2;
		}
		
		return $number;
	}
}
