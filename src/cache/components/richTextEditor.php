<!DOCTYPE html>
<html>
<head>
	<title>GlybeForum's Rich-Text-Editor</title>
	<style type="text/css">
	html body
		{
			margin: 5px;
			padding: 0px;
			font-family: Verdana;
			font-size: 12px;
			color: #333333;
		}
	html body div#RichTextFrameDiv
		{
			position: absolute;
			top: 5px;
			left: 5px;
			bottom: 5px;
			right: 5px;
		}
	html body span#ClickToType
		{
			color: #9A9A9A;
		}
	</style>
	<script type="text/javascript">
	var RteFrame = {
		"OnKeyUp": function(e) {
			var d = document.body;
			
		},
		"OnClick": function(e) {},
		"Command": function(cmd) {
			window.focus();
			return document.execCommand(cmd, false);
		},
		"GetText": function() {
			return document.body.innerHTML;
		}
	};
	document.designMode = "On";
	document.contentEditable = true;
	</script>
</head>
<body onkeyup="RteFrame.OnKeyUp(window.event);" onclick="RteFrame.OnClick(e);">
	
</body>
</html>