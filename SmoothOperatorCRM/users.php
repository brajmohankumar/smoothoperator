<?
$rounded[] = "div.thin_700px_box";
require "header.php";

if (isset($_GET['delete_sure'])) {
    ?><div class="thin_700px_box"><?
    $id = sanitize($_GET['delete_sure']);
    $result = mysqli_query($connection, "DELETE FROM users WHERE id = $id");
    if (!$result) {
        $messages[] = "There was a problem deleting this user: ".mysqli_error();
        $_SESSION['messages'] = $messages;
    }
    draw_progress("Please wait we are saving your changes...");
    redirect("users.php", 0);
    ?></div><?
    require "footer.php";
    exit(0);
}
if (isset($_GET['delete'])) {
    ?><div class="thin_700px_box"><?
    ?>
    Are you sure you would like to delete this user?<br />
    <br />
    <?
    $id = sanitize($_GET['delete']);
    $result = mysqli_query($connection, "SELECT * FROM users WHERE id = $id");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "<b>Name: </b>".$row['first_name']." ".$row['last_name']."<br />";
        echo "<b>UserName: </b>".$row['username']."<br />";
    }
    ?>
    <br />
    <a href="users.php?delete_sure=<?=$_GET['delete']?>">Yes, delete them</a><br />
    <a href="users.php">No, do not delete them</a><br />
    </div><?
    require "footer.php";
    exit(0);
}
if (isset($_GET[save])) {
    ?><div class="thin_700px_box"><?
    $id = sanitize($_POST[id]);
    $sql = "UPDATE users SET ";
    foreach ($_POST as $field=>$value) {
        $field = sanitize($field, false);
        $value = sanitize($value);
        $sql.= "$field = $value, ";
    }
    $sql = substr($sql, 0, strlen($sql)-2);
    $sql.= " WHERE id=$id";
    $result = mysqli_query($connection, $sql);
    if (!$result) {
        $messages[] = "There was a problem saving your changes: ".mysqli_error();
        $_SESSION['messages'] = $messages;
    }
    draw_progress("Please wait we are saving your changes...");
    redirect("users.php", 0);
    ?></div><?
    require "footer.php";
    exit(0);
}
if (isset($_GET[save_new])) {
    ?><div class="thin_700px_box"><?
    $sql1 = "INSERT INTO users (";
    $sql2 = ") VALUES (";
    foreach ($_POST as $field=>$value) {
        $field = sanitize($field, false);
        if ($field == "password") {
            $value = sanitize(sha1($value));
        } else {
            $value = sanitize($value);
        }
        $sql1.= "$field, ";
        $sql2.= "$value, ";
    }
    $sql = substr($sql1,0,strlen($sql1)-2).substr($sql2,0,strlen($sql2)-2).")";
    $result = mysqli_query($connection, $sql);
    if (!$result) {
        $messages[] = "There was a problem saving your changes: ".mysqli_error();
        $_SESSION['messages'] = $messages;
    }
    draw_progress("Please wait we are saving your changes...");
    redirect("users.php", 0);
    ?></div><?
    require "footer.php";
    exit(0);
}
if (isset($_GET[save_password])) {
    ?><div class="thin_700px_box"><?
    $id = sanitize($_POST[id]);
    if ($_POST['new_password'] != $_POST['new_password_repeat']) {
        $message[] = "Your Passwords Do Not Match";
        $_SESSION['messages'] = $message;
        redirect("users.php?change_password=".$_POST[id]);
        ?></div><?
        exit(0);
    }
    $new_password = sanitize(sha1($_POST['new_password']));
    $sql = "UPDATE users SET password = $new_password WHERE id = $id";
    draw_progress("Please wait we are saving your changes...");
    $result = mysqli_query($connection, $sql);
    redirect("users.php", 0);
    ?></div><?
    require "footer.php";
    exit(0);
}
if (isset($_GET[change_password])) {
    ?><div class="thin_700px_box"><?
    ?>
<form action="users.php?save_password=1" method="post">
    <input type="hidden" name="id" value="<?=$_GET['change_password']?>">
    New Password: <input type="password" name="new_password"><br />
    New Password again: <input type="password" name="new_password_repeat"><br />
    <input type="submit" value="Save Changes">
</form>

</div><?
    exit(0);
}

if (isset($_GET['new'])) {
    ?><div class="thin_700px_box"><?
    function display_user_new($row) {
        $fields_to_hide[] = "";
        $fields_to_ignore[] = "id";
        $textarea_fields[] = "";
        $select_fields[] = "security_level";
        $select_values['security_level'][] = "0";
        $select_names['security_level'][] = "No Access";
        $select_values['security_level'][] = "1";
        $select_names['security_level'][] = "Normal User";
        $select_values['security_level'][] = "10";
        $select_names['security_level'][] = "Administrator";
        $select_values['security_level'][] = "100";
        $select_names['security_level'][] = "Super User";
        echo '<form action="users.php?save_new=1" method="post">';
        echo "<table>";
        foreach ($row as $field=>$value) {
            if (in_array($field, $select_fields)) {
                echo '<tr><td>'.clean_field_name($field).'</td><td><select name="'.$field.'">';
                for ($i = 0;$i<sizeof($select_names[$field]);$i++) {
                    echo '<option value="'.$select_values[$field][$i].'">'.$select_names[$field][$i].'</option>';
                }
                echo "</select></td></tr>";
            } else if (in_array($field, $fields_to_ignore)) {

            } else if (in_array($field, $fields_to_hide)) {
                echo '<input type="hidden" name="'.$field.'" value="'.$value.'">';
            } else if (in_array($field, $textarea_fields)) {
                echo '<tr><td colspan="2">'.clean_field_name($field).'</td></tr>';
                echo '<tr><td colspan="2"><textarea cols="60" rows="10" name="'.$field.'"></textarea></td></tr>';
            } else {
                echo '<tr><td>'.clean_field_name($field).'</td><td><input type="text" name="'.$field.'" value=""></td></tr>';
            }
        }
        echo '<tr><td colspan="2"><input type="submit" value="save changes"></td></tr>';
        echo '</form>';
        echo "</table>";
    }
    $id = sanitize($_GET[edit]);
    $result = mysqli_query($connection, "SELECT * FROM users LIMIT 1");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        //print_pre($row);
        display_user_new($row);
    }
    ?></div><?
    require "footer.php";
    exit(0);
}


if (isset($_GET[edit])) {
    ?><div class="thin_700px_box"><?
    function display_user_edit($row) {
        $fields_to_hide[] = "id";
        $fields_to_ignore[] = "password";
        $textarea_fields[] = "";
        $select_fields[] = "security_level";
        $select_values['security_level'][] = "0";
        $select_names['security_level'][] = "No Access";
        $select_values['security_level'][] = "1";
        $select_names['security_level'][] = "Normal User";
        $select_values['security_level'][] = "10";
        $select_names['security_level'][] = "Administrator";
        $select_values['security_level'][] = "100";
        $select_names['security_level'][] = "Super User";
        echo '<form action="users.php?save=1" method="post">';
        echo "<table>";
        foreach ($row as $field=>$value) {
            if (in_array($field, $select_fields)) {
                echo '<tr><td>'.clean_field_name($field).'</td><td><select name="'.$field.'">';
                for ($i = 0;$i<sizeof($select_names[$field]);$i++) {
                    if ($value == $select_values[$field][$i]) {
                        $selected = " selected";
                    } else {
                        $selected = "";
                    }
                    echo '<option value="'.$select_values[$field][$i].'" '.$selected.'>'.$select_names[$field][$i].'</option>';
                }
                echo "</select></td></tr>";
            } else if (in_array($field, $fields_to_ignore)) {
                
            } else if (in_array($field, $fields_to_hide)) {
                echo '<input type="hidden" name="'.$field.'" value="'.$value.'">';
            } else if (in_array($field, $textarea_fields)) {
                echo '<tr><td colspan="2">'.clean_field_name($field).'</td></tr>';
                echo '<tr><td colspan="2"><textarea cols="60" rows="10" name="'.$field.'">'.$value.'</textarea></td></tr>';
            } else {
                echo '<tr><td>'.clean_field_name($field).'</td><td><input type="text" name="'.$field.'" value="'.$value.'"></td></tr>';
            }
        }
        echo '<tr><td colspan="2"><input type="submit" value="save changes"></td></tr>';
        echo '</form>';
        echo "</table>";
    }
    $id = sanitize($_GET[edit]);
    $result = mysqli_query($connection, "SELECT * FROM users WHERE id = $id LIMIT 1");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        //print_pre($row);
        display_user_edit($row);
    }
    ?></div><?
    require "footer.php";
    exit(0);
}
?>
<div class="thin_700px_box">
    <center>
        <a href="users.php?new=1"><img src="images/user.png">&nbsp;Add User</a>&nbsp;
    </center>
</div>

<div class="thin_700px_box">
<table class="sample2" width="100%">
    <tr>
        <th>Username</th>
        <th>Password</th>
        <th>Name</th>
        <th>Extension</th>
        <th>Security Level</th>
        <th>Delete</th>
    </tr>
    <?
    $result = @mysqli_query($connection, "SELECT * FROM users");
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td><a href="users.php?edit='.$row[id].'">'.$row[username].'&nbsp;<img src="images/pencil.png"></a></td>';
        echo '<td><a href="users.php?change_password='.$row[id].'">Change Password</a></td>';
        echo "<td>$row[first_name] $row[last_name]</td>";
        echo "<td>$row[extension]</td>";
        echo "<td>";
        switch ($row[security_level]) {
            case 100:
                echo "Super User";
                break;
            case 10:
                echo "Administrator";
                break;
            case 1:
                echo "Normal User";
                break;
            default:
                echo "No Access";
                break;

        }
        echo "</td>";

        echo '<td>';
        ?><a href="#" onclick="show_confirm('Are you really sure you want to delete <b><?=$row[username]?></b>?', 'Delete', 'users.php?delete_sure=<?=$row[id]?>');return false;"><?
        //echo '<a href="users.php?delete='.$row[id].'">';
        echo '<img src="images/delete.png"></td>';
        echo '</tr>';
    }
    ?>
</table>
</div>
<?
require "footer.php";
?>
