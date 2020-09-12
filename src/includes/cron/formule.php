<?php
include '../inc.bootstrap.php';
/*
 * Activiteits percentage formule Versie 2
 * (c) Jesse Reitsma // BasenJesse.nl 2012
 * De techniek die gebruikt wordt om het percentage te berekenen
 * Is bedacht en ontwikkelt door BasenJesse.nl 
 * Alle rechten voorbehouden
 * 
 * 
 */
 
$a = 0;
$hScore = 0;
$haScore = 0;

$ledenQuery = DB::Query("SELECT * FROM users ORDER BY page_views DESC LIMIT 2");

while($lid = DB::Fetch($ledenQuery))
{
	$b = $lid['page_views'];
	$cQ1 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(CURDATE()) AND UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL + 86399 SECOND)) AND user = '" . $lid['id'] . "'");
	$cQ2 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 1 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ3 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 2 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ4 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 3 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ5 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 4 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ6 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 5 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ7 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 6 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ8 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 7 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ9 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 8 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ10 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 9 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ11 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 10 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ12 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 11 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ13 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 12 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ14 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 13 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ15 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 14 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ16 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 15 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ17 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 16 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ18 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 17 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ19 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 18 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ20 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 19 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ21 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 20 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ22 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 21 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ23 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 22 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ24 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 23 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ25 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 24 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ26 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 25 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ27 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 26 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ28 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 27 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ29 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 28 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	$cQ30 = DB::Query("SELECT COUNT(*) AS t, date FROM parse_time WHERE date BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 29 DAY)) AND UNIX_TIMESTAMP(CURDATE()) AND user = '" . $lid['id'] . "'");
	
	$cF1 = DB::Fetch($cQ1);
	$cF2 = DB::Fetch($cQ2);
	$cF3 = DB::Fetch($cQ3);
	$cF4 = DB::Fetch($cQ4);
	$cF5 = DB::Fetch($cQ5);
	$cF6 = DB::Fetch($cQ6);
	$cF7 = DB::Fetch($cQ7);
	$cF8 = DB::Fetch($cQ8);
	$cF9 = DB::Fetch($cQ9);
	$cF10 = DB::Fetch($cQ10);
	$cF11 = DB::Fetch($cQ11);
	$cF12 = DB::Fetch($cQ12);
	$cF13 = DB::Fetch($cQ13);
	$cF14 = DB::Fetch($cQ14);
	$cF15 = DB::Fetch($cQ15);
	$cF16 = DB::Fetch($cQ16);
	$cF17 = DB::Fetch($cQ17);
	$cF18 = DB::Fetch($cQ18);
	$cF19 = DB::Fetch($cQ19);
	$cF20 = DB::Fetch($cQ20);
	$cF21 = DB::Fetch($cQ21);
	$cF22 = DB::Fetch($cQ22);
	$cF23 = DB::Fetch($cQ23);
	$cF24 = DB::Fetch($cQ24);
	$cF25 = DB::Fetch($cQ25);
	$cF26 = DB::Fetch($cQ26);
	$cF27 = DB::Fetch($cQ27);
	$cF28 = DB::Fetch($cQ28);
	$cF29 = DB::Fetch($cQ29);
	$cF30 = DB::Fetch($cQ30);

	echo 'Lid: '.htmlspecialchars($lid['username']).'<br />';
	echo '<br />Vandaag: ('.date("D d F", $cF1['date']).')<br />' . $cF1['t'];
	echo '<br />Gister:  ('.date("D d F", $cF2['date']).')<br />' . $cF2['t'];
	echo '<br />Eergister:  ('.date("D d F", $cF3['date']).')<br />' . $cF3['t'];
	echo '<br />Eer-Eergister:  ('.date("D d F", $cF4['date']).')<br />' . $cF4['t'];
	echo '<br />eer-Eer-eergister:  ('.date("D d F", $cF5['date']).')<br />' . $cF5['t'];
	echo '<br />eer-eer-eer-eergister:  ('.date("D d F", $cF6['date']).')<br />' . $cF6['t'];
	echo '<br />vorige week:  ('.date("D d F", $cF7['date']).')<br />' . $cF7['t'];
	
	echo '<br />('.date("D d F", $cF8['date']).')<br />' . $cF8['t'];
	echo '<br />('.date("D d F", $cF9['date']).')<br />' . $cF9['t'];
	echo '<br />('.date("D d F", $cF10['date']).')<br />' . $cF10['t'];
	echo '<br />('.date("D d F", $cF11['date']).')<br />' . $cF11['t'];
	echo '<br />('.date("D d F", $cF12['date']).')<br />' . $cF12['t'];
	echo '<br />('.date("D d F", $cF13['date']).')<br />' . $cF13['t'];
	echo '<br />('.date("D d F", $cF14['date']).')<br />' . $cF14['t'];
	echo '<br />('.date("D d F", $cF15['date']).')<br />' . $cF15['t'];
	echo '<br />('.date("D d F", $cF16['date']).')<br />' . $cF16['t'];
	echo '<br />('.date("D d F", $cF17['date']).')<br />' . $cF17['t'];
	echo '<br />('.date("D d F", $cF18['date']).')<br />' . $cF18['t'];
	echo '<br />('.date("D d F", $cF19['date']).')<br />' . $cF19['t'];
	echo '<br />('.date("D d F", $cF20['date']).')<br />' . $cF20['t'];
	echo '<br />('.date("D d F", $cF21['date']).')<br />' . $cF21['t'];
	echo '<br />('.date("D d F", $cF22['date']).')<br />' . $cF22['t'];
	echo '<br />('.date("D d F", $cF23['date']).')<br />' . $cF23['t'];
	echo '<br />('.date("D d F", $cF24['date']).')<br />' . $cF24['t'];
	echo '<br />('.date("D d F", $cF25['date']).')<br />' . $cF25['t'];
	echo '<br />('.date("D d F", $cF26['date']).')<br />' . $cF26['t'];
	echo '<br />('.date("D d F", $cF27['date']).')<br />' . $cF27['t'];
	echo '<br />('.date("D d F", $cF28['date']).')<br />' . $cF28['t'];
	echo '<br />('.date("D d F", $cF29['date']).')<br />' . $cF29['t'];
	echo '<br />('.date("D d F", $cF30['date']).')<br />' . $cF30['t'];
	$g1 = $cF1['t'];
	$g2 = $cF2['t'];
	$g3 = $cF3['t'];
	$g4 = $cF4['t'];
	$g5 = $cF5['t'];
	$g6 = $cF6['t'];
	$g7 = $cF7['t'];
	$g8 = $cF8['t'];
	$g9 = $cF9['t'];
	$g10 = $cF10['t'];
	$g11 = $cF11['t'];
	$g12 = $cF12['t'];
	$g13 = $cF13['t'];
	$g14 = $cF14['t'];
	$g15 = $cF15['t'];
	$g16 = $cF16['t'];
	$g17 = $cF17['t'];
	$g18 = $cF18['t'];
	$g19 = $cF19['t'];
	$g20 = $cF20['t'];
	$g21 = $cF21['t'];
	$g22 = $cF22['t'];
	$g23 = $cF23['t'];
	$g24 = $cF24['t'];
	$g25 = $cF25['t'];
	$g26 = $cF26['t'];
	$g27 = $cF27['t'];
	$g28 = $cF28['t'];
	$g29 = $cF29['t'];
	$g30 = $cF30['t'];
	$c = round((($g1 + $g2 + $g3 + $g4 + $g5 + $g6 + $g7 + $g8 + $g9 + $g10 + $g11 + $g12 + $g13 + $g14 + $g15 + $g16 + $g17 + $g18 + $g19 + $g20 + $g21 + $g22 + $g23 + $g24 + $g25 + $g26 + $g27 + $g28 + $g29 + $g30) / 30), 0);
	echo '<br />Gemiddelde: <br /> '.$c.'<br />';
	echo '<hr />';
	
	if($c > $haScore)
	{
		$haScore = $c;
	}
	
	//sleep(1);
		
	//DB::Query("UPDATE users SET active = '".$a."' WHERE id = '".$u->Id."'");

}
/*
$ledenQuery2 = DB::Query("SELECT * FROM users WHERE id != '1' AND id != '2' AND id != '3'");
while($lid2 = DB::Fetch($ledenQuery2))
{
	global $hScore;
	global $haScore;
	$a = $lid2['active'];
	
	
	$percentage = $a / ( $haScore / 100 );
	$percentage = ceil($percentage);
	
	if($a == intval($hScore))
	{
		$percentage = 100;
	}
	echo '<br />a: '.$a.'<br />hScore: '.intval($hScore).'<br />';
	//DB::Query("UPDATE users SET active = '".$percentage."' WHERE id = '".$lid2['id']."'");
	echo 'Activiteits formule voor '.$lid2['username'].' ('.$percentage.') gelukt!<br />';
}
*/
		$pTimeEnd = microtime();
		$pExplode = explode(" ", $pTimeEnd);
		$pTimeEnd2 = $pExplode[0];
		$pSec2 = date("U");
		$pEnd = $pTimeEnd2 + $pSec2;
		$parseTime = $pEnd - $pStart;
		$parseTime = round($parseTime,5);
		echo '<strong>Parse tijd is: '.$parseTime.'</strong>';
?>