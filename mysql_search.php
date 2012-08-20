<?php
/**
 * In an effort to make searching entire databases for specific strings, numbers,
 * and email addresses easier, I have made this script that will pull the list of
 * tables and their respective columns from the database and then search each and
 * every column for the search term, ie,
 *
 *   SELECT column_name
 *   FROM table_name
 *   WHERE column_name LIKE '%search_term%'
 *
 * It will then output the results grouped by the table names.
 *
 * I have added an easier way to add clients as options. Just copy the
 * connection information to the $db_client array and select them from the
 * drop-down menu.
 *
 * @author  Adam Dunson.
 * @version Last Updated on 2012-08-20
 */

set_time_limit(0);

/* MySQL Connection Information */
// Add clients here if you need them as an option
$db_client = array(
  "Example Database" => array(
      "db_server" => "localhost",
      "db_username" => "root",
      "db_password" => "password",
      "db_db" => "example_db"
    )
);

/* MySQL Connection Information */

if(!empty($_GET['client']))
  $client = $_GET['client'];
else if(is_array($db_client)) {
  $client = array_keys($db_client);
  $client = $client[0];
}

$db_server = $db_client[$client]['db_server'];
$db_username = $db_client[$client]['db_username'];
$db_password = $db_client[$client]['db_password'];
$db_db = $db_client[$client]['db_db'];

$db_link = mysql_connect($db_server, $db_username, $db_password);
mysql_select_db($db_db, $db_link);

if(!empty($_GET['search_term'])) {
	$search_term = mysql_real_escape_string(trim($_GET['search_term']));

	$table_sql = "SHOW TABLES";
	$table_result = mysql_query($table_sql, $db_link) or die(mysql_error());
	if(mysql_num_rows($table_result))
	{
		while($table_row = mysql_fetch_assoc($table_result))
			$tables[] = $table_row['Tables_in_'.$db_db];

		foreach($tables as $table)
		{
			$column_sql = "DESC `$table`";
			$column_result = mysql_query($column_sql, $db_link) or die(mysql_error());

			if(mysql_num_rows($column_result))
				while($column_row = mysql_fetch_assoc($column_result))
					$columns[$table][] = $column_row['Field'];
		}
	}
}

header("Content-Type: text/html;charset=utf-8");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>MySQL Database Search Tool</title>
		<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
		<style type="text/css">
		/*<![CDATA[*/
		.highlight { background-color: #ffff00; }
		/*]]>*/
		</style>
	</head>

	<body>
		<h1>MySQL Database Search Tool</h1>
    <h2>
      <form name="changeClientForm" action="db_search.php" method="get">
        <label for="client">Database:</label>
        <select name="client" onchange="this.form.submit()">
          <?php foreach($db_client as $client_option=>$client_array): ?>
          <option value="<?php echo $client_option; ?>"<?php if(strcmp($client_option, $client)==0) echo " selected=\"selected\""; ?>>
            <?php echo $client_option; ?>
          </option>
          <?php endforeach; ?>
        </select>
      </form>
    </h2>
    <?php if(!empty($db_link)): ?><h2>Connected to: <?php echo $client; ?></h2><?php endif; ?>

		<p>This tool will search an entire MySQL database for a string. Be patient.</p>
		<form name="searchForm" action="db_search.php" method="get">
			<input type="hidden" name="client" value="<?php echo htmlentities($client); ?>" />
			<label for="search_term">Search Term</label>
			<?php echo "<input type=\"text\" name=\"search_term\" value=\"$search_term\" />\n"; ?>
			<input type="submit" value="Search" />
		</form>

		<!-- Begin Search -->
<?php
if(!empty($search_term)) {
	$got_one_final = false;
	$c = 0;

	foreach($tables as $table) {
		$print_table = true;
		$got_one = false;

		foreach($columns[$table] as $column) {
			$search_sql = "
				SELECT `$column`
				FROM `$table`
				WHERE `$column` like '%$search_term%'";
			$search_result = mysql_query($search_sql, $db_link) or die(mysql_error());

			if(mysql_num_rows($search_result)) {
				$got_one = true;

				if($print_table) {
					echo "<h3>$table</h3>\n";
					echo "<ul>\n";
					$print_table = false;
				}

				echo "<li>$column\n";
				echo "<ol>\n";

				while($search_row = mysql_fetch_assoc($search_result)) {
					$c++;
					echo "<li>" . preg_replace("/({$search_term})/i", "<span class=\"highlight\">$1</span>", htmlspecialchars($search_row[$column])) . "</li>\n";
				}

				echo "</li>\n";
				echo "</ol>\n";
			}
		}

		if($got_one)
			echo "</ul>\n";
	}

	echo "<p>Found $c results.</p>\n";
}

if($db_link)
	mysql_close($db_link);
?>
		<!-- End Search -->

	</body>
</html>
