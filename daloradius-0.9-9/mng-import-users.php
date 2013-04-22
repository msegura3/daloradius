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

	$logAction = "";
	$logDebugSQL = "";

	isset($_POST['csvdata']) ? $csvdata = $_POST['csvdata'] : $csvdata = "";
	isset($_POST['groups']) ? $groups = $_POST['groups'] : $groups = "";
        $planName = "";
	$userType = "";

	if (isset($_POST['submit'])) {

		$users = array();
		if ( (isset($csvdata)) && (!empty($csvdata)) ) {

			$csvFormattedData = explode("\n", $csvdata);
		
			include 'library/opendb.php';

			// initialize some required variables

			$currDate = date('Y-m-d H:i:s');
			$currBy = $_SESSION['operator_user'];
			
			$passwordType = "Cleartext-Password";
			
			$userCount = 0;
			
			//var_dump($csvFormattedData);
			foreach($csvFormattedData as $csvLine) {
				//list($user, $pass) = explode(",", $csvLine);
				$users = explode(",", $csvLine);

				//makeing sure user and pass are specified and are not empty
				//columns by chance
				if ( (isset($users[0]) && (!empty($users[0])))
						&& 
						((isset($users[1]) && (!empty($users[1])))) )
					{

						$user = $dbSocket->escapeSimple($users[0]);
						$pass = $dbSocket->escapeSimple($users[1]);
						$planName = $dbSocket->escapeSimple($planName);
						$userType = $dbSocket->escapeSimple($userType);
						
						if ($userType == "userType") {
							$passwordType = "Auth-Type";
							$pass = "Accept";
						}
						
						// insert username/password into radcheck
						$sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADCHECK'].
								" (id,Username,Attribute,op,Value) ".
								" VALUES (0, '$user', '$passwordType', ".
								" ':=', '$pass')";
						$res = $dbSocket->query($sql);
						$logDebugSQL .= $sql . "\n";

						// insert user into userinfo table
						$sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_DALOUSERINFO'].
								" (id,username,creationdate,creationby) ".
								" VALUES (0, '$user', '$currDate', '$currBy')";
						$res = $dbSocket->query($sql);
						$logDebugSQL .= $sql . "\n";
						
						// associate user with groups (profiles)
						foreach($groups as $groupName) {
							
							if ( (isset($groupName)) && (!empty($groupName)) ) {
								
								$sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_RADUSERGROUP']." (UserName,GroupName,priority) ".
									" VALUES ('".$dbSocket->escapeSimple($user)."', '".$dbSocket->escapeSimple($groupName)."',0) ";
								$res = $dbSocket->query($sql);
								$logDebugSQL .= $sql . "\n";
								
							}
						}
						
						
						// associate user with plans
						if ( (isset($planName)) && (!empty($planName)) ) {
							$sql = "INSERT INTO ".$configValues['CONFIG_DB_TBL_DALOUSERBILLINFO'].
									" (id,planname,username,creationdate,creationby) ".
									" VALUES (0, '$planName', '$user', '$currDate', '$currBy')";
							$res = $dbSocket->query($sql);
							$logDebugSQL .= $sql . "\n";
						}





						
						$userCount++;

					}
			}
			
			include 'library/closedb.php';

		   $successMsg = "Se han importado con éxito un total de <b>$userCount</b> usuarios al sistema";
		   $logAction .= "Successfully imported a total of <b>$userCount</b> users to database on page: ";
	   
		} else {
			
		   $failureMsg = "Se ingresaron datos CSV";
		   $logAction .= "Fallo al importar los usuario, no se ingreso datos CSV al sistema: ";
		}

	} //if (isset)


	include_once('library/config_read.php');
	$log = "visited page: ";

          include_once ("lang/main.php");

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title><?php echo $l['header']['titles']; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/1.css" type="text/css" media="screen,projection" />
</head>
<script src="library/javascript/pages_common.js" type="text/javascript"></script>
<script src="library/javascript/productive_funcs.js" type="text/javascript"></script>
<script type="text/javascript" src="library/javascript/ajax.js"></script>
<script type="text/javascript" src="library/javascript/ajaxGeneric.js"></script>

<?php

	include ("menu-mng-users.php");
	
?>

	<div id="contentnorightbar">
	
			<h2 id="Intro"><a href="#" onclick="javascript:toggleShowDiv('helpPage')"><?php echo $l['Intro']['mngimportusers.php'] ?>
			<h144>+</h144></a></h2>
			
			<div id="helpPage" style="display:none;visibility:visible" >
				<?php echo $l['helpPage']['mngimportusers'] ?>
				<br/>
			</div>
			<?php
				include_once('include/management/actionMessages.php');
			?>

			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

	<fieldset>

		<h302> <?php echo $l['title']['ImportUsers']; ?> </h302>
		<br/>

		<ul>
                    Pegue los datos de entrada de los usuarios en formato CSV, el formato es: usuario,password<br/>
                    Nota: Solo se conciderarán dos campos por usuario, el resto será ignorado.
		<br/>
		
	
		<li class='fieldset'>
		<label for='group' class='form'><?php echo $l['all']['Group']?></label>
		<?php   
			include_once 'include/management/populate_selectbox.php';
			populate_groups("Elija Grupos","groups[]");
		?>

		<a class='tablenovisit' href='#'
			onClick="javascript:ajaxGeneric('include/management/dynamic_groups.php','getGroups','divContainerGroups',genericCounter('divCounter')+'&elemName=groups[]');">Agregar</a>
		<img src='images/icons/comment.png' alt='Tip' border='0' onClick="javascript:toggleShowDiv('group')" />
		<div id='divContainerGroups'>
		</div>






		
		<li class='fieldset'>
		<label for='csvdata' class='form'><?php echo $l['all']['CSVData'] ?></label>
		<textarea class='form_fileimport' name='csvdata' tabindex=101></textarea>
		</li>

		
		<li class='fieldset'>
		<br/>
		<hr><br/>
		<input type='submit' name='submit' value='<?php echo $l['buttons']['apply'] ?>' tabindex=10000 class='button' />
		</li>

		</ul>
	</fieldset>

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





