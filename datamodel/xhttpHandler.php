<?php
define("XHTTP_REQUEST_TYPE_USERINFO", 1);
define("XHTTP_REQUEST_TYPE_DPTSCHEDULE", 2);
define("XHTTP_REQUEST_TYPE_AUTHENTICATE", 3);
define("XHTTP_REQUEST_TYPE_LOGOUT",4);
define("XHTTP_REQUEST_TYPE_SELECTDEPARTMENT", 5);
define("XHTTP_REQUEST_TYPE_CHANGEPW", 6);

//requests from the system admin
define("XHTTP_REQUEST_TYPE_GETUSERS", 7);
define("XHTTP_REQUEST_TYPE_ADDUSER", 8);
define("XHTTP_REQUEST_TYPE_GETDEPARTMENTS", 9); //used in homepage-dptlist/combobox and, in the adduser modal-adminpage
define("XHTTP_REQUEST_TYPE_REMOVEUSER", 10);
define("XHTTP_REQUEST_TYPE_GETDEPARTMENTS_FORADMIN", 11); //used in showing the dpts list itme in the adminpage
define("XHTTP_REQUEST_TYPE_ADDDEPARTMENT", 12);
define("XHTTP_REQUEST_TYPE_REMOVEDEPARTMENT", 13);
define("XHTTP_REQUEST_TYPE_GETINSTRUCTORS", 14);
define("XHTTP_REQUEST_TYPE_ADDINSTRUCTOR", 15);
define("XHTTP_REQUEST_TYPE_REMOVEINSTRUCTOR", 16);
define("XHTTP_REQUEST_TYPE_GETCOURSES", 17);
define("XHTTP_REQUEST_TYPE_ADDCOURSE", 18);
define("XHTTP_REQUEST_TYPE_REMOVECOURSE", 19);

 define("CS_USER_NAME","csUserName" );
 define("CS_USER_PWORD","csUserPw" );
 define("CS_USER_DEPARTMENT","csUserDepartment" );
 define("CS_USER_TYPE","csUserType" );
 define("CS_USER_NAME_DFT", "unknown");//defualt user name
 define("CS_USER_DEPARTMENT_DFT", "ECE");// defualt user department


//initially the sessions are set in the index page  , unknown user, usertype=3, dept=ece
if(isset($_POST["requestType"]))
	$requestType = test_input($_POST["requestType"]);
elseif(isset($_GET["requestType"]))
	$requestType = $_GET["requestType"];

switch ($requestType) {
	case XHTTP_REQUEST_TYPE_USERINFO:
		//send user info
		session_start(); 
		$userInfo = $_SESSION[CS_USER_NAME].",".$_SESSION[CS_USER_TYPE].",".$_SESSION[CS_USER_DEPARTMENT];
		echo $userInfo;
		break;

	case XHTTP_REQUEST_TYPE_DPTSCHEDULE:
		//for which department
		$dptName ;
		if(isset($_POST["dpt"]))
			$dptName = test_input($_POST["dpt"]);
		elseif(isset($_GET["dpt"]))
			$dptName = $_GET["dpt"];

		if($dptName == false)
			return;
	
		//open the xml datamodel
		$myfile = fopen($dptName.".xml", "r") or die("Unable to open file!");
		$data = fread($myfile,filesize($dptName.".xml"));
 		fclose($myfile);
 		echo $data; //encode
		break;
	
	case XHTTP_REQUEST_TYPE_AUTHENTICATE:
		$uname =isset($_POST["un"])? test_input($_POST["un"]):"";
		$pword =isset($_POST["pd"])? test_input($_POST["pd"]):"";
		if($uname== false || $pword==false)
			return;
		$response = "false";

		//authenticate	
		$xmlUsers=simplexml_load_file("users.xml") or die("Error: sys cant open user datamodel.");
        foreach($xmlUsers->children() as $user) { 
        	if($uname==$user->uName && $pword==$user->pWord){
        		//set sessioin userinfo variables

			    session_start();
			    $dpt=$user['department'];
			    $utype=$user['type'];

			   $_SESSION[CS_USER_NAME] = "$uname";
			   $_SESSION[CS_USER_DEPARTMENT]="$dpt";
			   $_SESSION[CS_USER_TYPE]="$utype";
			   //store user pw in the session for later use;
			   $_SESSION[CS_USER_PWORD]="$pword";

        		$response="true";
        		break;
        	}
         }
	
		echo  $response ;
		break;

	case XHTTP_REQUEST_TYPE_LOGOUT:
		session_start();
		// destroy the session 
		session_destroy();
		echo "true";
		break;

	case XHTTP_REQUEST_TYPE_SELECTDEPARTMENT:
		$dptName = test_input($_POST["dpt"]);

		session_start();
		$_SESSION[CS_USER_DEPARTMENT]="$dptName";
		echo "true";
		break;
	case XHTTP_REQUEST_TYPE_CHANGEPW:
		//get the user input, oldpw and newpw
		$opw=test_input($_POST["opw"]);
		$npw=test_input($_POST["npw"]);
		//verify if the stored old pw is same as the user typed old pw
			//get the stored oldpw from the user session// system stores the user pw when user login correctly for later use
		session_start();
		$sopw = isset($_SESSION[CS_USER_PWORD])? $_SESSION[CS_USER_PWORD]:"";
			//verify the user
		if($opw==$sopw){
			//if the user is verified update the users data model
				//open user datamodel
			$xmlUsers=simplexml_load_file("users.xml") or die("Error: sys cant open user datamodel.");
				//iterate throgh each user and find user requested the services
	        foreach($xmlUsers->children() as $user) { 
	        	if($_SESSION[CS_USER_NAME]==$user->uName && $opw==$user->pWord){
					//when the user file is found
	        		$user->pWord = $npw; //set the password node
	        		//save the change
	        		$xmlUsers->asXML('users.xml'); //similar to saveXML();
	        		//also update the user session
	        		$npw=$user->pWord; //to make sure if saved succesfully
					$_SESSION[CS_USER_PWORD]="$npw";
					break;
	        	}
	         }

			//response "true"
			echo "true";
		}else{
			//if not verified response "false"
			echo "false";
		}

		break;
	case XHTTP_REQUEST_TYPE_GETUSERS:
	   //format "un1:dpt1,un2:dpt2,..." if no error
		//get the user datamodel and iterate through each user
		$xmlUsers = simplexml_load_file("users.xml") or die("Error: cant open users datamodel");
		foreach ($xmlUsers->children() as $user) {
			//send only scheduler users i.e type=2
			if($user['type']=='2'){
				$un_dpt = $user->uName.":".$user['department'];
				//identify those with default pw
				if($user->pWord=="12345678")
					$un_dpt.=" ~ PW [12345678]";
				echo ($un_dpt.",");
			}
		}
		//remove the last comma injs code
		//$userslist=substr($userslist, 0,(strlen($userslist)-1));
		//echo $userslist;
		break;
	case XHTTP_REQUEST_TYPE_ADDUSER:
		$uname=test_input($_POST['un']);
		$dpt=test_input($_POST['dpt']);
		if($uname== false || $dpt == false)
			return;
		//check if the username alreay exists
		//get the user datamodel and iterate through each user
		$xmlUsers = simplexml_load_file("users.xml") or die("Error: cant open users datamodel");
		foreach ($xmlUsers->children() as $user) {
			//send only scheduler users i.e type=2
			if($user->uName==$uname){
				echo "false";//username already exists;
				return;
			}
		}
		//if username doesnt eixst before
		//create the user and add to the system
		$newUser=$xmlUsers->addChild("user");
		//addattribute
		$newUser->addAttribute("type","2");
		$newUser->addAttribute("department",$dpt);
		$newUser->addChild("uName",$uname);
		$newUser->addChild("pWord","12345678");
		//save the datamodel
		$xmlUsers->asXML('users.xml');
		echo "true";

		break;
	case XHTTP_REQUEST_TYPE_GETDEPARTMENTS:
		//send the departments list
		$dptXml = simplexml_load_file("departments.xml") or die("error");
		foreach ($dptXml->children() as $dpt) {
			# code...
			echo '<option value= "'.$dpt.'">'.$dpt.'</option>';
		}
		

		break;
	case XHTTP_REQUEST_TYPE_REMOVEUSER:
		$uname=test_input($_POST['un']);
		if($uname == false)
			return;
		echo removeUser($uname);
		

		# code...
		break;
		case XHTTP_REQUEST_TYPE_GETDEPARTMENTS_FORADMIN:
			# code...
		//send the departments list each separated in comma
		$dptXml = simplexml_load_file("departments.xml") or die("error");
		foreach ($dptXml->children() as $dpt) {
			# code...
			echo $dpt.',';
			}

		break;
		case XHTTP_REQUEST_TYPE_ADDDEPARTMENT:
			# code...
		$newDpt=test_input($_POST['dpt']);
		if($newDpt == false)
			return;
		//check if the udepartment alreay exists
		//get the departments.xml  and iterate through each department
		$xmlDpts = simplexml_load_file("departments.xml") or die("Error: cant open departments datamodel");
		foreach ($xmlDpts->children() as $dpt) {
			//
			if($dpt == $newDpt){
				echo "false";//department name already exists;
				return;
			}
		}
		//if dptname doesnt eixst before
		//create the department and add to the system
			//1st the newdptname to dpts list
		$xmlDpts->addChild("dpt",$newDpt);
		//save the datamodel
		$xmlDpts->asXML('departments.xml');
			//2nd create the schedule datamodel for the dpartment
		//open the sample, dptDatamodel as domdocument
		$docnewDpt= new DOMDocument();
		$docnewDpt->load('dptModel.xml');
		$dptNode = $docnewDpt->documentElement;
		//change the department value attribute
		$dptNode->setAttribute('value',$newDpt);

		// add the yosSampleModel , to the new DPT
		$yosSampleModel = new DOMDocument();
		$yosSampleModel->load('yosModel.xml');
		$yosSampleModel->documentElement->setAttribute('value','1');

		//append the yosmodel as achild
		$clonedYos = $docnewDpt->importNode($yosSampleModel->documentElement,true);
		$dptNode->appendChild($clonedYos);
		//save the changes
		$docnewDpt->saveXML();
		$xmlNewDpt =simplexml_import_dom($docnewDpt);
		$xmlNewDpt->saveXML($newDpt.'.xml');
		//3rd add to the hint.xml file

        $hintDoc = new DOMDocument();
        $hintDoc->load('hint.xml');
        $hintNode= $hintDoc->documentElement;

        //get hintModel
        $hintModel = new DOMDocument();
        $hintModel->load('hintModel.xml');
        $hintModel->documentElement->setAttribute('value',$newDpt); //set the departmet value
        //append the hintmodel as child to hint xml file

        $clonedHintModel = $hintDoc->importNode($hintModel->documentElement,true);
        $hintNode->appendChild($clonedHintModel);
        //save the changes to the hint dom
        $hintDoc->saveXML();
        //save to the or update the hint.xml file
        $xmlHint = simplexml_import_dom($hintDoc);
        $xmlHint->saveXML('hint.xml');
		echo "true";
		break;
		case XHTTP_REQUEST_TYPE_REMOVEDEPARTMENT:
			# code...
		$dptname=test_input($_POST['dpt']);
		if($dptname == false)
			return;
		$dptNode;
		$doc= new DOMDocument();
		$doc->load('departments.xml');

		foreach ($doc->documentElement->getElementsByTagName('dpt') as $dpt) {
			$dpt_name = $dpt->childNodes->item(0)->nodeValue;
			//echo $un;
			if($dpt_name == $dptname){
				$dptNode=$dpt; //get the department to be deleted
				break;
			}
		}
		//remove the node
		if($dptNode!=null){
			$dptNode->parentNode->removeChild($dptNode);
			$doc->saveXML();
			$dptxml =simplexml_import_dom($doc);
			$dptxml->saveXML('departments.xml');

			//delete the schedule datamodel file
			$fileDeleted = unlink($dptname.'.xml');
			//remove the corresponding user
			$userDeleted = removeUsersOfDpt($dptname);
			//remove the department from the hint.xml file
			$dptDeletedFromHint = removeDptFromHint($dptname);
			if($fileDeleted == true && $userDeleted == true && 	$dptDeletedFromHint==true ){
				echo "true";
			}else
				echo "false";
			
		}else
			echo "false";
			break;
		case XHTTP_REQUEST_TYPE_GETINSTRUCTORS:
			$dptname = $_REQUEST["dpt"];
			getAllInstructors($dptname);
			break;
		case XHTTP_REQUEST_TYPE_ADDINSTRUCTOR:
			$title = $_REQUEST['title'];
			$iname = $_REQUEST['iname'];
			addInstructor($title ,$iname);
			echo "true";
			break;
		case XHTTP_REQUEST_TYPE_REMOVEINSTRUCTOR:
			$iname = $_REQUEST['iname'];
			session_start();	
	        $dpt = $_SESSION[CS_USER_DEPARTMENT];
	        echo removeInstructor($dpt,$iname);
			break;
		case XHTTP_REQUEST_TYPE_GETCOURSES:
			session_start();	
	        $dpt = $_SESSION[CS_USER_DEPARTMENT];
	        echo getAllCourses($dpt);
			break;
		case XHTTP_REQUEST_TYPE_ADDCOURSE:
			$cyos = $_REQUEST['yos'];
			$ctitle = $_REQUEST['ctitle'];
			addCourse($cyos,$ctitle);
			echo true;
			
			break;
		case XHTTP_REQUEST_TYPE_REMOVECOURSE:
			$cyos = $_REQUEST['yos'];
			$ctitle = $_REQUEST['ctitle'];
			session_start();	
	        $dpt = $_SESSION[CS_USER_DEPARTMENT];
	       echo  removeCourse($dpt,$cyos,$ctitle);
			break;
	default:
		# code...
		break;
}

//test input
function test_input($data) {
  //$data = trim($data);
  //$data = stripslashes($data);
  //$data = htmlspecialchars($data);
  //$data= urldecode($data);
	if($data == ""){
		echo "false";
		return false;
	}
		
  return $data;
 }



function removeUser($uname){
	    $userNode = null;
		$doc= new DOMDocument();
		$doc->load('users.xml');

		//$xmlUsers = simplexml_load_file("users.xml") or die("Error: cant open users datamodel");
		foreach ($doc->documentElement->getElementsByTagName('user') as $user) {
			$un=$user->getElementsByTagName('uName')->item(0)->childNodes->item(0)->nodeValue;
			//echo $un;
			if($un==$uname){
				$userNode=$user; //get the user to be deleted
				break;
			}
		}
		//remove the node
		if($userNode!=null){
			$userNode->parentNode->removeChild($userNode);
			$doc->saveXML();
			$userxml =simplexml_import_dom($doc);
			$userxml->saveXML('users.xml');
			return  "true";
		}else
			return  "false";
}


function removeUsersOfDpt($dpt){
	    $userNode = null;
		$doc= new DOMDocument();
		$doc->load('users.xml');

		//$xmlUsers = simplexml_load_file("users.xml") or die("Error: cant open users datamodel");
		foreach ($doc->documentElement->getElementsByTagName('user') as $user) {
			$udpt=$user->getAttribute('department');
			//echo $un;
			if($udpt==$dpt){
				$userNode=$user; //get the user to be deleted
				break;
			}
		}
		//remove the node
		if($userNode!=null){
			$userNode->parentNode->removeChild($userNode);
			$doc->saveXML();
			$userxml =simplexml_import_dom($doc);
			$userxml->saveXML('users.xml');
			return  true;
		}else//means no user for that dpt
			return  true;

}

function removeDptFromHint($dpt){
	$dptNode = null;
	$hintDoc = new DOMDocument();
	$hintDoc->load('hint.xml');
	foreach ($hintDoc->documentElement->getElementsByTagName('department') as $department) {
			if($department->getAttribute('value')==$dpt){
				$dptNode=$department; //get the department node to be deleted
				break;
			}
		}
	//remove the node
	if($dptNode!=null){
		$dptNode->parentNode->removeChild($dptNode);
		$hintDoc->saveXML();
		$hintXml =simplexml_import_dom($hintDoc);
		$hintXml->saveXML('hint.xml');
		return  true;
	}else
		return  false;
}

//function that show instructors in a given department from the hint.xml file
function getAllInstructors($dpt){
	$xmlHint=simplexml_load_file("hint.xml") or die("Error: sys cant open hint datamodel.");
 	foreach($xmlHint->children() as $department) {
 		if($department['value']==$dpt){
 			$instructors = $department->instructors;
 			foreach ($instructors->children() as $instructor) {
 				$title = $instructor['title'];
 				$instructorView = "<li> <button type='button'  class='close' title ='delete this Instructor' onclick='removeInstructor(".'"'.$instructor.'"'.")' > &times;</button> <h6>".$title." ".$instructor."</h6>"."</li>";
 				echo $instructorView;
 			}
 		}
 	}

}

function addInstructor($title ,$iname){
	$xmlHint=simplexml_load_file("hint.xml") or die("Error: sys cant open hint datamodel.");
	session_start();	
	$dpt = $_SESSION[CS_USER_DEPARTMENT];
 	foreach($xmlHint->children() as $department) {
 		if($department['value']==$dpt){
 			$instructors = $department->instructors;
 			$newInstructor = $instructors->addChild("instructor",$iname);
 			$newInstructor->addattribute("title",$title);
 			break;
 		}
 	}
 	$xmlHint->saveXML("hint.xml");
}

function addCourse($cyos ,$ctitle){
	$xmlHint=simplexml_load_file("hint.xml") or die("Error: sys cant open hint datamodel.");
	session_start();	
	$dpt = $_SESSION[CS_USER_DEPARTMENT];
 	foreach($xmlHint->children() as $department) {
 		if($department['value']==$dpt){
 			$courses = $department->courses;
 			$newCourse = $courses->addChild("course",$ctitle);
 			$newCourse->addattribute("yos",$cyos);
 			break;
 		}
 	}
 	$xmlHint->saveXML("hint.xml");
}

function removeInstructor($dpt,$iname){
	$instNode = null;
	$hintDoc = new DOMDocument();
	$hintDoc->load('hint.xml');
	foreach ($hintDoc->documentElement->getElementsByTagName('department') as $department) {
			if($department->getAttribute('value')==$dpt){
				$instructors=$department->getElementsByTagName('instructors')->item(0);
				foreach ($instructors->getElementsByTagName('instructor') as $instructor) {
					$instName = $instructor->childNodes->item(0)->nodeValue;
					if($instName==$iname){
						$instNode=$instructor;
						break;
					}

				}
				
			}
		}
	//remove the node
	if($instNode!=null){
		$instNode->parentNode->removeChild($instNode);
		$hintDoc->saveXML();
		$hintXml =simplexml_import_dom($hintDoc);
		$hintXml->saveXML('hint.xml');
		return  true;
	}else
		return  false;
}

function removeCourse($dpt,$cyos,$ctitle){
	$courseNode = null;
	$hintDoc = new DOMDocument();
	$hintDoc->load('hint.xml');
	foreach ($hintDoc->documentElement->getElementsByTagName('department') as $department) {
			if($department->getAttribute('value')==$dpt){
				$courses=$department->getElementsByTagName('courses')->item(0);
				foreach ($courses->getElementsByTagName('course') as $course) {
					$corsTitle = $course->childNodes->item(0)->nodeValue;
					$corsYos = $course->getAttribute('yos');
					if($corsTitle==$ctitle && $corsYos==$cyos){
						$courseNode=$course;
						break;
					}

				}
				
			}
		}
	//remove the node
	if($courseNode!=null){
		$courseNode->parentNode->removeChild($courseNode);
		$hintDoc->saveXML();
		$hintXml =simplexml_import_dom($hintDoc);
		$hintXml->saveXML('hint.xml');
		return  true;
	}else
		return  false;
}

function getAllCourses($dpt){
	$xmlHint=simplexml_load_file("hint.xml") or die("Error: sys cant open hint datamodel.");
 	foreach($xmlHint->children() as $department) {
 		if($department['value']==$dpt){
 			$courses = $department->courses;
 			foreach ($courses->children() as $course) {
 				$cyos = $course['yos'];
               $courseView ="<tr>".
               				"<td><h6> ".$course."</h6></td>".
               				"<td><button type='button' class='close' title ='delete this Course' onclick='removeCourse(".'"'.$cyos.'",'.'"'.$course.'"'.")'>"."&times;</button> ".$cyos."</td>".
               				"</tr>";	
 				echo $courseView;
 			}
 		}
 	}

}


?>