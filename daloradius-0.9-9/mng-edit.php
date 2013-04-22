<?php

include ("library/checklogin.php");
$operator = $_SESSION['operator_user'];

include('library/check_operator_perm.php');


function addedit($value,$value_db,$attribute_name,$table){
$value = trim($value);
$value_db=trim($value_db);

    if(strcmp($value,$value_db)!=0){


        global $username, $configValues,$dbSocket;

        if(isset ($value) && isset ($value_db)){
             $sql= "UPDATE ".$configValues[$table]." SET Value='".$dbSocket->escapeSimple($value)."' WHERE username='".$dbSocket->escapeSimple($username)."' AND Attribute='$attribute_name'";

                                        }
        elseif(!isset ($value) || ($value==0)){
            $sql="DELETE FROM ".$configValues[$table]." WHERE username='".$dbSocket->escapeSimple($username)."' AND Attribute='$attribute_name'";

            }
        else{
            $sql="INSERT INTO ".$configValues[$table]." (id,Username,Attribute,op,Value) ".
                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', '$attribute_name', ':=', '".
                                                    $dbSocket->escapeSimple($value)."')";
        }
        $res = $dbSocket->query($sql);
        $logDebugSQL .= $sql . "\n";
    }

}


$username = "";
$password = "";
$maxallsession = "";
$expiration = "";
$sessiontimeout = "";
$idletimeout = "";
$ui_changeuserinfo = "0";
$bi_changeuserbillinfo = "0";

$logAction = "";
$logDebugSQL = "";


include 'library/opendb.php';

if(isset($_GET['username']) && $_GET['username']!=''){
    $username=$_GET['username'];

        $sql = "SELECT attribute,value FROM ".$configValues['CONFIG_DB_TBL_RADREPLY']." WHERE UserName='$username'  UNION SELECT attribute,value FROM ".$configValues['CONFIG_DB_TBL_RADCHECK']." WHERE UserName='$username';";


        $res = $dbSocket->query($sql);
	$logDebugSQL .= $sql . "\n";

        while ($row = $res->fetchRow()) {
            $attributes[$row[0]]['value']=$row[1];

            }

}

if (isset($_POST['submit'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
        $groups = $_POST['groups'];

        $speedown=$_POST['speedown']*$_POST['speedownFactor'];


        $expiration = $_POST['expiration'];
        $sessiontimeout = $_POST['sessiontimeout']*$_POST['sessiontimeoutFactor'];

        $simultaneoususe = $_POST['simultaneoususe'];

        $downDaily = $_POST['downDaily']*$_POST['downDailyFactor'];
        $downWeekly = $_POST['downWeekly']* $_POST['downWeeklyFactor'];
        $downMontly = $_POST['downMontly']*$_POST['downMontlyFactor'] ;
        $downAll = $_POST['downAll']*$_POST['downAllFactor'];

        $timeDaily = $_POST['timeDaily']* $_POST['timeDailyFactor'];
        $timeWeekly = $_POST['timeWeekly']* $_POST['timeWeeklyFactor'] ;
        $timeMontly = $_POST['timeMontly']* $_POST['timeMontlyFactor'];
        $timeAll = $_POST['timeAll']* $_POST['timeAllFactor'] ;

        isset($_POST['firstname']) ? $firstname = $_POST['firstname'] : $firstname = "";
        isset($_POST['lastname']) ? $lastname = $_POST['lastname'] : $lastname = " ";
        isset($_POST['email']) ? $email = $_POST['email'] : $email = "";
        isset($_POST['department']) ? $department = $_POST['department'] : $department = "";
        isset($_POST['company']) ? $company = $_POST['company'] : $company = "";
        isset($_POST['workphone']) ? $workphone = $_POST['workphone'] : $workphone =  "";
        isset($_POST['homephone']) ? $homephone = $_POST['homephone'] : $homephone = "";
        isset($_POST['mobilephone']) ? $mobilephone = $_POST['mobilephone'] : $mobilephone = "";
        isset($_POST['address']) ? $address = $_POST['address'] : $address = "";
        isset($_POST['city']) ? $city = $_POST['city'] : $city = "";
        isset($_POST['state']) ? $state = $_POST['state'] : $state = "";
        isset($_POST['zip']) ? $zip = $_POST['zip'] : $zip = "";
        isset($_POST['notes']) ? $notes = $_POST['notes'] : $notes = "";
        isset($_POST['changeuserinfo']) ? $ui_changeuserinfo = $_POST['ui_changeuserinfo'] : $ui_changeuserinfo = "0";
        isset($_POST['enableUserPortalLogin']) ? $ui_enableUserPortalLogin = $_POST['enableUserPortalLogin'] : $ui_enableUserPortalLogin = "0";
        isset($_POST['portalLoginPassword']) ? $ui_PortalLoginPassword = $_POST['portalLoginPassword'] : $ui_PortalLoginPassword = "";







		$sql = "SELECT * FROM ".$configValues['CONFIG_DB_TBL_RADCHECK']." WHERE UserName='$username'";
		$res = $dbSocket->query($sql);
		$logDebugSQL .= $sql . "\n";

		//if ($res->numRows() != 0) {
                  //  $failureMsg = "¡El usuario: <b> $username </b> ya existe!";
                  //  $logAction .= "Failed adding new user already existing in database [$username] on page: ";
                //}

                ///else
                if (trim($username) == "" or trim($password) == "") {
                    $failureMsg = "El password o el usuario no pueden quedar vacios";
                    $logAction .= "Failed adding (possible empty user/pass) new user [$username] on page: ";
                }
                    else{

//                            $dbPassword = $dbSocket->escapeSimple($password);
//
//                            // insert username/password
//                            $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK'].
//                                            " (id,Username,Attribute,op,Value) ".
//                                            " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Cleartext-Password', ".
//                                            " ':=', '$dbPassword')";
//
//                            $sql2 = "UPDATE ".$configValues['CONFIG_DB_TBL_RADCHECK']."Value='$dbPassword' WHERE username='".$dbSocket->escapeSimple($username)."' AND Attribute='Cleartext-Password'";
//
//                            $res = $dbSocket->query($sql);
//                            $logDebugSQL .= $sql . "\n";

                             //*******DURACION DE LA SESION****//


//                        if ($speedown) {
//
//
//                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADREPLY']." (id,Username,Attribute,op,Value) ".
//                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'WISPr-Bandwidth-Max-Down', ':=', '".
//                                                    $dbSocket->escapeSimple($speedown)."')";
//
//
//                                $res = $dbSocket->query($sql);
//                                $logDebugSQL .= $sql . "\n";
//
//
//                            }




				 addedit($password,$attributes['Cleartext-Password']['value'],'Cleartext-Password','CONFIG_DB_TBL_RADCHECK');
                           addedit($speedown,$attributes['WISPr-Bandwidth-Max-Down']['value'],'WISPr-Bandwidth-Max-Down','CONFIG_DB_TBL_RADREPLY');
                           addedit($downDaily, $attributes['CS-Input-Octets-Daily']['value'],'CS-Input-Octets-Daily', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($downWeekly, $attributes['CS-Input-Octets-Weekly']['value'],'CS-Input-Octets-Weekly', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($downMontly, $attributes['CS-Input-Octets-Monthly']['value'],'CS-Input-Octets-Monthly', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($downAll, $attributes['CS-Input-Octets']['value'],'CS-Input-Octets', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($timeDaily, $attributes['Max-Daily-Session']['value'],'Max-Daily-Session', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($timeWeekly, $attributes['Max-Weekly-Session']['value'],'Max-Weekly-Session', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($timeMontly, $attributes['Max-Monthly-Session']['value'],'Max-Monthly-Session', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($timeAll, $attributes['Max-All-Session']['value'],'Max-All-Session', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($expiration, $attributes['Expiration']['value'],'Expiration', 'CONFIG_DB_TBL_RADCHECK');
                           addedit($sessiontimeout, $attributes['Session-Timeout']['value'],'Session-Timeout', 'CONFIG_DB_TBL_RADREPLY');
                       //    addedit($idletimeout, $attributes['Idle-Timeout']['value'],'Idle-Timeout', 'CONFIG_DB_TBL_RADREPLY');
                           addedit($simultaneoususe, $attributes['Simultaneous-Use']['value'],'Simultaneous-Use', 'CONFIG_DB_TBL_RADCHECK');

                            //********GRUPOS***********//
                            if (isset($groups)) {

                                    foreach ($groups as $group) {

                                            if (trim($group) != "") {
                                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADUSERGROUP']." (UserName,GroupName,priority) ".
                                                            " VALUES ('".$dbSocket->escapeSimple($username)."', '".$dbSocket->escapeSimple($group)."',0) ";
                                                    $res = $dbSocket->query($sql);
                                                    $logDebugSQL .= $sql . "\n";
                                            }
                                    }
                            }

                            //******** INFO USUARIO*************//
                            $currDate = date('Y-m-d H:i:s');
                            $currBy = $_SESSION['operator_user'];

                            $sql = "SELECT * FROM ".$configValues['CONFIG_DB_TBL_DALOUSERINFO'].
                                            " WHERE username='".$dbSocket->escapeSimple($username)."'";
                            $res = $dbSocket->query($sql);
                            $logDebugSQL .= $sql . "\n";

                            // if there were no records for this user present in the userinfo table
                            if ($res->numRows() == 0) {
                                    // insert user information table
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_DALOUSERINFO'].
                                                    " (id, username, firstname, lastname, email, department, company, workphone, homephone, ".
                                                    " mobilephone, address, city, state, zip, notes, changeuserinfo, portalloginpassword, enableportallogin, creationdate, creationby, updatedate, updateby) ".
                                                    " VALUES (0,
                                                    '".$dbSocket->escapeSimple($username)."', '".$dbSocket->escapeSimple($firstname)."', '".
                                                    $dbSocket->escapeSimple($lastname)."', '".$dbSocket->escapeSimple($email)."', '".
                                                    $dbSocket->escapeSimple($department)."', '".$dbSocket->escapeSimple($company)."', '".
                                                    $dbSocket->escapeSimple($workphone)."', '".$dbSocket->escapeSimple($homephone)."', '".
                                                    $dbSocket->escapeSimple($mobilephone)."', '".$dbSocket->escapeSimple($address)."', '".
                                                    $dbSocket->escapeSimple($city)."', '".$dbSocket->escapeSimple($state)."', '".
                                                    $dbSocket->escapeSimple($zip)."', '".$dbSocket->escapeSimple($notes)."', '".
                                                    $dbSocket->escapeSimple($ui_changeuserinfo)."', '".
                                                    $dbSocket->escapeSimple($ui_PortalLoginPassword)."', '".$dbSocket->escapeSimple($ui_enableUserPortalLogin).
                                                    "', '$currDate', '$currBy', NULL, NULL)";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";

                            }

                                $successMsg = "Se ha modificado el usuario: <b> $username </b>";
                                $logAction .= "Successfully added new user [$username] on page: ";
                            }

                            include 'library/closedb.php';
	}
        include_once('library/config_read.php');
        $log = "visited page: ";


	if ($configValues['CONFIG_IFACE_PASSWORD_HIDDEN'] == "yes")
		$hiddenPassword = "type=\"password\"";

/*
        $sql = "SELECT attribute,value FROM ".$configValues['CONFIG_DB_TBL_RADREPLY']." WHERE UserName='$username'  UNION SELECT attribute,value FROM ".$configValues['CONFIG_DB_TBL_RADCHECK']." WHERE UserName='$username';";


        $res = $dbSocket->query($sql);
	$logDebugSQL .= $sql . "\n";

        while ($row = $res->fetchRow()) {
            $attributes[$row[0]]['value']=$row[1];

            } */


function  timefactor($value,$tipo){

    $ret[0]="";
    $ret[1]="";


    if(($value%3600)==0){
       $ret[0]=$value/3600;
       $ret[1]="h";
    }
    elseif($value%60==0){
        $ret[0]=$value/60;
        $ret[1]="m";
    }

    if($tipo=='n'){
        if($ret[0]!=0)
            echo $ret[0];
    }
    elseif($ret[1]== $tipo){
        echo  "selected";
    }
}

function  getspeed($value,$tipo){

    $ret[0]="";
    $ret[1]="";


    if(($value%1000000)==0){
       $ret[0]=$value/1000000;
       $ret[1]="m";
    }
    elseif($value%1000==0){
        $ret[0]=$value/1000;
        $ret[1]="k";
    }

    if($tipo=='n'){
        if($ret[0]!=0)
            echo $ret[0];
    }
    elseif($ret[1]== $tipo){
        echo  "selected";
    }
}


function  getsize($value,$tipo){

    $ret[0]="";
    $ret[1]="";


    if(($value%1073741824)==0){
       $ret[0]=$value/1073741824;
       $ret[1]="g";
    }
    elseif($value%1048576==0){
        $ret[0]=$value/1048576;
        $ret[1]="m";
    }

    if($tipo=='n'){
        if($ret[0]!=0)
            echo $ret[0];
    }
    elseif($ret[1]== $tipo){
        echo  "selected";
    }
}



function  getuse($value,$tipo){
    if($value==1){
       $ret[1]="p";
    }
    elseif($value>1){
        $ret[1]="c";
    }
    else{
       $ret[1]="u";
    }

    if($tipo=='n'){
        if($value!=0)
            echo $value;
    }
    elseif($ret[1]== $tipo){
        echo  "selected";
    }
}



?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title>Linbox Manager</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/1.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" type="text/css" href="library/js_date/datechooser.css">
<!--[if lte IE 6.5]>
<link rel="stylesheet" type="text/css" href="library/js_date/select-free.css"/>
<![endif]-->
</head>
<script src="library/js_date/date-functions.js" type="text/javascript"></script>
<script src="library/js_date/datechooser.js" type="text/javascript"></script>
<script src="library/javascript/pages_common.js" type="text/javascript"></script>
<script src="library/javascript/productive_funcs.js" type="text/javascript"></script>

<script type="text/javascript" src="library/javascript/ajax.js"></script>
<script type="text/javascript" src="library/javascript/ajaxGeneric.js"></script>

<script type="text/javascript">

function showinput(value){
    var simultaneoususe =document.getElementById("simultaneoususe");
    switch (value){
        case "personal":
            simultaneoususe.value = 1;
            simultaneoususe.style.display= "none";
            break;
        case "public":
            simultaneoususe.value = '';
            simultaneoususe.style.display= "none";
            break;
        case "custom":
            simultaneoususe.value = '';
            simultaneoususe.style.display= "inline";
            simultaneoususe.focus();
            break;
    }
}

function numbercheck(simultaneoususe){

    if(isNaN(simultaneoususe.value) && simultaneoususe.value!=''){
        alert( 'Ingrese un número válido');
        simultaneoususe.focus();
    }

}
function timefactor(value, id){

var select =document.getElementById(id);

if((value%3600)==0){
 select.options[0].selected= 'true';
}



}
</script>

<?php include_once ("library/tabber/tab-layout.php"); ?>

<?php include ("menu-mng-users.php"); ?>

	<div id="contentnorightbar">

		<h2 id="Intro"><a href="#" onclick="javascript:toggleShowDiv('helpPage')"><?php echo $l['Intro']['mngedit.php'] ?>
		<h144>+</h144></a></h2>

		<div id="helpPage" style="display:none;visibility:visible" >
			<?php echo $l['helpPage']['mngedit'] ?>
			<br/>
		</div>
		<?php
			include_once('include/management/actionMessages.php');
                        if(isset($_GET['username']) && $_GET['username']!=''){

                        include_once('editUser.php');
                        }

		?>


                <?php
                include('include/config/logging.php');
                ?>
        </div>
<div id="footer">

                <?php
                include 'page-footer.php';
                ?>
</div>

</div>
</div>

</body>
</html>
