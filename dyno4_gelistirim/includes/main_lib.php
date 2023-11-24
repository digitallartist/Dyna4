<?php

//date.timezone = "US/Central"
//date_default_timezone_set('Turkey');
date_default_timezone_set('Asia/Baghdad'); putenv("TZ=Asia/Bagdat");

function user_pass_control($dbc, $user_id) {
	$prob=0;
	$q = "SELECT *, COALESCE(DATEDIFF(NOW(),pass_update_time),59) AS DiffDate FROM users WHERE id=$user_id";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)) {
		//$prob=$row['DiffDate'];
		$prob=0;
		$pwd=$row['pass'];
		if (strlen($pwd) < 8)
			$prob++;
		if (!preg_match("#[0-9]+#", $pwd))
			$prob++;
		if (!preg_match("#[a-zA-Z]+#", $pwd))
			$prob++;
		if(!preg_match('#[^a-z0-9]+#i', $pwd))
			$prob++;
			
	}

		return  $prob;

	
		
	
}

function quotNumber($dbc, $text) {

	if($text=='')
		return 'null';
	else	
		return quot($dbc, str_replace(",", ".", str_replace(".", "", $text)));
}

function rate($num1, $num2, $symbol="%") {
	if(make_cur($num1)==0)
		return "% 0";
	else 	
		return $symbol . " " . print_ytl( ((make_cur($num2)-make_cur($num1))/make_cur($num1))*100, 2 );
}

function nvl($data) {
	return (is_null($data) ? '' : $data);
}

function absolute_url ($page = 'index.php', $secure=0) {

	// Start defining the URL...
	// URL is http:// plus the host name plus the current directory:
	if ($secure==1)
		$url = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	else
		$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
		
	
	// Remove any trailing slashes:
	$url = rtrim($url, '/\\');
	
	// Add the page:
	$url .= '/' . $page;
	
	// Return the URL:
	return $url;

} // End of absolute_url() function.

function right($string, $count){
	$start = mb_strlen($string)-$count;
    return mb_substr($string,$start,$count,'UTF-8');	
}

function left($string, $count){
    return mb_substr($string,0,$count,'UTF-8');	
}

function crop_leading_zero($str) {
	return ltrim($str, "0");
}

function utf8_tur($string) {

	$patterns[0] = '/Ð/';
	$patterns[1] = '/Ü/';
	$patterns[2] = '/Þ/';
	$patterns[3] = '/Ý/';
	$patterns[4] = '/Ö/';
	$patterns[5] = '/Ç/';
	$patterns[6] = '/ð/';
	$patterns[7] = '/ü/';
	$patterns[8] = '/þ/';
	$patterns[9] = '/ý/';
	$patterns[10] = '/ö/';
	$patterns[11] = '/ç/';

	$replacements[0] = '&#286;';
	$replacements[1] = '&#220;';
	$replacements[2] = '&#350;';	
	$replacements[3] = '&#304;';	
	$replacements[4] = '&#214;';	
	$replacements[5] = '&#199;';	
	$replacements[6] = '&#287;';	
	$replacements[7] = '&#252;';	
	$replacements[8] = '&#351;';	
	$replacements[9] = '&#305;';	
	$replacements[10] = '&#246;';	
	$replacements[11] = '&#231;';	
	
	return preg_replace($patterns, $replacements, $string);
}

function print_ytl($amount, $decimal=2, $cur_symbol='', $bNull=false) {

	if (is_null($amount) && $bNull)
		return '';
		
	if ($cur_symbol=='') {
		return @number_format($amount, $decimal, ",", "."); 
	} else {
		return @number_format($amount, $decimal, ",", ".") . " " . $cur_symbol; 
	}	
}

function print_cur($amount, $cur_id=1) {
	
	$cur_symbol='';
	if($cur_id!=0)
		$cur_symbol = ' ' . $_SESSION['CUR'][$cur_id]['short_name'];
		
	if (is_null($amount))
		return '0' . $cur_symbol;

	$cur = rtrim(number_format($amount, 8, ",", "."), "0");
	return rtrim($cur, ",") . $cur_symbol; 

}	


function make_cur($amount) {
	
	if (!isset($amount)) {
		return 0;
	}	
	else { 
		if (empty($amount)) {
			return 0;	
		}	
		else {
			if (!is_numeric($amount)) {
				return 0;
			}	
			else {
				return $amount;
			}	
		}	
	}	
}

function month_name_tur($month=1, $short=0) {
	
	switch((int)$month) {
		case 1 :
			$month_name = ($short==0)?"Ocak":"Ocak";
			break;
		case 2 :
			$month_name =  ($short==0)?"Şubat":"Şub";
			break;
		case 3 :
			$month_name =  ($short==0)?"Mart":"Mart";
			break;
		case 4 :
			$month_name =  ($short==0)?"Nisan":"Nis";
			break;
		case 5 :
			$month_name =  ($short==0)?"Mayıs":"May";
			break;
		case 6 :
			$month_name =  ($short==0)?"Haziran":"Haz";
			break;
		case 7 :
			$month_name =  ($short==0)?"Temmuz":"Tem";
			break;
		case 8 :
			$month_name =  ($short==0)?"Ağustos":"Ağu";
			break;
		case 9 :
			$month_name =  ($short==0)?"Eylül":"Eyl";
			break;
		case 10 :
			$month_name =  ($short==0)?"Ekim":"Ekim";
			break;
		case 11 :
			$month_name =  ($short==0)?"Kasım":"Kas";
			break;
		case 12 :
			$month_name =  ($short==0)?"Aralık":"Ara";
			break;
	}
	return $month_name;		
}

function month_name_eng($month=1, $short=0) {
	
	switch((int)$month) {
		case 1 :
			$month_name = ($short==0)?"January":"Jan";
			break;
		case 2 :
			$month_name =  ($short==0)?"February":"Feb";
			break;
		case 3 :
			$month_name =  ($short==0)?"March":"Mar";
			break;
		case 4 :
			$month_name =  ($short==0)?"April":"Apr";
			break;
		case 5 :
			$month_name =  ($short==0)?"May":"May";
			break;
		case 6 :
			$month_name =  ($short==0)?"June":"Jun";
			break;
		case 7 :
			$month_name =  ($short==0)?"July":"Jul";
			break;
		case 8 :
			$month_name =  ($short==0)?"August":"Aug";
			break;
		case 9 :
			$month_name =  ($short==0)?"September":"Spt";
			break;
		case 10 :
			$month_name =  ($short==0)?"October":"Oct";
			break;
		case 11 :
			$month_name =  ($short==0)?"November":"Nov";
			break;
		case 12 :
			$month_name =  ($short==0)?"December":"Dec";
			break;
	}
	return $month_name;		
}

function day_tur($mydate, $short=0) {
	
	switch (date("D" , strtotime($mydate))) {
		case "Sun" :
			$day = ($short==0)?"Pazar":"Paz";
			break;
		case "Tue" :
			$day = "Salı";
			break;
		case "Wed" :
			$day = ($short==0)?"Çarşamba":"Çar";
			break;
		case "Thu" :
			$day = ($short==0)?"Perşembe":"Per";
			break;
		case "Fri" :
			$day = "Cuma";
			break;
		case "Sat" :
			$day = ($short==0)?"Cumartesi":"Cts";
			break;
		default :
			$day = ($short==0)?"Pazartesi":"Pzt";
			break;
	}
	return $day;
}


function day_eng($mydate, $short=0) {
	
	switch (date("D" , strtotime($mydate))) {
		case "Sun" :
			$day = ($short==0)?"Sunday":"Sun";
			break;
		case "Tue" :
			$day = ($short==0)?"Tuesday":"Tue";
			break;
		case "Wed" :
			$day = ($short==0)?"Wednesday":"Wed";
			break;
		case "Thu" :
			$day = ($short==0)?"Thursday":"Thu";
			break;
		case "Fri" :
			$day = ($short==0)?"Friday":"Fri";
			break;
		case "Sat" :
			$day = ($short==0)?"Saturday":"Sat";
			break;
		default :
			$day = ($short==0)?"Monday":"Mon";
			break;
	}
	return $day;
}

function date_tur($mydate, $format=1) {
	
	if($mydate==null || $mydate=='')
		return '';
		
	switch ($format) {
		case 1: //01 Ocak 2009
		   return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate));
		case 2: //01 Ocak 2009 Perşembe
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate)) . " " . day_tur($mydate);
		case 3: //01 Ocak Perşembe
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . day_tur($mydate);			
		case 4: //01 Ocak Perşembe 12:00:00
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . date("H:i:s" , strtotime($mydate));
		case 5: //5 saat 10 dakika 
		   $hour = "";
		   $min = "";
		   	
		   if((int)date("G" , strtotime($mydate))>0)	
		   		$hour = date("G" , strtotime($mydate)) . " saat ";

		   if((int)date("i" , strtotime($mydate))>0)	
		   		$min = date("i" , strtotime($mydate)) . " dakika ";
		
		   if($hour=="" && $min=="")
		   		return "1 dakika";
		   else				
    	   		return $hour . $min;
		case 6:
		   return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate)));
		case 7: //01 Ocak
		   return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate)));
		case 8: //01 Ocak Perşembe 12:00  
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . day_tur($mydate) . " " . date("H:i" , strtotime($mydate));
		case 9: //12:00:00
	       return date("H:i:s" , strtotime($mydate));
		case 10:
			$h=crop_leading_zero(date("H",strtotime($mydate)));
			$m=crop_leading_zero(date("i",strtotime($mydate)));
			$s=crop_leading_zero(date("s",strtotime($mydate)));
			
			$ret = "";
						
			if($h>0)
				if ($m>0 || $s>0)
					$ret  = $h . "sa. ";
				else	
					$ret  = $h . " saat";
		
			if($m>0)
				if ($h>0 or $s>0)	
					$ret  = $ret  . $m . "dk. ";	
				else
					$ret  = $ret  . $m . " dakika";	
						
			if($s>0)		
				if ($h>0 or $m>0)	
					$ret  = $ret  . $s . "sn.";
				else	
					$ret  = $ret  . $s . " saniye";
			
			return $ret;				   
		case 11: //01 Oca
		   return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate)),1);
		case 12: //01 Oca 2011 12:00
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate)), 1) . " " . date("H:i" , strtotime($mydate));
		case 13: //01 Oca Cuma 12:00
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate)),1) . " " . day_tur($mydate,1) . " " . date("H:i" , strtotime($mydate));
   
		case 14: //01.01.2009
		   return date("d" , strtotime($mydate)) . "." . date("m" , strtotime($mydate)) . "." . date("Y" , strtotime($mydate));

		case 15: //01 Oca Cuma 12:00
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate)) . " " . day_tur($mydate) . " " . date("H:i" , strtotime($mydate));
		case 16: //01 Ocak 2009
		   return date("d" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate));
		case 17: //12:00
	       return date("H:i" , strtotime($mydate));

		case 18: //01 Ocak 2011 Perşembe 12:00  
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate)) . ' ' . day_tur($mydate) . " " . date("H:i" , strtotime($mydate));
		   
		case 19: //Ocak 2009
		   return month_name_tur(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate));
		case 20: //01 Oca 2011
	       return date("j" , strtotime($mydate)) . " " . month_name_tur(date("n" , strtotime($mydate)), 1) . " "  . date("Y" , strtotime($mydate));
		
	}		
}


function date_eng($mydate, $format=1) {
	
	if($mydate==null || $mydate=='')
		return '';
		
	switch ($format) {
		case 1: //01 Ocak 2009
		   return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate));
		case 2: //01 Ocak 2009 Perşembe
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate)) . " " . day_eng($mydate);
		case 3: //01 Ocak Perşembe
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . day_eng($mydate);			
		case 4: //01 Ocak Perşembe 12:00:00
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . date("H:i:s" , strtotime($mydate));
		case 5: //5 saat 10 dakika 
		   $hour = "";
		   $min = "";
		   	
		   if((int)date("G" , strtotime($mydate))>0)	
		   		$hour = date("G" , strtotime($mydate)) . " saat ";

		   if((int)date("i" , strtotime($mydate))>0)	
		   		$min = date("i" , strtotime($mydate)) . " dakika ";
		
		   if($hour=="" && $min=="")
		   		return "1 dakika";
		   else				
    	   		return $hour . $min;
		case 6:
		   return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate)));
		case 7: //01 Ocak
		   return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate)));
		case 8: //01 Ocak Perşembe 12:00  
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . day_eng($mydate) . " " . date("H:i" , strtotime($mydate));
		case 9: //12:00:00
	       return date("H:i:s" , strtotime($mydate));
		case 10:
			$h=crop_leading_zero(date("H",strtotime($mydate)));
			$m=crop_leading_zero(date("i",strtotime($mydate)));
			$s=crop_leading_zero(date("s",strtotime($mydate)));
			
			$ret = "";
						
			if($h>0)
				if ($m>0 || $s>0)
					$ret  = $h . "sa. ";
				else	
					$ret  = $h . " saat";
		
			if($m>0)
				if ($h>0 or $s>0)	
					$ret  = $ret  . $m . "dk. ";	
				else
					$ret  = $ret  . $m . " dakika";	
						
			if($s>0)		
				if ($h>0 or $m>0)	
					$ret  = $ret  . $s . "sn.";
				else	
					$ret  = $ret  . $s . " saniye";
			
			return $ret;				   
		case 11: //01 Oca
		   return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate)),1);
		case 12: //01 Oca 2011 12:00
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate)), 1) . " " . date("H:i" , strtotime($mydate));
		case 13: //01 Oca Cuma 12:00
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate)),1) . " " . day_eng($mydate,1) . " " . date("H:i" , strtotime($mydate));
   
		case 14: //01.01.2009
		   return date("d" , strtotime($mydate)) . "." . date("m" , strtotime($mydate)) . "." . date("Y" , strtotime($mydate));

		case 15: //01 Oca Cuma 12:00
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate)) . " " . day_eng($mydate) . " " . date("H:i" , strtotime($mydate));
		case 16: //01 Ocak 2009
		   return date("d" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate));
		case 17: //12:00
	       return date("H:i" , strtotime($mydate));

		case 18: //01 Ocak 2011 Perşembe 12:00  
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate)) . ' ' . day_eng($mydate) . " " . date("H:i" , strtotime($mydate));
		   
		case 19: //Ocak 2009
		   return month_name_eng(date("n" , strtotime($mydate))) . " " . date("Y" , strtotime($mydate));
		case 20: //01 Oca 2011
	       return date("j" , strtotime($mydate)) . " " . month_name_eng(date("n" , strtotime($mydate)), 1) . " "  . date("Y" , strtotime($mydate));
		
	}		
}


function show_minutes($control, $id, $default=-1) {
	
	if ($default==-1)
		$default = date('i');
		
	$showminute=array("0","30","15","45","5","10","20","25","35","40","50","55");
	$text="" ;  
	
	if($control!='')
		$control=substr($control, 3, 2);
	else
		$control=$default;
		
	for($i=0; $i<count($showminute); $i++) {
	  
	  $text .='<option value="' . str_pad($showminute[$i], 2, "0", STR_PAD_LEFT) . '"';
	  if($i<4)
	  	$text .='style="background-color: #ddeeee;"';
	  
	  if($control==str_pad($showminute[$i], 2, "0", STR_PAD_LEFT))
	   	$text .=' selected="selected"';
		
	  $text .='>' . str_pad($showminute[$i], 2, "0", STR_PAD_LEFT) . '</option>';	
	  
	}
	
	return $text;
}


/**
 * Copy a file, or recursively copy a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/repos/v/function.copyr.php
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function dir_copy($source, $dest)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
		//echo $source . "--->" . $dest . "<br/>";
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
		//echo "<br/><br/>Mkdir:$dest<br/><br/>";
        mkdir($dest);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        dir_copy("$source/$entry", "$dest/$entry");
    }

    // Clean up
    $dir->close();
    return true;
}

function is_date($value, $format = 'dd.mm.yyyy'){
    
    if(strlen($value) == 10 && strlen($format) == 10){
        
        // find separator. Remove all other characters from $format
        $separator_only = str_replace(array('m','d','y'),'', $format);
        $separator = $separator_only[0]; // separator is first character
        
        if($separator && strlen($separator_only) == 2){
            // make regex
            $regexp = str_replace('mm', '[0-1][0-9]', $value);
            $regexp = str_replace('dd', '[0-3][0-9]', $value);
            $regexp = str_replace('yyyy', '[0-9]{4}', $value);
            $regexp = str_replace($separator, "\\" . $separator, $value);
            
            if($regexp != $value && preg_match('/'.$regexp.'/', $value)){

                // check date
                $day   = substr($value,strpos($format, 'd'),2);
                $month = substr($value,strpos($format, 'm'),2);
                $year  = substr($value,strpos($format, 'y'),4);
                
                if(@checkdate($month, $day, $year))
                    return true;
            }
        }
    }
    return false;
}

function secsToTime($secs) {
   $times = array(3600, 60, 1);
   $time = '';
   $tmp = '';
   for($i = 0; $i < 3; $i++) {
      $tmp = floor($secs / $times[$i]);
      if($tmp < 1) {
         $tmp = '00';
      }
      elseif($tmp < 10) {
         $tmp = '0' . $tmp;
      }
      $time .= $tmp;
      if($i < 2) {
         $time .= ':';
      }
      $secs = $secs % $times[$i];
   }
   //if (strlen($time)==8 && substr($time,0,2)=="00")
   //		$time=substr($time,3,5);
		
   return $time;
}

function strposOffset($search, $string, $offset)
{
    /*** explode the string ***/
    $arr = explode($search, $string);
    /*** check the search is not out of bounds ***/
    switch( $offset )
    {
        case $offset == 0:
        return false;
        break;
    
        case $offset > max(array_keys($arr)):
        return false;
        break;

        default:
        return strlen(implode($search, array_slice($arr, 0, $offset)));
    }
}

function quot($dbc, $text, $html=false) {

	//if ($html)
	//	$text = mysqli_real_escape_string($dbc, trim(htmlspecialchars($text, ENT_QUOTES)));
	//else
		$text = mysqli_real_escape_string($dbc, trim($text));
			
	return "'" . $text . "'";
}

function session_control() {

if (!isset($_SESSION['user_id'])) {
		return false;	
	} else {
		@session_regenerate_id();
		return true;
	}
		
}	

function user_authorize($url, $title) {

	global $dbc;
	$url = quot($dbc, $url);   
	
	$page_id = '0';
	$q = "SELECT * FROM menu WHERE url=$url";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
		$page_id=$row['id'];

	$apage = explode(",", $_SESSION['menu_pages']);	
	for($i=0; $i<count($apage); $i++) {
		if ($apage[$i]==$page_id)
			return true;
	}		

	//$_SESSION['error_text'] = "DİKKAT: $title sayfasına girmeye yetkili değilsiniz, Anasayfaya yönlendirildiniz.";	
	return false;		
	
}

function user_page_authorize() {

	$page = explode("/",ltrim($_SERVER['SCRIPT_NAME'], '/\\'));   
	global $dbc;
	
	$page_id = '0';
	$q = "SELECT * FROM menu WHERE url='" . $page[0] . "'";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
		$page_id=$row['id'];

	$apage = explode(",", $_SESSION['menu_pages']);	
	for($i=0; $i<count($apage); $i++) {
		if ($apage[$i]==$page_id)
			return true;
	}		
	return false;			
}


function user_customer_authorize($customer_id) {

	global $dbc;
	$q_ek='';
	$cid=-1;
	$country_list=get_permitted_countries($dbc, $_SESSION['user_id']);

	
	if($country_list!="" && $country_list!=NULL)
		$q_ek .=" AND country_id IN (" . $country_list . ")";

	
	/*
	$q_ek .=" AND (";
	for($ii=0;$ii<count($city_list);$ii+=2) {
		if($ii>0)
			$q_ek .=" OR ";
		$jj=$ii+1;
		if($city_list[$ii]>0)
			$q_ek .=" (c.city_id='" . $city_list[$ii] . "' AND c.country_id='" . $city_list[$jj] . "')";
		else
			$q_ek .=" (c.city_id>='0' AND c.country_id='" . $city_list[$jj] . "')";
	}
	$q_ek .=" )";
		*/	
	$q = "SELECT c.id FROM customers c WHERE c.id='" . $customer_id . "'" . $q_ek ;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
		$cid=$row['id'];
	
	if($customer_id==0)
		return true;
	else if ($cid>-1)
		return true;
	else	
	 return false;			
}


function user_shipment_authorize($shipment_id) {

	global $dbc;
	$q_ek='';
	$cid=-1;
	$country_list=get_permitted_countries($dbc, $_SESSION['user_id']);
	$dealer_list=get_permitted_dealers($dbc, $_SESSION['user_id']);
	if($country_list!="" && $country_list!=NULL)
		$q_ek .=" AND country_id IN (" . $country_list . ")";
		
	if($dealer_list!="" && $dealer_list!=NULL)
		$q_ek .=" AND dagitici IN (" . $dealer_list . ")";	
		
	$q = "SELECT id FROM gonderi  WHERE id='" . $shipment_id . "'" . $q_ek ;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
		$cid=$row['id'];
	
	if (intval($_SESSION['user_id'])<1)
		return false;
	else if($shipment_id==0 )
		return true;
	else if ($cid>-1)
		return true;
	else	
	 	return false;			
}


function user_all_shipment_authorize($customer_id) {

	global $dbc;
	$q_ek='';
	$cid=-1;
	$country_list=get_permitted_countries($dbc, $_SESSION['user_id']);
	//$city_list=get_permitted_cities($dbc, $_SESSION['user_id']);
	
	
	if($country_list!="" && $country_list!=NULL)
		$q_ek .=" AND country_id IN (" . $country_list . ")";
	
	/*
	$q_ek .=" AND (";
	for($ii=0;$ii<count($city_list);$ii+=2) {
		if($ii>0)
			$q_ek .=" OR ";
		$jj=$ii+1;
		if($city_list[$ii]>0)
			$q_ek .=" (c.city_id='" . $city_list[$ii] . "' AND c.country_id='" . $city_list[$jj] . "')";
		else
			$q_ek .=" (c.city_id>='0' AND c.country_id='" . $city_list[$jj] . "')";
	}
	$q_ek .=" )";
	*/
		
	$q = "SELECT c.id FROM customers c WHERE c.id='" . $customer_id . "'" . $q_ek ;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
		$cid=$row['id'];
	
	if($customer_id==0)
		return true;
	else if ($cid>-1)
		return true;
	else	
	 return false;			
}

function get_status_id_from_shipment($shipment_id){
	global $dbc;
	$return_id=0;
	$q = "SELECT status_id FROM gonderi WHERE id='" . $shipment_id . "'" ;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
		$return_id=$row['status_id'];
	//echo $q;	
	return $return_id;
}


function check_email($email, $required=false) {

	if ($required && $email=='')
		return false;
		
	if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
		return true;
	else
		return false;	

}

function get_currencies($dbc) {
	
	$ok = false;
	
	if (isset($_SESSION['currency_update_time'])) {
		//echo (time()-$_SESSION['currency_update_time']); 
		if ((time()-$_SESSION['currency_update_time'])>=3600)
			$ok = true;
	} else {
		$ok = true;
	}	
	
	if ($ok) {	
		$_SESSION['CUR']=array();
		
		$_SESSION['CUR'][0]['id']="0";
		$_SESSION['CUR'][0]['symbol']='';
		$_SESSION['CUR'][0]['short_name']='';
		$_SESSION['CUR'][0]['buying']='0';
		$_SESSION['CUR'][0]['selling']='0';
		
		$q = "SELECT c.id, c.short_name, c.symbol, COALESCE(q1.buying,1) buying, COALESCE(q1.selling,1) selling FROM currencies c LEFT JOIN (SELECT * FROM (SELECT * FROM currency_rates WHERE currency_id=1 ORDER BY c_date DESC LIMIT 0,1) a UNION SELECT * FROM (SELECT * FROM currency_rates WHERE currency_id=2 ORDER BY c_date DESC LIMIT 0,1)b ) q1 ON c.id=q1.currency_id";
		$r = @mysqli_query ($dbc, $q); // Run the query.
		while ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)) {			
			$_SESSION['CUR'][$row['id']]=$row;
		}
		$_SESSION['currency_update_time']=time();	
	}	
	
}

function get_spec_rights($dbc, $user_id, $special_right_id) {

	$q = "SELECT * FROM user_special_rights WHERE user_id=$user_id AND special_right_id=$special_right_id";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
		return true;
	else
		return false;
}

function get_child_users($dbc, $user_id) {

	$q = "SELECT GROUP_CONCAT(child_user_id ORDER BY id SEPARATOR ', ') AS child_users FROM user_child_users WHERE user_id=$user_id";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	$row = @mysqli_fetch_array ($r, MYSQLI_ASSOC);
  
  $child_users=$row['child_users'];
  if($child_users!="")
  $child_users=$child_users.", ".$user_id;
  else
  $child_users=$user_id;
	
  return $child_users; 
  
}



function get_parent_users($dbc, $user_id) {

	$q = "SELECT * FROM user_child_users WHERE child_user_id=$user_id";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	
	$a_row = array();
	while ($row = @mysqli_fetch_array ($r, MYSQLI_ASSOC))
		$a_row[] = $row['user_id'];	

	return $a_row; 
}

function get_special_right_status($dbc, $user_id, $special_right_id) {
	$status=0;
	$q = "SELECT id FROM user_special_rights WHERE ok='1' AND special_right_id='" . $special_right_id . "' AND user_id='" . $user_id . "'";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if($row = @mysqli_fetch_array ($r, MYSQLI_ASSOC)){
		
		if($row['id']>0)
		$status=1;
	}

  return $status; 
}

function get_user_preparing_payment_plan_num($dbc, $user_id) {
	$num=0;
	$q = "SELECT COUNT(id) num FROM payment_plans WHERE approving_status_id='1' AND user_id=$user_id";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if($row = @mysqli_fetch_array ($r, MYSQLI_ASSOC)){
		
		if($row['num']>0)
		$num=$row['num'];
	}

  return $num; 
}

function get_permitted_countries($dbc, $user_id) {

	$q = "SELECT COALESCE(GROUP_CONCAT(country_id ORDER BY id SEPARATOR ', '),'') AS country_ids FROM user_countries WHERE user_id=$user_id";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	$row = @mysqli_fetch_array ($r, MYSQLI_ASSOC);
  
  $country_ids=$row['country_ids'];

  return $row['country_ids']; 
}




function get_permitted_cities($dbc, $user_id) {

	$q = "SELECT uc.country_id, COALESCE(uc2.city_id,0) city_id, uc.user_id FROM user_countries uc LEFt JOIN user_cities uc2 ON uc2.country_id=uc.country_id WHERE uc.user_id=" .$user_id;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	$cities =  array();
	//$countries =  array();
	
	while($row = @mysqli_fetch_array ($r, MYSQLI_ASSOC)){
		if($row['user_id']==$user_id) {
		array_push($cities, $row['city_id']);
		array_push($cities, $row['country_id']);
		}
	}
	

  return $cities; 
}


function get_permitted_dealers($dbc, $user_id) {

	$q = "SELECT COALESCE(GROUP_CONCAT(dealer_id ORDER BY dealer_id SEPARATOR ','),'') AS dealer_ids FROM user_dealers WHERE user_id=$user_id";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	$row = @mysqli_fetch_array ($r, MYSQLI_ASSOC);
  
  $dealer_ids=$row['dealer_ids'];

  return $row['dealer_ids']; 
}


function get_permitted_users_id($dbc, $user_id) {
 
  $people_id =$user_id; 
  
	$q = "SELECT user_id, manager_id FROM sales_region";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	while ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)) {		
  $users = explode(",", $row['manager_id']);
    for($i=0;$i<count($users);$i++){
     if ($users[$i]==$user_id) 
  	 $people_id =$people_id.",".$row['user_id'];	
  	}

  }
    return $people_id;
}



function get_customer_name($dbc, $customer_id, $param=0) {
	$q = "SELECT id, name, surname FROM customers WHERE id='" . $customer_id . "'";
	
	//echo $q;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);		
 	if($param==1)
		return $row['name'] . " " . $row['surname'];
	else
    	return "ATS". $row['id'] . " " . $row['name'] . " " . $row['surname'];
}


function get_company_name($dbc, $company_id, $param=0) {
	$q = "SELECT id, marka, firmaadi FROM firma WHERE id='" . $company_id . "'";
	
	//echo $q;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);		
 	if($param==1)
		if($row['marka']!="")
			return $row['marka'];
		else
			return $row['firmaadi'];
	else
		if($row['firmaadi']!="")
    		return "FIR". $row['id'] . " " . $row['marka'] . " (" . $row['firmaadi'] . ")";
		else
			return "FIR". $row['id'] . " " . $row['marka'];
			
}



function  get_country_id_of_customer($dbc, $cid) {


	$q = "SELECT co.id, co.iso FROM customers c LEFT JOIN country co ON co.id=c.country_id WHERE c.id=".$cid;
	$r = @mysqli_query ($dbc, $q); // Run the query.
	//echo $q;
  	if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
 	 return $row['id'];
	else
 	 return 0; 
}



function  get_par_data_details_right($dbc, $cid) {


	$q = "SELECT ";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	
  if ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
  return true;
	else
  return false; 
}

function exchange($val, $source_cur, $dest_cur) {  // 100, 1, 2
	
	if ($source_cur==$dest_cur || make_cur($source_cur)==0 || make_cur($dest_cur)==0)
		return $val;
	
	$sourceVal = $_SESSION['CUR'][$source_cur]['buying'];
	$destVal = $_SESSION['CUR'][$dest_cur]['buying'];

	return ($val*$sourceVal)/$destVal;
}

function list_matches($sItem, $sList) {

	if ($sList=='')
		return true;

	$aItem = explode(',', $sItem);
	$aList = explode(',', $sList);
	
	if (is_array($aItem)) {
		for($i=0;$i<count($aItem);$i++)
			if (in_array($aItem[$i], $aList))
				return true;
	} else {
		if (in_array($aItem, $aList))
			return true;
	}			
	
	return false;
					
}

function tr_to_eng($text) {

	$tr_set = array('ç','Ç','ı','İ','ğ','Ğ','ü','ö','Ş','ş','Ö','Ü','(',')','[',']'); 
	$en_set = array('c','C','i','I','g','G','u','o','S','s','O','U','' ,'' ,'' ,''); 
 
	return str_replace($tr_set, $en_set, $text); 

}

function sql_to_tr($text) {

	$sql_set = array('ç','Ç','ı','Ý','ğ','Ğ','ü','ö','Þ','ş','Ö','Ü'); 
	$tr_set = array('ç','Ç','ı','İ','ğ','Ğ','ü','ö','Ş','ş','Ö','Ü'); 
 
	return str_replace($sql_set, $tr_set, $text); 

}


 function par_development_types ($type_id=0) {
	
   if($type_id==-1) return 'Müşteri Görüşmesi';
   else if($type_id==-2) return '<b style="color:#FF0000 !important">Müşteriye<br/>Uyarı Mesajı</b>';   
   else if($type_id==-3) return '<b style="color:#FFB584 !important">Müşteriye<br/>Ödemenizi Yapın Mesajı</b>';   
   else if($type_id==-4) return 'Vade Tarihi Değişikliği';
   else if($type_id==-5) return 'Tahsilat Sorumlusu Değişikliği';
   else if($type_id==-6) return 'Tahsilata Destek';
   else if($type_id==0) return 'Açıklama';
   else if($type_id>0) return 'Tahsilat Sorumlusuna Mesaj'; 

   
}   

function ckeditor_text_to_array($text) {

	$text = str_replace("\n", "", $text);	
	$text = str_replace("&nbsp;", " ", $text);	
	$text = str_replace("<br />", "<br/>", $text);
	$text = str_replace("<br /><br />", "<br/>&nbsp;", $text);
	$text = str_replace("<br />", "<br/>", $text);
	$text = str_replace("<br />", "<br/>", $text);
	$text = str_replace("<p>", "", $text);		
	$text = str_replace("</p>", "<br/>", $text);
	$text = str_replace("strong>", "b>", $text);			
	$text = str_replace("em>", "red>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	
	$atext = explode('<br>', $text);
	return $atext;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
	$val=((float)$usec + (float)$sec) * 100;
    return str_replace(".", "", $val);
}

function secs_to_time($seconds) {
	
	if ($seconds==0)
		return '';
		
	$hours = floor($seconds / 3600);
	$mins = floor(($seconds - ($hours*3600)) / 60);
	$secs = floor($seconds % 60);
	
	if($hours>0)
		if ($mins>0)
			return $hours . ' sa. ' . $mins . ' dk.';
		else	
			return $hours . ' sa.';
	else
		if ($mins>0)
			return $mins . ' dk.';
		else	
			return '';
			

}

function getIP() {
      foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
         if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
               if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                  return $ip;
               }
            }
         }
      }
   }


function date_to_arabic($datex,$type=0){

$standard = array("0","1","2","3","4","5","6","7","8","9");
$eastern_arabic_symbols = array("٠","١","٢","٣","٤","٥","٦","٧","٨","٩");
$months = array(
    "Jan" => "يناير",
    "Feb" => "فبراير",
    "Mar" => "مارس",
    "Apr" => "أبريل",
    "May" => "مايو",
    "Jun" => "يونيو",
    "Jul" => "يوليو",
    "Aug" => "أغسطس",
    "Sep" => "سبتمبر",
    "Oct" => "أكتوبر",
    "Nov" => "نوفمبر",
    "Dec" => "ديسمبر"
);

$months_full = array(
    "January" => "كانون الثاني",
    "February" => "فبراير",
    "March" => "مارس",
    "April" => "أبريل",
    "May" => "مايو",
    "June" => "يونيو",
    "July" => "يوليو",
    "Augustus" => "أغسطس",
    "September" => "سبتمبر",
    "October" => "أشهر اكتوبر",
    "November" => "تشرين الثاني",
    "December" => "ديسمبر"
);

$months_number = array(
    "01" => "كانون الثاني",
    "02" => "فبراير",
    "03" => "مارس",
    "04" => "أبريل",
    "05" => "مايو",
    "06" => "يونيو",
    "07" => "يوليو",
    "08" => "أغسطس",
    "09" => "سبتمبر",
    "10" => "أشهر اكتوبر",
    "11" => "تشرين الثاني",
    "12" => "ديسمبر"
);

//$datex_arr=explode("-",$datex);

//$current_date = date('d').'-'.date('m').'-'.date('Y');

$year = date("Y", strtotime($datex));
$month = date("m", strtotime($datex));
$day = date("d", strtotime($datex));


$ar_month = $months_number[$month];
$ar_year = str_replace($standard , $eastern_arabic_symbols , $year);
$ar_day = str_replace($standard , $eastern_arabic_symbols , $day);

return $ar_day . " " . $ar_month  . " " . $ar_year ;
}


function validateTaxNo($taxNo)
{
    if (strlen($taxNo) == 10) {
        for ($i = 0; $i < 9; $i++) {
            $v[$i + 1] = ($taxNo[$i] + (9 - $i)) % 10;
            $vv[$i + 1] = ($v[$i + 1] * pow(2, (9 - $i))) % 9;
            $vv[$i + 1] = ($v[$i + 1] != 0 && $vv[$i + 1] == 0) ? 9 : $vv[$i + 1];
        }
        $sum = array_sum($vv);
        $sum = ($sum % 10 == 0) ? 0 : (10 - ($sum % 10));
        return ($sum == $taxNo[9]) ? true : false;
    }
    return false;
}


function isTcKimlik($tc)  
{  
if(strlen($tc) < 11){ return false; }  
if($tc[0] == '0'){ return false; }  
$plus = ($tc[0] + $tc[2] + $tc[4] + $tc[6] + $tc[8]) * 7;  
$minus = $plus - ($tc[1] + $tc[3] + $tc[5] + $tc[7]);  
$mod = $minus % 10;  
if($mod != $tc[9]){ return false; }  
$all = '';  
for($i = 0 ; $i < 10 ; $i++){ $all += $tc[$i]; }  
if($all % 10 != $tc[10]){ return false; }  
  
return true;  
}

function generate_secure_number_for_cheque($check_number,$customer_id){
	//ilk çek numarası ile müşteri id yi çar ve karekök al
	$your_number=sqrt((int)($check_number)*(int)($customer_id));
	// ondalık kısmı al
	list($whole, $decimal) = explode('.', $your_number);

	//ilk 3 hane oluşan sayının ondalık kısmından sonraki 3 hane rastgele
	$sonuc=substr($decimal, 0, 3). generatePIN(3);
	return $sonuc;
}

function generatePIN($digits = 3){
    $i = 0; //counter
    $pin = ""; //our default pin is blank.
    while($i < $digits){
	
		// ilk ve son rakam 0 olmasın
		if($i==0 || $i==$digits) 
			$pin .= mt_rand(1, 9);
		else 
       	 	$pin .= mt_rand(0, 9);
		
       $i++;
    }
    return $pin;
}


?>
