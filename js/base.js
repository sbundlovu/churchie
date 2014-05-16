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
		}
		menu.append("<li><a href='#' id='log-out'>Log Out</a></li>");
		$(".navbar>.navbar-inner>.nav>li").addClass("capitalize");
	}).fail(function(data){
		self.location = (baseUrl + "/");
	});
};

var checkIfLoggedIn = function(){
	var userType = null;
	console.log(userType);
	return userType;
};

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