<?php
Class User
{
	public $RawData;
	public $Ip;
	public $Kladblok;
	public $Ua;
	public $Id;
	public $LastIP;
	public $Username;
	public $Password;
	public $Mail;
	public $Avatar;
	public $Active;
	public $Respect;
	public $IsOnline;
	public $StatusId;
	public $Warn;
	public $PermissionName;
	private $Permissions;
	private $Settings;
	public $Friends;
	public $friendsReq;
	public $Realname;
	
	public function __construct($userId, $updateStatus = true, $loadPermissions = true, $loadSettings = true)
	{
		global $_SERVER;
		
		$userQuery = DB::Query("SELECT * FROM `users` WHERE `id` = '" . $userId . "'");
		$userFetch = DB::Fetch($userQuery);
		
		$this->Permissions = Array();
		$this->Settings = Array();
		$this->RawData = $userFetch;
		$this->Ua = Glybe::Security($_SERVER['HTTP_USER_AGENT']);
		$this->Id = $this->RawData['id'];
		$this->Ip = $this->RawData['ip'];
		$this->Active = $this->RawData['active'];
		$this->LastIP = $this->RawData['last_ip'];
		$this->Warn = $this->RawData['warn'];
		$this->Username = $this->RawData['username'];
		$this->Kladblok = $this->RawData['kladblok'];
		$this->Password = $this->RawData['password'];
		$this->Mail = $this->RawData['mail'];
		$this->Avatar = "http://grolsch.static-gly.be/static-content-a/user-profilepictures/" . $this->RawData['avatar'];
		$this->Respect = 0;
		$this->IsOnline = (in_array($this->Id, Glybe::GetOnlineUsersAsArrayWithIds()));
		$this->StatusId = $this->RawData['status_id'];
		$this->Realname = $this->Username;
		
		$this->Friends = Array();
		$this->FriendsReq = Array();
		
		$this->InitializeFriends();
			
		if($loadPermissions)
		{
			$permissionsQuery = DB::Query("SELECT * FROM `users_permissions` WHERE `id` = '" . $this->RawData['permission_id'] . "'");
			while($permissionsFetch = DB::Fetch($permissionsQuery))
			{
				$this->PermissionName = $permissionsFetch['caption'];
				foreach($permissionsFetch as $permission => $value)
				{
					if($permissionsFetch[$permission] == 'true' && !in_array($permission, $this->Permissions))
					{
						$this->Permissions[] = $permission;
					}
				}
				
			}
		}
		
		if($loadSettings)
		{
			$settingsQuery = DB::Query("SELECT * FROM `users_settings` WHERE `user_id` = '" . $this->Id . "'");
			if(DB::NumRows($settingsQuery) === 0)
			{
				DB::Query("INSERT INTO `users_settings` (user_id) VALUES ('" . $this->Id . "')");
				$this->__construct($userId, $updateStatus, $loadPermissions, $loadSettings);
			} else {
				$this->Settings = DB::Fetch($settingsQuery);
			}
		}
	}
	
	public function Exsist($id = 0)
	{
		if($id == 0) $id = $this->Id;
		$query = DB::NumRowsQuery("SELECT 1 FROM users WHERE id = '" . DB::Escape($id) . "'");
		if($query > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function UpdateStatus($topicId = 0, $overridePage = null)
	{
		global $_SERVER;
		
		$ids = Glybe::GetOnlineUsersAsArrayWithIds();
		
		if(!in_array($this->Id, $ids))
		{
			foreach($this->Friends as $uId)
			{
				if(in_array($uId, $ids))
				{
					$f = new User($uId, false, false, false);
					DB::Query("INSERT INTO `notifications` (`user_id`, `user_from_id`, `icon`, `title`, `message`, `n_ts`) VALUES ('" . $f->Id . "', '" . $this->Id . "', 'status_online', 'Online', '<strong>" . DB::Escape(htmlspecialchars(((str_replace(" ", "", $this->GetSetting("displayname")) != "") ? $this->GetSetting("displayname") : $this->Username))) . "</strong><br/>Is nu online op Glybe!', UNIX_TIMESTAMP())");
				}
			}
		}
		
		DB::Query("UPDATE `users` SET `last_active` = CURRENT_TIMESTAMP() WHERE `id` = '" . $this->Id . "'");
		if(DB::NumRowsQuery("SELECT 1 FROM `users_online` WHERE `user_id` = '" . $this->Id . "'") === 0)
		{
			DB::Query("INSERT INTO `users_online` (user_id, ip, u_a, last_active, last_page, last_topic) VALUES ('" . $this->Id . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', CURRENT_TIMESTAMP(), '" . (($overridePage != null) ? $overridePage : DB::Escape($_SERVER['REQUEST_URI'])) . "', '" . $topicId . "')");
		} else {
			DB::Query("UPDATE `users_online` SET ip = '" . $_SERVER['REMOTE_ADDR'] . "', u_a = '" . DB::Escape($_SERVER['HTTP_USER_AGENT']) . "', last_active = CURRENT_TIMESTAMP(), last_page = '" . (($overridePage != null) ? $overridePage : DB::Escape($_SERVER['REQUEST_URI'])) . "', last_topic = '" . $topicId . "' WHERE user_id = '" . $this->Id . "'");
		}
	}

	public function PrepareSession($expire = 604800)
	{
		global $glb_settings;
		global $_SERVER;
		
		$token = Glybe::CreateToken($this->Id, $this->Username, $this->Ip);
		$expire = (time() + $expire);
		
		setcookie($glb_settings['cookie_us'], $token, $expire, "/", "." . str_replace("www.", "", $_SERVER['HTTP_HOST']));
		
		DB::Query("INSERT INTO `web_sessions` (session_hash, session_start, session_end, user_ip, user_ua, user_id) VALUES ('" . $token . "', UNIX_TIMESTAMP(), '" . $expire . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $this->Ua . "', '" . $this->Id . "')");
	}
	
	public function KillSession($session, $hash)
	{
		$query = DB::Query("SELECT * FROM web_sessions WHERE user_id = '".DB::Escape($this->Id)."' AND session_hash = '" . $session . "' ORDER BY id DESC LIMIT 1");
		$fetch = DB::Fetch($query);
		if($fetch['session_hash'] == $hash)
		{
			global $glb_settings;
			setcookie($glb_settings['cookie_us'], "", (time() - 3600), "/", ".glybe.nl");
			
			DB::Query("UPDATE `web_sessions` SET `session_end` = '0' WHERE `session_hash` = '" . $session . "'");
			DB::Query("DELETE FROM `users_online` WHERE `user_id` = '" . $this->Id . "'");
		}
	}
	
	public function GetAvatar($size = 160)
	{
		if($size == 160)
		{
			return '<div class="forum_profilepicture"><img height="149" width="149" src="' . str_replace("thumb_", "thumb_149_", $this->Avatar) . '" alt="Profiel-foto" /></div>';
		}
		$ava = str_replace("thumb_", "%THUMB%", $this->Avatar);
		
		if($size <= 24)
			$ava = str_replace("%THUMB%", "thumb_24_", $ava);
		else if($size <= 36)
			$ava = str_replace("%THUMB%", "thumb_36_", $ava);
		else if($size <= 48)
			$ava = str_replace("%THUMB%", "thumb_48_", $ava);
		else if($size <= 128)
			$ava = str_replace("%THUMB%", "thumb_128_", $ava);
		else if($size <= 149)
			$ava = str_replace("%THUMB%", "thumb_149_", $ava);
		else
			$ava = str_replace("%THUMB%", "thumb_", $ava);
		
		return '<div style="border: 2px solid #D7D7D7; background: #FFFFFF; height: ' . $size . 'px; width: ' . $size . 'px; position: relative; margin: 0px auto;" class="usr_pf"><img src="' . $ava . '" alt="Profiel-foto van ' . $this->Username . '" title="Profiel-foto van ' . $this->Username . '" height="' . $size . '" width="' . $size . '" /></div>';
	}
	
	public function HasPermissions()
	{
		$arguments = func_get_args();
		$hasPermission = true;
		
		foreach($arguments as $arg)
		{
			if(!in_array($arg, $this->Permissions))
			{
				$hasPermission = false;
			}
		}
		
		return $hasPermission;
	}
	
	public function GetSetting($setting)
	{
		if(isset($this->Settings[$setting]))
		{
			return $this->Settings[$setting];
		} else {
			return 'undefined';
		}
	}
	
	private function InitializeFriends()
	{
		$vQuery = DB::Query("SELECT * FROM users_friends WHERE user_one_id = '" . $this->Id . "' OR user_two_id = '" . $this->Id . "'");
		while($vFetch = DB::Fetch($vQuery))
		{
			if($vFetch['user_one_id'] == $this->Id)
			{
				if(in_array($vFetch['user_two_id'], $this->Friends)) continue;
				$this->Friends[] = $vFetch['user_two_id'];
				continue;
			}
			if(in_array($vFetch['user_one_id'], $this->Friends)) continue;
			$this->Friends[] = $vFetch['user_one_id'];
		}
		$rQuery = DB::Query("SELECT * FROM users_friends_requests WHERE user_to = '" . $this->Id . "'");
		while($rFetch = DB::Fetch($rQuery))
		{
			if(in_array($rFetch['user_from'], $this->FriendsReq)) continue;
			$this->FriendsReq[] = $vFetch['user_from'];
		}
	}
	
	public function IsFriend($id)
	{
		return in_array($id, $this->Friends);
	}
}
?>