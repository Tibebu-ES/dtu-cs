<!doctype html>
<?php
    define("USER_TYPE_NORMAL", 3);
    define("USER_TYPE_SCHEDULER", 2);
    define("USER_TYPE_ADMIN", 1);

    define("CS_USER_NAME","csUserName" );
    define("CS_USER_DEPARTMENT","csUserDepartment" );
    define("CS_USER_TYPE","csUserType" );

    define("CS_USER_NAME_DFT", "unknown");//defualt user name
    define("CS_USER_DEPARTMENT_DFT", "ECE");// defualt user department

    //start session
    session_start();

    //set session variables to 
    if(!isset($_SESSION[CS_USER_NAME])){
        //set default usrname
        $_SESSION[CS_USER_NAME]=CS_USER_NAME_DFT;
    }else{
        //it is a;ready setted
    }

    if(!isset($_SESSION[CS_USER_DEPARTMENT])){
        //set default userdepartment
         $_SESSION[CS_USER_DEPARTMENT]=CS_USER_DEPARTMENT_DFT;
    }else{
        //it is a;ready setted
    }
    if(!isset($_SESSION[CS_USER_TYPE])){
        //set default usrtyep
         $_SESSION[CS_USER_TYPE]=USER_TYPE_NORMAL;
    }else{
        //it is a;ready setted
    }


?>
<html>
<head>
    <meta charset="utf-8">
    <title>My ClassScheduler -- Home </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="css/HoldOn.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <script src="js/custom.js"></script>
    <script src="js/respond.js"></script>

</head>

<body onload="initiate()">

 <div class="container">
    <!--nav bar-->
    <div class="row">
      <nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
       <div class="navbar-header">
        <a class="navbar-brand" href="index.php">
             <img alt ="MyClassScheduler">
        </a>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
       </div>
       <div class="collapse navbar-collapse" id="collapse">
         <ul class="nav navbar-nav">
          <li class="active"><a href="#">Home</a></li>
          <li><a href="about.php">About</a></li>
      
        </ul>
        
       </div>
      </nav>

    </div>
      <!-- row 1 header-->
    <header class="row" >
      <?php  include "includes/header.php" ?>  
    </header>
     
    <!-- row 2 body normal and scheduler users body-->
    <div class="row prv3">

        <div class="col-md-3" ><!--main  navigation column -->
            <?php  include "includes/navigation.php" ?>      
        </div>
        <div class="col-md-9" ><!-- navigation body -->
            <?php  include "includes/workspace.php" ?>  
        </div>
      
    </div><!-- row 2 body finish--> 

    <!--Admin panel-->
    <div class="row prv1 hidden">
        <!--title-->
        <div class="row"><h1 style="text-decoration-line:underline">SYSTEM ADMIN PANEL</h1></div>
        <!--row1 manage sys account-->
        <div class="row panel panel-danger">
          <div class="panel-heading">
            <a type="button" class=" btn btn-warning btn-xs" onclick="logout()"> <span class="glyphicon glyphicon-log-out" > </span> Logout </a>
            <a type="button" class=" btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal9" > <span class="glyphicon glyphicon-edit" > </span> Edit Account </a>
        </div>
        </div>
        <!-- row2 manage departments and manage users-->
        <div class="row">
            <div class="col col-md-6">
                 <?php  include "includes/manageDpts.php" ?> 
            </div>
            <div class="col col-md-6">
                 <?php  include "includes/manageUsers.php" ?>
            </div>
 
        </div>
    </div> <!--Admin panel-->



   

</div> <!--end of the container-->
  <!-- row  footer -->
    <footer  class="row">
        <?php include "includes/footer.php" ?>     
    </footer>
<!-- javascript -->
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script src="js/jquery-latest.min.js"></script>
  <script src="js/HoldOn.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

</body>
</html>

<!-- modal for changing password-->

<div id="myModal9" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Change <span id="loginStatus"><small> - your password.</small></span> </h4>
      </div>
      <div class="modal-body">
        <form id="cpwAForm" >
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
        <button type="submit" class="btn btn-default" onclick="changeMyPword(document.forms['cpwAForm']['opw'].value,document.forms['cpwAForm']['npw'].value)"  >OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div><!--document.getElementById('radioHasStreamId')-->
</div>
  
