<?php
// require('iframe.php');
require('../common.php');

// Purpose - Due to a small mess up of mine - some donations went into the wrong database. This will get them back into the right database.

// $old = new Sql('Temp_Cfrapp');
// $sql = new Sql('Project_Donut');
$old = new Sql('localhost', 'makeadiff', 'M@k3aDi', "cfrapp");
$sql = new Sql('localhost', 'makeadiff', 'M@k3aDi', "makeadiff_cfrapp");

$donations = $old->getAll("SELECT * FROM donations WHERE created_at>'2014-12-26 12:00:00' AND created_at<'2014-12-27 08:00:00'");

foreach($donations as $d) {
	$donor = $old->getAssoc("SELECT * FROM donours WHERE id=$d[donour_id]");
	print "Donation by $donor[first_name] : $d[donation_amount] ";

	if($donor) {
		$exists = $sql->getAssoc("SELECT id FROM donours WHERE phone_no=$donor[phone_no]");
		if($exists) {
			print "Inserting Donation ... ";
			$sql->insert("donations", array(
					'donation_type'		=> $d['donation_type'],
					'version'			=> $d['version'],
					'fundraiser_id'		=> $d['fundraiser_id'],
					'donour_id'			=> $exists['id'],
					'donation_status'	=> $d['donation_status'],
					'eighty_g_required'	=> $d['eighty_g_required'],
					'product_id'		=> $d['product_id'],
					'donation_amount'	=> $d['donation_amount'],
					'created_at'		=> $d['created_at'],
					'updated_at'		=> $d['updated_at'],
					'updated_by'		=> $d['updated_by'],
				));
		} else {
			print "Inserting Donor ... ";
			$insert_id = $sql->insert("donours", array(
					'first_name'=> $donor['first_name'],
					'last_name'	=> $donor['last_name'],
					'email_id'	=> $donor['email_id'],
					'phone_no'	=> $donor['phone_no'],
					'address'	=> $donor['address'],
					'created_at'=> $donor['created_at'],
					'updated_at'=> $donor['updated_at'],
				));
			print "Inserting Donation ... ";
			$sql->insert("donations", array(
					'donation_type'		=> $d['donation_type'],
					'version'			=> $d['version'],
					'fundraiser_id'		=> $d['fundraiser_id'],
					'donour_id'			=> $insert_id,
					'donation_status'	=> $d['donation_status'],
					'eighty_g_required'	=> $d['eighty_g_required'],
					'product_id'		=> $d['product_id'],
					'donation_amount'	=> $d['donation_amount'],
					'created_at'		=> $d['created_at'],
					'updated_at'		=> $d['updated_at'],
					'updated_by'		=> $d['updated_by'],
				));
		}
		print "Done\n<br />";
	}
}

