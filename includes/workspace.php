<div class="panel panel-danger">
  <div class="panel-heading">
      <a type="button" class=" btn btn-warning btn-xs" id="login" data-toggle="modal" data-target="#myModal3"><span class="glyphicon glyphicon-log-in" ></span> Login</a>
      <a type="button" class=" btn btn-warning btn-xs" id="logout"onclick="logout()"><span class="glyphicon glyphicon-log-out" ></span> Logout</a>
      <a type="button" class=" btn btn-warning btn-xs" id="changePw" data-toggle="modal" data-target="#myModal4" ><span class="glyphicon glyphicon-triangle-top" ></span> change my password</a>
      <a type="button" class=" btn btn-warning btn-xs" id="editIns" data-toggle="modal" data-target="#myModal10" ><span class="glyphicon glyphicon-pencil" ></span> Edit Instructors</a>
      <a type="button" class=" btn btn-warning btn-xs" id="editCors" data-toggle="modal" data-target="#myModal11" ><span class="glyphicon glyphicon-pencil" ></span> Edit Courses</a>
      <a  style="float:right;" type="button" class=" btn btn-warning btn-xs" id="editCors" data-toggle="modal" data-target="#myModal12" ><span class="glyphicon glyphicon-info-sign" ></span> Class info</a>
  </div>
</div>
<div class="panel panel-danger">
	<div class="panel-heading">
		<div class="panel-title">
		<!--Days nav goes here-->
		<ul class="nav nav-pills"> 
		   <li id="activeDayTab" class= "active" onclick="showSchedule('-1','-1','-1','monday','-1',this)" ><a href="#workingSpace">Monday</a></li> 
		   <li class="" onclick="showSchedule('-1','-1','-1','tuesday','-1',this)" ><a href="#workingSpace">Tuesday</a> </li> 
		   <li class= "" onclick="showSchedule('-1','-1','-1','wednesday','-1',this)" ><a href="#workingSpace">Wednesday</a></li> 
		   <li class= "" onclick="showSchedule('-1','-1','-1','thursday','-1',this)" ><a href="#workingSpace">Thursday</a></li> 
		   <li class= "" onclick="showSchedule('-1','-1','-1','friday','-1',this)" ><a href="#workingSpace">Friday</a></li> 
		   <li class= "" onclick="showSchedule('-1','-1','-1','saturday','-1',this)" ><a href="#workingSpace">Saturday</a></li> 
		   <li class= "" onclick="showSchedule('-1','-1','-1','sunday','-1',this)" ><a href="#workingSpace">Sunday</a></li> 
		</ul>
		</div> 
	</div>
    <div class="panel-body">
    	<!--periods nav-->
    	<div class="col-md-3">
    		<div class="panel panel-danger">
    			<div class="panel-heading">
    				<h4 class="panel-title"><span class="glyphicon glyphicon-bell"></span>  <a >PERIODS</a> </h4>
    			</div>
          <div class="panel-body">
	    		 <ul class="nav nav-pills nav-stacked"> 
				     <li id="activePrdTab" class= "active" onclick="showSchedule('-1','-1','-1','-1','1',this)" ><a href="#workingSpace" >Period 1</a></li> 
				     <li class= "" onclick="showSchedule('-1','-1','-1','-1','2',this)" ><a href="#workingSpace"  >Period 2</a></li> 
				     <li class= ""  onclick="showSchedule('-1','-1','-1','-1','3',this)"><a href="#workingSpace" >Period 3</a></li> 
				   </ul>
          </div> 
			</div>
    	</div>
    	<!--each periods detail/ main workingspace-->
    	<div class="col-md-9">
    		<?php include "includes/period_detail.php" ?>
    	</div>
    </div>
</div>

<!-- modal for login-->

<div id="myModal3" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Login  <span id="loginStatus"><small>  For Department Heads only.</small></span> </h4>
      </div>
      <div class="modal-body">
        <form id="loginForm" >
        	<div class="input-group"> 
		         <span class="input-group-addon">Username</span> 
		         <input id ="uName" type="text" class="form-control" placeholder=""> 
		   </div><br> 
      
          <div class="input-group"> 
             <span class="input-group-addon">Password</span> 
             <input id ="pWord" type="password" class="form-control">
         </div>
         
        </form>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-default" onclick="login(document.forms['loginForm']['uName'].value,document.forms['loginForm']['pWord'].value)"  >Login</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div><!--document.getElementById('radioHasStreamId')-->
</div>

<!-- modal for changing password-->

<div id="myModal4" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Change <span id="loginStatus"><small> - your password.</small></span> </h4>
      </div>
      <div class="modal-body">
        <form id="cpwForm" >
          <div class="input-group"> 
             <span class="input-group-addon">old password</span> 
             <input id ="opw" type="password" class="form-control" placeholder=""> 
       </div><br> 
      
          <div class="input-group"> 
             <span class="input-group-addon">new password</span> 
             <input id ="npw" type="password" class="form-control">
         </div>
         
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" onclick="changeMyPword(document.forms['cpwForm']['opw'].value,document.forms['cpwForm']['npw'].value)"  >OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div><!--document.getElementById('radioHasStreamId')-->
</div>

<!-- modal for editing instructors-->

<div id="myModal10" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Edit <span id="loginStatus"><small> - Instructors in your department.</small></span> </h4>
      </div>
      <div class="modal-body">
        <div class="panel panel-danger">
          <div class="panel-heading">
            <h4 class="panel-title"><a >List of Instructors</a></h4>
          </div>
          <div class="panel-body">
              <ul id="instList" class="ani nav nav-pills nav-stacked">
                <!-- list of instructors goes here-->
                
              </ul>
          </div>
          <div class="panel-footer">
            <h6 class=""><a >Add new Instructor</a></h6>
             <form id="newIForm" class="" >  
               <div class="input-group">
                   <span >
                     <select class="  form-control" id="titleSelect">
                        <option>Mr.</option>
                        <option>Ms.</option>
                        <option>Dr.</option>
                        <option>Prof.</option>
                     </select>
                   </span>
               </div>    
               <div class="input-group"> 
               <span class="input-group-addon">Instructor name</span> 
               <input id ="nin" type="text" class="form-control" required placeholder="Instructor name">
               <span class="input-group-btn">
                <button class="btn btn-default" style="background-color:#336600;color:#FFFFFF;"type="button" onclick="addNewInstructor(document.forms['newIForm']['titleSelect'].value,document.forms['newIForm']['nin'].value)">Add!</button>
              </span>
             </div>  
             </form>
             
        </div>
        </div>
      </div>
  
    </div>

  </div>
</div>

<!-- modal for editing courses-->

<div id="myModal11" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Edit <span id="loginStatus"><small> - Courses provided in your department.</small></span> </h4>
      </div>
      <div class="modal-body">
        <div class="panel panel-danger">
          <div class="panel-heading">
            <h4 class="panel-title"><a >List of Courses</a></h4>
          </div>
          <div class="panel-body" style="padding-bottom:0; margin-bottom:0;">
            <table class="table table-responsive " style="width:100%">
                <thead>
                 <tr>
                   <th><h6><a >Course Title</a></h6></th>
                   <th><h6><a >Year of study</a></h6></th>       
                </tr>
                </thead>
                 <tbody id= "corsList">
                   <!-- here goes the table rows. i.e the course lists-->
                </tbody>

            </table>
          </div>
          <div class="panel-footer">
            <h6 class=""><a >Add new Course</a></h6>
             <form id="newCForm" >  
               <div class="input-group"> 
                <span class="input-group-addon">Yr of Study</span> 
                <input id ="ncyos" type="number" min="1" max="7" class="form-control" required placeholder="">
              </div><br>    
              <div class="input-group"> 
                <span class="input-group-addon">Course title</span> 
               <input id ="nct" type="text" class="form-control" required placeholder="Insert Course title">
               <span class="input-group-btn">
                <button class="btn btn-default" style="background-color:#336600;color:#FFFFFF;"type="button" onclick="addNewCourse(document.forms['newCForm']['ncyos'].value,document.forms['newCForm']['nct'].value)">Add!</button>
               </span>
             </div>  
             </form>
             
        </div>
        </div>
      </div>
  
    </div>

  </div>
</div>

<!-- modal for class info-->

<div id="myModal12" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Class <span id="loginStatus"><small> - Information</small></span> </h4>
      </div>
      <div class="modal-body">
        <div class="panel panel-danger">
          <div style ="background-color: #337ab7" class="panel-heading">
            <h4 style ="background-color: #337ab7" class="panel-title"><a id="infotitle"  >Class Info</a></h4>
          </div>
          <div class="panel-body" style="padding-bottom:0; margin-bottom:0;">
             <form id="clsinfo">
                 <div class="input-group">
                     <span class="input-group-addon">Advisor</span>
                     <input id ="advisorName" type="text" class="form-control"  placeholder="Advisor name">
                 </div><br>
                 <div class="input-group">
                    <span class="input-group-addon " ><span class="glyphicon glyphicon-phone"></span></span>
                     <input id ="advisorPhone" type="number" class="form-control"  >
                 </div><br>
                 <div class="input-group">
                     <span class="input-group-addon">Representative</span>
                     <input id ="repName" type="text" class="form-control"  placeholder="Representative name">
                 </div><br>
                 <div class="input-group">
                     <span class="input-group-addon " ><span class="glyphicon glyphicon-phone"></span></span>
                     <input id ="repPhone" type="number"  class="form-control"  placeholder="">
                 </div><br>
             </form>
          </div>
         <div class="prv2 hidden" >
             <a onclick="saveClassInfo(document.forms['clsinfo']['advisorName'].value,document.forms['clsinfo']['advisorPhone'].value,document.forms['clsinfo']['repName'].value,document.forms['clsinfo']['repPhone'].value)" style="float:left ;color:#FFFFFF;margin-top:5px; " type="button" class=" btn btn-warning btn-xs saveSchedule" > <span class="glyphicon glyphicon-save-file"></span> Save info</a>
         </div>
         
        </div>
      </div>
  
    </div>

  </div>
</div>