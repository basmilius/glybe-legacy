<?php
Class Glybe
{
	private static $OnlineUsers = false;
	private static $OnlineUserIds;
	private static $guestUserIds;
	
	public static function SeoUrl($in)
	{
		$url = str_replace("-", " ", $in);
		$url = htmlentities($url);
		$url = strtolower($url);
		$url = preg_replace("/&([a-z])[a-z]+;/i", "$1", $url);
		$url = preg_replace("/[^a-zA-Z0-9\s]/", "", $url);
		$url = str_replace(" ", "-", $url);
		$url = str_replace("--", "-", $url);
		if(substr($url, (strlen($url) - 1), strlen($url)) == "-")
		{
			$url = substr($url, 0, -1);
		}
		return (($url != "") ? $url : sha1($in));
	}
	
	public static function TopicUrl($id, $title, $page = 1)
	{
		return "/forum/topic/" . $id . "-" . self::SeoUrl($title) . (($page > 1) ? '/' . $page . '.glb' : '.glb');
	}
	
	public static function HashForPassword($str)
	{
		$salt = "glybe-pwd-salt-8243.2354.6435";
		return hash("sha512", $salt . hash("sha256", $str . $salt));
	}
	
	public static function GetComputedName($str)
	{
		if(stristr($str, "bingbot"))
			return 'Bing Bot';
		
		if(stristr($str, "Googlebot"))
			return 'Google Bot';
		
		if(stristr($str, "Exabot"))
			return 'Exa Bot';
		
		if(stristr($str, "facebookexternalhit"))
			return 'Facebook Bot';
		
		if(stristr($str, "TweetmemeBot"))
			return 'Twitter Bot';
		
		if(stristr($str, "Sosospider"))
			return 'Soso Spider';
		
		if(stristr($str, "InAGist URL Resolver"))
			return 'World Trending Topics Resolver';
		
		if(stristr($str, "Windows-Live-Social"))
			return 'Windows-Live service';
		
		return 'Gast';
	}
	
	public static function CheckMobile($ua)
	{
		if(strstr($ua, "iPod"))
			return Array('iPod', 'Apple', 'iPhone OS ' . Glybe::GetIphoneOsVersion($ua));
		
		if(strstr($ua, "iPad"))
			return Array('iPad', 'Apple', 'iPhone OS ' . Glybe::GetIphoneOsVersion($ua));
		
		if(strstr($ua, "iPhone"))
			return Array('iPhone', 'Apple', 'iPhone OS ' . Glybe::GetIphoneOsVersion($ua));
		
		if(strstr($ua, "Android"))
			return Array('Android Device', 'Android', 'Android');
		
		if(strstr($ua, "BlackBerry"))
			return Array('BlackBerry', 'BlackBerry', 'BlackBerry OS');
		
		if(strstr($ua, "PlayBook"))
			return Array('BlackBerry PlayBook', 'BlackBerry', 'Tablet OS');
		
		/*if(strstr($ua, "MSIE"))
			return Array('Falende Browser', 'Microsoft Internet Explorer', 'Internet Explorer');*/ // :')
		
		return false;
	}
	
	public static function GetIphoneOsVersion($ua)
	{
		$raw = explode("OS ", $ua);
		$raw = explode(" like", $raw[1]);
		return str_replace("_", ".", $raw[0]);
	}
	
	public static function Security($str)
	{
		return DB::Escape(htmlspecialchars($str));
	}
	
	public static function PaginaSysteem($totaal, $huidige, $max_per_pagina)
	{
		$aantal_paginas = ceil($totaal / $max_per_pagina);
		$paginas = Array();
		
		if($aantal_paginas <= 1)
			return Array('paginas' => $paginas, 'limit' => (($huidige - 1) * $max_per_pagina), 'max' => 1);
		
		if($huidige != 1)
			$paginas[] = Array('&laquo; Eerste', '1');
		
		$voor = ($totaal - $huidige) < 4 ? 2 - ($totaal - $huidige) : 4; 
		for($i = ($huidige - $voor > 0 ? $huidige - $voor : 1), $zien = 9; $i <= $aantal_paginas && $zien > 0; $i++, $zien--) 
		{ 
			$paginas[] = Array((($i == $huidige) ? '<strong>' . $i . '</strong>' : $i), $i);             
		} 
		
		if($huidige != $aantal_paginas)
			$paginas[] = Array('Laatste &raquo;', $aantal_paginas);
		
		return Array('paginas' => $paginas, 'limit' => (($huidige - 1) * $max_per_pagina), 'max' => $aantal_paginas);
	}
	
	public static function CreateToken($data1, $data2, $data3)
	{
		$token  = "";
		$token .= substr(hash("sha1", $data1), rand(0, 20), 5);
		$token .= "-";
		$token .= substr(hash("sha1", rand(10000, 99999)), rand(0, 20), 5);
		$token .= "-";
		$token .= substr(hash("sha1", $data2), rand(0, 20), 5);
		$token .= "-";
		$token .= substr(hash("sha1", time()), rand(0, 20), 5);
		$token .= "-";
		$token .= substr(hash("sha1", $data3), rand(0, 20), 5);
		$token .= "-";
		$token .= substr(hash("sha1", rand(10000, 99999)), rand(0, 20), 5);
		
		return strtoupper($token);
	}
	
	public static function GetOnlineUsersAsArray($guests = false, $override = false)
	{
		/*if(self::$OnlineUsers != false && !$override)
			return self::$OnlineUsers;*/
		
		$onlineUsers = Array();
		self::$OnlineUserIds[] = Array();
		$usersQuery = DB::Query("	SELECT
									u.*,
									o.last_active,
									o.last_page,
									o.ip,
									o.last_active as uLastActive,
									o.user_id oUserId,
									o.u_a oUserAgent,
									concat(o.u_a, '', o.ip) as uaip
								FROM
									users u,
									(SELECT * FROM users_online ORDER BY user_id DESC) o
								WHERE
									o.last_active > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
								AND
									u.id = o.user_id
							" .  (($guests) ? "
								OR
									o.last_active > DATE_SUB(NOW(), INTERVAL 3 MINUTE)
								AND
									u.id = 1
								AND
									o.user_id = 0
							" : "") . "
								GROUP BY
									uaip
								ORDER BY
									uLastActive DESC,
									u.username");
										
		while($usersFetch = DB::Fetch($usersQuery))
		{
			if($usersFetch['oUserId'] == 0)
			{
				$usersFetch['id'] = 0;
			}
			$onlineUsers[] = $usersFetch;
			self::$OnlineUserIds[] = $usersFetch['id'];
		}
		self::$OnlineUsers = $onlineUsers;
		DB::Query("OPTIMIZE TABLE  `users_online`");
		return $onlineUsers;
	}
	
	public static function GetOnlineUsersAsString()
	{
		global $user;
		$onlineString = "";
		$onlineUsers = self::GetOnlineUsersAsArray();
		
		if(count($onlineUsers) > 0)
		{
			foreach($onlineUsers as $key => $onlineUser)
			{
				$onlineString .= ((isset($user) && $onlineUser['username'] == $user->Username) ? 'Jij' : ucfirst($onlineUser['username'])) . (((count($onlineUsers) - 2) == $key) ? ' en ' : (((count($onlineUsers) - 1) == $key) ? '' : ', '));
			}
			return $onlineString;
		}
		return '<center><i>Er is niemand online</i></center>';
	}
	
	public static function GetOnlineUsersAsArrayWithIds()
	{
		return self::$OnlineUserIds;
	}
	
	public static function TimeAgo($time)
	{
		$timeBase = time();
		if(!is_numeric($time))
		{
			$time = strtotime($time);
		}
		if ($time <= time())
		{
			$dif = $timeBase - $time;
			if($dif < 60)
			{
				if ($dif < 1)
				{
					return "Nu";
				} else if($dif < 2)
				{
					return "1 seconde geleden";
				}
				return $dif." seconden geleden";
			}
			if($dif < 3600)
			{
				$seconds = ($dif - (floor($dif / 60) * 60));
				if($seconds < 2)
				{
					$seconds = "1 sec";
				} else {
					$seconds = $seconds . " sec";
				}
				if(floor($dif / 60) < 2)
				{
					return "1 min en " . $seconds . " geleden";
				}
				return floor($dif / 60)." min en " . $seconds . " geleden";
			}
			if($dif < (3600 * 24))
			{
				$minutes = ($dif - (floor($dif / 3600) * 3600));
				if(floor($minutes / 60) < 2)
				{
					$minutes = "1 min";
				} else {
					$minutes = floor($minutes / 60) . " min";
				}
				if(floor($dif / 3600) < 2)
				{
					return "1 uur en " . $minutes . " geleden";
				}
				return floor($dif / 3600)." uur en " . $minutes . " geleden";
			}
			
			return date("d-m-Y h:i:s", $time);
		}
	}
	
	public static function MostUsersOnline()
	{
		$most_online_users_query = DB::Query("SELECT number_of_users FROM most_online");
		$most_online_users_fetch = DB::Fetch($most_online_users_query);
		return $most_online_users_fetch['number_of_users'];
	}
	
	public static function MostUsersOnlineDate()
	{
		$most_online_users_query = DB::Query("SELECT date FROM most_online");
		$most_online_users_fetch = DB::Fetch($most_online_users_query);		
		$NLTime = strftime('%e %B %Y om %H:%M uur', strtotime($most_online_users_fetch['date']));
		return $NLTime;
	}
	
	public static function WebPlayer($videoId, $height, $width)
	{
		$url = "http://cache.basmilius.com/swfbin/Player6.swf?code=" . $videoId . "&amp;autoplay=false";
		return '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="' . $url . '"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="' . $url . '" type="application/x-shockwave-flash" width="' . $width . '" height="' . $height . '" allowscriptaccess="always" allowfullscreen="true"></embed></object>';
	}
	
	public static function SuggestionBox($name, $jsArrayName, $predefined = Array(), $maxlength = 20)
	{
		$box  = '';
		
		$box .= '<div class="input user_suggestion" name="input_' . $name . '">';
		foreach($predefined as $value)
		{
			$box .= '<div class="user_item">' . $value . '<input type="hidden" name="' . $name . '[]" value="' . $value . '" /></div>';
		}
		$box .= '<div class="input_box"><div class="fake_input"></div><input data-source="' . $jsArrayName . '" type="text" maxlength="' . $maxlength . '" placeholder="..." /></div>';
		$box .= '<div class="clear"></div>';
		$box .= '</div>';
		
		return $box;
	}
}
?>