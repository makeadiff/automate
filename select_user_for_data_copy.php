<?php
//require('iframe.php');
require('../common.php');

/// Use to copy over selected users in Madapp User table to Donut User database table.

$madapp = new Sql("Project_Madapp");
$donut = new Sql("Project_Donut");
// $madapp = new Sql('localhost', 'makeadiff', 'M@k3aDi', "makeadiff_madapp");
// $donut = new Sql('localhost', 'makeadiff', 'M@k3aDi', "makeadiff_cfrapp");

showTop('Select Users to Import');

$city_id = i($QUERY,'city_id',0);
$all_cities = $madapp->getById("SELECT id,name FROM City WHERE id<26 ORDER BY name");
$all_cities[0] = 'Any';
$user_groups = i($QUERY, 'user_groups', array());
$all_user_groups = $madapp->getById("SELECT id,name FROM `Group` WHERE type='fellow' OR type='volunteer' AND group_type='normal'");

$all_users = array();
if(i($QUERY,'action') == 'Search') {
	$city_check = '';
	if($city_id) $city_check = 'AND city_id='.$city_id;
	$group_check = '';
	$group_check_join = '';
	if($user_groups) {
		$group_check_options = array();
		$group_check_join = ' INNER JOIN UserGroup ON UserGroup.user_id=User.id ';
		foreach ($user_groups as $group_id) {
			$group_check_options[] = "UserGroup.group_id=$group_id";
		}
		$group_check = ' AND (' . implode(' OR ', $group_check_options) . ')';
	}

	$all_users = $madapp->getById("SELECT User.id,User.name FROM User $group_check_join WHERE User.status='1' AND user_type='volunteer' $city_check $group_check");
}
?>

<form action="" method="post" class="form-area">
<label for="city_id">City</label>
<select name="city_id" id="city_id">
<option value="0">Any City</option>
<?php
foreach($all_cities as $this_city_id => $this_city_name) { ?>
<option value="<?php echo $this_city_id; ?>" <?php 
	if(!empty($city_id) and $city_id == $this_city_id) echo 'selected="selected"';
?>><?php echo $this_city_name; ?></option>
<?php } ?>
</select><br />

<label for="user_groups">Groups</label>

<select name="user_groups[]" id="user_groups" style="width:150px; height:100px;" multiple>
<?php
foreach($all_user_groups as $id => $gname) { ?>
<option value="<?php echo $id; ?>"<?php 
	if(in_array($id, $user_groups)) echo 'selected="selected"';
?>><?php echo $gname; ?></option>
<?php } ?>
</select><br />
<input type="submit" name="action" value="Search" />

</form><br />

<?php if($all_users) { ?>
<form action="user_data_copy.php" method="post">
<?php foreach($all_users as $user_id => $name) { ?>
<input type="checkbox" value="1" name="user_id[<?php echo $user_id ?>]" id="user_<?php echo $user_id ?>" /> <label for="user_<?php echo $user_id ?>"><?php echo $name ?></label><br />
<?php } ?>
<input type="submit" name="action" value="Import" />
</form>
<?php
}

showEnd();