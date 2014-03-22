(function($){
	$.fn.grid = function(option){
		var NoColumnsListError = "Provide a list of the names of the columns to display in the grid";
		var ColumnsNotAnArrayError = "The list of the names of the columns to display must be an array";
		var NoListDataUrlError = "Provide the url for getting data to display in the grid";
		var NoListCountUrlError = "Provide the url for getting the count of data in the database";
		var NoRowCountError = "Provide the number of rows of data to display in the grid at a time";
		var NoIdentityColumnError = "Provide the name of the column to be used as a unique identifier for each column";
		var ExtraControlsNotAnArrayError = "The value assigned to extraControls must be an array";

		if(!option.hasOwnProperty("columns"))
			throw NoColumnsListError;

		if(!Array.isArray(option.columns))
			throw ColumnsNotAnArrayError;
		
		if(!option.hasOwnProperty("listDataUrl"))
			throw NoListDataUrlError;

		if(!option.hasOwnProperty("listDataCountUrl"))
			throw NoListCountUrlError;

		if(!option.hasOwnProperty("rowCount"))
			throw NoRowCountError;

		if(!option.hasOwnProperty("identityColumn"))
			throw NoIdentityColumnError;

		if(option.hasOwnProperty("extraControls"))
			if(!Array.isArray(option.extraControls) && option.extraControls != undefined)
				throw ExtraControlsNotAnArrayError;

		var settings = $.extend({
			startingPoint: 0,
			rowCount: 20
		},option);

		var parentDiv = this;
		
		var tableHeaderCells = "";
		$.each(settings.columns, function(index, value){
			tableHeaderCells += "<th>"+value+"</th>";
		});

		if(settings.extraControls){
			tableHeaderCells += "<th>Operations</th>";
		}

		var headerHtml = "<table class='table table-striped table-bordered table-hover table-condensed' ";
		headerHtml += "id='mygrid'><thead><tr>"+ tableHeaderCells + "</tr></thead><tbody></tbody><tfoot></tfoot></table>";
		parentDiv.append(headerHtml);

		$.get(settings["listDataUrl"], {"index": settings.startingPoint, "limit": settings.rowCount}, function(data){
			if(data){
				var tableBody = $("#mygrid tbody");
				$.each(data,function(index, value){
					var row = "<tr id='"+value[settings.identityColumn]+"' class='grid-row'>";
					for (var i = 0, len = settings.columns.length; i < len; i++) {
						var cell = "<td>"+value[settings.columns[i]]+"</td>";
						row += cell;
					};
					if(settings.extraControls){
						var cell = "<td>";
						for(var i =0, len = settings.extraControls.length; i < len; i++){
							cell += settings.extraControls[i];
						}
						cell += "</td>";
						row += cell;
					}
					row += "</tr>";
					tableBody.append(row);
				});
			}
		},"json");

		$.get(settings["listDataCountUrl"], function(data){
			var footer = $("#mygrid tfoot");
			if(data){
				var dataRowCount = Number(data["count"]);
				var pageSizeControl = "<input type='hidden' id='recordcount' value='"+ 
					settings.rowCount +"'/>";
				var dataListUrl = "<input type='hidden' id='listDataUrl' value='"+ 
					settings.listDataUrl +"'/>";
				var dataListCountUrl = "<input type='hidden' id='listDataCountUrl' value='" + 
					settings.listDataCountUrl +"'/>"

				footer.append(pageSizeControl);
				footer.append(dataListUrl);
				footer.append(dataListCountUrl);
				
				var numberOfPages = Math.floor(dataRowCount / settings.rowCount) + (dataRowCount % settings.rowCount > 0 ? 1: 0);
				console.log((dataRowCount / settings.rowCount));
				console.log(numberOfPages);
				var paginationDiv = "<div class='pagination'><ul>";
				for(var i = 0; i < numberOfPages; i++){
					var pageNumber = i + 1;
					paginationDiv += "<li><a href='"+pageNumber+"' class='page'>"+pageNumber+"</li>";
				}
				paginationDiv += "</ul></div>";
				parentDiv.append(paginationDiv);
			}
			console.log(data);
		},"json");

		return this;
	};
}(jQuery));