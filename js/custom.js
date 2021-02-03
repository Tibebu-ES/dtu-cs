
//my variables 
var xmlDoc;  //XML DOM 
var activeDepartment,activeYosElement,activeSecElement,activeDayElement,activePrdElement; //xmlnodes
var activeSecTab,activeDayTab,activePrdTab; //html elements
	//variable , array to hold user info , like username usertype, userdepartment
var user = {userName:"", usertype:0, userDepartment:""};

//related with admin panel
var selectedUser; //holds the selected username in the admin panel 
var selectedDpt;  //holds the selected department

//hollds if the user make changes on the datamodel and schedule, 
var madeChanges=false;
var scheduleChanged=false; //flag for changes made on the 




//constants
	//tobe implemented
//'-1' FOR THE SAME YOS,SEC,DAY, OR PERIOD
//'-1' CONTEXT FOR SAME CONTEXT OR INITIALLY LOADING THE PAGE 

var USER_TYPE_NORMAL =3;
var USER_TYPE_SCHEDULER = 2;
var USER_TYPE_ADMIN =1;

//time span for periods
var PERIOD_1_T ="2:10 - 4:00";
var PERIOD_2_T ="4:10 - 6:00";
var PERIOD_3_T ="8:00 - 10:50";



//constant related to xhhtprequest types
 var XHTTP_REQUEST_TYPE_USERINFO =1;
 var XHTTP_REQUEST_TYPE_DPTSCHEDULE =2;
 var XHTTP_REQUEST_TYPE_AUTHENTICATE =3;
 var XHTTP_REQUEST_TYPE_LOGOUT =4;
 var XHTTP_REQUEST_TYPE_SELECTDEPARTMENT =5;
 var XHTTP_REQUEST_TYPE_CHANGEPW =6;
 var XHTTP_REQUEST_TYPE_GETUSERS =7;
 var XHTTP_REQUEST_TYPE_ADDUSER =8;
 var XHTTP_REQUEST_TYPE_GETDEPARTMENTS =9; //used in the addnewuser modal and select tag of departments list, homepage
 var XHTTP_REQUEST_TYPE_REMOVEUSER=10;
 var XHTTP_REQUEST_TYPE_GETDEPARTMENTS_FORADMIN =11;
 var XHTTP_REQUEST_TYPE_ADDDEPARTMENT =12;
 var XHTTP_REQUEST_TYPE_REMOVEDEPARTMENT =13;
 var XHTTP_REQUEST_TYPE_GETINSTRUCTORS =14;
 var XHTTP_REQUEST_TYPE_ADDINSTRUCTOR =15;
 var XHTTP_REQUEST_TYPE_REMOVEINSTRUCTOR =16;
 var XHTTP_REQUEST_TYPE_GETCOURSES=17;
 var XHTTP_REQUEST_TYPE_ADDCOURSE = 18;
 var XHTTP_REQUEST_TYPE_REMOVECOURSE =19;

 

function initiate(){

	//modify the view based on the usertype
	modifyView();

	//when the modal2 show set the initial state of the radio buttons
	$("#myModal2").on('shown.bs.modal',function (e){
		$(".radioHasNoStream").trigger('click');
	});


	//when the addnew sec modal init or populate available streams as option/combobox
	$("#myModal").on('shown.bs.modal',function (e){
		var strmSelects="";
		var allSecs = activeYosElement.getElementsByTagName("sec");
		for (var i = 0; i < allSecs.length; i++) 
		{
			if(allSecs[i]=='-'){
				strmSelects="<option>"+"No stream"+"</option";
				break;
			}else
				strmSelects+="<option>"+allSecs[i].getAttribute("stream")+"</option>";
		};

		document.getElementById("strmSelect").innerHTML=strmSelects;
	});

	//when the addnew user modal init or populate available departments as option/combobox
	$("#myModal5").on('shown.bs.modal',function (e){
		//request for departments list
		var dptSelects="";
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange=function()
		{
			if(xhttp.readyState==4 && xhttp.status==200)
			{
				var dpts = xhttp.responseText;
				document.getElementById("dptSelect").innerHTML=dpts;
			}
		}

		var params= "requestType="+XHTTP_REQUEST_TYPE_GETDEPARTMENTS;

		xhttp.open("POST","datamodel/xhttpHandler.php");
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	xhttp.send(params); //request for departments list

	
	});

	//when the edit instructo modal init or populate list of instructors
	$("#myModal10").on('shown.bs.modal',function (e){
		//request for instructors list
		showInstructorsList();	
	});


	//when  the edit courses modal init populate list of courses
	$('#myModal11').on('shown.bs.modal',function(e){
		showCoursesList();
	});


	//when  the show class info modal views
	$('#myModal12').on('shown.bs.modal',function(e){
		showClassInfo();
	});

		//when the modal3 show set readonly property of inputs false
	$("#myModal3").on('shown.bs.modal',function (e){
		$("#uName").attr('readonly',false);
		$("#pWord").attr('readonly',false);
	});


}


//makehttprequest
function requestXML(userDepartment){
	if(typeof userDepartment == "undefined" || userDepartment == ""){
		//window.alert("No need to load schedule!");
		return;
	}
	try{
		//window.alert("boom");
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function()
			{
				if(xhttp.readyState==4 && xhttp.status==200){
					var xmlStr = xhttp.responseText;

					//window.alert(xmlStr);
					//change to xmldoc
					parser = new DOMParser();
 					xmlDoc = parser.parseFromString(xmlStr,"text/xml");
					//window.alert((new serializeToString));
	    			setActiveDepartment(xmlDoc.documentElement.getAttribute("value"));
					//initially show schedule for yos=1, sec=1, day=1, prd=1;
                  	showSchedule("1","-","1","monday","1","-1");  //"-1" nocontext
                  	//construct the html views based on the xml data model
                  	constructView(xmlDoc);
				 
					//myparser(xhttp);
				}else if(xhttp.status==404){
					//file not found
					document.getElementById("room").placeholder="file Not found";
				}
				closeHoldon();
			};

   var params= "requestType="+XHTTP_REQUEST_TYPE_DPTSCHEDULE+"&dpt="+userDepartment;
   //window.alert(params);
   if(user.userDepartment==USER_TYPE_NORMAL){ //use get methode
   		xhttp.open("GET","datamodel/xhttpHandler.php?"+params,true);
   		xhttp.send(); //request for dataschedule
   }else
   { //use postmethod
   		xhttp.open("POST","datamodel/xhttpHandler.php");
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	xhttp.send(params); //request for dataschedule
   }
	openHoldon();
	}

	catch(err){
		window.alert("error"+err.message);
	}
	
}




//function that show the schedule for the requested period

function showSchedule(yosV,stream,secV,dayV,prdV,context){//context html element that request the schedule
	var course="undefined",instructor="undifined",room="undifined";
		//get the elements
    var yosElement,secElement,dayElement,prdElement;

    if(yosV=="-1")
    	yosElement=activeYosElement;
    else
		yosElement=getYosElement(yosV); //get the yos element with the value attribute yosV
	if(secV=="-1")
		secElement=activeSecElement;
	else
		secElement =getSecElement(yosElement,stream,secV,context); //get sec element with value attribute of secV on the given yosElement element
	if((secV=="-1") && (dayV =="-1"))
		dayElement=activeDayElement;
	else
		dayElement =getDayElement(secElement,dayV,context); //get day element with value attribute of dayV on the given secElement
	prdElement =getPrdElement(dayElement,prdV,context); //get period element with value attribute of prdV on the given dayElement
	
	if(typeof prdElement != 'undefined'){
		course=prdElement.getElementsByTagName("cName")[0].childNodes[0].nodeValue;
		instructor=prdElement.getElementsByTagName("iName")[0].childNodes[0].nodeValue;
		room=prdElement.getElementsByTagName("room")[0].childNodes[0].nodeValue;
	}
	

	//show the schedule
	//set the perioddetail value on he page
	//replace : with &  // in the datamodel ':' corresponds to & in the presentation //motive '&' cause error in the xmlparser
	document.getElementById("courseName").value=course.replace(":","&");
	document.getElementById("instructorName").value=instructor.replace(":","&");
	document.getElementById("room").value=room.replace(":","&");

	
	
	
	
}

	



 function getYosElement(yosV){
 	var allYos = xmlDoc.getElementsByTagName("yos");
 	//window.alert("yos length "+ allYos.length);
 	for (var i = allYos.length - 1; i >= 0; i--) {
 		//window.alert(allYos.nodeName);
 		 if(allYos[i].getAttribute("value")==yosV){
 		 	//update the activeYosElement
 		 	setActiveYosElement(allYos[i]);
 		 	return allYos[i];
 		 }
 	};

 	//return undefined;
 }

//get sec element with value attribute of secV on the given yosElement element

function getSecElement(yosElement,stream,secV,context){
    if(typeof yosElement == 'undefined'){
    	window.alert("undefined yos")
    	return undefined;
    }
    
    	
    //window.alert("nodename :"+yosElement.nodeName;
	var allSec = yosElement.getElementsByTagName("sec");
	for (var i = allSec.length - 1; i >= 0; i--) {

		if(allSec[i].getAttribute("value")==secV && allSec[i].getAttribute("stream")==stream){
			//window.alert("sec1 with stream - elemnt found");
			setActiveSecElement(allSec[i],context)
			return allSec[i];
		}
	};

	//return undefined;
}

//get day element with value attribute of dayV on the given secElement

function getDayElement(secElement,dayV,context){
	if(typeof secElement == 'undefined'){
		window.alert("getDayElement"+ "undefined sec");
		return undefined;
	}
	//check dayV if it is for the same dayV
	if(dayV=="-1"){
		dayV=activeDayElement.getAttribute("value");//if so get the activeDayElement dayV
		context="-1"; // means use the previous context/tab
	}
	
	//window.alert("getDayElement"+ "before "+secElement.childNodes[0].nodeName;

	var allDays = secElement.getElementsByTagName("day");
	for (var i = allDays.length - 1; i >= 0; i--) {
		if(allDays[i].getAttribute("value")==dayV){
			//update the activeDayElement
			setActiveDayElement(allDays[i],context);
			return allDays[i];
		}
	};
	//return undefined;
}

//get period element with value attribute of prdV on the given dayElement
function getPrdElement(dayElement,prdV,context){
	if (typeof dayElement == 'undefined') 
		 return undefined;
	//check prdV if it is for the same prdV
	if(prdV=="-1"){
		prdV=activePrdElement.getAttribute("value");//if so get the activePrdElement prdV
		context="-1"; // means use the previous context/tab
	}
		
	var allPrds = dayElement.getElementsByTagName("period");
	for (var i = allPrds.length - 1; i >= 0; i--) {
		if(allPrds[i].getAttribute("value")==prdV){
			//update activePrdElement
			setActivePrdElement(allPrds[i],context);
			return allPrds[i];
		}
	};

	//return undefined;
}

//setters for active elements

function setActiveYosElement(aYosE){
	activeYosElement=aYosE;
	$("#bcYos").text(activeYosElement.getAttribute("value"));
	//set active tab 
}

function setActiveSecElement(aSecE,context){
	
	//set active tab 
	//using jquey to set the active tab
     //if(context!="-1") //if contest =-1 then it is the first time to load so no need to set active tab default is fine
			
		if(context=="-1") 
			{
				//case 1 when it is loading
				//case 2 from the year selection panel
				var temp = (typeof activeSecElement);
				if( temp == 'undefined')
				{
					//first case
				}else
				{
					//window.alert("myCase");
					
					//second case
					//find id of the previous activeSectab by iterating through all the secs in that yos
					var yosV= activeYosElement.getAttribute("value");
					var allSecs = activeYosElement.getElementsByTagName("sec");
					for(var i=0;i<allSecs.length;i++){
						var strm = allSecs[i].getAttribute("stream");
						var secV =allSecs[i].getAttribute("value");
						var activeSecTabId= "sec_"+secV+"_yos_"+yosV+"_strm_"+strm; //construct the previous activesectab
						//window.alert(activeSecTabId);
						$("#"+activeSecTabId).removeClass('active');//1st find the active tab and remove the active class

					}
					
					//in this case the context is the first sec of year yosV
					var strm= allSecs[0].getAttribute("stream");
					var id="sec_1_yos_"+yosV+"_strm_"+strm;
					$("#"+id).addClass('active'); //3rd add the active class to the context
					
					
				}
             
			
			}
			else{
					//if it is not first time
				$(document).ready(function(){
					//find id of the previous activeSectab
					var yosV= activeYosElement.getAttribute("value");
					var secV= activeSecElement.getAttribute("value");
					var strm= activeSecElement.getAttribute("stream");

					var activeSecTabId= "sec_"+secV+"_yos_"+yosV+"_strm_"+strm; //construct the previous activesectab
					$("#"+activeSecTabId).removeClass('active');//1st find the active tab and remove the active class

					$(context).addClass('active'); //3rd add the active class to the context
					
					
				});
			}
	
    activeSecElement=aSecE;
    //set breadcrumb
    $("#bcSec").text(activeSecElement.getAttribute("stream")+"Sec-"+activeSecElement.getAttribute("value"));
  
}
function setActiveDayElement(aDayE,context){
	activeDayElement=aDayE;
	//set breadcrumb
	$("#bcDay").text(activeDayElement.getAttribute("value"));

	//using jquey to set the active tab
     if(context!="-1") //if contest =-1 then it is the first time to load so no need to set active tab default is fine
			//if it is not first time		
			$(document).ready(function(){
				$("#activeDayTab").removeClass('active');//1st find the active tab and remove the active class
				$("#activeDayTab").attr("id"," ");//2ndset the id to null

				$(context).addClass('active'); //3rd add the active class to the context
				$(context).attr("id","activeDayTab");//4th set its id to "activePrdTab" important to identify the previous active tab
				
			});

}

function setActivePrdElement(aPrdE,context){
	if(!(typeof activePrdElement == "undefined") && user.userType==USER_TYPE_SCHEDULER)//if not first time setting the activeperiod and user is scheduler
	{
		//before leaving the period check if the user change the schedule if so request to save changes
		//chnge "&" to ":" since we are comparing this to the previos value which is in the datamodel,
		var cnameField = ($("#courseName").val()).replace('&',':');
		var inameField=  ($("#instructorName").val()).replace('&',':');
		var room = ($("#room").val()).replace('&',':');
		var message="Do you want to save changes you made?";

		//previous values
		var pcourse=activePrdElement.getElementsByTagName("cName")[0].childNodes[0].nodeValue;
		var pinstructor=activePrdElement.getElementsByTagName("iName")[0].childNodes[0].nodeValue;
		var proom=activePrdElement.getElementsByTagName("room")[0].childNodes[0].nodeValue;
		//window.alert(cnameField+"=="+pcourse+" ,"+inameField+"=="+pinstructor+" ,"+room+"=="+proom);
		//check for changes
		if(cnameField!=pcourse || inameField!=pinstructor || room != proom){
			//request user to save the schedule change
			var result = window.confirm(message);
			if(result==true){
				//save schedule change // programmatically click the save schedulebutton
				//window.alert("ready to save schedule");
				$(".saveSchedule").trigger('click');
			}
		}

	}

	

	//update the activeprdelement
	activePrdElement=aPrdE;
	//set breadcrumb
	var prdN=activePrdElement.getAttribute("value");
	$("#bcPrd").text(prdN);
	if(prdN=="1")
		$("#bcprdTime").text(PERIOD_1_T);
	else if(prdN=="2")
		$("#bcprdTime").text(PERIOD_2_T);
	else
		$("#bcprdTime").text(PERIOD_3_T);
	
	//set active tab
	//using jquey to set the active tab
     if(context!="-1") //if contest =-1 then it is the first time to load so no need to set active tab default is fine
			//if it is not first time	
			$(document).ready(function(){
				$("#activePrdTab").removeClass('active');//1st find the active tab and remove the active class
				$("#activePrdTab").attr("id"," ");//2ndset the id to null

				$(context).addClass('active'); //3rd add the active class to the context
				$(context).attr("id","activePrdTab");//4th set its id to "activePrdTab" important to identify the previous active tab
				
			});

			
}

function setActiveDepartment(depName){
	activeDepartment=depName;

	$(document).ready(function(){
	
			$("#departments").val(activeDepartment);
	});
	//select option

}


//construct view

function constructView(xmlDoc){
	//window.alert("constructing");
	//get all yos
	var allYos = xmlDoc.getElementsByTagName("yos");
	//iterate through all yos and construct each yos views
	//accrding to the data model order// starting from the first
	for (var i = 0; i < allYos.length; i++) {

		var yosElement = allYos[i];

		//construct the yos container
		if(i>0){ //since the first yos view is already constructed //construct yos other than the first year
			//code goes here
			var sampleYrPanel = document.getElementById("panelYR1");// use first year panel view as sample and modify it
			constructYrPanel(sampleYrPanel,yosElement);
		}

		//construct each sec views in each yos
		var allSecs = yosElement.getElementsByTagName("sec"); //get all secs 
		for (var j = 0; j < allSecs.length; j++) {
			var secElement = allSecs[j];
			//construct the sec view other than the first year first sec, b/c it is constructed bu default
			if(!(j==0 && i==0)){
				//construct view 
				constructSecView(yosElement,secElement);	
			
			}
		};

	};
}

function constructYrPanel(sampleYrPanel,yosElement){
	var newYrPanel = $(sampleYrPanel).clone();
	var yosV=yosElement.getAttribute("value");
	//change the id of the panel
	var panelid = "panelYR"+yosV;
	$(newYrPanel).attr("id",panelid);
	//change the panel-headingid
	var  panelHeadingId = "headingYR"+yosV;


	//change the panel-body id
	var panelBodyId ="collapseYR"+yosV;

	//decompose the panel in to child elements
	var childes = $(newYrPanel).children();
	var panelHeading=childes[0];
	$(panelHeading).attr("id",panelHeadingId);

	var headAtag = ($(($(panelHeading).children())[0]).children())[0];
	//"showSchedule("+"'"+yosV+"',"+"'"+secV+"',"+"'-1"+"',"+"'-1"+"',"+"this)"
	//bug with yrs that has stream //no way to know stram
	//determine the first sec stream value
	var defaultStrm =(yosElement.getElementsByTagName("sec")[0]).getAttribute("stream");
	var onClickvalue = "showSchedule('"+yosV+"','"+defaultStrm+"','1','-1','-1','-1')" ;
	$(headAtag).attr("onclick",onClickvalue); 
	$(headAtag).attr("href","#"+panelBodyId);
	$(headAtag).text("YEAR-"+yosV);


	var panelBody =childes[1]; 
	$(panelBody).attr("id",panelBodyId);
	$(panelBody).removeClass("in");
	var ul_li_element=$($(panelBody).children()[0]).children()[0];
	$(ul_li_element).attr("id","ul_li_yr_"+yosV);//change secview container
	$(ul_li_element).empty(); //remove the sec views

	
	//get the last yos view and append the new yos view
	$($($(sampleYrPanel).parent()).last()).append(newYrPanel); 
}


function constructSecView(yosElement,secElement){

	//get the last sec view
				//first get the direct parent view of sections in this yos view
				var yosV =yosElement.getAttribute("value");
				var ul_li_Id="ul_li_yr_"+yosV; //
				var lastSecView = $("#"+ul_li_Id).last(); 
				
				
				var secV =secElement.getAttribute("value");
				var stream=secElement.getAttribute("stream");
				var newId = "sec_"+secV+"_yos_"+yosV+"_strm_"+stream;
				var onClickvalue = "showSchedule("+"'"+yosV+"',"+"'"+stream+"',"+"'"+secV+"',"+"'-1"+"',"+"'-1"+"',"+"this)";
				var hrefValue="#workingSpace";
				var view="<li id='"+newId+"'"+" onclick="+onClickvalue+"><a href='"+hrefValue+"'"+ ">"+stream+" SEC-"+secV+" </a></li> "
				$(lastSecView).append(view);

}

//creates new Yos NodeElement in the datamodel and create the corresponding view
function addNewYos(yosV,strmNames,yosType){//by default create a single sec of value 1 if hasStream is false
	//validate input
	var yosvFilled = document.getElementById("yosv");
	if(yosvFilled.checkValidity()==false){
		window.alert(yosvFilled.validationMessage);
		return;
	}
	//check if the year of study already exists in the deparment

	var allYoss = xmlDoc.documentElement.getElementsByTagName("yos");
	for(var i=0 ;i <allYoss.length;i++){
		var c_yosV = allYoss[i].getAttribute("value");
		if(c_yosV== yosV){
			window.alert("The year of study already exists.");
			return;
		}
	}

	try{
		var hasStream=false;
		var strmNamesAry;
		if(yosType=="has stream")//user selects has stream//yos has stream
		{
			hasStream=true;
			//get stream names
			if(strmNames==""){
				window.alert("please give the name of streams");
				return;
			}
			strmNamesAry=strmNames.split(",");
			//window.alert(strmNamesAry.length+strmNamesAry[0]);
			//
		}
		else if(yosType=="has no stream"){
			hasStream=false;
		}
			
		
		//window.alert("boom");
		var xhttp2 = new XMLHttpRequest();
		xhttp2.onreadystatechange = function()
			{
				if(xhttp2.readyState==4 && xhttp2.status==200)
				{
					
					var xmlDoc2=xhttp2.responseXML;
					var newYos =xmlDoc2.documentElement.cloneNode(true);
					newYos.setAttribute("value",yosV);

					//construct the view
					var sampleYrPanel = document.getElementById("panelYR1");// use first year panel view as sample and modify it
					//close the modal
					//$("#myModal2").modal('hide');
					if(hasStream)
					{
						//create streams
						for (var i = 0; i < strmNamesAry.length; i++) 
						{
							if(i==0){
								newYos.getElementsByTagName("sec")[0].setAttribute("stream",strmNamesAry[i]);
								//construct the yrpanel
								constructYrPanel(sampleYrPanel,newYos);
								//construct the first sec view
								constructSecView(newYos,newYos.getElementsByTagName("sec")[0]);
							}
							else{
								var newSec = newYos.getElementsByTagName("sec")[0].cloneNode(true);
								//set the stream
								newSec.setAttribute("stream",strmNamesAry[i]);
								//construct the secview
								constructSecView(newYos,newSec);
								//append the sec
								newYos.appendChild(newSec);
							}		
						};
						
					}else{//if yos has no stream create default sec
						//construct hte yr panel
						constructYrPanel(sampleYrPanel,newYos);
						var newSecElement = newYos.getElementsByTagName("sec")[0];
						constructSecView(newYos,newSecElement);
					}
					

					//append to main model
					xmlDoc.documentElement.appendChild(newYos);
					//update madeChanges flag
					madeChanges = true;  //turn flag on when user made changes, to remind saving the changes he made
					
					closeHoldon();
				}
				else if(xhttp2.status==404)
				{
					//file not found
					window.alert("Yossample model file Not found");
				}
			};

		xhttp2.open("GET","datamodel/yosModel.xml",true);
		xhttp2.send();
		openHoldon();
	}

	catch(err){
		window.alert("error"+err.message);
	}

}



//add new sec of given value on the active  yosElement

function addNewSec(secV,stream){
//validate input
	var secvFilled = document.getElementById("secv");
	if(secvFilled.checkValidity()==false){
		window.alert(secvFilled.validationMessage);
		return;
	}

   try{
		//get the corresponding yosElement 
		//var yosElement=activeYosElement;
		//window.alert(stream);
		var yosElement;
		var yosV;
		if(typeof activeYosElement == "undefined"){
			window.alert("Error undefined activeYosElement on adding new sec");
			return;
			//throw error // means of handling this error
		}else{
			//window.alert("activeYosElement ="+activeYosElement.getAttribute("value"));
			yosElement=activeYosElement;
			//check if the new secvale, and stream is already created
				//iterate through all secs
			var secs = yosElement.getElementsByTagName("sec");
			for(var i= 0;i<secs.length;i++){
				var c_secV = secs[i].getAttribute("value"); //current sec value
				var c_stream = secs[i].getAttribute("stream");
				if(c_secV==secV && c_stream==stream){
					window.alert("The section already exists.");
					return;
				}
			
			}
		
		}	

	//get the sample yos model then get the sample sec
	
		//window.alert("boom");
		var xhttp2 = new XMLHttpRequest();
		xhttp2.onreadystatechange = function()
			{
				if(xhttp2.readyState==4 && xhttp2.status==200)
				{
					//update madeChanges flag
					madeChanges = true;  //turn flag on when user made changes, to remind saving the changes he made
					var xmlDoc2=xhttp2.responseXML;
					var newSec =xmlDoc2.getElementsByTagName("sec")[0].cloneNode(true);
					//var newSec =xmlDoc2.documentElement.childNodes[0].cloneNode(true);
					newSec.setAttribute("value",secV);
					newSec.setAttribute("stream",stream);

					//append to yosElement
					yosElement.appendChild(newSec);
					//construct the sec view
					
					constructSecView(yosElement,newSec);
					//close the modal
					//$("#myModal").modal('hide');

				
					closeHoldon();
				}
				else if(xhttp2.status==404)
				{
					//file not found
					window.alert("Yossample model file Not found");
				}
			};

		xhttp2.open("GET","datamodel/yosModel.xml",true);
		xhttp2.send();
		openHoldon();
	}

	catch(err){
		window.alert("error"+err.message);
	}

}

//add new period on the active period
function addNewPeriod(cName,iName,room){
	//validate the inputs 
	var cinput =document.getElementById("courseName");
	var iinput =document.getElementById("instructorName");

	if(cinput.checkValidity()==false){
		//todo validate for htmlspecialcharacters
		$("#txtHint").text(cinput.validationMessage);
		return;
	}
	if(iinput.checkValidity()==false){
		$("#txtHint2").text(iinput.validationMessage);
		return;
	}

	//window.alert(cName+iName+room);
	  //check for ':' char, it is reserved
	if(cName.search(':') >= 0 || iName.search(':') >= 0 || room.search(':') >= 0 ){
		$("#txtHint").text("':' not allowed ");
		return;
	}

	//since room is optional
	if(typeof room=="undefined" || room=="")
		room="None";
    //replace '&' with ':' because '&' in presentation cooresponds to ':' in the datamodel
    cName=cName.replace('&',':');
    iName=iName.replace('&',':');
    room=room.replace('&',':');

	//cName =encodeURIComponent(cName);
	//iName=encodeURIComponent(iName);
	//window.alert(cName+"  "+iName+"  "+room);
	if(!(cName == null || cName == ""))
	{
		activePrdElement.getElementsByTagName("cName")[0].childNodes[0].nodeValue=cName;
		if(!(iName == null || iName == "")){
			activePrdElement.getElementsByTagName("iName")[0].childNodes[0].nodeValue=iName;

		}
			
		if(!(room == null || room == "")){
			activePrdElement.getElementsByTagName("room")[0].childNodes[0].nodeValue=room;
		}
		
		madeChanges=true; //to remind user to save the changed schedule
		window.alert("Period saved succesfully");
			
	}
	

	/*window.alert(activePrdElement.getElementsByTagName("cName")[0].childNodes[0].nodeValue+
		activePrdElement.getElementsByTagName("iName")[0].childNodes[0].nodeValue+
		activePrdElement.getElementsByTagName("room")[0].childNodes[0].nodeValue)
  */
  /*
  $(document).ready(function(){
     $("#prdTime2").text((new XMLSerializer()).serializeToString(xmlDoc));
 }); 
  */
}

//save the modified data model on the web server
function saveChanges(){
	try
	{
		var xmlString = (new XMLSerializer()).serializeToString(xmlDoc);
		//window.alert(xmlString);
		var xhttp2 = new XMLHttpRequest();
		xhttp2.onreadystatechange = function()
			{
				if(xhttp2.readyState==4 && xhttp2.status==200)
				{
					//update the xmlDoc
					//window.alert(xhttp2.responseText);
					if(xhttp2.responseText=="true"){
						//update madeChanges flag
						madeChanges = false;  //turn flag off when user save changes, 
						window.alert("Change Saved succesfully!");
					}else{
						window.alert("Change not saved: "+xhttp2.responseText);
					}
					
					closeHoldon();
				}
				else if(xhttp2.status==404)
				{
					//file not found
					window.alert("xml file Not found");
				}
			};

		var params= "data="+xmlString+"&dpt="+user.userDepartment;	
		xhttp2.open("POST","datamodel/updateDataModel.php",true);
		xhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
		xhttp2.send(params);
		openHoldon();
 
	}

	catch(err){
		window.alert("error"+err.message);
	}


}


// AJAX // showHint for course names of the activedepartment
//hintfor = 1 for courses and 2 for insructor name
function showHint(str,hintFor){
	 if (str.length == 0) { 
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else {
    	//if & is in the str, then start looking after '&'
    	if(str.search('&')>0){
    		str=str.slice((str.search('&'))+1);
    		//trim white space
    		str=str.trim();
    	}
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
            }
        };
        var yos=1 ;
        if(typeof activeYosElement !="undefined")
                yos=activeYosElement.getAttribute("value");

        xmlhttp.open("GET", "datamodel/gethint.php?key=" + str+"&dpt="+activeDepartment+"&yos="+yos+"&hintfor="+hintFor, true);
        xmlhttp.send();
    }
}

//function that set selected hint text to course name input field and clear hints
function setHint(context){
	//if there is & in the field// then append after the & char
	var strV =document.getElementById("courseName").value;
	if(strV.search('&')>0){
		document.getElementById("courseName").value=strV.slice(0,strV.search('&')+1)+" "+context.innerHTML;
	}else{
		document.getElementById("courseName").value=context.innerHTML;
	}
	document.getElementById("txtHint").innerHTML="";
}

//hint for instructors
function showHint2(str,hintFor){
	 if (str.length == 0) { 
        document.getElementById("txtHint2").innerHTML = "";
        return;
    } else {
    	//if & is in the str, then start looking after '&'
    	if(str.search('&')>0){
    		str=str.slice((str.search('&'))+1);
    		//trim white space
    		str=str.trim();
    	}
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint2").innerHTML = xmlhttp.responseText;
            }
        };
        var yos=1 ;
        if(typeof activeYosElement !="undefined")
                yos=activeYosElement.getAttribute("value");

        xmlhttp.open("GET", "datamodel/gethint.php?key=" + str+"&dpt="+activeDepartment+"&yos="+yos+"&hintfor="+hintFor, true);
        xmlhttp.send();
    }
}

//function that set selected hint text to course name input field and clear hints
function setHint2(context){
	//if there is & in the field// then append after the & char
	var strV =document.getElementById("instructorName").value;
	if(strV.search('&')>0){
		document.getElementById("instructorName").value=strV.slice(0,strV.search('&')+1)+"  "+context.innerHTML;
	}else{
		document.getElementById("instructorName").value=context.innerHTML;
	}
	document.getElementById("txtHint2").innerHTML="";
}

//flip the state of the Stream mames input based on the radios state 
function modal2InputChanged(radio){
	var checked = "hasStream";
	if(!(radio.checked && radio.value=="has stream"))
		checked="hasnostream";

	//if hasnostream disable the stream names input
	if(checked=="hasnostream"){
		$("#strmNamesDiv").addClass("hidden");
	}else{
		$("#strmNamesDiv").removeClass("hidden");
	}


	//window.alert(checked);
}


//modify the view based on the user type
function modifyView(){
try
{
	//request the server for usertype
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

     xhttp.onreadystatechange=function(){
     	if(xhttp.readyState==4 && xhttp.status==200)
     	{
     		var response=xhttp.responseText; //phphandler sends string of username,usertype,userdepartment each separated by comma
     		//window.alert(response);
     		//convert string into array
     		var userInfoAry=response.split(",");// 
     		//window.alert("userType="+userInfoAry[1]);
     		//check if the usertype is changed, if so modify the view accordingly
     		//set user info
     		user.userName=userInfoAry[0];
     		
     		user.userDepartment=userInfoAry[2];
     		if(user.userType!=userInfoAry[1])
     		{
     			//window.alert("usertype not equal");
     			user.userType=Number(userInfoAry[1]);
     			//window.alert(user.userType+" "+USER_TYPE_NORMAL+" "+USER_TYPE_SCHEDULER+" "+USER_TYPE_ADMIN);
     			switch(user.userType)
     			{

					case USER_TYPE_NORMAL:
						//code goes here
						viewForNormalUser();
						break;
					case USER_TYPE_SCHEDULER:
						//code goes here
						viewForSchedulerUser();
						break;
					case USER_TYPE_ADMIN:
						//code goes her
						viewForAdminUser();
						break;

	        	}
     		}

     		  
     		//populate departments list
     		
     		//request for the department datamodel
     		//window.alert(user.userDepartment);
     		requestXML(user.userDepartment);  		
     		closeHoldon();
     	}
     };


     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send("requestType="+XHTTP_REQUEST_TYPE_USERINFO); //request for user info
     openHoldon();

}
catch(err){
	window.alert("Error:modifyView => "+err.message);
}

}



function viewForNormalUser(){
	//window.alert("u r in the viewForNormalUser method");
	//views to hide // those with class prv2 and prv3
	$(document).ready(function(){
		//hide the admin panel and views for scheduler user if vissible
		$(".prv1,.prv2").addClass("hidden");
		//make inputs uneditable
		$("input").attr("readonly",true);
		$("#logout").attr("disabled",true);
		$("#login").attr("disabled",false);
		$("#changePw").attr("disabled",true);
		$("#editIns").attr("disabled",true);
		$("#editCors").attr("disabled",true);
		$("#departments").attr("disabled",false);
		populateDepartmentsList(); //populate the available dpts list
		
	});

}

//populate the departments list

function populateDepartmentsList(){
	//request department lists 

	//POPULATE THE AVAILABLE DEPARTMENTS, 
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange=function()
		{
			if(xhttp.readyState==4 && xhttp.status==200)
			{
				var dpts = xhttp.responseText;
				document.getElementById("departments").innerHTML=dpts;
				setActiveDepartment(user.userDepartment);
				closeHoldon();
			}
		}

		var params= "requestType="+XHTTP_REQUEST_TYPE_GETDEPARTMENTS;

		xhttp.open("POST","datamodel/xhttpHandler.php");
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	xhttp.send(params); //request for departments list
    	openHoldon();
	
}


function viewForSchedulerUser(){
	//window.alert("u r in the viewForNormalUser method");
	//views to hide // those with class prv2 and prv3
	$(document).ready(function(){
		//hide the admin panel if vissible
		$(".prv1").addClass("hidden");
		$(".prv2,.prv3").removeClass("hidden");
		//make inputs editable
		$("input").attr("readonly",false);
		$("#login").attr("disabled",true);
		$("#logout").attr("disabled",false);
		$("#changePw").attr("disabled",false);
		$("#editIns").attr("disabled",false);
		$("#editCors").attr("disabled",false);
		$("#departments").attr("disabled",true);
		populateDepartmentsList(); //populate the available dpts list
	});

}


function viewForAdminUser(){
	//modify the view
	$(document).ready(function(){
		//hide unnecessary views
		$(".prv2,.prv3").addClass("hidden");
		//show the admin panel
		$(".prv1").removeClass("hidden");
		//showuserslist
		showUsers();
		showDepartments();
	});
}

//login 
function login(uname,pword){
try
{
	//request the server for user Authentication
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

     xhttp.onreadystatechange=function(){
     	if(xhttp.readyState==4 && xhttp.status==200)
     	{
     		var response=xhttp.responseText; //phphandler sends true if granted , false if not
     		//window.alert(response);
     		if(response=="true")//if granted
     		{
     			//window.alert(response);
     			//reload page
     			window.location.reload();
     			$("#myModal3").modal('hide');
     		}
     		else{//if not 
     			$("#loginStatus").text("Wrong username & password!");
     			$("#loginStatus").css("color","#FF0000");
     		}

     		closeHoldon();
     	 		
     	}
     };

     var params= "requestType="+XHTTP_REQUEST_TYPE_AUTHENTICATE+"&un="+uname+"&pd="+pword;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); //request for user info
     openHoldon();


}
catch(err){
	window.alert("Error:login => "+err.message);
}

}


function logout(){
	//ask user to save changes if , user made changes
if(madeChanges)//check if the user made changes
{
	var result =window.confirm("Do you want to save changes you made?");
	if(result)
		saveChanges();
}

try
{

	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

     xhttp.onreadystatechange=function(){
     	if(xhttp.readyState==4 && xhttp.status==200)
     	{
     		var response=xhttp.responseText; //phphandler sends true if logout succesfully
     		//window.alert(response);
     		if(response=="true")//if granted
     		{
     			//reload page
     			window.location.reload();
 
     		}
 
     	}
     };

     var params= "requestType="+XHTTP_REQUEST_TYPE_LOGOUT;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); //request for user info


}
catch(err){
	window.alert("Error:logout => "+err.message);
}

}


//select department
function selectDepartment(department){
	//change department
try
{
	//window.alert("selectdpt="+department);

	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

     xhttp.onreadystatechange=function(){
     	if(xhttp.readyState==4 && xhttp.status==200)
     	{
     		var response=xhttp.responseText; //
     		//window.alert(response);
     		if(response=="true")//if granted
     		{
     			//reload page
     			window.location.reload();
 
     		}
     		closeHoldon();
 
     	}
     };

     var params= "requestType="+XHTTP_REQUEST_TYPE_SELECTDEPARTMENT+"&dpt="+department;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); //request for user info
     openHoldon();


}
catch(err){
	window.alert("Error:logout => "+err.message);
}

}


//user change pWord

function changeMyPword(oldPw,newPw){
	//get the old and new pword
		//check for wrong inputs//validate the input
	if(typeof oldPw =="undefined" || oldPw=="" || typeof newPw == "undefined" || newPw==""){
		//wrong input
		//TO Do check other requerments
		window.alert("Wrong Input please fill all the fields correctly.");
		return;
	}
	//request server for pword change
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("new pw="+response);
    		if(response=="true"){
    			//if the request is succesful notify user &display the new pword //server send "true" for succes
    				//close the  modal
    			$("#myModal9").modal('hide');
    			$("#myModal4").modal('hide');
    			window.alert("Your password is changed to "+newPw);
    		}else{
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("Operation is not completed. please insert your old password correctly.");
    		}

    		closeHoldon();
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_CHANGEPW+"&opw="+oldPw+"&npw="+newPw;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
     openHoldon();
	

}


//used in the admin panel , called 'viewForAdmin method'
//show all the users
function showUsers(){
	//request the server
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var usersStr = xhttp.responseText; //response is the list of username and their department, un1->dpt, un2->dpt2,...
    		//remove the last comma
    		var usersStr = usersStr.substr(0,(usersStr.length-1));
    		//window.alert("userslist="+usersStr);
    		//change to Aray
    		var usersAry = usersStr.split(",");
    		//window.alert("num of users="+usersAry.length);
    		var userViews="";
    		//build list view for each useres and set to the parent <ul> element
    		for (var i = 0; i < usersAry.length; i++) {
    			//parse username and department name, they come like un1:dptn1
    			var uname = usersAry[i].slice(0,usersAry[i].indexOf(":"));
    			var department = usersAry[i].slice((usersAry[i].indexOf(":")+1));
    			var cls='';
    			if(i==0){//make the first view active by default
    				cls='active';
    				selectedUser=uname; //set the selected user
    			}

    			var view = "<li class='"+cls+"' id='"+uname+"' onclick='userSelected(this)'><a>"+uname+" / "+department+"</a></li>";

    			userViews+=view;
    		};

    		//set to the parent element
    		$(document).ready(function(){
    			$("#usersList").html(userViews);
    		});
    		
    		closeHoldon();
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_GETUSERS;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
     openHoldon();

}

//used in the admin panel , called 'viewForAdmin method'
//show all the departments
function showDepartments(){
	//request the server
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var dptsStr = xhttp.responseText; //response is the list of departments each separated by comma,...
    		//remove the last comma
    		var dptsStr = dptsStr.substr(0,(dptsStr.length-1));
    		//change to Aray
    		var dptsAry = dptsStr.split(",");
    		//window.alert("num of users="+usersAry.length);
    		var dptsViews="";
    		//build list view for each useres and set to the parent <ul> element
    		for (var i = 0; i < dptsAry.length; i++) {
    			var cls='';
    			if(i==0){//make the first view active by default
    				cls='active';
    				selectedDpt=dptsAry[i]; //set the selected department
    			}

    			var view = "<li class='"+cls+"' id='"+dptsAry[i]+"' onclick='dptSelected(this)'><a>"+dptsAry[i]+"</a></li>";

    			dptsViews+=view;
    		};

    		//set to the parent element
    		$(document).ready(function(){
    			$("#departmentsListAdmin").html(dptsViews);
    		});
    		closeHoldon();	
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_GETDEPARTMENTS_FORADMIN;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
     openHoldon();

}

//add new user, with type=2, and defaultpw ='12345678'

function addNewUser(uname,dpt){

	var un = document.getElementById("un");
	if(un.checkValidity == false){
		$('#un').attr("placeholder",un.validationMessage);
		return;
	}
	if(uname==""){
		$('#un').attr("placeholder","Fill the field please");
		return;
	}
	if(!(/[a-zA-z0-9_]/.test(uname))){
		$('#un').val("");
		$('#un').attr("placeholder","user name cannot contain special characters!");
		return;
	}

	//request server for new user
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("addnewuser="+response);
    		if(response=="true"){
    			//if the request is succesful notify user & reload page  //server send "true" for succes
    				//close the  modal
    			//$("#myModal4").modal('hide');
    			window.alert("new user added succesfully!");
    			//reload the page
    			document.location.reload();
    		}else{
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("user name already exists.");
    		}
    		closeHoldon();
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_ADDUSER+"&un="+uname+"&dpt="+dpt;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
     openHoldon();
	

}


function  addNewDepartment(dpt){

	var dn = document.getElementById("dn");
	if(dn.checkValidity == false){
		$('#dn').val("");
		$('#dn').attr("placeholder",dn.validationMessage);
		return;
	}
	if(!(/[a-zA-z0-9\-_]/.test(dpt))){
		$('#dn').val("");
		$('#dn').attr("placeholder","department name cannot contain special characters");
		return;
	}
	if(dpt==""){
		$('#dn').attr("placeholder","please fill this filled");
		return;
	}
	dpt= dpt.trim();

	//request server for new user
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("addnewuser="+response);
    		if(response=="true"){
    			//if the request is succesful notify user & reload page  //server send "true" for succes
    				//close the  modal
    			//$("#myModal4").modal('hide');
    			window.alert("new Department added succesfully!");
    			//reload the page
    			document.location.reload();
    		}else if(response=="false"){
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("Department name already exists.");
    		}else{
    			window.alert("Error Adding new dpt:"+response);
    		}
    		closeHoldon();
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_ADDDEPARTMENT+"&dpt="+dpt;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
     openHoldon();
}


//set the selected user var , sli = list item
function userSelected(sli){
	//deactive the previous selected tab
	if(!(typeof selectedUser =="undefined" || selectedUser==""))
		$("#"+selectedUser).removeClass("active");
	selectedUser = $(sli).attr("id");
	$(sli).addClass("active");
	
}

//set the selected user var , sli = list item , called in the admin panel, when department li item is selected
function dptSelected(sli){

	//deactive the previous selected tab
	if(!(typeof selectedDpt =="undefined" || selectedDpt==""))
		$("#"+selectedDpt).removeClass("active");
	selectedDpt = $(sli).attr("id");
	$(sli).addClass("active");
	
}

//remove selected user
function removeSelectedUser(){
	//confirm deletion
	

	//request server for removing selecteduser
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("removeuser="+response);
    		if(response=="true"){
    			//if the request is succesful notify user & reload page  //server send "true" for succes
    				//close the  modal
    			//$("#myModal4").modal('hide');
    			window.alert("selected user deleted succesfully!");
    			//reload the page
    			document.location.reload();
    		}else{
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("operation failed.");
    		}
    		closeHoldon();
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_REMOVEUSER+"&un="+selectedUser;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
     openHoldon();
	
}

function removeSelectedDepartment(){

	//request server for removing selecteduser
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("removeuser="+response);
    		if(response=="true" || response =="1"){
    			//if the request is succesful notify user & reload page  //server send "true" for succes
    				//close the  modal
    			//$("#myModal4").modal('hide');
    			window.alert("selected Department deleted succesfully!");
    			//reload the page
    			document.location.reload();
    		}else if(response=="false" || response =="0"){
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("operation failed.");
    		}
    		else{
    			window.alert("Error removing dpt:"+response);
    		}

    		closeHoldon();
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_REMOVEDEPARTMENT+"&dpt="+selectedDpt;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
     openHoldon();

}

//validates input and returns "true" if valid, if not returns error message
function validateThis(data){

}

//remove selected section
function removeSelectedSec(){
	if(activeSecElement.getAttribute("value")=="1")//cant remove first sec of any year scheule
	{
		window.alert("You can't remove this section.");
		return;
	}
	var result= window.confirm("Are you sure you want to remove the selected section?");
	if(result){
		//remove the selected node
		activeYosElement.removeChild(activeSecElement);
		//save changes
		saveChanges();
		//reload
		document.location.reload();

	}

}

//remove selected year of study

function removeSelectedYOS(){
	if(activeYosElement.getAttribute("value")=="1")//cant remove first year scheule
	{
		window.alert("You can't remove First year Schedule");
		return;
	}

	var result= window.confirm("Are you sure you want to remove all the year "+activeYosElement.getAttribute("value")+" schedules?");
	if(result){
		//remove the selected node
		(activeYosElement.parentNode).removeChild(activeYosElement);
		//save changes
		saveChanges();
		//reload
	   document.location.reload();
	}


}
function removeInstructor(iname){
	//request server to delete instructor
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("removeuser="+response);
    		if(response=="true" || response =="1"){
    			window.alert(iname+" removed from the system succesfully!");
    			//refresh the instructors list
    			 showInstructorsList();
    		}else if(response=="false" || response =="0"){
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("operation failed.");
    		}
    		else{
    			window.alert("Error removing instructor:"+response);
    		}
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_REMOVEINSTRUCTOR+"&iname="+iname;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
}

function removeCourse(cyos, ctitle){
	//request server to delete course
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("removeuser="+response);
    		if(response=="true" || response =="1"){
    			window.alert(ctitle+" removed from the system succesfully!");
    			//refresh the instructors list
    			showCoursesList();
    		}else if(response=="false" || response =="0"){
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("operation failed.");
    		}
    		else{
    			window.alert("Error removing course:"+response);
    		}
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_REMOVECOURSE+"&yos="+cyos+"&ctitle="+ctitle;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
}

function addNewInstructor(title,iname){
	//validate the input
	var iinput =document.getElementById("nin");
	if(iinput.checkValidity()==false){
		//todo validate for htmlspecialcharacters
		$("#nin").attr("placeholder",iinput.validationMessage);
		return;
	}

	//request server to add new instructor
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("removeuser="+response);
    		if(response=="true" || response =="1"){
    			window.alert(title+" "+iname+" added to the system succesfully!");
    			$("#nin").val("");
    			//refresh the instructors list
    			 showInstructorsList();
    		}else if(response=="false" || response =="0"){
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("operation failed.");
    		}
    		else{
    			window.alert("Error adding new instructor:"+response);
    		}
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_ADDINSTRUCTOR+"&title="+title+"&iname="+iname;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 


}

function addNewCourse(cyos, ctitle){
	//validate the input
	var ctinput =document.getElementById("nct");
	var cyosinput= document.getElementById("ncyos");
	if(ctinput.checkValidity()==false){
		//todo validate for htmlspecialcharacters
		$("#nct").attr("placeholder",ctinput.validationMessage);
		return;
	}
	if(cyosinput.checkValidity()== false){
		$("#ncyos").attr("placeholder", cyosinput.validationMessage);
		return;
	}

	//request server to add new instructor
	var xhttp;
	if (window.XMLHttpRequest) {
    	xhttp = new XMLHttpRequest();
     }
     else 	// code for IE6, IE5
    	 xhttp = new ActiveXObject("Microsoft.XMLHTTP");

    	//when the response is ready, process it and do accordingly
    xhttp.onreadystatechange= function(){
    	if(xhttp.readyState==4 && xhttp.status==200){
    		////get the server response
    		var response = xhttp.responseText;
    		//window.alert("removeuser="+response);
    		if(response=="true" || response =="1"){
    			$("#nct").val("");
    			//refresh the instructors list
    			window.alert(ctitle+" added to the system succesfully!");
    			 showCoursesList();
    		}else if(response=="false" || response =="0"){
    			//if not (response is other than "true")notify the user that the operation is not succesful and request to try again// due to wrong old pw
    			window.alert("operation failed.");
    		}
    		else{
    			window.alert("Error adding new course:"+response);
    		}
    	}
    };

    //send the request

    var params= "requestType="+XHTTP_REQUEST_TYPE_ADDCOURSE+"&yos="+cyos+"&ctitle="+ctitle;
     xhttp.open("POST","datamodel/xhttpHandler.php");
     xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     xhttp.send(params); 
}

function showInstructorsList(){

	var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange=function()
		{
			if(xhttp.readyState==4 && xhttp.status==200)
			{
				var instView = xhttp.responseText;
					$(document).ready(function(){
						document.getElementById("instList").innerHTML=instView;
						});
				
			}
		}

		var params= "requestType="+XHTTP_REQUEST_TYPE_GETINSTRUCTORS+"&dpt="+user.userDepartment;

		xhttp.open("GET","datamodel/xhttpHandler.php?"+params);
    	xhttp.send(); //request for departments list
}

function showCoursesList(){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange=function()
		{
			if(xhttp.readyState==4 && xhttp.status==200)
			{
				var corsView = xhttp.responseText;
					$(document).ready(function(){
						document.getElementById("corsList").innerHTML=corsView;
						});
				
			}
		}
		var params= "requestType="+XHTTP_REQUEST_TYPE_GETCOURSES+"&dpt="+user.userDepartment;

		xhttp.open("GET","datamodel/xhttpHandler.php?"+params);
    	xhttp.send(); //request for departments list

}

//show the active section info, display on the modal
function showClassInfo(){

	if(typeof activeSecElement == "undefined" || activeSecElement == null){
		return;
	}
	else{//if the activeSecElement is nt null

		var infotitle = activeDepartment+" - year "+activeYosElement.getAttribute("value")+" - sec "+activeSecElement.getAttribute("value");
		$('#infotitle').text(infotitle);

		var advisorName = activeSecElement.getElementsByTagName("advisor")[0].childNodes[0].nodeValue;
		var advisorPhone = activeSecElement.getElementsByTagName("advisor")[0].getAttribute("phone");
		var repName = activeSecElement.getElementsByTagName("rep")[0].childNodes[0].nodeValue;
		var repPhone = activeSecElement.getElementsByTagName("rep")[0].getAttribute("phone");
		
		$('#advisorName').val(advisorName);
		if(advisorPhone=="")
			$('#advisorPhone').attr("placeholder","advisor phone unknown!");
		else
			$('#advisorPhone').val(advisorPhone);

		$('#repName').val(repName);

		if(repPhone==""){

			$('#repPhone').attr("placeholder","rep phone unknown!");
		}
			
		else{
			
			$('#repPhone').val(repPhone);
		}
					
	}	

}

function saveClassInfo(adname,adphone, repname, repphone){

	  if(adname == null || adname =="")
	  	adname="unknown";
	  if(repname == null || repname =="")
	  	repname="unknown";

	  var advisorPhone= document.getElementById("advisorPhone");
	  var repPhone= document.getElementById("repPhone");
	  if(advisorPhone.checkValidity()==false ){
	  	//todo validate for htmlspecialcharacters
	  	$('#advisorPhone').attr("placeholder",advisorPhone.validationMessage);
	  	return;
	  }
	  if(repPhone.checkValidity()== false){
	  	$('#repPhone').attr("placeholder",repPhone.validationMessage);
	  	return;
	  }
	  if(adphone.length <10 && adphone.length > 0){
	  	$('#advisorPhone').attr("placeholder","phone number need to be at least 10 digits");
	  	return;
	  }
	  if(repphone.length<10 && repphone.length > 0){
	  	$('#repPhone').attr("placeholder","phone number need to be at least 10 digits");
	  	return;
	  }
	  activeSecElement.getElementsByTagName("advisor")[0].childNodes[0].nodeValue = adname;
	  activeSecElement.getElementsByTagName("advisor")[0].setAttribute("phone",adphone);
	  activeSecElement.getElementsByTagName("rep")[0].childNodes[0].nodeValue = repname;
	  activeSecElement.getElementsByTagName("rep")[0].setAttribute("phone", repphone);

	  saveChanges();

}

function openHoldon(){
	HoldOn.open({
		 theme:"sk-cube-circle",
     // background color
		backgroundColor:"#1847B1",
		// loading message here
		message:'wait...',
		// text color
		textColor:"white"
});
	
}

function closeHoldon(){
	HoldOn.close();
}
