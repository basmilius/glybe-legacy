<?php
/* 
 * UBB Parser
 * Copyright © Bas Milius
 */

Class UBB{
	public static $SmilieArray = Array();
	public static $SmiliePath = "/cache/images/smilies/";
	public static $DefaultYouTubePlayer = true;
	public static $PlayerVersion = "Player6"; // Kan ook Player4 zijn (Huidige stabiele versie)
	public static $MaxQuotes = 4;
	public static $VideoTitleUrl = "http://data.basmilius.com/player/get_video_title?video_id=";
	public static $yt_height;
	
	public static function Parse($string, $user_is_mod = false, $yt_height = 320)
	{
		self::$yt_height = $yt_height;
		self::InitSmilies();
		
		$string = stripslashes($string);
		$string = htmlspecialchars($string);
		$string = str_replace("&quot;", '"', $string);
		$string = nl2br($string);
		
		$string = self::ParseIgnores($string);
		$string = self::ParseCode($string);
		$string = self::ParseSmilies($string);
		$string = self::ParseBbCodes($string);
		$string = self::ParseQuotes($string);
		$string = self::ParseSpoilers($string);
		$string = self::ParseUrls($string);
		$string = (($user_is_mod) ? self::ParseModerationTags($string) : $string);
		
		return ($string);
	}
	
	private static function ParseIgnores($string)
	{
		$string = preg_replace_callback("_\[ignore\](.*?)\[/ignore\]_si", "UBB::IgnoreParser", $string);
		
		return $string;
	}
	
	private static function ParseBbCodes($string)
	{
		$string = str_replace("[hr]", '<div style="border: 1px solid #333333;"></div>', $string);
		$string = preg_replace("_\[b\](.*?)\[/b\]_si", '<strong>$1</strong>', $string);
		$string = preg_replace("_\[i\](.*?)\[/i\]_si", '<i>$1</i>', $string);
		$string = preg_replace("_\[u\](.*?)\[/u\]_si", '<u>$1</u>', $string);
		$string = preg_replace("_\[s\](.*?)\[/s\]_si", '<strike>$1</strike>', $string);
		$string = preg_replace("_\[m\](.*?)\[/m\]_si", '<marquee>$1</marquee>', $string);
		$string = preg_replace("_\[center\](.*?)\[/center\]_si", '<center>$1</center>', $string);
		$string = preg_replace("_\[img\](.*?)\[/img\]_si", '<img src="$1" onclick="window.open(\'$1\');" style="max-width: 100%;" />', $string);
		$string = preg_replace("_\[hl\](.*?)\[/hl\]_si", '<span style="background: #D5C8D0;">$1</span>', $string);
		$string = preg_replace("_\[hl=(.*?)\](.*?)\[/hl\]_si", '<span style="background: $1;">$2</span>', $string);
		$string = preg_replace("_\[size=(.*?)\](.*?)\[/size\]_si", '<span style="font-size: $1px;">$2</span>', $string);
		$string = preg_replace("_\[color=(.*?)\](.*?)\[/color\]_si", '<span style="color: $1;">$2</span>', $string);
		$string = preg_replace("_\[font=(.*?)\](.*?)\[/font\]_si", '<span style="font-family: $1;">$2</span>', $string);
		$string = preg_replace("_\[url\]http://(.*?)\[/url\]_si", '<a href="http://$1" target="_blank">$1</a>', $string);
		$string = preg_replace("_\[url\](.*?)\[/url\]_si", '<a href="http://$1" target="_blank">$1</a>', $string);
		$string = preg_replace("_\[url=http://(.*?)\](.*?)\[/url\]_si", '<a href="http://$1" target="_blank">$2</a>', $string);
		$string = preg_replace("_\[url=(.*?)\](.*?)\[/url\]_si", '<a href="http://$1" target="_blank">$2</a>', $string);
		$string = preg_replace("_\[list\](.*?)\[/list\]_si", '<ul>$1</ul>', $string);
		$string = preg_replace("_\[item\](.*?)\[/item\]_si", '<li>$1</li>', $string);
		$string = preg_replace('_\[youtube\].*?(v=|v/)(.+?)(&.*?|/.*?)?\[/youtube\]_is', '[youtube]$2[/youtube]', $string);
		$string = preg_replace('_\[spotify\]spotify:track:(.*?)\[/spotify\]_is', '[spotify]$1[/spotify]', $string);
		$string = preg_replace('_\[spotify\](.*?)\[/spotify\]_is', '<iframe src="https://embed.spotify.com/?uri=spotify:track:$1" width="300" height="80" frameborder="0" allowtransparency="true"></iframe>', $string);
		$string = preg_replace('_\[skype\](.*?)\[/skype\]_is', '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script><a href="skype:$1?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="Skype Me™!" /></a>', $string);
		$string = preg_replace_callback("_\[youtube\](.*?)\[/youtube\]_si", "UBB::ParseYouTube", $string);
		$string = preg_replace_callback("_\[flash\](.*?)\[/flash\]_si", "UBB::Flash", $string);
		$string = preg_replace_callback("_\[music\](.*?)\[/music\]_si", "UBB::ParseMusic", $string);
		$string = preg_replace_callback("_\[poll\](.*?)\[/poll\]_si", "UBB::ParsePoll", $string);
		
		return $string;
	}
	
	private static function ParseUrls($string)
	{
		$string = preg_replace_callback('#(^|[ \n\r\t])([a-z0-9]{1,6}://([a-z0-9\-]{1,}(\.?)){1,}[a-z]{2,5}(:[0-9]{2,5}){0,1}((\/|~|\#|\?|:|=|&amp;|!|&|\+){1}[a-z0-9\-._%]{0,}){0,})#si', 'UBB::UrlParseCallback', $string);
		$string = preg_replace_callback('#(^|[ \n\r\t])((www\.){1}([a-z0-9\-]{1,}(\.?)){1,}[a-z]{2,5}(:[0-9]{2,5}){0,1}((\/|~|\#|\?|:|=|&amp;|&|!|\+){1}[a-z0-9\-._%]{0,}){0,})#si', 'UBB::UrlParseCallback', $string);
		return $string;
	}
	
	private static function ParseQuotes($string)
	{
		$c = 0;
		$matches = Array();
		while(preg_match("((\[quote\](.+?)\[\/quote\])|(\[quote\=\"(.+?)\" id=(.+?)\](.+?)\[\/quote\])|(\[quote\=\"(.+?)\"\](.+?)\[\/quote\]))ms", $string, $matches))
		{
			$c++;
			$qid = sha1($c . rand(1000, 9999) . substr($string, 0, 10));
			
			$quote = '{Q1}<fieldset class="ubb ubb_quote"><legend><strong>{1}</strong> {2}</legend><div class="in">{3}</div></fieldset>{Q2}';
			$quote = str_replace("{Q1}", (($c == self::$MaxQuotes) ? '<div class="ubb ubb_morequotes" onclick="jQuery(this).slideUp(); jQuery(this).parent().children(\'div.hidden_div\').animate({height:\'toggle\',opacity:\'toggle\'}, 500);">Laat oudere quotes zien</div><div class="hidden_div" style="display: none;">' : ''), $quote);
			$quote = str_replace("{Q2}", '', $quote);
			
			$string = preg_replace("(\[quote\](.+?)\[\/quote\])ms", str_replace("{1}", "Quote", str_replace("{2}", '', str_replace("{3}", "$1", $quote))), $string);
			$string = preg_replace("(\[quote\=\"(.+?)\" id=(.+?)\](.+?)\[\/quote\])ms", str_replace("{1}", "$1 schreef", str_replace("{2}", '(<a href="/forum/postredir?pid=$2">Ga naar bericht</a>)', str_replace("{3}", "$3", $quote))), $string);
			$string = preg_replace("(\[quote\=\"(.+?)\"\](.+?)\[\/quote\])ms", str_replace("{1}", "$1 schreef", str_replace("{2}", '', str_replace("{3}", "$2", $quote))), $string);
		}
		
		return $string;
	}
	
	private static function ParseSpoilers($string)
	{
		while(preg_match("((\[spoiler=(.+?)\](.+?)\[\/spoiler\])|(\[spoiler\](.+?)\[\/spoiler]))is", $string)) {
			$spoiler = '<fieldset class="ubb ubb_spoiler"><legend><strong>{1} (<a href="javascript:void(0);" onclick="$(this).parent().parent().parent().children(\'div\').children(\'div.in\').animate({height: \'toggle\', opacity: \'toggle\'}, 500);">open/sluit</a>)</strong></legend><div style="position: relative; padding: 1px 0px;"><div style="display: none;" class="in">{2}</div></div></fieldset>';
			
			$string = preg_replace("(\[spoiler=(.+?)\](.+?)\[\/spoiler\])is", str_replace("{1}", "Spoiler: $1", str_replace("{2}", "$2", $spoiler)), $string);
			$string = preg_replace("(\[spoiler\](.+?)\[\/spoiler\])is", str_replace("{1}", "Spoiler", str_replace("{2}", "$1", $spoiler)), $string);
		}
		
		return $string;
	}
	
	private static function ParseModerationTags($string)
	{
		$string = preg_replace("_\[mod\](.*?)\[/mod\]_si", '<div class="ubb ubb_modmessage"><span class="title">Moderator Meld:</span><br/><span class="msg">$1</span></div>', $string);
		$string = preg_replace("_\[mod=\"(.*?)\"\](.*?)\[/mod\]_si", '<div class="ubb ubb_modmessage"><span class="title">Moderator Meld aan $1:</span><br/><span class="msg">$2</span></div>', $string);
		$string = preg_replace("_\[mod=(.*?)\](.*?)\[/mod\]_si", '<div class="ubb ubb_modmessage"><span class="title">Moderator Meld aan $1:</span><br/><span class="msg">$2</span></div>', $string);
		$string = preg_replace("_\[js-script\](.*?)\[/js-script\]_si", '<script type="text/javascript">$1</script>', $string);
		$string = preg_replace_callback("_\[u:(.*?)\]_si", "UBB::ParseUserTag", $string);
		
		return $string;
	}
	
	private static function ParseSmilies($string)
	{
		foreach(self::$SmilieArray as $key => $value)
		{
			$string = str_replace($key, '<img src="' . self::$SmiliePath . $value . '" alt="' . $key . '" class="gui-tooltip" tt-data="' . $key . '" />', $string);
		}
		
		return $string;
	}
	
	private static function ParseUserTag($str)
	{
		global $user;
		if(isset($user))
		{
			if(isset($user->RawData[$str[1]]))
				return $user->RawData[$str[1]];
			return "<i>Undefined</i>";
		}
		return "<i>Gast</i>";
	}
	
	private static function ParseYouTube($string)
	{
		if(self::$DefaultYouTubePlayer)
		{
			$string = '<object width="100%" height="' . self::$yt_height . '" style="max-width: 580px;"><param name="movie" value="http://www.youtube.com/v/' . substr($string[1], 0, 11) . '&amp;version=3&amp;iv_load_policy=3&amp;vq=hd720&amp;modestbranding=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . substr($string[1], 0, 11) . '&amp;version=3&amp;iv_load_policy=3&amp;vq=hd720&amp;modestbranding=1" type="application/x-shockwave-flash" width="100%" height="' . self::$yt_height . '" style="max-width: 580px;" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed></object>';
		} else {
			$string = '<object width="100%" height="' . self::$yt_height . '" style="max-width: 580px;"><param name="movie" value="http://cache.basmilius.com/swfbin/' . self::$PlayerVersion . 'swf?code=' . substr($string[1], 0, 11) . '&amp;autoplay=false&amp;vq=large"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://cache.basmilius.com/swfbin/' . self::$PlayerVersion . '.swf?code=' . substr($string[1], 0, 11) . '&amp;autoplay=false&amp;vq=large" type="application/x-shockwave-flash" width="100%" height="' . self::$yt_height . '" style="max-width: 580px;" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed></object>';
		}
		return $string;
	}
	
	private static function Flash($string)
	{
		$string = explode("|", $string[1]);
		$string = '<object width="' . $string[1] . '" height="' . $string[2] . '"><param name="movie" value="' . $string[0] . '"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="wmode" value="transparent"></param><embed src="' . $string[0] . '" type="application/x-shockwave-flash" width="' . $string[1] . '" height="' . $string[2] . '" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed></object>';
		return $string;
	}
	
	private static function ParseMusic($string)
	{
		$videoId = $string[1];
		$musicId = sha1($videoId . rand(10000, 99999) . $videoId);
		
		$string = '	<div class="ubb ubb_music" id="' . $musicId . '">
					<div class="video_thumb"><img src="http://i.ytimg.com/vi/' . $videoId . '/' . rand(1, 3) . '.jpg" /></div>
					<div class="video_info">
						<span class="video_title">Bezig met laden..</span><br/>
						<a class="link" href="javascript:void();" onclick="Glybe.Sounds.HeadingPlayer.LoadVideoById(\'' . $videoId . '\');">Afspelen &raquo;</a><br/>
					</div>
					<div style="clear: both;"></div>
				</div>
				<script type="text/javascript">
				jQuery.get("' . self::$VideoTitleUrl . $videoId . '", function(d){
					jQuery("div.ubb.ubb_music#' . $musicId . '").children("div.video_info").children("span.video_title").html(d);
				});
				</script>';
		
		return $string;
	}
	
	private static function ParsePoll($string)
	{
		$pollId = $string[1];
		
		try
		{
			$poll = new Poll(DB::Escape($pollId));
			$string = '	<div class="c_box" style="width: 350px;">
						<div class="heading"><div class="icon newspaper"></div>' . $poll->Vraag . '</div>
						<div class="inner">'; 
							foreach($poll->Antwoorden as $Antwoord)
							{
								$string .= $poll->GetDefaultStyledAnswer($Antwoord);
							}
				$string .='	</div>
					</div>';
		}
		catch(Exception $e)
		{
			$string = '&#91;poll&#93;' . $pollId . '&#91;/poll&#93;';
		}
		
		return $string;
	}
	
	public static function SmiliesCode($string)
	{
		$key2 = str_replace(' ', '&nbsp;', $string);
		$key2 = str_replace('(', '&#40;', $string);
		$key2 = str_replace(')', '&#41;', $key2);
		$key2 = str_replace('-', '&#45;', $key2);
		$key2 = str_replace(':', '&#58;', $key2);
		$key2 = str_replace('.', '&#46;', $key2);
		$key2 = str_replace('*', '&#42;', $key2);
		$key2 = str_replace(';;', ';&#59;', $key2);
		return $key2;
	}
	
	public static function ParseCode($string)
	{
		$string = preg_replace_callback("_\[code\](.*?)\[/code\]_is", "UBB::CodeHL", $string);
		return $string;
	}
	
	public static function CodeHL($string)
	{
		$string = highlight_string(htmlspecialchars_decode(strip_tags($string[1])), true);
		$string = str_replace('\"', '"', $string);
		$string = str_replace("?&gt;", "<span style=\"color: #0000BB\">?&gt;</span>", $string);
		
		$aLines = explode("<br />", $string);
		$iLines = count($aLines);
		
		$string2  = '<table cellpadding="3" cellspacing="0" class="bbcode" style="table-layout: fixed">';
		$string2 .= '<tbody>';
		$string2 .= '<tr>';
		$string2 .= '<td class="bb_num" class="codett" nowrap="nowrap" valign="top" style="width: 22px;">';
		for($i = 1; $i <= ($iLines); $i++)
		{
			$string2 .= $i . '<br/>';
		}
		$string2 .= '</td>';
		$string2 .= '<td valign="top" nowrap="nowrap">';
		$string2 .= '<div style="width: 100%; overflow: auto;" id="field_1">';
		$string2 .= '<tt class="codett">';
		$string2 .= self::IgnoreParser(Array('', $string));
		$string2 .= '</tt>';
		$string2 .= '</div>';
		$string2 .= '</td>';
		$string2 .= '</tr>';
		$string2 .= '</tbody>';
		$string2 .= '</table>';
		return $string2;
	}
	
	private static function UrlParseCallback($string)
	{
		global $_SERVER;
		$string2 = html_entity_decode($string[2]);
		/*if(stristr($string2, str_replace("www.", "", $_SERVER['HTTP_HOST'])) && stristr($string2, "/forum/topic"))
		{
			$topicId = explode("id=", $string2);
			$pageId = explode("p=", $string2);
			$hashId = explode("#", $string2);
			
			if(count($topicId) > 1)
			{
				$url = "http://" . $_SERVER['HTTP_HOST'] . "/forum/topic?id=";
				$topicId = explode("&", $topicId[1]);
				$topicQuery = DB::Query("SELECT caption, id FROM forum_topics WHERE id = '" . $topicId[0] . "'");
				$topicFetch = DB::Fetch($topicQuery);
				$url .= $topicFetch['id'];
				if(count($pageId) > 1)
				{
					$pageId = explode("#", $pageId[1]);
					$url .= '&p=' . $pageId[0];
				}
				if(count($hashId) > 1)
				{
					$url .= '#' . $hashId[1];
				}
				return ' <a href="' . $url . '" target="_blank"><strong>Topic:</strong> ' . $topicFetch['caption'] . '</a>';
			}
		}*/
		$title = str_replace("http://", "", htmlspecialchars($string[2]));
		if(substr($title, (strlen($title) - 1), strlen($title)) == "/")
		{
			$title = substr($title, 0, -1);
		}
		return ' <a href="http://' . str_replace("http://", "", htmlspecialchars($string[2])) . '" target="_blank">' . $title . '</a>';
	}
	
	private static function IgnoreParser($string)
	{
		$string = self::SmiliesCode($string[1]);
		$string = str_replace("[", "&#91", $string);
		$string = str_replace("]", "&#93", $string);
		$string = str_replace("http://www.", "http%3A%2F%2Fwww.", $string);
		return $string;
	}
	
	public static function InitSmilies()
	{
		self::$SmilieArray[':f']        = 'schater.gif';
		self::$SmilieArray[':F']        = 'schater.gif';
		self::$SmilieArray['(amen)']        = 'amen.gif';
		self::$SmilieArray['(ban)']        = 'banplease.gif';
		self::$SmilieArray['(bier)']        = 'bier.gif';
		self::$SmilieArray[':D']        = 'biggrin.gif';
		self::$SmilieArray[':d']        = 'biggrin.gif';
		self::$SmilieArray['(bla)']		  = 'blabla.gif';
		self::$SmilieArray[':$']        = 'bloos.gif';
		self::$SmilieArray['(zwaai)']        = 'bye.gif';
		self::$SmilieArray['(kip)']        = 'chicksmiley.gif';
		self::$SmilieArray[':+']        = 'clown.gif';
		self::$SmilieArray[':s']        = 'confused.gif';
		self::$SmilieArray[':S']        = 'confused.gif';
		self::$SmilieArray['(h)']        = 'coool.gif';
		self::$SmilieArray['(H)']        = 'coool.gif';
		self::$SmilieArray[':\'(']        = 'cry.gif';
		self::$SmilieArray['(duivel)']        = 'develish.gif';
		self::$SmilieArray['(6)']        = 'devil.gif';
		self::$SmilieArray[':\')']        = 'emo.gif';
		self::$SmilieArray['(focus)']        = 'focus2.gif';
		self::$SmilieArray[':@']        = 'frown.gif';
		self::$SmilieArray['(pok)']        = 'frusty.gif';
		self::$SmilieArray['(handsup)']        = 'handsup.gif';
		self::$SmilieArray['(l)']        = 'wls_heart.gif';
		self::$SmilieArray['(L)']        = 'wls_heart.gif';
		self::$SmilieArray['(a)']        = 'hypocrite.gif';
		self::$SmilieArray['(A)']        = 'hypocrite.gif';
		self::$SmilieArray['(agree)']        = 'iagree.gif'; 
		self::$SmilieArray['(kerst)']        = 'kerst.gif'; 
		self::$SmilieArray['(koffie)']        = 'koffie.gif';
		self::$SmilieArray['(kutslot)']        = 'kutslotje.gif';
		self::$SmilieArray['(kwijl)']        = 'kwijl.gif'; 
		self::$SmilieArray['(cigar)']        = 'loveit.gif';
		self::$SmilieArray['(love)']        = 'loveys.gif';
		self::$SmilieArray['(k)']        = 'kiss.gif';
		self::$SmilieArray['(K)']        = 'kiss.gif';
		self::$SmilieArray['(marry)']        = 'marrysmile.gif';
		self::$SmilieArray['(michel)']        = 'michel.gif';
		self::$SmilieArray['8-|']        = 'nerd.gif';
		self::$SmilieArray['(n)']        = 'nooo.gif';
		self::$SmilieArray['(N)']        = 'nooo.gif';
		self::$SmilieArray['-o-']        = 'nopompom.gif';
		self::$SmilieArray[':L']        = 'nosmile.gif';
		self::$SmilieArray[':l']        = 'nosmile.gif';
		self::$SmilieArray['(paashaas)']        = 'paashaas.gif';
		self::$SmilieArray['(party)']        = 'party.gif';
		self::$SmilieArray['(peace)']        = 'peace.gif';
		self::$SmilieArray['*o*']        = 'pompom.gif';
		self::$SmilieArray['(puh)']        = 'puh2.gif';
		self::$SmilieArray['+o(']        = 'pukey.gif';
		self::$SmilieArray['(push)']        = 'push.gif';
		self::$SmilieArray['(koe)']        = 'rc5.gif';
		self::$SmilieArray[':o']        = 'redface.gif';
		self::$SmilieArray[':O']        = 'redface.gif';
		self::$SmilieArray[':(']        = 'sadley.gif';
		self::$SmilieArray['(aa)']        = 'santabaard.gif';
		self::$SmilieArray['(AA)']        = 'santabaard.gif';
		self::$SmilieArray['(slaan)']        = 'sbatje.gif';
		self::$SmilieArray['_o-']        = 'schater.gif';
		self::$SmilieArray[':*']        = 'shiny.gif';
		self::$SmilieArray[':x']        = 'shutup.gif';
		self::$SmilieArray[':X']        = 'shutup.gif';
		self::$SmilieArray['(sint)']        = 'sint.gif';
		self::$SmilieArray['(zzz2)']        = 'sleepey.gif';
		self::$SmilieArray['(zzz)']        = 'sleephappy.gif';
		self::$SmilieArray['(slotje)']        = 'slotje.gif';
		self::$SmilieArray['-_-']        = 'sm_eerie.gif';
		self::$SmilieArray[':)']        = 'smile.gif';
		self::$SmilieArray['(spin)']        = 'spinsmile.gif';
		self::$SmilieArray['~o']        = 'swhip.gif';
		self::$SmilieArray['~O']        = 'swhip.gif';
		self::$SmilieArray['(Y)']        = 'thumbsup.gif';
		self::$SmilieArray['(y)']        = 'thumbsup.gif';
		self::$SmilieArray['(vork)']        = 'vork.gif';
		self::$SmilieArray['(tss)']        = 'we.gif';
		self::$SmilieArray[';)']        = 'wink.gif';
		self::$SmilieArray['_o_']        = 'workshippy.gif';
		self::$SmilieArray['xd']        = 'xd.png';
		self::$SmilieArray['XD']        = 'xd.png';
		self::$SmilieArray['(gaap)']        = 'yawnee.gif';
		self::$SmilieArray['(j)']        = 'yes_new.gif';
		self::$SmilieArray['(J)']        = 'yes_new.gif';
		self::$SmilieArray[':p']        = 'yummie.gif';
		self::$SmilieArray[':P']        = 'yummie.gif';
		self::$SmilieArray[';;']        = 'wls_oehh.gif';
		self::$SmilieArray[':jop:']        = 'wls_jop.gif';
		self::$SmilieArray['(woot)']        = 'wls_woot.gif';
		self::$SmilieArray[':3']        = 'wls_dubbelepuntdrie.gif';
		self::$SmilieArray[';oo']        = 'wls_puntkommaoo.gif';
		self::$SmilieArray[':\'O']        = 'wls_nouhouw.gif';
		self::$SmilieArray[':g']        = 'wls_shahiemssmilie.png';
		self::$SmilieArray[':G']        = 'wls_shahiemssmilie.png';
		self::$SmilieArray['*dance*']        = 'wls_dance.gif';
		self::$SmilieArray['*.*']        = 'icon_kirakira.gif';
	}
}
?>