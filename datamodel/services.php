<?php

define("JSON_REQUEST_TYPE_AVADPTS", 1);
define("JSON_REQUEST_TYPE_AVAYOS", 2);
define("JSON_REQUEST_TYPE_SCHEDULE", 3);

$requestType="";
if(isset($_GET["requestType"]))
	$requestType = $_GET["requestType"];

switch ($requestType) {
	case JSON_REQUEST_TYPE_AVADPTS:
		//open the departments.xml file and send the list of ava dpts
		
		$dptXml = simplexml_load_file("departments.xml") or die("error");
		$json='{"departments":[';
		$childrenCount=$dptXml->count();
		$count=1;
		foreach ($dptXml->children() as $dpt) {
			//if it is the last children, dont put comma,
			if($count==$childrenCount)
				$json.='{"name":'.'"'.$dpt.'"}';
			else
				$json.='{"name":'.'"'.$dpt.'"},';
			//increament the counter
			$count++;
		}
		
		$json.=']}';
		echo $json;
		break;

	case JSON_REQUEST_TYPE_AVAYOS:
		$dpt="";
		if(isset($_GET["dpt"]))
			$dpt=$_GET["dpt"];
		$dptXml = simplexml_load_file($dpt.".xml") or die("error");
		$json ='{"yos":[';
		$yosNum=$dptXml->count();
		$yosCount=1;
		foreach ($dptXml->children() as $yos) {
			$yosValue=$yos['value'];
			$json.='{"value":'.'"'.$yosValue.'",';
			//start the secs aray
			$json.='"secs":[';
			$secsNum = $yos->count();
			$secsCount=1;
			foreach ($yos->children() as $sec ) {
				$stream= ($sec['stream']=='-') ? "" :'-'.$sec['stream'];
				$value='Sec-'.$sec['value'].$stream; //format= Sec-.secvalue.stream//Sec-1-Computer
				if($secsCount==$secsNum)
					$json.='{"value":'.'"'.$value.'"}]';//close the secs aray tag
				else
					$json.='{"value":'.'"'.$value.'"},';
				$secsCount++;
			}

			if($yosCount==$yosNum)
				$json.='}]'; //close the yos aray tag
			else
				$json.='},';  //close the current yos objects
			$yosCount++;
		}
		//close the Json file
		$json.='}';
		echo $json;
		break;
	
	
	case JSON_REQUEST_TYPE_SCHEDULE:
		$dpt="";
		$yos_value="";$strm="";$sec_value="";

		if(isset($_GET["dpt"]))
			$dpt=$_GET["dpt"];
		if(isset($_GET["yos"]))
			$yos_value=$_GET["yos"];
		if(isset($_GET["strm"]))
			$strm=$_GET["strm"];
		if(isset($_GET["sec"]))
			$sec_value=$_GET["sec"];

		//create AllCschedules object to store all schedules
		$allCSchedules = new AllCSchedules();
		//open the required datamodel
		$dptXml = simplexml_load_file($dpt.".xml") or die("error");
		// get the required 
		foreach ($dptXml->children() as $yos) {
			if($yos['value']==$yos_value){
				foreach ($yos->children() as $sec ) {
					if($sec['stream']==$strm && $sec['value']==$sec_value){
						//here the target sec is found
						foreach ($sec->children() as $day) {
							$day_value=$day['value'];
							foreach ($day->children() as $period) {
								$prd_value=$period['value'];
								//check if there are two course in a schedule, in the datamodel it is rep by :
								$cNames = explode(":", $period->cName);
								$iNames = explode(":", $period->iName);
								$rooms = explode(":", $period->room);
								//$cName = $period->cName;
								//$iName = $period->iName;
								//$room = $period->room;

								for ($i=0; $i < count($cNames) ; $i++) { 
									if(count($iNames)<=$i)
										array_push($iNames, "unknown");
									if(count($rooms)<=$i)
										array_push($rooms, "unknown");
									//get the CSchedule // if not added it will add it and return it
									$CSchedule = $allCSchedules->getCSchedule($cNames[$i],$iNames[$i]);
									//add the schedule to the returned CSchedule
									$CSchedule->addSchedule($day_value,$prd_value,$rooms[$i]);
								}
						

							}
						}
					}
				}
			}

		}// outer foreach

		//echo json file
		echo $allCSchedules->getJSONString();

		break;
	default:
		# code...
		break;
}

//class for  acourseschedule

/**
* 
*/
class CSchedule 
{
	
	var $cName;
	var $cInstructor;
	var $schedules;    //string array, each are schedules with format 'Day:prd:room'
	function __construct($cname,$cinstructor)
	{
		$this->cName=$cname;
		$this->cInstructor=$cinstructor;
		//$this->$schedules ;
	}


	function addSchedule($day,$prd,$room)
	{
		$schedule=$day.':'.$prd.':'.$room;
		if($this->schedules == null){
			$this->schedules =  array($schedule);
	
		}else
			array_push($this->schedules, $schedule);;
	}

	//return true if the cname is same

	function isCName($cname)
	{
		if(trim($this->cName)==trim($cname))
			return true;
		else
			return false;
	}
}

/**
* 
*/
class AllCSchedules
{
	var $CSchedules;
	
	function __construct()
	{
		$this->CSchedules =  array();
	}

	//add if not laready added and return the CSchedule
	//
	function getCSchedule($cname,$iname){
		//itterate through all the cschedules and check if it is lareday added
		foreach ($this->CSchedules as $value) {
			if($value->isCName($cname)){
				return $value;
			}
		}

		//if not added
		$newCSchedule = new CSchedule($cname,$iname);
		array_push($this->CSchedules, $newCSchedule);
		return $newCSchedule;
	}

	//construct and return the corresponding Json file
	function getJSONString(){

		$jsonString='{"allcschedules":[';
		$CSchedules_Num=count($this->CSchedules);
		$CSchedules_count=0;
		foreach ($this->CSchedules as $CSchedule) {
			$jsonString.='{'.'"cname":'.'"'.$CSchedule->cName.'"'.',"iname":'.'"'.$CSchedule->cInstructor.'"'.',';
			$jsonString.='"schedules":[';
			$schedules = $CSchedule->schedules;
			for ($i=0; $i < count($schedules) ; $i++) { 
				$schedule_strg= $schedules[$i];  //'day:prd:room'
				$day = substr($schedule_strg, 0, strpos($schedule_strg,":"));
				$tmp= substr($schedule_strg, strpos($schedule_strg,":")+1);
				$prd=substr($tmp, 0, strpos($tmp,":"));
				$room=substr($tmp, strpos($tmp,":")+1);

				$jsonString.='{"day":'.'"'.$day.'"'.', "prd":'.'"'.$prd.'"'.',"room":'.'"'.$room.'"'.'}';
				if($i < (count($schedules)-1))
					$jsonString.=',';
			}

			$jsonString.=']';//close schedules array
			$jsonString.='}';//close the cschedule object
			if($CSchedules_count<($CSchedules_Num-1))
				$jsonString.=','; // separate cschedules object by comma
			else
				$jsonString.=']'; //close the allschedules array 

			$CSchedules_count++;
		}
		$jsonString.='}'; //close the final object

		return $jsonString;
	}
	
}


?>