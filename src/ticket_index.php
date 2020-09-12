<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Ticket overzicht', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container epic_large">
			<div class="c_box">
				<div class="heading"><div class="icon information"></div>Mijn tickets</div>
				<div class="inner">
				<a href="/ticket/nieuw">Maak een nieuw ticket aan!</a><br /><br />
					<?php
					if($user->HasPermissions('is_support'))
					{
						$query = DB::Query("SELECT * FROM tickets ORDER BY gelezen, id DESC");
					}
					else
					{
						$query = DB::Query("SELECT * FROM tickets WHERE user_id = '" . $user->Id . "' ORDER BY gelezen, id DESC");
					}
					if(DB::NumRows($query) > 0)
					{
						$i = 1;
						while($fetch = DB::Fetch($query))
						{
							if($user->HasPermissions('is_support'))
							{
								if($fetch['gelezen'] == 0)
								{
									echo '<strong>';								
								}
							}
								echo '<a href="../../ticket/lees?id=' . $fetch['id'] . '">';						
									echo '<img align="absmiddle" src="../../cache/style_default/images/icons/famfamfam/bullet_';
									if($fetch['state'] == 'open')
									{
										echo 'green';
									}
									else
									{
										echo 'red';
									}
									echo '.png" border="0" />';
									echo $i . '&nbsp;' . htmlspecialchars($fetch['caption']) . '<br />';
								echo '</a>';
							if($user->HasPermissions('is_support'))
							{
								if($fetch['gelezen'] == 0)
								{
									echo '</strong>';								
								}
							}
							$i++;
						}
					}
					else
					{
						echo '<div class="error_notif warning">Je hebt nog geen tickets aangemaakt!</div>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Tickets' => '/ticket');
include'content/footer.php';
?>