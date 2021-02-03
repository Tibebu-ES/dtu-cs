<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My ClassScheduler -- About</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <script src="js/custom.js"></script>
    <script src="js/respond.js"></script>

</head>

<body >

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
          <li ><a href="index.php">Home</a></li>
          <li class="active"><a href="#">About</a></li>
          <li><a href="#">Help</a></li>
        </ul>
        
       </div>
      </nav>

    </div>
      <!-- row 1 header-->
    <header class="row" >
      <?php  include "includes/header.php" ?>  
    </header>

     
    <!-- row 2 about the Myclass scheduler-->
    <div style="margin-top: 2em;padding-left:200px; padding-right:200px;" class="row" >

        <div  style="padding-right: 0px"  ><!--about the myscheduler web app and android app -->
          <h2>About MyClassScheduler - MyCS</h2>
          <p>
             MyCS is a class scheduling system that enable the schedules to flow directly from 
             the department heads’ laptop to the student’s cell phone. 
             MyCS  consists of a web application and an android mobile application, the web application enable the department 
             heads to  easily prepare and post class schedules on the internet, 
             the mobile application enable students to access any class schedule 
             they want from the internet and even to save the schedules to their cell phone storage for later use. 
          </p>

          <h2>About MyCS android App</h2>
          <p>
             The MyCS android app enable end users to load their class schedule directly 
             from the system server and to store the loaded schedule to their phone local storage.<br/>
             <a href="download_apk/MyCS.apk">Download the MyCS android App here</a>
          </p> 
          <div class="panel panel-danger">
          <div class="panel-heading">
            <h4 class="panel-title"><a> Screen shoot photos of the App</a> 
            </h4>
          </div>
          <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <img class="img-thumbnail img-responsive" src="img/ap/ss1.JPEG" alt="android app screen shoot 1">
            </div>
            <div class="col-md-4">
              <img class="img-thumbnail img-responsive" src="img/ap/ss2.JPEG" alt="android app screen shoot 2">
            </div>
          </div>   
           <div style="margin-top:20px;"class="row">
            <div class="col-md-4">
              <img class="img-thumbnail img-responsive" src="img/ap/ss3.JPEG" alt="android app screen shoot 3">
            </div>
            <div class="col-md-4">
              <img class="img-thumbnail img-responsive" src="img/ap/ss4.JPEG" alt="android app screen shoot 4">
            </div>
            <div class="col-md-4">
              <img class="img-thumbnail img-responsive" src="img/ap/ss5.JPEG" alt="android app screen shoot 5">
            </div>
          </div> 
          </div>
          </div> 

            <div style="padding-top:20px ;"  ><!-- give me feedback or report an error and contact me-->
              <div class="panel panel-danger">
                  <div class="panel-heading">
                     <h4 class="panel-title "><a >Give Me Feedback </a> </h4> 
                  </div>
                  <div class="panel-body">
                      <form id="comment_form">
                           <textarea class="form-control" rows="5" id="comment"></textarea>
                      </form>
                  </div>
                  <div class="panel-footer">
                     <button type="submit" style ="color:#FFFFFF" class="btn btn-default" onclick="commentSave(document.forms['comment_form']['comment'].value)"> Send</button>
                 </div>
             </div>
        </div>
        </div>
      
      
    </div><!-- row 2 body finish--> 



 

</div> <!--end of the container-->
  <!-- row  footer -->
    <footer  class="row">
        <?php include "includes/footer.php" ?>     
    </footer>
<!-- javascript -->
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="js/jquery-latest.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
