var baseUrl = location.origin + "/" + location.pathname.split("/")[1];

var apiBaseUrl = baseUrl+"/api.php";

//This method is responsible for showing messages when actions fail or succeed
var showMsg = function(tag, msgType, msg){
	tag = $(tag);
	var classes = ["alert-error", "alert-info", "alert-success"];
	for(var i = 0, k = classes.length; i < k; i++){
		tag.removeClass(classes[i]);
	}

	if(msgType === "error"){
		tag.addClass("alert-error");
	}else if(msgType === "info"){
		tag.addClass("alert-info");
	}else if(msgType === "success"){
		tag.addClass("alert-success");
	}
	tag.text(msg);
	tag.show();
	tag.alert();
	setTimeout(function(){
		$(tag).hide();
	}, 1000);
};

//This method is responsible for populating a select with members
var getMembers = function(control, args){
	var url = apiBaseUrl + "/members";
	if(args == null || args == undefined){
		args = {'index': 0, 'limit': 0};
	}
	$.get(url, args, function(data){
		for(var i = 0, k = data.length; i < k; i++){
			var option = "<option value='"+ data[i]['id'] +"'>"+ 
				(data[i]['firstname'] +' '+ data[i]['othernames']) +"</option>";
			control.append(option)
		}
	});
};

//This method is responsible for populating a select with usertypes
var getUsertypes = function(control, args){
	var url = apiBaseUrl + "/usertypes";
	if(args == undefined || args == undefined){
		args = {'index': 0, 'limit': 0};
	}
	$.get(url, args, function(data){
		for(var i = 0, k = data.length; i < k; i++){
			var option = "<option>" + data[i]['name'] + "</option>";
			control.append(option);
		}
	});
};

//This method is responsible for hiding all the messages boxes that are passed
//to it
var HideMsgBoxes = function(msgTags){
	if(Array.isArray(msgTags)){
		for(var i = 0, k = msgTags.length; i < k; i++){
			$(msgTags[i]).hide();
		}
	}
};

var createGrid = function (gridTag, listUrl, countUrl, startingPoint, rowCount, 
		columns, identityColumn, reloadInterval, filterControls, extraControls){
	$(gridTag).html("");
	$(gridTag).grid({dataListUrl: listUrl, dataCountUrl: countUrl, 
		startingPoint: startingPoint, rowCount: rowCount, 
		columns: columns, identityColumn: identityColumn, 
		reloadInterval: reloadInterval, filterControls: filterControls, 
		extraControls: extraControls
	});		
};

//This method is responsible for creating menus
var createMenu = function(){
	var menu = $(".navbar>.navbar-inner>.nav");
	var url = apiBaseUrl + "/menus";
	$.get(
		url,
		"json"
	).done(function(data){
		var result = data.result;
		var menuItem = null;
		menu.addClass("pull-right");
		for(var i = 0, k = result.length; i < k; i++){
			menu.append("<li class='divider-vertical'>&nbsp;</li>");
			menuItem = "<li><a href='"+ result[i]['endpoint'] +"'>"+ result[i]['name'] +"</a></li>"
			menu.append(menuItem);
			menu.append("<li class='divider-vertical'>&nbsp;</li>");
		}
		menu.append("<li><a href='#' id='log-out'>Log Out</a></li>");
		$(".navbar>.navbar-inner>.nav>li").addClass("capitalize");
	}).fail(function(data){
		self.location = (baseUrl + "/");
	});
};

//Todo: This method should check if the user is logged in at all
var checkIfLoggedIn = function(){
	var userType = null;
	console.log(userType);
	return userType;
};

//Event handler for signing out of the application
$("body").on("click", "#log-out", function(event){
	event.preventDefault();
	var url = apiBaseUrl + "/users/logout";
	$.get(
		url,
		"json"
	).done(function(event){
		self.location = (baseUrl + "/");
	});
});