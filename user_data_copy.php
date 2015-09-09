<?php
//require('iframe.php');
require('../common.php');

/// Use to copy over all the users in Madapp User table to Donut User database table.

$madapp = new Sql("Project_Madapp");
$donut = new Sql("Project_Donut");
// $madapp = new Sql('localhost', 'makeadiff', 'M@k3aDi', "makeadiff_madapp");
// $donut = new Sql('localhost', 'makeadiff', 'M@k3aDi', "makeadiff_cfrapp");

$city_transilation = array(
		// Madapp City ID 		=> Donut City ID
		'26'	=> '25',
		'24'	=> '13',
		'1'		=> '44',
		'21'	=> '12',
		'13'	=> '21',
		'6'		=> '14',
		'10'	=> '3',
		'16'	=> '19',
		'25'	=> '24',
		'12'	=> '20',
		'23'	=> '18',
		'19'	=> '23',
		'11'	=> '17',
		'14'	=> '11',
		'20'	=> '22',
		'2'		=> '4',
		'4'		=> '9',
		'22'	=> '5',
		'15'	=> '8',
		'5'		=> '10',
		'3'		=> '15',
		'8'		=> '6',
		'18'	=> '16',
		'17'	=> '7',
		'29'	=> '25',
		'30'	=> '25',
		'31'	=> '25',
		'32'	=> '25',
	);

$selected_users = '';
if(isset($QUERY['action'])) {
	$all_user_ids = array_keys($QUERY['user_id']);
	$selected_users = ' AND id IN (' . implode(',', $all_user_ids) . ')';
}
$m_users = $madapp->getAll("SELECT * FROM User WHERE status='1' AND user_type='volunteer' $selected_users");
$d_users = $donut->getAll("SELECT * FROM users WHERE is_deleted='0'");

$d_phones = array();
foreach ($d_users as $u) {
	$d_phones[$u['phone_no']] = $u['id'];
	$d_madapped[$u['madapp_user_id']] = $u['id'];
}
$total = count($m_users);

$count = 0;
foreach($m_users as $u) {
	$count++;
	//print "$count/$total) ";
	$small_phone_number = ltrim($u['phone'], '0');
	if(isset($d_phones[$u['phone']]) 
			or isset($d_phones[$small_phone_number])) {

		$donut_user_id = isset($d_phones[$u['phone']]) ? $d_phones[$u['phone']] : $d_phones[$small_phone_number];
		print "Found: " . $u['name'] . " : " . $donut_user_id . "<br />\n";

		if(!isset($d_madapped[$u['id']])) 
			$donut->execQuery("UPDATE users SET madapp_user_id='$u[id]' WHERE id='$donut_user_id'");
	} else {
		// dump($u);
		// dump($d_phones[$u['phone']]);
		// exit;


		print "Adding user $u[name] - ";
		$insert_id = 0;
		// $insert_id = $donut->insert("users", array(
		// 	'encrypted_password'=> '$2a$10$ZMf.qdZnLG3Iy.8d/4NFIeUEpbwZszgKYEU5Yua8upmb92tOQx2H.',
		// 	'email'				=> $u['email'],
		// 	'created_at'		=> 'NOW()',
		// 	'updated_at'		=> 'NOW()',
		// 	'address'			=> $u['address'],
		// 	'first_name'		=> $u['name'],
		// 	'phone_no'			=> ltrim($u['phone'], '0'),
		// 	'city_id'			=> $city_transilation[$u['city_id']],
		// 	'madapp_user_id'	=> $u['id'],
		// 	'is_deleted'		=> '0',
		// ));
		$d_phones[ltrim($u['phone'], '0')] = $insert_id; // Make sure it won't be inserted again.

		// $donut->insert("user_role_maps", array(
		// 		'role_id'	=> 10, //Volunteer
		// 		'user_id'	=> $insert_id,
		// 		'created_at'=> 'NOW()',
		// 		'updated_at'=> 'NOW()',
		// 	));
		print "Done($insert_id)<br />\n";
	}
}

print "All done";