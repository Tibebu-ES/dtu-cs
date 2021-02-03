<div id ="workingSpace_" class="panel panel-danger">
	<div class="panel-heading">
		<div class="panel-title">
			<!--breadcrumbs-->
			<ol class="breadcrumb">
				<li ><a >Year-<span id="bcYos"></span></a></li>
				<li ><a ><span id="bcSec"></span></a></li>
				<li ><a id="bcDay">Monday</a></li>
				<li ><a >Period-<span id="bcPrd"></span> <span class="glyphicon glyphicon-time"></span> <span id="bcprdTime"><small> </small></span> </a> </li>
			</ol>		
		</div>	
	</div>
    <div class="panel-body">
		<div style="padding: 0px 0px 0px;"> 
		   <form id="prdForm" role="form"> 
		      <div class="input-group"> 
		         <span class="input-group-addon">Course Name</span> 
		         <input id="courseName" type="text" onkeyup="showHint(this.value,1)" class="form-control" required  style="color: #336600;font-weight:bold; text-align:center"> 
		      </div><br> 
		 		<span id="txtHint" style="text-align:center;" ></span> 
		      <div class="input-group"> 
		         <span class="input-group-addon">Instructor Name</span> 
		         <input id="instructorName" type="text" onkeyup="showHint2(this.value,2)" class="form-control" required style="color: #336600;font-weight:bold; text-align:center"> 
		      </div><br> 
		        <span id="txtHint2" style="text-align:center;" ></span> 
		      <div class="input-group"> 
		         <span class="input-group-addon">Class Room</span> 
		         <input id="room" type="text" class="form-control" style="color: #336600;font-weight:bold; text-align:center"> 
		      </div>
		   </form> 
		</div> 
    </div><!--panel-body-->

</div>

<div class="prv2 hidden" >
   <a onclick="addNewPeriod(document.forms['prdForm']['courseName'].value,document.forms['prdForm']['instructorName'].value,document.forms['prdForm']['room'].value)" style="float:right;color:#FFFFFF" type="button" class=" btn btn-warning btn-xs saveSchedule" > <span class="glyphicon glyphicon-save-file"></span> Save schedule</a>
</div>