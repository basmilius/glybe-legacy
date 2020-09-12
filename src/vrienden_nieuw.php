<?php
include'includes/inc.bootstrap.php';

$uId = ((isset($_GET['id']) && is_numeric($_GET['id'])) ? DB::Escape($_GET['id']) : 0);

$page = Array('title' => 'Nieuwe vriendschap', 'access' => Array(true, false));
include'content/header.php';
?>
	<div class="content" style="width: 500px; border: 1px solid #C6C6C6; border-radius: 6px; margin-top: 80px;">
		<div class="container" style="width: 490px;">
			<div class="c_box">
				<div class="heading"><div class="icon user_add"></div>Vrienden worden</div>
				<div class="inner" style="text-align: center;">
					<?php
					if(true)
					{
						echo'<div class="error_notif error">Link is uitgeschakeld.</div>';
					} else 
					if(DB::NumRowsQuery("SELECT 1 FROM users WHERE id = '" . $uId . "'") > 0)
					{
						$u = new User($uId, false, false, true);
						if(DB::NumRowsQuery("SELECT 1 FROM users_friends WHERE (user_one_id = '" . $user->Id . "' AND user_two_id = '" . $u->Id . "' OR user_one_id = '" . $u->Id . "' AND user_two_id = '" . $user->Id . "')") === 0)
						{
							if($u->Id != $user->Id)
							{
								if(DB::NumRowsQuery("SELECT 1 FROM users_friends_requests WHERE (user_from = '" . $user->Id . "' AND user_to = '" . $u->Id . "' OR user_to = '" . $user->Id . "' AND user_from = '" . $u->Id . "')") === 0)
								{
									DB::Query("INSERT INTO `notifications` (`user_id`, `user_from_id`, `icon`, `title`, `message`, `n_ts`) VALUES ('" . $u->Id . "', '" . $user->Id . "', 'user_add', 'Vrienden-verzoek', '<strong>" . DB::Escape(htmlspecialchars(((str_replace(" ", "", $user->GetSetting("displayname")) != "") ? $user->GetSetting("displayname") : $user->Username))) . "</strong><br/>wilt vrienden met je worden op Glybe!', UNIX_TIMESTAMP())");
									DB::Query("INSERT INTO users_friends_requests (user_from, user_to, date_requested) VALUES ('" . $user->Id . "', '" . $u->Id . "', UNIX_TIMESTAMP())");
									echo'<div class="error_notif success">Er is een vrienden-verzoek gestuurd naar ' . htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname")) != "") ? $u->GetSetting("displayname") : $u->Username)) . '</div>';
								} else {
									echo'<div class="error_notif error">Er bestaat al een verzoek tussen jullie 2</div>';
								}
							} else {
								echo'<div class="error_notif error">Serieus, hoe laag ben je als je vrienden met jezelf wilt worden?</div>';
							}
						} else {
							echo'<div class="error_notif error">Je bent al bevriend met ' . htmlspecialchars(((str_replace(" ", "", $u->GetSetting("displayname")) != "") ? $u->GetSetting("displayname") : $u->Username)) . '</div>';
						}
					} else {
						echo'<div class="error_notif error">Ongeldig verzoek.</div>';
					}
					?>
					<center><input type="button" value="Door naar Glybe" onclick="window.location = '/';" /></center>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>