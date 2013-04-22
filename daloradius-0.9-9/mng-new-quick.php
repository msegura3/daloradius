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

	if (isset($_POST['submit'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$groups = $_POST['groups'];

                $speedown=$_POST[speedown]*$_POST[speedownFactor];


		$expiration = $_POST['expiration'];
		$sessiontimeout = $_POST['sessiontimeout'];
                $sessiontimeoutFactor=$_POST['sessiontimeoutFactor'];
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

                include 'library/opendb.php';
		
		$sql = "SELECT * FROM ".$configValues['CONFIG_DB_TBL_RADCHECK']." WHERE UserName='$username'";
		$res = $dbSocket->query($sql);
		$logDebugSQL .= $sql . "\n";

		if ($res->numRows() != 0) {
                    $failureMsg = "¡El usuario: <b> $username </b> ya existe!";
                    $logAction .= "Failed adding new user already existing in database [$username] on page: ";
                }
                elseif (trim($username) == "" or trim($password) == "") {
                    $failureMsg = "El password o el usuario no pueden quedar vacios";
                    $logAction .= "Failed adding (possible empty user/pass) new user [$username] on page: ";
                }
                    else{

                            $dbPassword = $dbSocket->escapeSimple($password);

                            // insert username/password
                            $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK'].
                                            " (id,Username,Attribute,op,Value) ".
                                            " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Cleartext-Password', ".
                                            " ':=', '$dbPassword')";

                            $res = $dbSocket->query($sql);
                            $logDebugSQL .= $sql . "\n";

                             //*******DURACION DE LA SESION****//
                            if ($speedown) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADREPLY']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'WISPr-Bandwidth-Max-Down', ':=', '".
                                                    $dbSocket->escapeSimple($speedown)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }




                            //****** TRAFICO DIARIO*******//--------------------CONTADOR!!
                            if ($downDaily) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'CS-Input-Octets-Daily', ':=', '".
                                                    $dbSocket->escapeSimple($downDaily)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //****** TRAFICO SEMANAL*******//--------------------CONTADOR!!
                            if ($downWeekly) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'CS-Input-Octets-Weekly', ':=', '".
                                                    $dbSocket->escapeSimple($downWeekly)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //****** TRAFICO MENSUAL*******//--------------------CONTADOR!!
                            if ($downMontly) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'CS-Input-Octets-Monthly', ':=', '".
                                                    $dbSocket->escapeSimple($downMontly)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //****** TRAFICO TOTAL*******//
                            if ($downAll) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'CS-Input-Octets', ':=', '".
                                                    $dbSocket->escapeSimple($downAll)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }


                            //****** TIEMPO DIARIO*******//--------------------CONTADOR!!
                            if ($timeDaily) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Max-Daily-Session', ':=', '".
                                                    $dbSocket->escapeSimple($timeDaily)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //****** TIEMPO SEMANAL*******//--------------------CONTADOR!!
                            if ($timeWeekly) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Max-Weekly-Session', ':=', '".
                                                    $dbSocket->escapeSimple($timeWeekly)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //****** TIEMPO MENSUAL*******//--------------------CONTADOR!!
                            if ($timeMontly) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Max-Monthly-Session', ':=', '".
                                                    $dbSocket->escapeSimple($timeMontly)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //****** TIEMPO TOTAL*******//
                            if ($timeAll) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Max-All-Session', ':=', '".
                                                    $dbSocket->escapeSimple($timeAll)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //********FECHA EXPIRACION****//
                            if ($expiration) {

                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Expiration', ':=', '".
                                                    $dbSocket->escapeSimple($expiration)."')";
                                    $res = $dbSocket->query($sql);   //2001-12-18T19:00:00+00:00    YYYY-MM-DDThh:mm:ssTZD
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //*******DURACION DE LA SESION****//
                            if ($sessiontimeout) {
                                $sessiontimeout = $sessiontimeout*$sessiontimeoutFactor;
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADREPLY']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Session-Timeout', ':=', '".
                                                    $dbSocket->escapeSimple($sessiontimeout)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

                            //******TIEMPO INACTIVIDAD**** 5 MIN//

                            $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADREPLY']." (id,Username,Attribute,op,Value) ".
                                            " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Idle-Timeout', ':=', '300')";
                            $res = $dbSocket->query($sql);
                            $logDebugSQL .= $sql . "\n";

                            //******USO SIMULTANEO****************//
                            if ($simultaneoususe) {
                                    $sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK']." (id,Username,Attribute,op,Value) ".
                                                    " VALUES (0, '".$dbSocket->escapeSimple($username)."', 'Simultaneous-Use', ':=', '".
                                                    $dbSocket->escapeSimple($simultaneoususe)."')";
                                    $res = $dbSocket->query($sql);
                                    $logDebugSQL .= $sql . "\n";
                            }

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

                                $successMsg = "Se ha agregado al usario: <b> $username </b>";
                                $logAction .= "Successfully added new user [$username] on page: ";
                            }

                            include 'library/closedb.php';
	}
        include_once('library/config_read.php');
        $log = "visited page: ";

	
	if ($configValues['CONFIG_IFACE_PASSWORD_HIDDEN'] == "yes")
		$hiddenPassword = "type=\"password\"";

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

<?php include ("menu-mng-users.php"); ?>

	<div id="contentnorightbar">

		<h2 id="Intro"><a href="#" onclick="javascript:toggleShowDiv('helpPage')"><?php echo $l['Intro']['mngnewquick.php'] ?>
		<h144>+</h144></a></h2>

		<div id="helpPage" style="display:none;visibility:visible" >
			<?php echo $l['helpPage']['mngnewquick'] ?>
			<br/>
		</div>
		<?php
			include_once('include/management/actionMessages.php');
		?>
		
		<form name="newuser" action="mng-new-quick.php" method="post" >
                <div class="tabber">
                    <div class="tabbertab" title="<?php echo $l['title']['AccountInfo']; ?>">
                        <fieldset>
			<h302> <?php echo $l['title']['AccountInfo']; ?> </h302><br/>		
                        <ul>
                            <li class='fieldset'>
                                <label for='username' class='form'><?php echo $l['all']['Username']?></label>
                                <input name='username' type='text' id='username' value='' tabindex=100  />
                                <input type='button' value='Aleatorio' class='button' onclick="javascript:randomAlphanumeric('username',8,<?php echo "'".$configValues['CONFIG_USER_ALLOWEDRANDOMCHARS']."'" ?>)" />
                                <img src='images/icons/comment.png' alt='Tip' border='0' onClick="javascript:toggleShowDiv('usernameTooltip')" />
                                <div id='usernameTooltip'  style='display:none;visibility:visible' class='ToolTip'>
                                    <img src='images/icons/comment.png' alt='Tip' border='0' />
                                    <?php echo $l['Tooltip']['usernameTooltip'] ?>
                                </div>
                            </li>

                            <li class='fieldset'>
                                <label for='password' class='form'><?php echo $l['all']['Password']?></label>
                                <input name='password' type='text' id='password' value='' <?php if (isset($hiddenPassword)) echo $hiddenPassword ?> tabindex=101 />
                                <input type='button' value='Aleatorio' class='button' onclick="javascript:randomAlphanumeric('password',8,<?php echo "'".$configValues['CONFIG_USER_ALLOWEDRANDOMCHARS']."'" ?>)" />
                                <img src='images/icons/comment.png' alt='Tip' border='0' onClick="javascript:toggleShowDiv('passwordTooltip')" />
                                <div id='passwordTooltip'  style='display:none;visibility:visible' class='ToolTip'>
                                    <img src='images/icons/comment.png' alt='Tip' border='0' />
                                    <?php echo $l['Tooltip']['passwordTooltip'] ?>
                                </div>
                            </li>

                            <input type="hidden" name='passwordType' value='Cleartext-Password'/>
                            
                            <li class='fieldset'>
                                <label for='group' class='form'><?php echo $l['all']['Group']?></label>
                                <?php
                                    include_once 'include/management/populate_selectbox.php';
                                    populate_groups("Elegir perfiles","groups[]");
                                ?>
                                <a class='tablenovisit' href='#'onClick="javascript:ajaxGeneric('include/management/dynamic_groups.php','getGroups','divContainerGroups',genericCounter('divCounter')+'&elemName=groups[]');">Agregar</a>
                                <img src='images/icons/comment.png' alt='Tip' border='0' onClick="javascript:toggleShowDiv('group')" />
                                <div id='divContainerGroups'>
                                </div>

                                <div id='groupTooltip'  style='display:none;visibility:visible' class='ToolTip'>
                                    <img src='images/icons/comment.png' alt='Tip' border='0' />
                                    <?php echo $l['Tooltip']['groupTooltip'] ?>
                                </div>
                            </li>

                            <li class='fieldset'>
                                <br/>
                                <hr><br/>
                                <input type="submit" name="submit" value="<?php echo $l['buttons']['apply']?>" onclick = "javascript:small_window(document.newuser.username.value, document.newuser.password.value, document.newuser.maxallsession.value);" tabindex=10000 class='button' />
                            </li>
                        </ul>
                        </fieldset>
                        <br/>

                        <fieldset>
                            <h302> <?php echo $l['title']['Attributes']; ?> </h302>
                            <br/>
                            
                            <label for='speedown' class='form'><?php echo "Velocidad Descarga";?></label>
                            <input value='' id='speedown' name='speedown'  tabindex=112 />
                            <select  id="speedownFactor" name ="speedownFactor" class='form' >
                                <option value="1000" selected >Kbps</option>
                                <option value="1000000"   >Mbps</option>
                            </select></br>
                           
                            <label for='sessiontimeout' class='form'><?php echo $l['all']['SessionTimeout']?></label>
                            <input value='' id='sessiontimeout' name='sessiontimeout'  tabindex=109 />
                            <select  id="sessiontimeoutFactor" name ="sessiontimeoutFactor" class='form' >
                                <option value="60">Minutos</option>
                                <option value="3600" selected="selected"  >Horas</option>
                                <option value="86400">Días</option>
                            </select>
                            <br/>

                            <label for='expiration' class='form'><?php echo $l['all']['Expiration']?></label>
                            <input value='' id='expiration' name='expiration'  tabindex=108 readonly />
                            <img src="library/js_date/calendar.gif" onclick="showChooser(this, 'expiration', 'chooserSpan', 2010, 2015, 'd M Y', false);">
                            <br/>

                            <label for='simultaneoususe' class='form'><?php echo $l['all']['SimultaneousUse']?></label>
                            <select  id="simultaneoususe1" name ="simultaneoususe1" class='form' onchange="showinput(this.value)">
                                <option value="personal" selected >No</option>
                                <option value="public"   >Sí</option>
                                <option value="custom" >Indicar número</option>
                            </select>
                            <input id='simultaneoususe' name='simultaneoususe' onblur="numbercheck(this)"style="display:none;width:20px;" type='text' value='1' tabindex=106 maxlength='2' />
                            <br/>
                            <!-- framedipaddress-->
                            <!-- idletimeout-->
                            <br/>
                        </fieldset>
                    </div>

                     <div id="chooserSpan" class="dateChooser select-free" style="display: none; visibility: hidden; width: 160px;"></div>
                    
                    <div class="tabbertab" title="<?php echo "Cuotas"; ?>">
                        <fieldset>
                            <h302> Cuotas de Descarga</h302>
                            <br/>
                            <label for='downDaily' class='form'><?php echo "Diaria";?></label>
                            <input value='' id='downDaily' name='downDaily'  tabindex=112 />
                            <select  id="downDailyFactor" name ="downDailyFactor" class='form' >
                                <option value="1048576" selected >Megabytes</option>
                                <option value="1073741824"   >Gigabytes</option>
                            </select></br>
                            <label for='downWeekly' class='form'><?php echo "Semanal";?></label>
                            <input value='' id='downWeekly' name='downWeekly'  tabindex=112 />
                            <select  id="downWeeklyFactor" name ="downWeeklyFactor" class='form' >
                                <option value="1048576" selected >Megabytes</option>
                                <option value="1073741824"   >Gigabytes</option>
                            </select></br>

                            <label for='downMontly' class='form'><?php echo "Mensual";?></label>
                            <input value='' id='downMontly' name='downMontly'  tabindex=112 />
                            <select  id="downMontlyFactor" name ="downMontlyFactor" class='form' >
                                <option value="1048576" selected >Megabytes</option>
                                <option value="1073741824"   >Gigabytes</option>
                            </select></br>

                            <label for='downAll' class='form'><?php echo "Total";?></label>
                            <input value='' id='downAll' name='downAll'  tabindex=112 />
                            <select  id="downAllFactor" name ="downAllFactor" class='form' >
                                <option value="1048576" selected >Megabytes</option>
                                <option value="1073741824"   >Gigabytes</option>
                            </select></br>

                        </fieldset>
                       
                        <fieldset>
                            <h302> Cuotas de tiempo</h302><br/>

                            <label for='timeDaily' class='form'><?php echo "Diaria";?></label>
                            <input value='' id='timeDaily' name='timeDaily'  tabindex=112 />
                            <select  id="timeDailyFactor" name ="timeDailyFactor" class='form' >
                                <option value="60">Minutos</option>
                                <option value="3600" selected="selected"  >Horas</option>
                            </select></br>
                            
                            <label for='timeWeekly' class='form'><?php echo "Semanal";?></label>
                            <input value='' id='timeWeekly' name='timeWeekly'  tabindex=112 />
                            <select  id="timeWeeklyFactor" name ="timeWeeklyFactor" class='form' >
                                <option value="60">Minutos</option>
                                <option value="3600" selected="selected"  >Horas</option>
                                <option value="86400">Días</option>
                            </select></br>

                            <label for='timeMotly' class='form'><?php echo "Mensual";?></label>
                            <input value='' id='timeMontly' name='timeMontly'  tabindex=112 />
                            <select  id="timeMontlyFactor" name ="timeMontlyFactor" class='form' >
                                <option value="60">Minutos</option>
                                <option value="3600" selected="selected"  >Horas</option>
                                <option value="86400">Días</option>
                            </select></br>


                            <label for='timeAll' class='form'><?php echo $l['all']['MaxAllSession'] ?></label>
                            <input value='' id='timeAll' name='timeAll'  tabindex=111 />
                            <select  id="timeAllFactor" name="timeAllFactor" class='form' >
                                <option value="60">Minutos</option>
                                <option value="3600" selected="selected"  >Horas</option>
                                <option value="86400">Días</option>
                                <option value="604800">Semanas</option>
                            </select>
                            <br/>

                            <br/>
                            <hr><br/>
                            <input type="submit" name="submit" value="<?php echo $l['buttons']['apply']?>" onclick = "javascript:small_window(document.newuser.username.value, document.newuser.password.value, document.newuser.maxallsession.value);" tabindex=10000 class='button' />
                            
                         
                        </fieldset>

                    </div>
                     <div class="tabbertab" title="<?php echo $l['title']['UserInfo']; ?>">
                        <?php
                        $customApplyButton = "<input type=\"submit\" name=\"submit\" value=\"".$l['buttons']['apply']."\"
		                        onclick = \"javascript:small_window(document.newuser.username.value,
		                        document.newuser.password.value, document.newuser.maxallsession.value);\" tabindex=10000
		                        class='button' />";
                        include_once('include/management/userinfo.php');
                        ?>
                     </div>
                     
                     <!-- BillingInfo -->
                </div>
                </form>
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





