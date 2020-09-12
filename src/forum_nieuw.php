<?php
include'includes/inc.bootstrap.php';

if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$id = DB::Escape($_GET['id']);
} else {
	die();
}

$foraQuery = DB::Query("SELECT * FROM `forum_foras` WHERE `id` = '" . $id . "'");
if(DB::NumRows($foraQuery) === 0)
{
	die();
}
$foraFetch = DB::Fetch($foraQuery);

if(isset($_POST['tp_submit']))
{
	$title = DB::Escape($_POST['tp_title']);
	$text = DB::Escape($_POST['tp_text']);
	$bot = (isset($_POST['tp_bot']) && $_POST['tp_bot'] == '1' && $user->HasPermissions("is_team"));
	
	if(!empty($title) && !empty($text))
	{
		if(strlen(str_replace(" ", "", $title)) >= 3 && strlen($title) <= 100)
		{
			if(strlen(str_replace(" ", "", $text)) >= 3 && strlen($text) <= 500000)
			{
				$alnum = preg_replace("/[^a-zA-Z0-9\s]/", "", $title);
				if(strlen($alnum) > 1)
				{
					if($user->HasPermissions($foraFetch['permission_for_post']))
					{
						DB::Query("INSERT INTO `forum_topics` (forum_id, user_id, caption, created_at, last_post) VALUES ('" . $foraFetch['id'] . "', '" . (($bot) ? 3 : $user->Id) . "', '" . $title . "', UNIX_TIMESTAMP(), '0')");
						$lastId = DB::InsertId();
						DB::Query("INSERT INTO `forum_posts` (first_post, forum_id, topic_id, user_id, message, post_timestamp) VALUES ('1', '" . $foraFetch['id'] . "', '" . $lastId . "', '" . (($bot) ? 3 : $user->Id) . "', '" . $text . "', UNIX_TIMESTAMP())");
						$lastPostId = DB::InsertId();
						DB::Query("UPDATE `forum_topics` SET `last_post` = UNIX_TIMESTAMP() WHERE `id` = '" . $lastId . "'");
						sleep(1);
						header("location: /forum/postredir?pid=" . $lastPostId);
					} else {
						$error = 'Je hebt geen rechten om in deze categorie te posten van het Forum';
					}
				} else {
					$error = 'Je titel moet minimaal 2 letters (a-z) of 2 cijfers (0-9) bevatten.';
				}
			} else {
				$error = 'Het bericht van je topic is te lang of te kort, minimaal 3 en maximaal 500.000 karakters.';
			}
		} else {
			$error = 'Je titel is te lang of te kort, minimaal 3 en maximaal 100 karakters.';
		}
	} else {
		$error = 'Je moet wel alles invullen wat je moet invullen om een topic te maken.';
	}
}

$page = Array('title' => 'Nieuw topic', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<?php if($user->HasPermissions($foraFetch['permission_for_post'])) { ?>
		<div class="container epic_large">
			<?php echo((isset($error)) ? '<div class="error_notif error">' . $error . '</div>' : ''); ?>
		</div>
		<div class="container" style="width: 300px;">
			<div class="c_box">
				<div class="heading"><div class="icon page"></div>UBB Codes</div>
				<div class="inner">
					<div>
						<input type="button" onclick="Glybe.Forum.AddUBB('[b]', '[/b]');" value="b" style="font-weight: bold;" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[i]', '[/i]');" value="i" style="font-style: italic;" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[u]', '[/u]');" value="u" style="text-decoration: underline;" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[s]', '[/s]');" value="s" style="text-decoration: line-through;" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[url]', '[/url]');" value="URL" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[quote]', '[/quote]');" value="Quote" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[code]', '[/code]');" value="Code" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[youtube]', '[/youtube]');" value="YouTube" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[spoiler]', '[/spoiler]');" value="Spoiler" class="ubb-button" />
						<input type="button" onclick="Glybe.Forum.AddUBB('[img]', '[/img]');" value="img" class="ubb-button" />
					</div>
				</div>
			</div>
		</div>
		<div class="container" style="width: 580px;">
			<?php if(isset($_POST['tp_sample'])) { ?>
			<div class="c_box">
				<div class="heading"><div class="icon eye"></div>Voorbeeld</div>
				<div class="inner">
					<?php echo UBB::Parse($_POST['tp_text']); ?>
				</div>
			</div>
			<?php } ?>
			<div class="c_box">
				<div class="heading"><div class="icon pencil"></div>Topic aanmaken in <?php echo $foraFetch['caption']; ?></div>
				<div class="inner">
					<form action="" method="post">
						<strong>Titel van je Topic</strong><br/>
						<i style="color: Grey;">Minimaal 3 en maximaal 100 karakters lang</i><br/>
						<input type="text" name="tp_title" style="width: 540px;" value="<?php echo((isset($_POST['tp_title'])) ? htmlspecialchars($_POST['tp_title']) : ''); ?>" /><br/>
						<br/>
						<strong>Bericht</strong><br/>
						<i style="color: Grey;">Minimaal 3 en maximaal 500.000 karakters lang</i><br/>
						<textarea id="topic_post_txt" name="tp_text" style="width: 540px; height: 220px;"><?php echo((isset($_POST['tp_text'])) ? htmlspecialchars($_POST['tp_text']) : ''); ?></textarea><br/>
						<br/>
						<?php if($user->HasPermissions("is_admin")) { ?>
						<strong>Glybe-Bot</strong><br/>
						<i style="color: Grey;">Je kunt het bericht posten als Glybe</i><br/>
						<input type="checkbox" name="tp_bot" value="1" />Posten als Glybe-Bot<br/>
						<br/>
						<?php } ?>
						<input type="submit" name="tp_submit" value="Topic aanmaken" />
						<input type="submit" name="tp_sample" value="Voorbeeld" />
					</form>
				</div>
			</div>
		</div>
		<?php } else { ?>
		<div class="container epic_large">
			<div class="error_notif error">Je mag niet posten in dit gedeelte van het Forum!</div>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
<?php
include'content/footer.php';
?>