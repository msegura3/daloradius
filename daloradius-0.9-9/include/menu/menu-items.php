                <div id="header">

                                <!--form action="mng-search.php">
                                <input name="username" value="Search" />
                                </form -->

                                <!-- h1><a href="index.php"> <img src="images/daloradius_small.png" border=0/></a></h1 -->

                                <!-- h2>
                                
                                <? echo $l['all']['copyright1']; ?>
                                
				                                </h2 -->

                                <ul id="nav">
				<a name='top'></a>

	<!--			<li><a href="index.php" <?php echo ($m_active == "Home") ? "class=\"active\"" : ""?>><?php echo $l['menu']['Home']; ?></a></li> -->
				<li><a href="mng-list-all.php" <?php echo ($m_active == "Management") ? "class=\"active\"" : "" ?>><?php echo $l['menu']['Managment']; ?></a></li>
				<li><a href="rep-online.php" <?php echo ($m_active == "Reports") ? "class=\"active\"" : "" ?>><?php echo $l['menu']['Reports']; ?></a></li>
				<li><a href="acct-username.php" <?php echo ($m_active == "Accounting") ? "class=\"active\"" : "" ?>><?php echo $l['menu']['Accounting']; ?></a></li>
				<li><a href="graphs-alltime_logins.php" <?php echo ($m_active == "Graphs") ? "class=\"active\"" : ""?>><?php echo $l['menu']['Graphs']; ?></li>
				<li><a href="config-main.php" <?php echo ($m_active == "Config") ? "class=\"active\"" : ""?>><?php echo $l['menu']['Config']; ?></li>
	

                                </ul>

