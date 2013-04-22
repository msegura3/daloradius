
                                <ul id="subnav">

					<li><a href="config-main.php"><em>G</em>eneral</a></li>
					<li><a href="config-maint.php"><em>M</em>aintenance</a></li>
					<li><a href="config-operators.php"><em>O</em>perators</a></li>
					<li><a href="config-backup.php"><em>B</em>ackup</a></li>
					
					<div id="logindiv" style="text-align: right;">
                                                <li>Ubicación: <b><?php echo $_SESSION['location_name'] ?></b></li><br/>
						<li>Bienvenido, <?php echo $operator; ?></li>
						<li><a href="logout.php">[Salir]</a></li>

                                </ul>
								
                </div>

