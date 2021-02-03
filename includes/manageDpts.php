<div class="panel panel-danger">
	<div class="panel-heading">
		<h4 class="panel-title" ><a >Manage Departments</a></h4>
	</div>
	<div class="panel-body">
		<!--here goes the toolbar and list of departments in new panel-->
		<div class="panel panel-warning">
			<!--here goes the toolba-->
			<div class="panel-heading">
				<div class="btn-group btn-group-xs" role="group" >
				  <button  data-toggle="modal" data-target="#myModal7" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-plus"></span> New</button>
				  <button  data-toggle="modal" data-target="#myModal8" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-remove"></span> Delete</button>
				</div>

			</div>
			<div class="panel-body">
				<!--here goes list of departments -->
				<ul id="departmentsListAdmin" class="nav nav-pills nav-stacked">
					

				</ul>

			</div>

		</div>
	</div><!--first panel body-->
</div>

<!-- modal for addingnew dpt-->

<div id="myModal7" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Add <span id=""><small> - new Department.</small></span> </h4>
      </div>
      <div class="modal-body">
        <form id="andForm" >
          <div class="input-group"> 
             <span class="input-group-addon">Department Name</span> 
             <input id ="dn" type="text" class="form-control" required placeholder=""> 
       	  </div>
      	   
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" onclick="addNewDepartment(document.forms['andForm']['dn'].value)"  >OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- modal for user remove confirmation-->

<div id="myModal8" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Are you sure you want to remove the selected Department? </h4>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" onclick="removeSelectedDepartment()"  data-dismiss="modal" >Confirm</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>