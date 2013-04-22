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

        $profile = "";
	$maxallsession = "";
	$expiration = "";
	$sessiontimeout = "";
	$idletimeout = "";
	$ui_changeuserinfo = "0";
	$bi_changeuserbillinfo = "0";

	$logAction = "";
	$logDebugSQL = "";
        //// addedit(VALOR_NUEVO, VALOR_DB, NOMBRE_ATRIBUTO, TABLA)
        function addedit($value,$value_db,$attribute_name,$table){

    if($value!=$value_db){
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
                    $attibute_list[$row[0]]=$row[1];
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

                              addedit($speedown, $attibute_list['WISPr-Bandwidth-Max-Down'],'WISPr-Bandwidth-Max-Down','CONFIG_DB_TBL_RADGROUPREPLY');

                               addedit($downDaily, $attibute_list['CS-Input-Octets-Daily'],'CS-Input-Octets-Daily','CONFIG_DB_TBL_RADGROUPCHECK');

                               addedit($downWeekly, $attibute_list['CS-Input-Octets-Weekly'],'CS-Input-Octets-Weekly','CONFIG_DB_TBL_RADGROUPCHECK');
   
                               addedit($downMontly, $attibute_list['CS-Input-Octets-Monthly'],'CS-Input-Octets-Monthly','CONFIG_DB_TBL_RADGROUPCHECK');

                            addedit($downAll, $attibute_list['CS-Input-Octet'],'CS-Input-Octet','CONFIG_DB_TBL_RADGROUPCHECK');

                            addedit($timeDaily, $attibute_list['Max-Daily-Session'],'Max-Daily-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                            addedit($timeWeekly, $attibute_list['Max-Weekly-Session'],'Max-Weekly-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                             addedit($timeMontly, $attibute_list['Max-Monthly-Session'],'Max-Monthly-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                              addedit($timeAll, $attibute_list['Max-All-Session'],'Max-All-Session','CONFIG_DB_TBL_RADGROUPCHECK');

                                addedit($expiration, $attibute_list['Expiration'],'Expiration','CONFIG_DB_TBL_RADGROUPCHECK');

                                addedit($sessiontimeout, $attibute_list['Session-Timeout'],'Session-Timeout','CONFIG_DB_TBL_RADGROUPREPLY');
  
                            addedit($simultaneoususe, $attibute_list['Simultaneous-Use'],'Simultaneous-Use','CONFIG_DB_TBL_RADGROUPCHECK');


                                $successMsg = "Se ha agregado el perfil: <b> $profile </b>";
                                $logAction .= "Successfully added new profile [$profile] on page: ";
                            }

                            include 'library/closedb.php';
	}
        include_once('library/config_read.php');
        $log = "visited page: ";



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

		<h2 id="Intro"><a href="#" onclick="javascript:toggleShowDiv('helpPage')"><?php echo $l['Intro']['mngradprofilesnew.php'] ?>
		<h144>+</h144></a></h2>

		<div id="helpPage" style="display:none;visibility:visible" >
			<?php echo $l['helpPage']['mngnewquick'] ?>
			<br/>
		</div>
		<?php
			include_once('include/management/actionMessages.php');
		?>

		<form name="newuser" action="mng-rad-profiles-new.php" method="post" >
                <div class="tabber">
                    <div class="tabbertab" title="<?php echo $l['title']['AccountInfo']; ?>">
                        <fieldset>
			<h302> <?php echo $l['title']['AccountInfo']; ?> </h302><br/>
                        <ul>
                            <li class='fieldset'>
                                <label for='profile' class='form'>Nombre del perfil</label>
                                <input name='profile' type='text' id='profile' value='' tabindex=100  />
                                <input type='button' value='Aleatorio' class='button' onclick="javascript:randomAlphanumeric('username',8,<?php echo "'".$configValues['CONFIG_USER_ALLOWEDRANDOMCHARS']."'" ?>)" />
                                <img src='images/icons/comment.png' alt='Tip' border='0' onClick="javascript:toggleShowDiv('usernameTooltip')" />
                            </li>





                            <li class='fieldset'>
                                <br/>
                                <hr><br/>
                                <input type="submit" name="submit" value="<?php echo $l['buttons']['apply']?>"  tabindex=10000 class='button' />
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
                            <input type="submit" name="submit" value="<?php echo $l['buttons']['apply']?>"  tabindex=10000 class='button' />


                        </fieldset>

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





