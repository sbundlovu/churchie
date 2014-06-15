$(function(){

	hideMsgBoxes(['#msg']);

	$("body").on("click", "#signin", function(event){
		event.preventDefault();
		var args = {username : $("#username").val(), password: $("#password").val()};
		var url = apiBaseUrl + "/users/login";

		$.post(
			url,
			args
		).done(function(data){
			
			if(data['result'] == true){
				showMsg("#msg", "success", "login successful");
				setInterval(function(){
					self.location = baseUrl + "/main.html";
				}, 1000);
			}else{
				showMsg("#msg", "error", 
					"Please check your username and password and try agains");
			}
			
		}).fail(function(data){
			
			showMsg("#msg", "error", 
				"Unexpected error occurred please try again");

		});

	});
});