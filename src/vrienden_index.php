<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Vrienden', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';

$p = ((isset($_GET['p']) && is_numeric($_GET['p'])) ? DB::Escape($_GET['p']) : 1);
$ps = Glybe::PaginaSysteem(count($user->Friends), $p, 15);
?>
	<div class="content">
		<div class="container big">
			<div class="c_box">
				<div class="heading"><div class="icon group"></div>Mijn vrienden (<?php echo count($user->Friends); ?>)</div>
				<div class="inner">
					<div class="psystem">
						<?php
						foreach($ps['paginas'] as $key => $value)
						{
							echo'<a href="/vrienden/index?p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
						}
						?>
						<div class="clear"></div>
					</div>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<?php
					$fQuery = DB::Query("SELECT * FROM `users_friends` WHERE (user_one_id = '" . $user->Id . "' OR user_two_id = '" . $user->Id . "') ORDER BY id LIMIT " . $ps['limit'] . ", 15");
					if(DB::NumRows($fQuery) === 0)
					{
						echo'<center><i>Je hebt nog geen vrienden!</i></center>';
					} else {
						while($fFetch = DB::Fetch($fQuery))
						{
							$friendId = (($fFetch['user_one_id'] != $user->Id) ? $fFetch['user_one_id'] : $fFetch['user_two_id']);
							$myFriends = new User($friendId, false, false, true);
							echo'	<a href="/profiel/'.strtolower($myFriends->Username).'">
									<div style="position: relative; margin-left: 5px; float: left; min-height: 150px; max-width: 124px;">
										' . $myFriends->getAvatar(120) . '
										<a href="javascript:void(0);" onclick="Glybe.Overlay.OpenUrlOverlay(\'/data/vriend_verwijderen.php\',{uId:' . $myFriends->Id . '});"><div class="icon delete" style="top: 10px; right: 3px; position: absolute;"></div></a>					
										<div style="font-weight: bold; width: 100%; word-wrap: break-word; text-align: center;">
											' . ((str_replace(" ", "", $myFriends->GetSetting("displayname")) != "") ? htmlspecialchars($myFriends->GetSetting("displayname")) : htmlspecialchars(ucfirst($myFriends->Username))) . '
										</div>
									</div>
								</a>';
						}
					}
					?>
					<div class="clear"></div>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<div class="psystem">
						<?php
						foreach($ps['paginas'] as $key => $value)
						{
							echo'<a href="/vrienden/index?p=' . $value[1] . '"><div class="item">' . $value[0] . '</div></a>';
						}
						?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="container small">
			<div class="c_box">
				<div class="heading"><div class="icon house"></div>Menu</div>
				<div class="inner">
					<a href="/vrienden/index">
						<div class="nav_link">
							<div class="icon group"></div>
							Mijn vrienden
						</div>
					</a>
					<a href="/vrienden/requests">
						<div class="nav_link">
							<div class="icon user_comment"></div>
							Vrienden verzoeken 
							<?php
							if(count($user->FriendsReq) > 0)
							{
								echo '<strong>(' . count($user->FriendsReq) . ')</strong>';
							}
							?>
						</div>
					</a>
					<a href="/vrienden/suggesties">
						<div class="nav_link">
							<div class="icon group_add"></div>
							Vrienden van vrienden
						</div>
					</a>
					<a href="/vrienden/nieuw">
						<div class="nav_link">
							<div class="icon magnifier"></div>
							Zoek vrienden op
						</div>
					</a>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Vrienden' => '/vrienden', 'Mijn vrienden' => '/vrienden/index');
include'content/footer.php';
?>