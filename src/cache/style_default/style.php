<?php
header("Content-Type: text/css");

function removenewline($in)
{
	return str_replace(Array("\r\n", "\n", "\r", "	"), "", $in);
}

ob_start("removenewline");

include "main.css";
include "heading.css";
include "content.css";
include "footer.css";
include "icons.css";
include "user_suggestion_input.css";
include "smart_textarea.css";
include "selectbox.css";
include "user_status.css";
include "ubb.css";
include "overlay.css";
include "notifications.css";
include "tooltip.css";
include "menu_black.css";
include "timeline.css";
include "profile.css";
include "music.css";
include "beheer.css";
?>