<?php
// FUNCTIONS

// --- Checks if pageurl exists. Returns True/False.
function pageurl_exists($pageurl) 
{
	$numrows = query_prep("SELECT COUNT(*) FROM page WHERE pageurl = ?", array($pageurl));
	if ($numrows != 0) return TRUE;
	else return FALSE;
}

function extrapageurl_exists($pageurl) 
{
	$numrows = query_prep("SELECT COUNT(*) FROM extrapage WHERE pageurl = ?", array($pageurl));
	if ($numrows != 0) return TRUE;
	else return FALSE;
}

// --- Checks if snippet name exists. Returns True/False.
function snippetname_exists($name) 
{
	$numrows = query_prep("SELECT COUNT(*) FROM snippet WHERE name = ?", array($name));
	if ($numrows != 0) return TRUE;
	else return FALSE;
}

// --- Checks if username exists. Returns True/False.
function username_exists($name) 
{
	$numrows = query_prep("SELECT COUNT(*) FROM cmsuser WHERE username = ?", array($name));
	if ($numrows != 0) return TRUE;
	else return FALSE;
}

// --- Checks if extranet username exists. Returns True/False.
function extrausername_exists($name) 
{
	$numrows = query_prep("SELECT COUNT(*) FROM extrauser WHERE username = ?", array($name));
	if ($numrows != 0) return TRUE;
	else return FALSE;
}

// --- Checks if page has children. Returns True/False.
function has_children($pageurl, $table='page') {
	$numrows = query_prep("SELECT COUNT(*) FROM ".$table." WHERE mother = ?", array($pageurl));
	if ($numrows != 0) return TRUE;
	else return FALSE;
}

// --- Gets unique url with characters replaced or removed. Adds counter if needed. Takes 1) string 2) integer: 0 = normal pageurl, 1 = snippet url, 2 = username, 3 = extranet username, 4 = extranet pageurl
function get_unique_url($str, $type)
{
	// array of replaced characters (from, to)
	$replace = array();
	$replace[] = array("ä", "a");
	$replace[] = array("Ä", "a");
	$replace[] = array("ö", "o");
	$replace[] = array("Ö", "o");
	$replace[] = array("å", "a");
	$replace[] = array("Å", "å");
			
	for ($j=0 ; $j<count($replace) ; $j++) {
		$str = str_replace($replace[$j][0], $replace[$j][1], $str);
	}
	
	$str = strtolower(trim($str)); // trim & make all lowercase
	$newStr = '';
	
	for ($j=0 ; $j<strlen($str) ; $j++) {
		if (ord($str[$j]) == 32 OR ord($str[$j]) == 45) $newStr .= '-'; // add space and '-' as '-' to filename
		if (ord($str[$j]) >= 48 && ord($str[$j]) <= 57 ) $newStr .= $str[$j]; // add to string if ASCII value equals to numbers 0-9
		if (ord($str[$j]) >= 97 && ord($str[$j]) <= 122 ) $newStr .= $str[$j]; // add to string if ASCII value equals to "normal" lowercase letters
		if ($type == 2 OR $type == 3) {
			if (ord($str[$j]) == 64 OR ord($str[$j]) == 46 ) $newStr .= $str[$j]; // in usernames, allow "." OR "@" for email addresses
		}
	}
	if (isset($newStr)) $str = $newStr;
	if (empty($str)) $str = "no-name";
	//if (!$str) $str = "no-name";
	if ($str == '---notinmenu---') $str = 'notinmenu'; // '---notinmenu---' is used to select free page
	if ($str == 'index') $str = 'index1'; // 'index' does not work with cleanurl & front page uri just index.php
	$str = str_replace('--', '-', $str); // remove double --
	
	// check for duplicate names and add counter number to end if needed
	$basestr = $str;
	$counter = 1;
	if ($type == 1) {
		while (snippetname_exists($str)) {
				$str = $basestr . '-'. strval($counter);
				$counter++;	
		}	
	} elseif ($type == 0) {
		while (pageurl_exists($str)) {
				$str = $basestr . '-'. strval($counter);
				$counter++;	
		}
	} elseif ($type == 2) {
		while (username_exists($str)) {
				$str = $basestr . '-'. strval($counter);
				$counter++;	
		}
	} elseif ($type == 3) {
		while (extrausername_exists($str)) {
				$str = $basestr . '-'. strval($counter);
				$counter++;	
		}
	} elseif ($type == 4) {
		while (extrapageurl_exists($str)) {
				$str = $basestr . '-'. strval($counter);
				$counter++;	
		}
	}

	return($str);
}

// --- Get next ordernum for page in given level
function get_next_ordernum($mother, $table='page')
{
	$maxnum = query_prep("SELECT MAX(ordernum) FROM ".$table." WHERE mother = ?", array($mother));
	return $maxnum + 1;
}

// --- Refreshes pageorder for pages with same mother. Used *after* page delete & mother change.
function refresh_pageorder($mother, $table='page') 
{
	global $dbh;
	$counter = 1;
	
	$stmt = $dbh->prepare("SELECT pageurl FROM ".$table." WHERE mother = ? ORDER BY ordernum");
	$stmt->bindParam(1, $mother);	
	$stmt->execute();
		
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$dbh->exec("UPDATE ".$table." SET ordernum = ".$counter." WHERE pageurl = '".$row['pageurl']."'");
		$counter++;
	}	
}

// --- Saves message to log and to be used in infobox
function save_infomsg($message) 
{	
	global $dbh;
	$logdate = date("Y-m-d H:i:s");
	$stmt = $dbh->prepare("INSERT INTO log (msg, time, ip, user) VALUES (?, ?, ?, ?)");
	$stmt->bindParam(1, $message);
	$stmt->bindParam(2, $logdate);
	$stmt->bindParam(3, $_SESSION['ip']);
	$stmt->bindParam(4, $_SESSION['uid']);
	$stmt->execute();
}

// --- Return pages as an array for html select element. Takes number of levels to return (1-3). Slow because of many queries.
function pages_for_select($levels, $table='page', $free=FALSE)
{
	global $dbh;
	$pagelist = array();
	$sql = "SELECT pageurl, name FROM ".$table." WHERE levelnum = 1 AND status = 1 ORDER BY ordernum";
	foreach ($dbh->query($sql) as $row) {
		$pagelist[$row['pageurl']] = $row['name'];
		$sql2 = "SELECT pageurl, name FROM ".$table." WHERE levelnum = 2 AND mother = '".$row['pageurl']."' ORDER BY ordernum";
		foreach ($dbh->query($sql2) as $row2) {
			if ($levels > 1) $pagelist[$row2['pageurl']] = '&nbsp;&nbsp;'.$row2['name'];
			$sql3 = "SELECT pageurl, name FROM ".$table." WHERE levelnum = 3 AND mother = '".$row2['pageurl']."' ORDER BY ordernum";
			foreach ($dbh->query($sql3) as $row3) {
				if ($levels > 2) $pagelist[$row3['pageurl']] = '&nbsp;&nbsp;&nbsp;&nbsp;'.$row3['name'];
			}
		}
	}
	if ($free) {
		$sql = "SELECT pageurl, name FROM ".$table." WHERE levelnum = 0 AND status = 1 ORDER BY ordernum";
		foreach ($dbh->query($sql) as $row) {
			$pagelist[$row['pageurl']] = $row['name'];
		}
	}
	return $pagelist;
}

// --- Returns the root page (main level page) for a given pageurl.
function root_page($pageurl, $table='page')
{
	global $dbh;
	$level = query_prep("SELECT levelnum FROM ".$table." WHERE pageurl = ?", array($pageurl));
	if ($level == '0') {
		// this is free page
		return '---notinmenu---'; 
	} elseif ($level == '1') {
		// already main level page
		return $pageurl;
	} elseif ($level == '2') {
		// return mother
		return query_single("SELECT mother FROM ".$table." WHERE pageurl = '$pageurl'");
	} elseif ($level == '3') {
		// return grandmother
		$mother = query_single("SELECT mother FROM ".$table." WHERE pageurl = '$pageurl'");
		return query_single("SELECT mother FROM ".$table." WHERE pageurl = '$mother'");
	}
}

// --- Validate mysql date (yyyy-mm-dd)
function valid_mysql_date($date)
{
	if (preg_match("/^([123456789][[:digit:]]{3})-(0[1-9]|1[012])-(0[1-9]|[12][[:digit:]]|3[01])$/", $date, $date_part) 
	&& checkdate($date_part[2], $date_part[3], $date_part[1])) return true;
	else return false;
}

// --- Validate time (hh:mm:ss)
function valid_time($value)
{
			$arr = explode(":", $value);
			if($arr[0] > 23 OR $arr[0] < 0 OR $arr[1] > 59 OR $arr[1] < 0 OR $arr[2] > 59 OR $arr[2] < 0 OR !is_numeric($arr[0]) OR !is_numeric($arr[1]) OR !is_numeric($arr[2]))
			return False;
			else return True;
}

// --- Strips slashes if magic_quotes_gpc is on
function stripslashes_gpc($data)
{
	if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc()) 
	{
		$data = stripslashes($data);
	}
	return $data;
}

// --- Strips slashes from array
function stripslashes_deep($value)
{
	$value = is_array($value) ?
		array_map('stripslashes_deep', $value) :
		stripslashes($value);
	return $value;
}


// --- Creates a unique token and saves this in user's session (CSRF protection)
function csrf_token() {
	$token = md5(uniqid(rand(), true));
	$_SESSION['token'] = $token;
	return $token;
}

// Checks if CRSF token is valid
function valid_csrf_token($token)
{
	if (empty($_SESSION['token']) OR empty($token) OR $_SESSION['token'] != $token) return False;
	else return True;
}
?>