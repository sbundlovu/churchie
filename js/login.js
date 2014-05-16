$(function(){

	$("body").on("click", "#signin", function(event){
		event.preventDefault();
		var args = {username : $("#username").val(), password: $("#password").val()};
		var url1 = apiBaseUrl + "/users/login";

		$.ajax({
			type: "POST",
			url: url1,
			data: args,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			success: function(data){
				self.location = baseUrl + "/main.html"
			},
			error: function(data){
				console.log("error");
			}
		});

	});
});