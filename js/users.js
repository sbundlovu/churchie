$(function(){

	var url = apiBaseUrl + "/users/",
		userTypeControls = ['#usertype', '#uusertype'],
		listUrl = apiBaseUrl + "/users",
		countUrl = apiBaseUrl + "/users/meta/count",
		filterUrl = apiBaseUrl + "/users/filter",
		findUrl = apiBaseUrl + "/users/",
		startingPoint = 0,
		limit = 100,
		columns = [
			{'name': 'id'}, {'name': 'firstname', 'label': 'First Name'}, 
			{'name': 'othernames', 'label': 'Othernames'}, 
			{'name': 'username', 'label': 'Username'}, 
			{'name': 'usertype', 'label': 'User Type'}
			],
		identityColumn = "id",
		reloadInterval = 40000,
		extraControls = [
				"<div class='btn-group'>","<button class='btn btn-mini edit-btn'>Edit</button>", 
				"<button class='btn btn-mini delete-btn'>Delete</button>", "</div>"
			],
		filterControls = [{"name": "usertype", "label": "User Type"}],
		gridTag = "#gridZ",
		tabControlId = "#myTab";

	var clearInputArea = function(controlPrefix){
		$("#" + controlPrefix + "memberid").val('--Member--');
		$("#" + controlPrefix + "uname").val("");
		$("#" + controlPrefix + "usertype").val("");
		$("#" + controlPrefix + "pword").val("");
		$("#" + controlPrefix + "huserId").val("");
		$("#" + controlPrefix + "member").val("");
	};

	var findUser = function(userId, controlPrefix){
		var url = findUrl + userId;
		$.get(url, 'json').done(function(data){
			$("#" + controlPrefix + "member").val(data['firstname'] + " " + data['othernames']);
			$("#" + controlPrefix + "uname").val(data['username']);
			$("#" + controlPrefix + "usertype").val(data['usertype']);
			$("#" + controlPrefix + "huserId").val(data['id']);
		}).fail(function(error){
			showMsg("#msg", "error", "Couldn't find the details of the specified user please refresh the page");
		});
	};

	hideMsgBoxes(['#msg']);
	getMembers($("#memberid"));

	createGrid(gridTag, listUrl, countUrl, startingPoint, limit, columns, 
		identityColumn, reloadInterval, filterUrl, filterControls, extraControls);

	for(var i = 0, k = userTypeControls.length; i < k; i++){
		getUsertypes($(userTypeControls[i]));
	}

	$("#myTab a:first").tab('show');

	$("body").on('click', "#save", function(event){
		event.preventDefault();

		var args = {
			'username': $("#uname").val(),
			'usertype': $('#usertype').val(),
			'password': $('#pword').val(),
			'memberid': $('#memberid').val()
		};

		$.post(
			url,
			args
		).done(function(args){
			showMsg('#msg', 'success', 'User has been added successfully');
			clearInputArea("");

			createGrid(gridTag, listUrl, countUrl, 
				startingPoint, limit, columns, 
				identityColumn, reloadInterval, 
				filterUrl, filterControls, extraControls);
		
		}).fail(function(args){
			showMsg('#msg', 'error', "Couldn't add user");
		});

	});

	$("body").on("click", ".edit-btn", function(event){
		event.preventDefault();
		$("#myTab li:eq(1) a").tab('show');
		findUser($(this).parent().parent().parent().attr("id"), "u");
	});

	$("body").on("click", ".delete-btn", function(event){
		event.preventDefault();
		$("#myTab li:eq(2) a").tab('show');
		findUser($(this).parent().parent().parent().attr("id"), "d");
	});

	$("body").on("click", "#update", function(event){
		event.preventDefault();
		var updateUrl = findUrl + $("#uhuserId").val();
		var args = {
			'username': $("#uuname").val(),
			'usertype': $("#uusertype").val(),
			'password': $("#upword").val()
		};

		$.ajax({
			url: updateUrl,
			type: 'PUT',
			data: args
		}).done(function(data){
			if(data['result'] == true){
				clearInputArea("u");

				createGrid(gridTag, listUrl, countUrl, 
					startingPoint, limit, columns, 
					identityColumn, reloadInterval, 
					filterUrl, filterControls, extraControls);
				$("#myTab li:eq(0) a").tab('show');
				showMsg("#msg", "success", "User details have been updated successfully");
			}else{
				showMsg('#msg', 'info', 'The current user details is the same as the one you provided');
			}
		}).fail(function(data){
			showMsg("#msg", "error", "Failed to update user details");
		});
	});

	$("body").on("click", "#delete", function(event){
		event.preventDefault();
		var deleteUrl = findUrl + $("#dhuserId").val();
		$.ajax({
			url: deleteUrl,
			type: 'DELETE'
		}).done(function(data){
			if(data['result'] == true){
				clearInputArea("d");
				createGrid(gridTag, listUrl, countUrl, 
					startingPoint, limit, columns, 
					identityColumn, reloadInterval, 
					filterUrl, filterControls, extraControls);
				$("#myTab li:eq(0) a").tab('show');
				showMsg("#msg", "success", "User has been deleted successfully");
			}else{
				showMsg("#msg", "info", "Couldn't delete the user please refresh the paged");
			}
		}).fail(function(data){
			showMsg("#msg", "error", "Couldn't delete the users details");
		});
	});

});