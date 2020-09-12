<?php
include'includes/inc.bootstrap.php';

if(isset($_GET['id']) && is_numeric($_GET['id']))
{ } else {
	header("location: /berichten/index");
	die();
}

$id = Glybe::Security($_GET['id']);

$pmQuery = DB::Query("SELECT m.*, ua.username sender, ub.username receiver FROM messages m, users ua, users ub WHERE m.id = '" . $id . "' AND ua.id = m.user_from_id AND ub.id = m.user_to_id AND m.state != 'perm_deleted' AND (m.user_from_id = '" . $user->Id . "' OR m.user_to_id = '" . $user->Id . "')");
if(DB::NumRows($pmQuery) === 0)
{
	header("location: /berichten/index");
	die();
}
$pmFetch = DB::Fetch($pmQuery);

if($user->Id == $pmFetch['user_to_id'] && $pmFetch['readed'] == 'false')
{
	DB::Query("UPDATE `messages` SET `readed_on` = UNIX_TIMESTAMP(), `readed` = 'true' WHERE `id` = '" . $pmFetch['id'] . "'");
}

if(isset($_GET['delete']) && $_GET['delete'] == 'true' && $user->Id == $pmFetch['user_to_id'])
{
	DB::Query("UPDATE `messages` SET `state` = 'deleted' WHERE `id` = '" . $pmFetch['id'] . "'");
	header("location: /berichten/index?fid=" . $pmFetch['folder_id']);
}

$page = Array('title' => $pmFetch['subject'], 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container small">
			<div class="c_box">
				<div class="heading"><div class="icon house"></div>Mappen</div>
				<div class="inner">
					<a href="/berichten/index"><div class="nav_link"><div class="icon email"></div>Postvak IN <?php $c = DB::NumRowsQuery("SELECT 1 FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `folder_id` = '0' AND `state` = 'open' AND `readed` = 'false'"); echo(($c > 0) ? '<strong>(' . $c . ')</strong>' : ''); ?></div></a>
					<a href="/berichten/verzonden"><div class="nav_link"><div class="icon email_go"></div>Verzonden</div></a>
					<a href="/berichten/verwijderd"><div class="nav_link"><div class="icon email_delete"></div>Verwijderd <?php $c = DB::NumRowsQuery("SELECT 1 FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `state` = 'deleted' AND `readed` = 'false'"); echo(($c > 0) ? '<strong>(' . $c . ')</strong>' : ''); ?></div></a>
					<a href="/berichten/maak"><div class="nav_link"><div class="icon email_edit"></div>Nieuw bericht</div></a>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<?php
					$fQuery = DB::Query("SELECT * FROM `messages_folders` WHERE `user_id` = '" . $user->Id . "'");
					if(DB::NumRows($fQuery) === 0)
					{
						echo '<center><i>Je hebt nog geen mappen!</i></center>';
					} else {
						while($fFetch = DB::Fetch($fQuery))
						{
							$c = DB::NumRowsQuery("SELECT 1 FROM `messages` WHERE `user_to_id` = '" . $user->Id . "' AND `folder_id` = '" . $fFetch['id'] . "' AND `state` = 'open' AND `readed` = 'false'");
							echo '<a href="/berichten/index?fid=' . $fFetch['id'] . '"><div class="nav_link"><div class="icon ' . $fFetch['icon'] . '"></div>' . htmlspecialchars($fFetch['caption']) . (($c > 0) ? ' <strong>(' . $c . ')</strong>' : '') . '&nbsp;</div></a>';
						}
					}
					?>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<a href="javascript:void(0);" onclick="Glybe.Berichten.CreateFolder();"><div class="nav_link"><div class="icon folder_add"></div>Nieuwe map maken</div></a>
				</div>
			</div>
		</div>
		<div class="container big">
			<div class="c_box">
				<div class="heading"><div class="icon email"></div><?php echo $pmFetch['subject']; ?></div>
				<div class="inner">
					<table class="top_list" border="0" cellspacing="1" style="width: 100%;">
						<tr>
							<td style="width: 60px;"><strong>Datum</strong></td>
							<td style="width: 180px;"><?php echo strftime('%e %B %Y om %H:%M:%S', $pmFetch['sended_on']); ?></td>
							<td style="width: 60px;"></td>
							<td style="width: 180px;" align="right">
								<a href="#"><img src="/cache/style_default/images/icons/famfamfam/error.png" style="margin: -3px 2px;" /> Reporten</a>
							</td>
						</tr>
						<tr>
							<td><strong>Afzender</strong></td>
							<td><?php echo $pmFetch['sender']; ?></td>
							<td><strong>Ontvanger</strong></td>
							<td><?php echo $pmFetch['receiver']; ?></td>
						</tr>
					</table>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<div style="position: relative; margin: 5px 10px;"><?php echo UBB::Parse($pmFetch['message']); ?></div>
					<?php if($user->Id == $pmFetch['user_to_id']) { ?>
					<div style="border-top: 1px solid #C6C6C6; margin: 5px;"></div>
					<input type="button" value="Beantwoorden" onclick="window.location = '/berichten/maak?in_reply_to=<?php echo $pmFetch['id']; ?>';" />
					<input type="button" value="Verwijderen" onclick="window.location = '/berichten/bericht?id=<?php echo $pmFetch['id']; ?>&delete=true';" />
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Berichten' => '/berichten/index');
include'content/footer.php';
?>