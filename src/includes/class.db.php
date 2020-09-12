<?php
if(stristr($_SERVER['REQUEST_URI'], 'class.db.php')){
	die("Whoat?! Hier mag jij niet in komen..");
}

Class DB {
	private static $verbinding	= null;
	public static $gebruiker	= null;
	public static $queries		= 0;
	public static $settings	 	= Array(true, false); //Eerste = queries onthouden; Tweede = debug mode
	public static $queriesb		= Array();
	public static $params		= Array();
	public static $paramOp	= "@";
	
	public static function Initialize(array $data, array $settings = Array(true, false)){
		self::$settings = $settings;
		if(!self::$verbinding = @mysqli_connect($data['host'],$data['gebruiker'],$data['wachtwoord'],$data['db'])){
			$range = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
			$error  = "Er kon geen verbinding gemaakt worden met de database!<br/>";
			$error .= "Gebruiker: " . $data['gebruiker'] . "<br/>";
			$error .= "Wachtwoord: " . str_replace($range, "*", $data['wachtwoord']) . "<br/>";
			$error .= "Host: " . $data['host'] . "<br/>";
			$error .= "Database: " . $data['db'] . "<br/>";
			self::Error("Verbindings Error!", $error);
			die();
		} else {
			if(!@mysqli_select_db(self::$verbinding, $data['db'])){
				$range = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'), range('!', '$'));
				$error  = "Er kon geen verbinding gemaakt worden met de database!<br/>";
				$error .= "Gebruiker: " . $data['gebruiker'] . "<br/>";
				$error .= "Wachtwoord: " . str_replace($range, "*", $data['wachtwoord']) . "<br/>";
				$error .= "Host: " . $data['host'] . "<br/>";
				$error .= "Database: " . $data['db'];
				self::Error("Verbindings Error!", $error);
				die();
			} else {
				self::$gebruiker = $data['gebruiker'];
			}
		}
	}
	
	public static function AddParam($key, $value){
		self::$params[$key] = ((is_string($value)) ? "'" . $value . "'" : $value);
	}
	
	public static function Query($query, $is_sellect = false){
		self::$queries++;
		if(self::$settings[0]){
			self::SaveQuery($query);
		}
		foreach(self::$params as $key => $value){
			$query = str_replace(self::$paramOp . $key, $value, $query);
		}
		$quer = mysqli_query(self::$verbinding, $query);
		if(mysqli_error(self::$verbinding)){
			$error  = "De query kon niet worden uitgevoerd!<br/>";
			$error .= "Query: " . addslashes($query) . "<br/>";
			$error .= "Error: " . mysqli_error(self::$verbinding);
			self::Error("Kon Query niet uitvoeren", $error);
		} else {
			return $quer;
		}
	}
	
	public static function GetDataArray($query){
		$quer = self::Query($query);
		$ret = Array();
		while($data = self::Fetch($quer)){
			$ret[$data['naam']] = $data['value'];
		}
		return $ret;
	}
	
	public static function GetTable($query){
		$quer = self::Query($query);
		$ret = Array();
		while($data = self::Fetch($quer)){
			$ret[] = $data;
		}
		return $ret;
	}
	
	public static function GetRow($query){
		$data = self::Query($query);
		if(self::NumRows($data) >= 2){
			$error  = "De data kon niet worden opgehaald omdat er meerdere rijen zijn gevonden!<br/>";
			$error .= "Query: " . $query . "<br/>";
			$error .= "Gebruik MySQL::GetTable() om meerdere rijen op te halen!";
			self::Error("Kon data niet ophalen!", $error);
		} else {
			return self::Fetch($data);
		}
	}
	
	public static function NumRows($resource){
		return mysqli_num_rows($resource);
	}
	
	public static function InsertId(){
		return mysqli_insert_id(self::$verbinding);
	}
	
	public static function NumRowsQuery($query){
		$data = self::Query($query);
		return self::NumRows($data);
	}
	
	public static function RunQuery($query){
		self::Query($query);
	}
	
	public static function Escape($string){
		return mysqli_real_escape_string(self::$verbinding, $string);
	}
	
	public static function Fetch($resource){
		return @mysqli_fetch_assoc($resource);
	}
	
	public static function FetchObject($resource){
		return @mysqli_fetch_object($resource);
	}
	
	public static function FetchArray($resource, $dingdatiknietweet){
		return @mysqli_fetch_array($resource, $dingdatiknietweet);
	}
	
	public static function Info(){
		return mysqli_get_client_info(self::$verbinding);
	}
	
	public static function InsertedID(){
		return mysqli_insert_id(self::$verbinding);
	}
	
	public static function Error($titel, $error){
		return print $error;
		$out  = "\n";
		$out .= "<div class=\"mysql_error\">\n";
		$out .= "	<div class=\"mysql_error_titel\">" . $titel . "</div>\n";
		$out .= "	<div class=\"mysql_error_inner\">" . $error . "</div>\n";
		$out .= "</div>\n";
		
		return print $out;
	}
	
	public static function SaveQuery($query){
		self::$queriesb[] = $query;
	}
	
	public static function ShowQueries(){
		echo"\n";
		echo"<div class=\"mysql_list\">\n";
		echo"<div class=\"mysql_list_info\">Uitgevoerde Queries</div>\n";
		foreach(self::$queriesb as $query){
			echo"	<div class=\"mysql_list_query\">" . $query . "</div>\n";
		}
		echo"</div>\n";
	}
	
	public static function Close(){
		return mysqli_close(self::$verbinding);
	}
}
?>