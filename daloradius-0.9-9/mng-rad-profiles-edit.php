<?php
/*
 *********************************************************************************************************
 * daloRADIUS - RADIUS Web Platform
 * Copyright (C) 2007 - Liran Tal <liran@enginx.com> All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *********************************************************************************************************
 *
 * Authors:	Liran Tal <liran@enginx.com>
 *
 *********************************************************************************************************
 */
    include ("library/checklogin.php");
    $operator = $_SESSION['operator_user'];
    include('library/check_operator_perm.php');
    include 'library/opendb.php';
    
    $logAction = "";
    $logDebugSQL = "";
        //// addedit(VALOR_NUEVO, VALOR_DB, NOMBRE_ATRIBUTO, TABLA)
    function addedit($value,$value_db,$attribute_name,$table){
        if($value!=$value_db)
        {
            global $profile, $configValues,$dbSocket;
            if(isset ($value) && isset ($value_db)){
                $sql= "UPDATE ".$configValues[$table]." SET Value='".$dbSocket->escapeSimple($value)."' WHERE groupname='".$dbSocket->escapeSimple($profile)."' AND Attribute='$attribute_name'";
                                        }
            elseif(!isset ($value) || ($value==0)){
                $sql="DELETE FROM ".$configValues[$table]." WHERE groupname='".$dbSocket->escapeSimple($profile)."' AND Attribute='$attribute_name'";
            }
                else{
                    $sql="INSERT INTO ".$configValues[$table]." (id,groupname,Attribute,op,Value) ".
                                    " VALUES (0, '".$dbSocket->escapeSimple($profile)."', '$attribute_name', ':=', '".                                                   $dbSocket->escapeSimple($value)."')";
                }
                $res = $dbSocket->query($sql);
                $logDebugSQL .= $sql . "\n";
        }
    }
    if (isset($_POST['submit'])) {
	$profile = $_POST['profile'];

        $sql="SELECT Attribute, Value FROM ".$configValues['CONFIG_DB_TBL_RADGROUPREPLY']." WHERE GroupName='".$dbSocket->escapeSimple($profile)."'
                UNION SELECT Attribute, Value FROM ".$configValues['CONFIG_DB_TBL_RADGROUPCHECK']." WHERE GroupName='".$dbSocket->escapeSimple($profile)."'";
        $res = $dbSocket->query($sql);
        while($row = $res->fetchRow()) {
            $attributes[$row[0]]=$row[1];
        }




        $speedown=$_POST[speedown]*$_POST[speedownFactor];
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

                    if (trim($profile) == "" ) {
                        $failureMsg = "El nombre del perfil no puede quedar vacio";
                        $logAction .= "Failed adding (possible empty user/pass) new user [$profile] on page: ";
                    }
                    else{

                              addedit($speedown, $attributes['WISPr-Bandwidth-Max-Down'],'WISPr-Bandwidth-Max-Down','CONFIG_DB_TBL_RADGROUPREPLY');

                               addedit($downDaily, $attributes['CS-Input-Octets-Daily'],'CS-Input-Octets-Daily','CONFIG_DB_TBL_RADGROUPCHECK');

                               addedit($downWeekly, $attributes['CS-Input-Octets-Weekly'],'CS-Input-Octets-Weekly','CONFIG_DB_TBL_RADGROUPCHECK');

                               addedit($downMontly, $attributes['CS-Input-Octets-Monthly'],'CS-Input-Octets-Monthly','CONFIG_DB_TBL_RADGROUPCHECK');

                            addedit($downAll, $attributes['CS-Input-Octet'],'CS-Input-Octet','CONFIG_DB_TBL_RADGROUPCHECK');

                            addedit($timeDaily, $attributes['Max-Daily-Session'],'Max-Daily-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                            addedit($timeWeekly, $attributes['Max-Weekly-Session'],'Max-Weekly-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                             addedit($timeMontly, $attributes['Max-Monthly-Session'],'Max-Monthly-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                              addedit($timeAll, $attributes['Max-All-Session'],'Max-All-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                                addedit($expiration, $attributes['Expiration'],'Expiration','CONFIG_DB_TBL_RADGROUPCHECK');

                                addedit($sessiontimeout, $attributes['Session-Timeout'],'Session-Timeout','CONFIG_DB_TBL_RADGROUPREPLY');

                            addedit($simultaneoususe, $attributes['Simultaneous-Use'],'Simultaneous-Use','CONFIG_DB_TBL_RADGROUPCHECK');


                                $successMsg = "Se ha agregado el perfil: <b> $profile </b>";
                                $logAction .= "Successfully added new profile [$profile] on page: ";
                            }


	}
        include_once('library/config_read.php');
        $log = "visited page: ";


                                   $profile = $_GET['profile'];

                $sql="SELECT Attribute, Value FROM ".$configValues['CONFIG_DB_TBL_RADGROUPREPLY']." WHERE GroupName='".$dbSocket->escapeSimple($profile)."'
                        UNION SELECT Attribute, Value FROM ".$configValues['CONFIG_DB_TBL_RADGROUPCHECK']." WHERE GroupName='".$dbSocket->escapeSimple($profile)."'";
                $res = $dbSocket->query($sql);
                while($row = $res->fetchRow()) {
                    $attributes[$row[0]]=$row[1];
                }

                            include 'library/closedb.php';




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
</script>

<?php include_once ("library/tabber/tab-layout.php"); ?>

<?php include ("menu-mng-rad-profiles.php"); ?>

	<div id="contentnorightbar">

		<h2 id="Intro"><a href="#" onclick="javascript:toggleShowDiv('helpPage')"><?php echo $l['Intro']['mngradprofilesedit.php'] ?>
		<h144>+</h144></a></h2>

		<div id="helpPage" style="display:none;visibility:visible" >
			<?php echo $l['helpPage']['mngnewquick'] ?>
			<br/>
		</div>
		<?php
			include_once('include/management/actionMessages.php');
                                                if(isset($_GET['profile']) && $_GET['profile']!=''){

                        include_once('editProfile.php');
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





