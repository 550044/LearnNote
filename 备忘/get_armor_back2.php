<?php

// to get armor back (ver 2.2)
// last edit: 2019-06-13

require_once("../rust/rust.php");

// php file name partner serial_id [0]
// set 0 to close dump2 (from armor backup)

if ($argc < 4)
{
	echo "argc < 4\n";
	echo "para error\n";
	exit;
}

$do_dump2 = false;
if ($argc == 4)
	$do_dump2 = true;

$name = $argv[1];
$partner = $argv[2];
$serial_id = $argv[3];

$uid = rust_get_uid($name, $partner, $serial_id);
$server = rust_get_server_id($partner, $serial_id);

echo "server_id = $server\n\n";
echo "uid = $uid\n\n";

if ($uid <= 3000 || $server == 0)
{
	echo "para error\n";
	exit;
}

$backup_dir = "/data2/backup/armor_temp/";
if ($server == 50768 || $server == 62990)	//创世1 & 王者53
	$backup_dir = "~/scripts/rus/_armor_temp/";

$db = "rxsg2_s".$server."_20190322";
if ($server == 50768)		// 创世1
	$db = "rxsg2_s".$server."_20190317";
else if ($server == 343983)	// 77313 创世1
	$db = "rxsg2_s".$server."_20190321";
else if ($server == 377821)	// 91wan 创世1
	$db = "rxsg2_s".$server."_20190321";

$out_file = $uid."_armor.sql";
@unlink($out_file);
@unlink("temp.sql");

$input1 = "mysql -urxsg2 -prxsg2 test -e \"source ".$backup_dir."s".$server."_mem_armor_temporary.sql\"";
$alter1 = "mysql -urxsg2 -prxsg2 test -e \"source alter_table.sql\"";
$dump1 = "mysqldump -S /usr/local/mysql/mysql.sock -urxsg2 -prxsg2 test mem_armor_temporary -t --where=\"uid=$uid\" > $out_file";

$get2 = "mysqldump -h121.41.73.56 -ubinuser -psg2binuser $db mem_armor --where=\"uid=$uid\" > temp.sql";
$input2 = "mysql -urxsg2 -prxsg2 test -e \"source temp.sql\"";
$alter2 = "mysql -urxsg2 -prxsg2 test -e \"source alter_table2.sql\"";
$sethid = "mysql -urxsg2 -prxsg2 test -e 'update mem_armor set hid=0'";
$dump2 = "mysqldump -S /usr/local/mysql/mysql.sock -urxsg2 -prxsg2 test mem_armor -t --where=\"uid=$uid\" >> $out_file";

echo "exec cmds:\n";

// get temp armor
echo $input1."\n";	exec($input1);
echo $alter1."\n";	exec($alter1);
echo $dump1."\n";	exec($dump1);
echo "\n";

// get armor from backup
if ($do_dump2)
{
	echo $get2."\n";	exec($get2);
	echo $input2."\n";	exec($input2);
	echo $alter2."\n";	exec($alter2);
	echo $sethid."\n";  exec($sethid);
	echo $dump2."\n";	exec($dump2);
}

$server_info = array();
rust_get_server_info($server_info, $server);
$cmd = "scp $out_file root@".$server_info['dbip'].":~";

$sql = "source $out_file";

$sql1 = "SELECT * FROM user_armor_clear_num where uid=$uid;";
$sql2 = "DELETE FROM user_armor_clear_num where uid=$uid;";
$sql3 = "INSERT INTO exc_start_sql(sql_str) VALUES('$sql2');";

echo "\n";
echo "useful cmd & sql:\n";
echo $cmd."\n";
echo $sql."\n";
echo $sql1."\n";
echo $sql3."\n";
echo "\n";

?>