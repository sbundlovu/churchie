$(function(){

	var url = apiBaseUrl + "/users/";
	getMembers($("#memberid"));
	getUsertypes($("#usertype"));
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
			console.log('done');
		}).fail(function(args){
			console.log('failed');
		});

		console.log(args);
	});

});