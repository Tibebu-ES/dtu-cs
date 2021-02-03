	<div class="prv2 hidden panel panel-danger">
  <div class=" panel-heading">
    <div class="btn-group btn-group-xs" role="group" >
		<a type="button" class=" btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal2"><span class="glyphicon glyphicon-plus" ></span> year</a>
		<a type="button" class=" btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal"> <span class="glyphicon glyphicon-plus"></span> sec</a>
		<a type="button" class=" btn btn-warning btn-xs" onclick="saveChanges()"> <span class="glyphicon glyphicon-save-file"></span> Save </a>
    <a type="button" class=" btn btn-warning btn-xs" onclick="removeSelectedSec()"> <span class="glyphicon glyphicon-remove"></span>  sec</a>
    <a type="button" class=" btn btn-warning btn-xs" onclick="removeSelectedYOS()"> <span class="glyphicon glyphicon-remove"></span>  year</a>
  </div>	
  </div>
</div>
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h4 class="panel-title ">
				<a class="">Select your Department  
          <span>
            <select id="departments" onchange="selectDepartment(this.value)">
              <!-- department list goes here-->
            </select>
        </span>
      </a> 
			</h4>	
		</div>
		<div class="panel-body">
				<!--department YEAR OF STUDY goes here-->
				 <?php  include "includes/yos_nav.php"  ?>
		</div>

	</div>

	<!-- Modal to accept secV-->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Section number please?.</h4>
      </div>
      <div class="modal-body">
        <form id="secForm" >
		    <div class="input-group"> 
		         <span class="input-group-addon">New Section  </span> 
		         <input id="secv" name ="secv"  type="number" max="5" min="1" class="form-control" required placeholder=""> 
		    </div>
        <div class="input-group"> 
             <span class="input-group-addon">Section Stream</span> 
             <select class="form-control" id="strmSelect">
                <!--options goes here-->
             </select> 
        </div>
        </form>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-default" onclick="addNewSec(document.forms['secForm']['secv'].value,document.forms['secForm']['strmSelect'].value)">OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


	<!-- Modal to accept yearV-->
<div id="myModal2" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Year of Study please?.</h4>
      </div>
      <div class="modal-body">
        <form id="yosForm" >
        	<div class="input-group"> 
		         <span class="input-group-addon">Year of Study</span> 
		         <input id ="yosv" type="number" max="5" min="1" class="form-control" required placeholder=""> 
		     </div><br>
         
              <input type="radio" name="stream" id="radioHasStreamId" value="has stream" onchange="modal2InputChanged(this)"> Has Stream              
              <input class="radioHasNoStream"type="radio" name="stream" value="has no stream" onchange="modal2InputChanged(this)" checked> Has No Stream<br><br>
          <div ID="strmNamesDiv"class="hidden input-group"> 
             <span class="input-group-addon">Stream Names</span> 
             <input id ="strmNames" type="text" class="form-control" placeholder="Stream name1,stream name2,..."><br> 
         </div>
         <p><small>If the new year of study has streams click on has Stream radio button and list all stream names, separet each by comma.</small> </p>
        </form>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-default" onclick="addNewYos(document.forms['yosForm']['yosv'].value,document.forms['yosForm']['strmNames'].value,document.forms['yosForm']['stream'].value)"  >OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div><!--document.getElementById('radioHasStreamId')-->
</div>


