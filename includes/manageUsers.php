<div class="panel panel-danger">
	<div class="panel-heading">
		<h4 class="panel-title" ><a >Manage Users</a></h4>
	</div>
	<div class="panel-body">
		<!--here goes the toolbar and list of departments in new panel-->
		<div class="panel panel-warning">
			<!--here goes the toolba-->
			<div class="panel-heading">
				<div class="btn-group btn-group-xs" role="group" >
				  <button data-toggle="modal" data-target="#myModal5"  type="button" class="btn btn-warning"><span class="glyphicon glyphicon-plus"></span> New</button>
				  <button data-toggle="modal" data-target="#myModal6" type="button" class="btn btn-warning" ><span class="glyphicon glyphicon-remove"></span> Delete</button>
				  
				</div>

			</div>
			<div class="panel-body">
				<!--here goes list of users-->
				<ul id="usersList" class="nav nav-pills nav-stacked">
					<li><a href="">User1</a></li>
					<li><a href="">User1</a></li>
					<li><a href="">User1</a></li>
					<li><a href="">User1</a></li>

				</ul>

			</div>

		</div>
	</div><!--first panel body-->
</div>

<!-- modal for addingnew user-->

<div id="myModal5" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Add <span id=""><small> - new User.</small></span> </h4>
      </div>
      <div class="modal-body">
        <form id="anuForm" >
          <div class="input-group"> 
             <span class="input-group-addon">User Name</span> 
             <input id ="un" type="text" class="form-control" required placeholder=""> 
       	  </div><br> 
      	  <div class="input-group"> 
             <span class="input-group-addon">Department</span> 
             <select class="form-control" id="dptSelect">
                <!--options goes here-->
             </select> 
          </div>
          
         
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" onclick="addNewUser(document.forms['anuForm']['un'].value,document.forms['anuForm']['dptSelect'].value)"  >OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- modal for user remove confirmation-->

<div id="myModal6" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Are you sure you want to remove the selected User? </h4>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default" onclick="removeSelectedUser()"  data-dismiss="modal" >Confirm</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>