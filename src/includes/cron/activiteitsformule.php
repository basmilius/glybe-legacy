<?php
include '../inc.bootstrap.php';
/*
 * Activiteits formule door Jesse Reitsma
 * 
 * Current Version: 1.1.2
 * Last edit: Jesse Reitsma
 * 
 * Formule wordt middels een cronjob elke nacht om 03:00 draaien
 * Aangezien er hevige query's worden uitgevoerd over veel leden
 * Daarom dit bestand NIET overdag uitvoeren zodat Glybe snel blijft
 * 
 * Changelog:
 * 
 * V1.1.1 - 11-05-2012 9:57 - Jesse Reitsma - Bestand aangemaakt, basis gelegd.
 * V1.1.1 - 16-05-2012 15:46 - Jesse Reitsma - Debug foutje opgelost.
 *
 * 
 */
 
 /* Hoofd query's die niet voor elk lid moet worden uitgevoerd */
$a = 0;
$hScore = 0;
$user_most_posts_query = DB::Query("	SELECT
											u.username uName,
											COUNT(p.user_id) cPosts
										FROM
											forum_posts p,
											users u
										WHERE
											u.id = p.user_id
										AND
											p.state != 'deleted'
										AND
											u.id != 3
										GROUP BY
											p.user_id
										ORDER BY
											cPosts DESC,
											u.id
										LIMIT
											1");
$user_most_posts_fetch = DB::Fetch($user_most_posts_query);			
$most_forum_posts = $user_most_posts_fetch['cPosts'];								
								
$cQuery = DB::Query("SELECT page_views FROM users ORDER BY page_views DESC LIMIT 1");
$cFetch = DB::Fetch($cQuery);

$ledenQuery = DB::Query("SELECT * FROM users WHERE id != '1' AND id != '8' AND id != '3'");

while($lid = DB::Fetch($ledenQuery))
{
	global $hScore;
	$u = new User($lid['id'], false);
	/* Start: forum posts */
	$user_posts_query = DB::Query("
									SELECT
										COUNT(id) AS cPosts
									FROM
										forum_posts
									WHERE
										user_id
									=
										'" . $u->Id . "'
									AND
										state
									!=
										'deleted'
								  ");
	$user_posts_fetch = DB::Fetch($user_posts_query);			
	$my_forum_posts = $user_posts_fetch['cPosts'];								

	if($my_forum_posts == $most_forum_posts)
	{
		$a = ($a + 100);
	}
	elseif($my_forum_posts > 154)
	{
		$a = ($a + 1);
	}
	elseif($my_forum_posts > 267)
	{
		$a = ($a + 2);
	}
	elseif($my_forum_posts > 387)
	{
		$a = ($a + 3);
	}
	elseif($my_forum_posts > 485)
	{
		$a = ($a + 4);
	}
	elseif($my_forum_posts > 512)
	{
		$a = ($a + 5);
	}
	elseif($my_forum_posts > 632)
	{
		$a = ($a + 6);
	}
	elseif($my_forum_posts > 745)
	{
		$a = ($a + 7);
	}
	elseif($my_forum_posts > 885)
	{
		$a = ($a + 8);
	}
	elseif($my_forum_posts > 951)
	{
		$a = ($a + 9);
	}
	elseif($my_forum_posts > 1015)
	{
		$a = ($a + 10);
	}
	elseif($my_forum_posts > 1104)
	{
		$a = ($a + 11);
	}
	elseif($my_forum_posts > 1287)
	{
		$a = ($a + 12);
	}
	elseif($my_forum_posts > 1365)
	{
		$a = ($a + 13);
	}
	elseif($my_forum_posts > 1478)
	{
		$a = ($a + 14);
	}
	elseif($my_forum_posts > 1596)
	{
		$a = ($a + 15);
	}
	elseif($my_forum_posts > 1684)
	{
		$a = ($a + 16);
	}
	elseif($my_forum_posts > 1764)
	{
		$a = ($a + 17);
	}
	elseif($my_forum_posts > 1823)
	{
		$a = ($a + 18);
	}
	elseif($my_forum_posts > 1954)
	{
		$a = ($a + 19);
	}
	elseif($my_forum_posts > 2078)
	{
		$a = ($a + 20);
	}
	elseif($my_forum_posts > 2154)
	{
		$a = ($a + 21);
	}
	elseif($my_forum_posts > 2236)
	{
		$a = ($a + 22);
	}
	elseif($my_forum_posts > 2341)
	{
		$a = ($a + 23);
	}
	elseif($my_forum_posts > 2411)
	{
		$a = ($a + 24);
	}
	elseif($my_forum_posts > 2565)
	{
		$a = ($a + 25);
	}
	elseif($my_forum_posts > 2612)
	{
		$a = ($a + 26);
	}
	elseif($my_forum_posts > 2785)
	{
		$a = ($a + 27);
	}
	elseif($my_forum_posts > 2852)
	{
		$a = ($a + 28);
	}			
	elseif($my_forum_posts > 2963)
	{
		$a = ($a + 29);
	}
	elseif($my_forum_posts > 3014)
	{
		$a = ($a + 30);
	}
	elseif($my_forum_posts > 4012)
	{
		$a = ($a + 40);
	}
	elseif($my_forum_posts > 5310)
	{
		$a = ($a + 50);
	}
	elseif($my_forum_posts > 16542)
	{
		$a = ($a + 100);
	}

	/* Eind: Forum posts */

	/* Start: Forum topics */

	$user_topics_query = DB::Query("
									SELECT
										COUNT(id) AS cTopics
									FROM
										forum_topics
									WHERE
										user_id
									=
										'" . $u->Id . "'
									AND
										state
									!=
										'deleted'
								   ");
	$user_topic_fetch = DB::Fetch($user_topics_query);			
	$my_forum_topics = $user_topic_fetch['cTopics'];	

	if($my_forum_topics > 5)
	{
		$a = ($a + 1);
	}
	elseif($my_forum_topics > 10)
	{
		$a = ($a + 2);
	}
	elseif($my_forum_topics > 15)
	{
		$a = ($a + 3);
	}
	elseif($my_forum_topics > 20)
	{
		$a = ($a + 4);
	}
	elseif($my_forum_topics > 25)
	{
		$a = ($a + 5);
	}
	elseif($my_forum_topics > 30)
	{
		$a = ($a + 6);
	}
	elseif($my_forum_topics > 35)
	{
		$a = ($a + 7);
	}
	elseif($my_forum_topics > 40)
	{
		$a = ($a + 8);
	}
	elseif($my_forum_topics > 45)
	{
		$a = ($a + 9);
	}
	elseif($my_forum_topics > 50)
	{
		$a = ($a + 10);
	}
	elseif($my_forum_topics > 55)
	{
		$a = ($a + 11);
	}
	elseif($my_forum_topics > 60)
	{
		$a = ($a + 12);
	}
	elseif($my_forum_topics > 65)
	{
		$a = ($a + 13);
	}
	elseif($my_forum_topics > 70)
	{
		$a = ($a + 14);
	}
	elseif($my_forum_topics > 75)
	{
		$a = ($a + 15);
	}
	elseif($my_forum_topics > 80)
	{
		$a = ($a + 16);
	}
	elseif($my_forum_topics > 85)
	{
		$a = ($a + 17);
	}
	elseif($my_forum_topics > 90)
	{
		$a = ($a + 18);
	}
	elseif($my_forum_topics > 95)
	{
		$a = ($a + 19);
	}
	elseif($my_forum_topics > 100)
	{
		$a = ($a + 20);
	}
	/* Eind: Forum topics */
	
	/* Start: Topics / Posts readed */
	$readedQuery = DB::Query("
								SELECT
									COUNT(id) AS aantal
								FROM
									forum_readed
								WHERE
									user_id
								=
									'" . $u->Id . "'
							 ");
	$readedFetch = DB::Fetch($readedQuery);
	$aantalReaded = $readedFetch['aantal'];
	if($aantalReaded > 10)
	{
		$a = ($a + 1);
	}
	elseif($aantalReaded > 25)
	{
		$a = ($a + 2);
	}
	elseif($aantalReaded > 34)
	{
		$a = ($a + 3);
	}
	elseif($aantalReaded > 56)
	{
		$a = ($a + 4);
	}
	elseif($aantalReaded > 64)
	{
		$a = ($a + 5);
	}
	elseif($aantalReaded > 78)
	{
		$a = ($a + 6);
	}
	elseif($aantalReaded > 84)
	{
		$a = ($a + 7);
	}
	elseif($aantalReaded > 95)
	{
		$a = ($a + 8);
	}
	elseif($aantalReaded > 105)
	{
		$a = ($a + 9);
	}
	elseif($aantalReaded > 116)
	{
		$a = ($a + 10);
	}
	elseif($aantalReaded > 127)
	{
		$a = ($a + 11);
	}
	elseif($aantalReaded > 129)
	{
		$a = ($a + 12);
	}
	elseif($aantalReaded > 134)
	{
		$a = ($a + 13);
	}
	elseif($aantalReaded > 146)
	{
		$a = ($a + 14);
	}
	elseif($aantalReaded > 152)
	{
		$a = ($a + 15);
	}
	elseif($aantalReaded > 163)
	{
		$a = ($a + 16);
	}
	elseif($aantalReaded > 177)
	{
		$a = ($a + 17);
	}
	elseif($aantalReaded > 187)
	{
		$a = ($a + 18);
	}
	elseif($aantalReaded > 194)
	{
		$a = ($a + 19);
	}
	elseif($aantalReaded > 250)
	{
		$a = ($a + 20);
	}
	/* Eind: Topics / Posts readed */
	/* Start: PM's ontvangen */
	
	$pm_query1 = DB::Query("SELECT COUNT(id) AS totaal FROM messages WHERE user_to_id = '".$u->Id."'");
	$pm_fetch1 = DB::Fetch($pm_query1);
	$pm_ontvangen = $pm_fetch1['totaal'];
	if($pm_ontvangen > 5)
	{
		$a = ($a + 1);
	}
	elseif($pm_ontvangen > 10)
	{
		$a = ($a + 2);
	}
	elseif($pm_ontvangen > 21)
	{
		$a = ($a + 3);
	}
	elseif($pm_ontvangen > 33)
	{
		$a = ($a + 4);
	}
	elseif($pm_ontvangen > 41)
	{
		$a = ($a + 5);
	}
	elseif($pm_ontvangen > 53)
	{
		$a = ($a + 6);
	}
	elseif($pm_ontvangen > 68)
	{
		$a = ($a + 7);
	}
	elseif($pm_ontvangen > 74)
	{
		$a = ($a + 8);
	}
	elseif($pm_ontvangen > 89)
	{
		$a = ($a + 9);
	}
	elseif($pm_ontvangen > 97)
	{
		$a = ($a + 10);
	}
	elseif($pm_ontvangen > 103)
	{
		$a = ($a + 11);
	}
	elseif($pm_ontvangen > 114)
	{
		$a = ($a + 12);
	}
	elseif($pm_ontvangen > 127)
	{
		$a = ($a + 13);
	}
	elseif($pm_ontvangen > 132)
	{
		$a = ($a + 14);
	}
	elseif($pm_ontvangen > 142)
	{
		$a = ($a + 15);
	}
	elseif($pm_ontvangen > 159)
	{
		$a = ($a + 16);
	}
	elseif($pm_ontvangen > 167)
	{
		$a = ($a + 17);
	}
	elseif($pm_ontvangen > 174)
	{
		$a = ($a + 18);
	}
	elseif($pm_ontvangen > 182)
	{
		$a = ($a + 19);
	}
	elseif($pm_ontvangen > 200)
	{
		$a = ($a + 20);
	}
	/* Eind: PM's ontvangen */
	/* Start: PM's verstuurd */
	
	$pm_query2 = DB::Query("SELECT COUNT(id) AS totaal FROM messages WHERE user_from_id = '".$u->Id."'");
	$pm_fetch2 = DB::Fetch($pm_query2);
	$pm_verstuurd = $pm_fetch2['totaal'];
	if($pm_verstuurd > 5)
	{
		$a = ($a + 1);
	}
	elseif($pm_verstuurd > 12)
	{
		$a = ($a + 2);
	}
	elseif($pm_verstuurd > 24)
	{
		$a = ($a + 3);
	}
	elseif($pm_verstuurd > 36)
	{
		$a = ($a + 4);
	}
	elseif($pm_verstuurd > 48)
	{
		$a = ($a + 5);
	}
	elseif($pm_verstuurd > 56)
	{
		$a = ($a + 6);
	}
	elseif($pm_verstuurd > 63)
	{
		$a = ($a + 7);
	}
	elseif($pm_verstuurd > 74)
	{
		$a = ($a + 8);
	}
	elseif($pm_verstuurd > 82)
	{
		$a = ($a + 9);
	}
	elseif($pm_verstuurd > 91)
	{
		$a = ($a + 10);
	}
	elseif($pm_verstuurd > 102)
	{
		$a = ($a + 11);
	}
	elseif($pm_verstuurd > 114)
	{
		$a = ($a + 12);
	}
	elseif($pm_verstuurd > 123)
	{
		$a = ($a + 13);
	}
	elseif($pm_verstuurd > 149)
	{
		$a = ($a + 14);
	}
	elseif($pm_verstuurd > 156)
	{
		$a = ($a + 15);
	}
	elseif($pm_verstuurd > 162)
	{
		$a = ($a + 16);
	}
	elseif($pm_verstuurd > 170)
	{
		$a = ($a + 17);
	}
	elseif($pm_verstuurd > 188)
	{
		$a = ($a + 18);
	}
	elseif($pm_verstuurd > 194)
	{
		$a = ($a + 19);
	}
	elseif($pm_verstuurd > 200)
	{
		$a = ($a + 20);
	}
	/* Eind: PM's verstuurd */
	/* Start: gastenboek berichten ontvangen */
	$gb_query1 = DB::Query("SELECT COUNT(id) AS totaal FROM profile_guestbook WHERE profile_id = '".$u->Id."'");
	$gb_fetch1 = DB::Fetch($gb_query1);
	$gb_ontvangen = $gb_fetch1['totaal'];
	if($gb_ontvangen > 5)
	{
		$a = ($a + 1);
	}
	elseif($gb_ontvangen > 10)
	{
		$a = ($a + 2);
	}
	elseif($gb_ontvangen > 15)
	{
		$a = ($a + 3);
	}
	elseif($gb_ontvangen > 20)
	{
		$a = ($a + 4);
	}
	elseif($gb_ontvangen > 25)
	{
		$a = ($a + 5);
	}
	elseif($gb_ontvangen > 30)
	{
		$a = ($a + 6);
	}
	elseif($gb_ontvangen > 35)
	{
		$a = ($a + 7);
	}
	elseif($gb_ontvangen > 40)
	{
		$a = ($a + 8);
	}
	elseif($gb_ontvangen > 45)
	{
		$a = ($a + 9);
	}
	elseif($gb_ontvangen > 50)
	{
		$a = ($a + 10);
	}
	elseif($gb_ontvangen > 55)
	{
		$a = ($a + 11);
	}
	elseif($gb_ontvangen > 60)
	{
		$a = ($a + 12);
	}
	elseif($gb_ontvangen > 65)
	{
		$a = ($a + 13);
	}
	elseif($gb_ontvangen > 70)
	{
		$a = ($a + 14);
	}
	elseif($gb_ontvangen > 75)
	{
		$a = ($a + 15);
	}
	elseif($gb_ontvangen > 80)
	{
		$a = ($a + 16);
	}
	elseif($gb_ontvangen > 85)
	{
		$a = ($a + 17);
	}
	elseif($gb_ontvangen > 90)
	{
		$a = ($a + 18);
	}
	elseif($gb_ontvangen > 95)
	{
		$a = ($a + 19);
	}
	elseif($gb_ontvangen > 100)
	{
		$a = ($a + 20);
	}
	
	/* Eind: gastenboek berichten ontvangen */
	/* Start: gastenboek berichten verstuurd */
	
	$gb_query2 = DB::Query("SELECT COUNT(id) AS totaal FROM profile_guestbook WHERE user_id = '".$u->Id."'");
	$gb_fetch2 = DB::Fetch($gb_query2);
	$gb_verstuurd = $gb_fetch2['totaal'];
	if($gb_verstuurd > 5)
	{
		$a = ($a + 1);
	}
	elseif($gb_verstuurd > 10)
	{
		$a = ($a + 2);
	}
	elseif($gb_verstuurd > 15)
	{
		$a = ($a + 3);
	}
	elseif($gb_verstuurd > 20)
	{
		$a = ($a + 4);
	}
	elseif($gb_verstuurd > 25)
	{
		$a = ($a + 5);
	}
	elseif($gb_verstuurd > 30)
	{
		$a = ($a + 6);
	}
	elseif($gb_verstuurd > 35)
	{
		$a = ($a + 7);
	}
	elseif($gb_verstuurd > 40)
	{
		$a = ($a + 8);
	}
	elseif($gb_verstuurd > 45)
	{
		$a = ($a + 9);
	}
	elseif($gb_verstuurd > 50)
	{
		$a = ($a + 10);
	}
	elseif($gb_verstuurd > 55)
	{
		$a = ($a + 11);
	}
	elseif($gb_verstuurd > 60)
	{
		$a = ($a + 12);
	}
	elseif($gb_verstuurd > 65)
	{
		$a = ($a + 13);
	}
	elseif($gb_verstuurd > 70)
	{
		$a = ($a + 14);
	}
	elseif($gb_verstuurd > 75)
	{
		$a = ($a + 15);
	}
	elseif($gb_verstuurd > 80)
	{
		$a = ($a + 16);
	}
	elseif($gb_verstuurd > 85)
	{
		$a = ($a + 17);
	}
	elseif($gb_verstuurd > 90)
	{
		$a = ($a + 18);
	}
	elseif($gb_verstuurd > 95)
	{
		$a = ($a + 19);
	}
	elseif($gb_verstuurd > 100)
	{
		$a = ($a + 20);
	}
	
	/* Eind: gastenboek berichten verstuurd */
	
	$b = $lid['page_views'];
	$c = $cFetch['page_views'];
	$a = (($a + (($b / $c) * ($c / 2) / 15 / 2)) / 2);
	
	if($a > $hScore)
	{
		$hScore = $a;
	}
	
	//if($a > 100)
	//{
	//	$a = 100;
	//}
	echo $hScore;
	DB::Query("UPDATE users SET active = '".$a."' WHERE id = '".$u->Id."'");
}
$ledenQuery2 = DB::Query("SELECT * FROM users WHERE id != '1' AND id != '8' AND id != '3'");
while($lid2 = DB::Fetch($ledenQuery2))
{
	global $hScore;
	$a = $lid2['active'];
	$percentage = $a / ( $hScore / 100 );
	$percentage = ceil($percentage);
	
	if($a == intval($hScore))
	{
		$percentage = 100;
	}
	echo '<br />a: '.$a.'<br />hScore: '.intval($hScore).'<br />';
	DB::Query("UPDATE users SET active = '".$percentage."' WHERE id = '".$lid2['id']."'");
	echo 'Activiteits formule voor '.$lid2['username'].' ('.$percentage.') gelukt!<br />';
}
$pTimeEnd = microtime();
		$pExplode = explode(" ", $pTimeEnd);
		$pTimeEnd2 = $pExplode[0];
		$pSec2 = date("U");
		$pEnd = $pTimeEnd2 + $pSec2;
		$parseTime = $pEnd - $pStart;
		$parseTime = round($parseTime,5);
		echo '<strong>Parse tijd is: '.$parseTime.'</strong>';
?>