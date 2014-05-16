var baseUrl = location.origin + "/" + location.pathname.split("/")[1];

var apiBaseUrl = baseUrl+"/api.php";

var showMsg = function(tag, msgType,msg){
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

var createGrid = function (gridTag, listUrl, countUrl, startingPoint, rowCount, 
		columns, identityColumn, extraControls){
	$(gridTag).html("");
	$(gridTag).grid({listDataUrl: listUrl, listDataCountUrl: countUrl, 
		startingPoint: startingPoint, rowCount: rowCount, 
		columns: columns, identityColumn: identityColumn, 
		extraControls: extraControls});		
};

var createMenu = function(userType){
	var menu = $(".navbar>.navbar-inner>.nav");
	var url = apiBaseUrl + "/menus";
	$.get(
		url,
		"json"
	).done(function(data){
		console.log(data);
		// userType = data['usertype'];
		// console.log(data);
		// menu.addClass("pull-right");
		// menu.append("<li class='divider-vertical'>&nbsp;</li>");
		// menu.append("<li><a href='#' id='attendance-menu'>Attendance</a></li>");
		// menu.append("<li class='divider-vertical'>&nbsp;</li>");
		// menu.append("<li><a href='#' id='registration-menu'>Registration</a></li>");

		// if(userType == "admin"){
		// 	menu.append("<li class='divider-vertical'>&nbsp;</li>");
		// 	menu.append("<li><a href='#' id='user-menu'>User</a></li>");
		// 	menu.append("<li class='divider-vertical'>&nbsp;</li>");
		// 	menu.append("<li><a href='#' id='equipment-menu'>Equipment</a></li>");
		// 	menu.append("<li class='divider-vertical'>&nbsp;</li>");
		// }
		menu.append("<li><a href='#' id='log-out'>Log Out</a></li>");

	}).fail(function(data){
		self.location = (baseUrl + "/");
	});
};

var checkIfLoggedIn = function(){
	var userType = null;
	console.log(userType);
	return userType;
};

$("body").on("click", "#attendance-menu", function(event){
	event.preventDefault();
	self.location = baseUrl + "/attendance.html";
});

$("body").on("click", "#registration-menu", function(event){
	event.preventDefault();
	self.location = baseUrl + "/registration.html";
});

$("body").on("click", "#user-menu", function(event){
	event.preventDefault();
	self.location = baseUrl + "/User.html";
});

$("body").on("click", "#equipment-menu", function(event){
	event.preventDefault();
	self.location = baseUrl + "/equipment.html";
});

$("body").on("click", "#log-out", function(event){
	event.preventDefault();
	var url = apiBaseUrl + "/users/logout";
	$.get(
		url,
		"json"
	).done(function(event){
		self.location = (baseUrl + "/");
	});
})