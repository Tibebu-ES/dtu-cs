<?php
//hintFor course name  =1; 
define("HINTFOR_COURSE", 1);
define("HINTFOR_INSTRUCTOR", 2);

$keyword =$_REQUEST["key"];
$dpt=$_REQUEST["dpt"];
$yos=$_REQUEST["yos"];
$hintFor = $_REQUEST["hintfor"];


//open the hint.xml file and get the particular node based on the request
$xmlHint=simplexml_load_file("hint.xml") or die("Error: sys cant open hint datamodel.");
 foreach($xmlHint->children() as $department) { 
    if($department['value']==$dpt){ //activedepartment found
        switch ($hintFor) {
            case HINTFOR_COURSE:
                 $courses = $department->courses;  // foreach ($courses->children() as $course) { if($course->['yos']==$yos)
                 sendHint($courses,HINTFOR_COURSE);  
                 break;
            case HINTFOR_INSTRUCTOR:
                 $instructors = $department->instructors;
                 sendHint($instructors,HINTFOR_INSTRUCTOR);
                break;
            
            default:
                # code...
                break;
        }
    }


 }



//send hint from the given xmlNode  // xmlNode could e courses or instructors
 function sendHint($xmlNode,$hintfor){
    $hint = "";
    $keyword =$GLOBALS['keyword'];
    $yos=$GLOBALS['yos'];
    if($keyword!="")
    {
        $keyword = strtolower($keyword);
        $keyword_len = strlen($keyword);
        switch ($hintfor) {
            case HINTFOR_COURSE:
                $courses = $xmlNode;
                foreach ($courses->children() as $course) 
                {
                    if($course['yos']==$yos)
                    {
                      
                        if (stristr($keyword, strtolower(substr($course,0, $keyword_len)))) {
                            $nameWithLink="<a onclick='setHint(this)'>".$course."</a>";
                            if ($hint === "")
                                 $hint = $nameWithLink;
                             else 
                                $hint .= ", $nameWithLink";
                         }
                    }
                }
                echo $hint === "" ? "<a style='color:red'".">???</a>" : $hint;
                break;
            case HINTFOR_INSTRUCTOR:
                $instructors = $xmlNode;
                foreach ($instructors->children() as $instructor) {
                    if (stristr($keyword, substr($instructor,0, $keyword_len))) {
                        $instructor_title=$instructor['title'];
                        $nameWithLink="<a onclick='setHint2(this)'>".$instructor_title." ".$instructor."</a>";
                        if ($hint === "")
                            $hint = $nameWithLink;
                        else 
                            $hint .= ", $nameWithLink";
                    }
                }
                echo $hint === "" ? "<a style='color:red'".">???</a>" : $hint;
                break;
            default:
                # code...
                break;
        }
    }
}

 
?> 