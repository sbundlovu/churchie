$(function(){

	var url = apiBaseUrl + "/users/",
		userTypeControls = ['#usertype', '#uusertype'],
		listUrl = apiBaseUrl + "/users",
		countUrl = apiBaseUrl + "/users/meta/count",
		startingPoint = 0,
		limit = 100,
		columns = [
			{'name': 'id'}, {'name': 'firstname', 'displayName': 'First Name'}, 
			{'name': 'othernames', 'displayName': 'Othernames'}, 
			{'name': 'username', 'displayName': 'Username'}, 
			{'name': 'usertype', 'displayName': 'User Type'}
			],
		identityColumn = "id",
		reloadInterval = 40000,
		extraControls = [
			"<div class='btn-group'>","<button class='btn btn-mini edit-btn'>Edit</button>", 
			"<button class='btn btn-mini delete-btn'>Delete</button>", "</div>"
			],
		gridTag = "#gridZ";

	HideMsgBoxes(['#msg'])
	getMembers($("#memberid"));

	createGrid(gridTag, listUrl, countUrl, startingPoint, limit, columns, 
		identityColumn, reloadInterval, null, extraControls);

	for(var i = 0, k = userTypeControls.length; i < k; i++){
		getUsertypes($(userTypeControls[i]));
	}


	$("#myTab a:first").tab('show');

	$("#save").on('click', function(event){

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
			$("#uname").val('');
			$('#usertype').val('--User Type--');
			$('#pword').val('');
			$('#memberid').val('--Member--');
		}).fail(function(args){
			showMsg('#msg', 'error', "Couldn't add user");
			console.log('failed');
		});

		console.log(args);
	});

});