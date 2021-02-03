<?php
$dataString = test_input($_POST["data"]);
 define("CS_USER_DEPARTMENT","csUserDepartment" );

//write this string on server

//open schedule.xml file
/*
session_start();
$dptname=$_SESSION[CS_USER_DEPARTMENT];
*/
$dptname=test_input($_POST["dpt"]);
if($dptname==null || $dptname==""){

}else{
	$myfile = fopen($dptname.".xml", "w") or die("Unable to open file!");
	//replace the content
	fwrite($myfile, $dataString);
	fclose($myfile);

	//send the new model
	echo "true";
}





//test input
function test_input($data) {
  //$data = trim($data);
  //$data = stripslashes($data);
  //$data = htmlspecialchars($data);
  
  return $data;
 }


?>
