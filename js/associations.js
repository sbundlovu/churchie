$(function(){

	var listUrl = apiBaseUrl + "/associations",
		countUrl = apiBaseUrl + "/associations/meta/count",
		startingPoint = 0,
		limit = 100,
		columns = [
			{'name': 'id' , 'label': 'ID'}, {'name': 'name', 'label': 'Name'},
			{'name': 'description', 'label': 'Description'}
		],
		identityColumn = "id", 
		filterUrl = apiBaseUrl + "/associations/filter",
		findUrl = apiBaseUrl + "/associations/",
		extraControls = [
				"<div class='btn-group'>","<button class='btn btn-mini edit-btn'>Edit</button>", 
				"<button class='btn btn-mini delete-btn'>Delete</button>", "</div>"
			],
		gridTag = "#gridZ",
		reloadInterval = 40000,
		filterControls = [
			{'name': 'name', 'label': 'Name'}
		];

	var clearInputArea = function(prefix){
		$('#' + prefix + "name").val('');
		$('#' + prefix + "description").val('');
		$('#' + prefix + "associationId").val('');
		$('#' + prefix + "reason").val('');
	};

	var findAssociation = function(associationId, prefix){
		var url = apiBaseUrl + '/associations/' + associationId;
		$.get(
			url
		).done(function(data){
			$('#' + prefix + "name").val(data['name']);
			$('#' + prefix + "description").val(data['description']);
			$('#' + prefix + "hassociationId").val(data['id']);
			$('#' + prefix + "reason").val(data['reason']);
		}).fail(function(data){
			showMsg('#msg', 'info', 
				"couldn't find the association please refresh your page and try again");
		})
	};

	hideMsgBoxes(['#msg']);
	$("#myTab a:first").tab('show');

	createGrid(gridTag, listUrl, countUrl, startingPoint, limit, columns, 
		identityColumn, reloadInterval, filterUrl, filterControls, extraControls);

	//click on #save
	$("body").on("click", "#save", function(event){
		event.preventDefault();

		var args = {
			"name": $('#name').val(),
			"description": $('#description').val()
		},
		url = apiBaseUrl + "/associations/";

		$.post(
			url, 
			args
		).done(function(data){
			if(data.result == true){
				showMsg('#msg', 'success', 'Association has been saved successfully');
				createGrid(gridTag, listUrl, countUrl, startingPoint, 
					limit, columns, identityColumn, reloadInterval, 
					filterUrl, filterControls, extraControls);
				clearInputArea('');
			}else{
				showMsg('#msg', 'info', "Association couldn't be saved check and try again");
			}
		}).fail(function(data){
			showMsg('#msg', 'error', "Couldn't save the associatioon's details");
			clearInputArea('');
		});

	});

	//clicked .edit-btn
	$("body").on("click", ".edit-btn", function(event){
		event.preventDefault();
		$("#myTab li:eq(1) a").tab('show');
		findAssociation($(this).parent().parent().parent().attr("id"), "u");
	});

	//clicked .delete-btn
	$('body').on('click', '.delete-btn', function(event){
		event.preventDefault();
		$("#myTab li:eq(2) a").tab('show');
		findAssociation($(this).parent().parent().parent().attr("id"), "d");
	});

	//clicked on #update
	$('body').on('click', '#update', function(event){
		event.preventDefault();
		var args = {
			'name': $('#uname').val(),
			'description': $('#udescription').val()
		},url = apiBaseUrl + "/associations/" + $('#uhassociationId').val();

		$.ajax({
			url: url,
			data: args,
			type: 'PUT'
		}).done(function(data){
			if(data.result == true){
				showMsg('#msg', 'success', 
					"Association details have been updated successfully");
				clearInputArea('u');
				createGrid(gridTag, listUrl, countUrl, startingPoint, 
					limit, columns, identityColumn, reloadInterval, 
					filterUrl, filterControls, extraControls);
			}else{
				showMsg('#msg', 'info', 
					"Make sure you have change so details of the " +
					"association before trying to update");
			}
		}).fail(function(data){
			showMsg('#msg', 'error', "Couldn't update the association");
		});

	});

	//clicked on #delete
	$('body').on('click', '#delete', function(event){
		event.preventDefault();

		var url = apiBaseUrl + "/associations/" + $("#dhassociationId").val();
		var args = { 'reason_removed': $('#dreason').val() };
		console.dir(args);
		$.ajax({
			url: url,
			data: args,
			type: 'DELETE'
		}).done(function(data){
			if(data.result == true){
				showMsg('#msg', 'success', "Association has been deleted successfully");
				clearInputArea('d');
				createGrid(gridTag, listUrl, countUrl, startingPoint, 
					limit, columns, identityColumn, reloadInterval, 
					filterUrl, filterControls, extraControls);
			}else{
				showMsg('#msg', 'info', "Couldn't delete the association please try again");
			}
		}).fail(function(data){
			showMsg('#msg', 'error', "Couldn't delete the association try again");
		});

	});

});