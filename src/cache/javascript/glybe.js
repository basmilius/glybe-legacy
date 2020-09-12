String.prototype.StartsWith = function(t, i) {
	if (i==false) { 
		return (t == this.substring(0, t.length));
	} else {
		return (t.toLowerCase() == this.substring(0, t.length).toLowerCase());
	}
};

(function(window){
	
	var Glybe = {
		"Storage": {
			"Database": [],
			"WindowIsActive": false,
			"ServerConnected": false
		},
		"Initialize": function() {
			Glybe.Storage.Database["logostate"] = 0;
			
			Glybe.Events.Initialize();
			
			Glybe.WebComponents.GlybeLogo(50);
			Glybe.WebComponents.SmartTextarea();
			Glybe.WebComponents.UsersuggestionBox();
			
			Glybe.Notifications.Initialize();
		},
		"Notifications": {
			"Initialize": function() {
				window.setInterval(Glybe.Notifications.Check, 6000);
			},
			"Create": function(t, c, m, l, i) {
				if(i == undefined) var i = 3000;
				var n = window.document.createElement("div");
				var h = window.document.createElement("div");
				var g = window.document.createElement("div");
				n.className = "notif_msg";
				h.className = "heading";
				g.className = "inner";
				n.appendChild(h);
				n.appendChild(g);
				h.innerHTML = '<div class="icon ' + m + '"></div>' + t;
				g.innerHTML = c;
				//jQuery(g).click(function(){window.location=l;});
				jQuery("div.notif_holder div").first().before(n);
				jQuery(n).animate({opacity:'toggle',marginBottom:'-=' + jQuery(n).height() + 'px'},0);
				jQuery(n).animate({opacity:'toggle',marginBottom:'+=' + jQuery(n).height() + 'px'},300);
				jQuery(n).delay(i).animate({opacity:'toggle',marginBottom:'-=' + jQuery(n).height() + 'px'}, 300, function(){jQuery(this).delay(500).remove();});
			},
			"Check": function() {
				if(Glybe.Storage.WindowIsActive != true) return;
				jQuery.getJSON("/data/notif_checker.php", function(d) {
					if(d.Notifications.length > 0)
					{
						var n = d.Notifications;
						for(var i in n)
						{
							var g = n[i];
							var t = '<table border="0" cellspacing="0" style="width: 100%;"><tr><td style="width: 40px;">' + g[3] + '</td><td style="padding: 10px;">' + g[2] + '</td></tr></table>';
							Glybe.Notifications.Create(g[1], t, g[0], '#', 5000);
						}
					}
				});
			}
		},
		"TopNotif": {
			"Error": function(text) {
				Glybe.TopNotif.MakeNotif(text, 'error');
			},
			"Warning": function(text) {
				Glybe.TopNotif.MakeNotif(text, 'warning');
			},
			"Success": function(text) {
				Glybe.TopNotif.MakeNotif(text, 'success');
			},
			"Information": function(text) {
				Glybe.TopNotif.MakeNotif(text, 'information');
			},
			"MakeNotif": function(text, sClass) {
				var n = window.document.createElement("div");
				n.innerHTML = text;
				n.className = "top_notif " + sClass;
				window.document.body.appendChild(n);
				jQuery(n).animate({height:'toggle',opacity:'toggle'},0);
				jQuery(n).animate({height:'toggle',opacity:'toggle'},500).delay(3000).animate({height:'toggle',opacity:'toggle'},500);
			}
		},
		"Overlay": {
			"Report": function(post)
			{
				jQuery.post("/includes/report.php", { id: post },
				function(data)
				{
					jQuery('#overlay_content').html('<a href="#" onclick="jQuery(\'#overlay\').fadeOut(\'slow\'); return false;"><img src="../../../../cache/style_default/images/icons/famfamfam/cancel.png" height="16" width="16" alt="Sluiten" style="floaT: right;" /></a><div class="clear"></div>' + data);
					jQuery('#overlay').fadeIn('slow');
				});
					
			},
			"OpenUrlOverlay": function(url, params) {
				jQuery("div.overlay").remove();
				var o = window.document.createElement("div");
				o.className = "overlay";
				window.document.body.appendChild(o);
				
				var obg = window.document.createElement("div");
				obg.className = "backdrop";
				o.appendChild(obg);
				jQuery(obg).fadeOut(0);
				jQuery(obg).fadeIn(500);
				
				var osc = window.document.createElement("div");
				osc.className = "screen";
				osc.innerHTML = '<div style="padding: 20px 40px;">Bezig met laden..</div>';
				osc.style.width = "180px";
				osc.style.height = "50px";
				osc.style.marginLeft = "-90px";
				osc.style.marginTop = "-25px";
				o.appendChild(osc);
				
				jQuery.post(url, ((params != undefined) ? params : {}), function(d) {
					jQuery(osc).html(d);
				});
			},
			"SetSize": function(w, h) {
				jQuery("div.overlay div.screen").animate({
					width: w + 'px',
					height: h + 'px',
					marginLeft: (0 - (w / 2)) + 'px',
					marginTop: (0 - (h / 2)) + 'px'
				}, 0);
			},
			"Close": function() {
				jQuery("div.overlay").remove();
			}
		},
		"Forum": {
			"TextAreaFocused": false,
			"Fouten": ["Je bent niet ingelogd.", "Je mag maximaal 1x per 5 seconden iets posten.", "Je hebt niet de juiste rechten om hier te posten.", "Dit topic bestaat niet (meer).", "Dit topic is gesloten.", "Je bericht is te kort of te lang, 2-50.000 karakters toegestaan.", "glb_show_msg", "glb_newpageloader"],
			"Post": function(topic_id, stoken_a, stoken_b, message, bot, _page) {
				var err = jQuery('#topic_post_error');
				var inf = jQuery('#topic_post_info');
				var suc = jQuery('#topic_post_success');
				if(message.length < 2 || message.length > 50000)
				{
					err.html(Glybe.Forum.Fouten[5]);
					err.slideDown();
				} else {
					inf.html("Je bericht word gepost..");
					inf.slideDown(0);
					jQuery.post("/includes/forumutils.php", { _act: 'post_message', pageNum: _page, topic_id: topic_id, stoken_a: stoken_a, bot: bot, stoken_b: stoken_b, message: message }, function(data)
					{
						inf.slideUp(0);
						if(data == "6" || data == "7")
						{
							if(data == "7")
							{
								window.location = '/forum/postredir?topic=' + topic_id + '&laatste';
								return;
							} else {
								Glybe.Forum.GetNewMessages(topic_id, _page);
							}
							suc.html("Je bericht is gepost!");
							suc.slideDown().delay(1000).slideUp();
							window.document.getElementById('topic_post_txt').value = "";
						} else {
							err.html(Glybe.Forum.Fouten[data]);
							err.slideDown().delay(1000).slideUp();
						}
					});
				}
			},
			"GiveRespect": function(userId) {
				Glybe.TopNotif.Information('Bezig met verwerken van verzoek..');
				jQuery.post("/includes/forumutils.php", { _act: 'give_respect', ui: userId }, function(d) {
					if(d != "ok")
					{
						Glybe.TopNotif.Error(d);
					} else {
						Glybe.TopNotif.Success('Je hebt succesvol respect gegeven!');
					}
				});
			},
			"GetNewMessages": function(topic_id, p) {
				if(Glybe.Storage.WindowIsActive != true) return;
				var since_id = jQuery("input.topicLastId").last().val();
				jQuery("input.topicLastId").remove();
				jQuery.post("/includes/forumutils.php", { _act: 'get_messages', topic_id: topic_id, p: p, since_id: since_id }, function(data)
				{
					if(data != "")
					{
						var d = window.document.createElement("div");
						d.className = "topic_hidden_new_messages";
						d.innerHTML = data;
						jQuery("table.postTable").last().after(d);
						jQuery(d).slideUp(0);
						if(Glybe.Forum.TextAreaFocused == false)
						{
							jQuery("div#topic_new_messages_inf").fadeOut(0);
							jQuery("div.topic_hidden_new_messages").slideDown(800);
						} else {
							jQuery("div#topic_new_messages_inf").fadeIn(0);
						}
					}
				});
			},
			"GetUbbMessage": function(id) {
				jQuery.post("/includes/forumutils.php", { _act: 'get_ubb_message', pid: id }, function(data)
				{
					if(data != "")
					{
						window.document.getElementById('topic_post_txt').focus();
						window.document.getElementById('topic_post_txt').value = window.document.getElementById('topic_post_txt').value + data + "\r\n";
						window.document.getElementById('topic_post_txt').focus();
					}
				});
			},
			"PostEditSubmit": function(pid, fid, msg, caption, start)
			{
				jQuery.post("/includes/forumutils.php", { _act: 'PostEditSubmit', pid: pid, fid: fid, message: msg, caption: caption },
				function(data1)
				{
					if(start == 1)
					{
						location.reload();						
					}
					else
					{
						jQuery('#postContent_' + pid).html(data1);
					}			
				});
			},
			"PostEditCancel": function(pid, fid)
			{
				jQuery.post("/includes/forumutils.php", { _act: 'PostEditCancel', pid: pid, fid: fid },
				function(data2)
				{
					if(data2)
					jQuery('#postContent_' + pid).html(data2);
				});
			},
			"PostEdit": function(pid, fid, start)
			{
				jQuery.post("/includes/forumutils.php", { _act: 'PostEdit', pid: pid, fid: fid, start: start },
				function(data)
				{
					if(data && data != "")	
					jQuery('#postContent_' + pid).html(data);
				});					
			},
			"Edits": function(pid)
			{
				jQuery('#allEdits_' + pid).slideToggle("slow");
			},
			"PostDel": function(pid)
			{
				jQuery.post("/includes/forumutils.php", { _act: 'PostDel', pid: pid },
				function(data1)
				{
					jQuery('#postDel_' + pid).html(data1);
				});
			},
			"PostDelBack": function(pid)
			{
				jQuery.post("/includes/forumutils.php", { _act: 'PostDelBack', pid: pid },
				function(data11)
				{
					jQuery('#del_' + pid).hide();
					jQuery('#postDel_' + pid).html(data11);
				});
			},
			"FirstPost": function(pid)
			{
				jQuery.post("/includes/forumutils.php", { _act: 'FirstPost', pid: pid },
				function(data1)
				{
					location.reload();
				});
			},
			"FirstPost2": function(pid)
			{
				jQuery.post("/includes/forumutils.php", { _act: 'FirstPost2', pid: pid },
				function(data1)
				{					
					location.reload();
				});
			},
			"AddUBB": function (a, b)
			{
				var c = document.getElementById('topic_post_txt');	if (!b)	{		b = ' '+a+' ';		a = '';	}	if (c.selectionStart || c.selectionStart == 0)	{		var ss = c.selectionStart;		var se = c.selectionEnd;		c.value = c.value.substring(0, ss) + a + c.value.substring(ss, se) + b + c.value.substring(se, c.value.length);		if (a.length != 0)		{			c.selectionStart = ss;			c.selectionEnd = se + a.length + b.length;		}		else		{			c.selectionStart = se + a.length + b.length;			c.selectionEnd = c.selectionStart;		}		c.focus();	}	else if (c.createTextRange)	{		c.focus();		document.selection.createRange().text = a + document.selection.createRange().text + b;	}	else	{		txtArea.value = txtArea.value + a + b;	}
			}
		},
		"WebComponents": {
			"GlybeLogo": function(time) {
				window.setTimeout(function() {
					
					var logo = window.document.getElementById('glb_header_logo');
					var state = 0;
					var newTime = 0;
					switch(Glybe.Storage.Database["logostate"])
					{
						default:
						case 0:
							logo.style.backgroundPosition = '0px 0px';
							state = 1;
							newTime = 2000;
							break;
						case 1:
							logo.style.backgroundPosition = '0px -50px';
							state = 2;
							newTime = 200;
							break;
						case 2:
							logo.style.backgroundPosition = '0px 0px';
							state = ((Math.floor(Math.random() * 30) < 18) ? 0 : 3);
							newTime = 2000;
							break;
						case 3:
							logo.style.backgroundPosition = '0px -100px';
							state = 0;
							newTime = 130;
							break;
					}
					Glybe.Storage.Database["logostate"] = state;
					Glybe.WebComponents.GlybeLogo(newTime);
					
				}, time);
			},
			"SmartTextarea": function() {
				jQuery("div.smart_textarea").each(function() {
					var box = jQuery(this);
					
					var txtLengthCheck = function(sp, ta) {
						if(ta.val() == "")
						{
							sp.children('span.click_to_type').show(0);
						} else {
							sp.children('span.click_to_type').hide(0);
						}
						sp.children('span.beam').remove();
						sp.children('span.char').remove();
						var c = ta.val().split('');
						for(var i in c) {
							var cr = window.document.createElement('span');
							cr.className = "char";
							cr.innerHTML = c[i];
							sp.children('span.click_to_type').before(cr);
						}
						sp.children('span.click_to_type').before('<span class="beam">|</span>');
					}
					
					jQuery(this).click(function() { box.children('textarea').focus(); });
					jQuery(this).children('div.smart_part').children('span.beam').hide();
					jQuery(this).children('textarea').keyup(function() {
						var sp = jQuery(this).parent().children('div.smart_part');
						var ta = jQuery(this);
						txtLengthCheck(sp, ta);
					});
					jQuery(this).children('textarea').keydown(function() {
						var sp = jQuery(this).parent().children('div.smart_part');
						var ta = jQuery(this);
						txtLengthCheck(sp, ta);
					});
					jQuery(this).children('textarea').focus(function() {
						var sp = jQuery(this).parent().children('div.smart_part');
						sp.children('span.beam').show();
					});
					jQuery(this).children('textarea').blur(function() {
						var sp = jQuery(this).parent().children('div.smart_part');
						sp.children('span.beam').hide();
					});
				});
			},
			"UsersuggestionBox": function() {
				jQuery("div.input.user_suggestion").each(function() {
					jQuery(this).click(function() { jQuery(this).children('div.input_box').children('input').focus(); });
					
					var func = function(input, e) {
						if(input.val() == "" && e && e.keyCode && e.keyCode == 8)
						{
							input.parent().parent().children('div.user_item').last().remove();
							return false;
						}
						if(input.val() != "" && e && e.keyCode && e.keyCode == 13 || input.val() != "" && e && e.keyCode && e.keyCode == 32 || input.val() != "" && e && e.keyCode && e.keyCode == 9)
						{
							var a = eval(input.attr("data-source"));
							for(var b in a)
							{
								var arr = a[b][1];
								for(var i in arr)
								{
									if(!arr[i][0].StartsWith(input.val())) continue;
									if(arr[i][0].toLowerCase() != input.val().toLowerCase()) continue;
									if(checkIfHasItem(input.parent().parent(), arr[i][0]))
									{
										if(input.parent().parent().parent().children('div#input_notif_error').length > 0)
										{
											input.parent().parent().parent().children('div#input_notif_error').remove();
										}
										var e = window.document.createElement("div");
										e.id = "input_notif_error";
										e.innerHTML = '<div class="error_notif warning">Dat item heb je al toegevoegd!</div>';
										input.parent().parent().before(e);
										jQuery(e).delay(2000).animate({opacity:'toggle',height:'toggle'}, 500);
										continue;
									}
									input.parent().parent().children('div.input_box').before('<div class="user_item">' + input.val() + '<input type="hidden" name="' + input.parent().parent().attr("name").replace("input_", "") +  '[]" value="' + input.val() + '" /></div>');
									reloadItems(input.parent().parent());
									input.val("");
									input.focus();
									input.parent().parent().children('div.suggestion_box').remove();
									break;
								}
							}
							if(input.val() == " ")
							{
								input.val("");
							}
							return false;
						}
						input.parent().children('div.fake_input').html(input.val());
						input.width(input.parent().children('div.fake_input').outerWidth() + 12);
						if(input.val() == " ")
						{
							input.val("");
						}
					};
					
					var checkIfHasItem = function(box, item) {
						var hasItem = false;
						box.children('div.user_item').each(function(){
							if(jQuery(this).text().toLowerCase() == item.toLowerCase())
							{
								hasItem = true;
							}
						});
						return hasItem;
					};
					
					jQuery(this).children('div.input_box').children('input').keydown(function(e){
						if(func(jQuery(this), e) == false) return false;
					});
					jQuery(this).children('div.input_box').children('input').keyup(function(e){
						if(func(jQuery(this), undefined) == false) return false;
						searchSuggestions(jQuery(this).val(), jQuery(this).parent().parent(), eval(jQuery(this).attr("data-source")), checkIfHasItem);
					});
					
					var reloadItems = function(box) {
						jQuery(box).children('div.user_item').each(function() {
							jQuery(this).click(function(){jQuery(this).remove();});
						});
					};
					reloadItems(this);
					
					var searchSuggestions = function(val, box, a, checkIfHasItem) {
						var data = a;
						box.children('div.suggestion_box').remove();
						if(val.length == 0) {
							return false;
						}
						var d = window.document.createElement("div");
						var gehad = [];
						var m = 0;
						d.className = "suggestion_box";
						for(var b in data) {
							var hasTitle = false;
							var dataSource = data[b][1];
							var c = 0;
							for(var i in dataSource) {
								if(!dataSource[i][0].StartsWith(val)) continue;
								if(checkIfHasItem(box, dataSource[i][0])) continue;
								if(gehad.indexOf(dataSource[i][0].toLowerCase()) > -1) continue;
								c = (c + 1);
								if(c == 1) m = (m + 1);
								if(c == 4) break;
								if(hasTitle == false) {
									hasTitle = true;
									var o = window.document.createElement("div");
									o.className = "suggestion_item static";
									o.innerHTML = data[b][0];
									d.appendChild(o);
								}
								var o = window.document.createElement("div");
								o.className = "suggestion_item";
								o.innerHTML = eval("'<span>' + dataSource[i][0].replace(/" + val + "/gi, '<u>" + val + "</u>') + '</span> <i style=\"float: right;\">" + dataSource[i][1] + "</i>';");
								gehad[gehad.length] = dataSource[i][0].toLowerCase();
								jQuery(o).mouseup(function() {
									jQuery(this).parent().parent().children('div.input_box').before('<div class="user_item">' + jQuery(this).children('span').text() + '<input type="hidden" name="' + jQuery(this).parent().parent().attr("name").replace("input_", "") +  '[]" value="' + jQuery(this).children('span').text() + '" /></div>');
									reloadItems(jQuery(this).parent().parent());
									jQuery(this).parent().parent().children('div.input_box').children('input').val("");
									jQuery(this).parent().parent().children('div.input_box').children('input').focus();
									jQuery(this).parent().remove();
								});
								d.appendChild(o);
							}
						}
						if(c == 0 && m == 0)
						{
							var o = window.document.createElement("div");
							o.className = "suggestion_item static";
							o.innerHTML = "Geen suggesties gevonden";
							o.style.fontWeight = "normal";
							o.style.fontStyle = "italic";
							d.appendChild(o);
						}
						box.append(d);
					}
				});
			}
		},
		"RteFrame": {
			"Get": function(d) {
				return window.document.getElementById(d).contentWindow;
			}
		},
		"Profile": {
			"GetGuestbook": function(u, p) {
				jQuery.post("/includes/profileutils.php", { _act: 'get_guestbook', u: u, p: p }, function(data)
				{
					jQuery("div#guestbook_goes_here").html(data);
				});
			},
			"GetFriends": function(u, p) {
				jQuery.post("/includes/profileutils.php", { _act: 'get_friends', u: u, p: p }, function(data)
				{
					jQuery("div#myFriends").html(data);
				});
			},
			"PostGuestbook": function(u, t) {
				var err = jQuery("div#gb_post_error");
				if(t.replace(/ /gi, "").length < 2)
				{
					err.html("Je bericht is te kort! Minimaal 2 karakters (spaties tellen niet mee)");
					err.slideDown().delay(3000).slideUp();
					return;
				}
				jQuery.post("/includes/profileutils.php", { _act: 'post_guestbook', u: u, t: t }, function(data)
				{
					Glybe.Profile.GetGuestbook(u, 1);
				});
			},
			"Search": function(val) {
				val = val.split('v=')[1].substr(0, 11);
				jQuery.get("http://data.basmilius.com/player/get_video_title?video_id=" + val, function(title) {
					jQuery.getJSON("/data/get_itunes?term=" + title.replace(/ft\./gi, '%26') + "&limit=1", function(response) {
						console.log(response);
					});
				});
			}
		},
		"Berichten": {
			"Command": function(cmd, data_a) {
				var ids = "";
				var msgs = window.document.getElementsByClassName('pm_sel');
				var err = jQuery("div#pm_error_notif");
				for(var i in msgs)
				{
					if(msgs[i].checked == false) continue;
					var n = msgs[i].name;
					if(typeof(n) == "undefined") continue; 
					ids += n.substr(3, (n.length - 4)) + ",";
				}
				ids = ids.substr(0, (ids.length - 2));
				if(ids.length == 0)
				{
					return alert("Selecteer wel wat berichten voordat je deze actie uitvoert.");
				}
				err.removeClass("error").addClass("information").html("Bezig met verwerken van verzoek..").slideDown();
				jQuery.post("/includes/pmutils.php", { _cmd: cmd, ids: ids, data_a: data_a }, function(d){
					if(d != "ok")
					{
						err.removeClass("information").addClass("error").html(d).slideDown().delay(4000).slideUp();
						return;
					}
					window.location.reload(true);
				});
			},
			"CreateFolder": function() {
				var name = window.prompt("Geef je nieuwe map een naam, of druk op annuleren als je geen nieuwe map wilt.");
				if(name)
				{
					if(name.replace(/ /gi, "").length < 3) return alert('Geef je map een naam van minstens 3 karakters.');
					if(name.length > 20) return alert('Geef je map een naam van maximaal 20 karakters.');
					jQuery.post("/includes/pmutils.php", { _cmd: 'create_folder', ids: '', data_a: name}, function(d){
						window.location.reload(true);
					});
				} else {
					
				}
			}
		},
		"Events": {
			"Initialize": function() {
				jQuery(window).focus(function(){Glybe.Storage.WindowIsActive=true;});
				jQuery(window.document.body).mouseover(function(){Glybe.Storage.WindowIsActive=true;});
				jQuery(window.document.body).mousemove(function(){Glybe.Storage.WindowIsActive=true;});
				jQuery(window).blur(function(){Glybe.Storage.WindowIsActive=false;});
				jQuery(window.document.body).mouseout(function(){Glybe.Storage.WindowIsActive=false;});
			}
		}
	};
	
	window["Glybe"] = Glybe;
	
})(window);

jQuery(document).ready(function(){
	Glybe.Initialize();
	//Glybe.Notifications.Create('Men at work', 'Hoi Glyber, op dit moment word er gewerkt aan een super-handig notificatie-systeem waardoor je altijd up-to-date bent over wat er allemaal gebeurt rondom je account.. Dit is nog steeds B&egrave;ta, dus let niet op de vele tests en fouten :)<br/>Dit verdwijnt na 10 seconden.', 'cog', '#', 10000);
});