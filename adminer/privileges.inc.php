<?php
page_header(lang('Privileges'));

if ($_POST && !$error) {
	if ($_POST["drop"] && is_array($_POST["users"])) {
		$decoded_users = array();
		foreach ($_POST["users"] as $user => $host) {
			$decoded_users[] = q($user) . "@" . q($host);
		}
		
		query_redirect("DROP USER " . implode(", ", $decoded_users), ME . "privileges=", count($decoded_users) > 1 ? lang('Users have been deleted.') : lang('User has been deleted.'));
	}
}



$result = $connection->query("SELECT User, Host FROM mysql." . (DB == "" ? "user" : "db WHERE " . q(DB) . " LIKE Db") . " ORDER BY Host, User");
$grant = $result;
if (!$result) {
	// list logged user, information_schema.USER_PRIVILEGES lists just the current user too
	$result = $connection->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");
}
echo "<form action='' method='post'><p>\n";
hidden_fields_get();
echo "<input type='hidden' name='db' value='" . h(DB) . "'>\n";
echo ($grant ? "" : "<input type='hidden' name='grant' value=''>\n");
echo "<table cellspacing='0'>\n";
echo "<thead><tr><th>" . lang('Username') . "<th>" . lang('Server') . "<th>&nbsp;</thead>\n";
while ($row = $result->fetch_assoc()) {
//	echo '<tr' . odd() . '><td>' . h($row["User"]) . "<td>" . h($row["Host"]) . '<td><a href="' . h(ME . 'user=' . urlencode($row["User"]) . '&host=' . urlencode($row["Host"])) . '">' . lang('Edit') . "</a>\n";
	echo '<tr' . odd() . '><td><input type="checkbox" name="users[' . bracket_escape($row["User"]) . ']" value="' . $row["Host"] . '"><td><a href="' . h(ME . 'user=' . urlencode($row["User"]) . '&host=' . urlencode($row["Host"])) . '">' . h($row["User"]) . '</a><td>' . h($row["Host"]) . "\n";
}
if (!$grant || DB != "") {
	echo "<tr" . odd() . "><td><input name='user'><td><input name='host' value='localhost'><td><input type='submit' value='" . lang('Edit') . "'>\n";
}
echo "</table>\n";

echo '<input type="hidden" name="token" value="' . $token . '">'."\n";
echo "<p>\n";
echo '<input type="submit" name="drop" value=" ' . lang('Drop') . '"' . confirm("formChecked(this, /users/)") . '>'."\n";
echo '</form>'."\n";

echo '<p><a href="' . h(ME) . 'user=">' . lang('Create user') . "</a>";
