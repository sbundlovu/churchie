$(function(){
		var url = apiBaseUrl + "/association_dues/",
		listUrl = apiBaseUrl + "/association_dues",
		countUrl = apiBaseUrl + "/association_dues/meta/count",
		filterUrl = apiBaseUrl + "/association_dues/filter",
		findUrl = apiBaseUrl + "/association_dues/",
		startingPoint = 0,
		limit = 100,
		columns = [
			{'name': 'id', 'label': 'Id'}, {'name': 'association', 'label': 'Association'}, 
			{'name': 'dues', 'label': 'Dues'}
			],
		identityColumn = "id",
		reloadInterval = 40000,
		extraControls = [
				"<div class='btn-group'>","<button class='btn btn-mini edit-btn'>Edit</button>", 
				"<button class='btn btn-mini delete-btn'>Delete</button>", "</div>"
			],
		filterControls = null, 
		gridTag = "#gridZ",
		tabControlId = "#myTab";

	var clearInputArea = function(controlPrefix){
		$("#" + controlPrefix + "associationdId").val('--Association--');
		$("#" + controlPrefix + "dues").val("");
		$("#" + controlPrefix + "hassociationId").val("");
		$("#" + controlPrefix + "association").val("");
	};

	var findAssociationDue = function(associationDueId, controlPrefix){
		var url = findUrl + associationDueId;
		$.get(url, 'json').done(function(data){
			$("#" + controlPrefix + "association").val(data['association']);
			$("#" + controlPrefix + "dues").val(data['dues']);
			$("#" + controlPrefix + "hduesId").val(data['id']);
		}).fail(function(error){
			showMsg("#msg", "error", "Couldn't find the details of the specified user please refresh the page");
		});
	};

	hideMsgBoxes(['#msg'])
	getAssociations($("#associationId"));

	createGrid(gridTag, listUrl, countUrl, startingPoint, limit, columns, 
		identityColumn, reloadInterval, filterUrl, filterControls, extraControls);


	$("#myTab a:first").tab('show');

	$("body").on('click', "#save", function(event){
		event.preventDefault();

		var args = {
			'association_id': $("#associationId").val(),
			'dues': $('#dues').val()
		};

		$.post(
			url,
			args
		).done(function(args){
			
			if(args.result){
				showMsg('#msg', 'success', 'Association Dues has been added successfully');
				clearInputArea("");

				createGrid(gridTag, listUrl, countUrl, 
					startingPoint, limit, columns, 
					identityColumn, reloadInterval, 
					filterUrl, filterControls, extraControls);	
			}else{
				showMsg('#msg', 'error', "Association Dues couldn't be saved");
			}
		
		}).fail(function(args){
			showMsg('#msg', 'error', "Couldn't add association");
		});

	});

	$("body").on("click", ".edit-btn", function(event){
		event.preventDefault();
		$("#myTab li:eq(1) a").tab('show');
		findAssociationDue($(this).parent().parent().parent().attr("id"), "u");
	});

	$("body").on("click", ".delete-btn", function(event){
		event.preventDefault();
		$("#myTab li:eq(2) a").tab('show');
		findAssociationDue($(this).parent().parent().parent().attr("id"), "d");
	});

	$("body").on("click", "#update", function(event){
		event.preventDefault();
		var updateUrl = findUrl + $("#uhduesId").val();
		var args = {'dues': $("#udues").val()};

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
				showMsg("#msg", "success", "Association details have been updated successfully");
			}else{
				showMsg('#msg', 'info', 'The current association details is the same as the one you provided');
			}
		}).fail(function(data){
			showMsg("#msg", "error", "Failed to update association details");
		});
	});

	$("body").on("click", "#delete", function(event){
		event.preventDefault();
		var deleteUrl = findUrl + $("#dhduesId").val();
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