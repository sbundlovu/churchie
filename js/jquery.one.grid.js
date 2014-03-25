(function($){

	$.fn.grid = function(option){
		
		if(option == undefined || option == null){
			option = {};
		}

		var parent = this,
			filterDiv,
			dataDiv, 
			settings = $.extend({
				startingPoint: 0, 
				rowCount: 100 }, option),
			FiltersNotArrayError = "Filters must be an array",
			ColumnsNotArrayError = "Columns must be an array",
			ColumnsNotSuppliedError = "Columns to display haven't been supplied",
			NoIdentityColumnError = "No Column has been specified to be used as the unique identifier for a row",
			NoDataListUrlError = "Url for getting a list of data hasn't been specified",
			NoDataCountUrlError = "Url for counting the number of data row hasn't been specified";

		console.log(settings);

		if(!settings.hasOwnProperty("dataListUrl")){
			throw NoDataListUrlError;
		}

		if(!settings.hasOwnProperty('dataCountUrl')){
			throw NoDataCountUrlError;
		}
		
		if(!settings.hasOwnProperty("identityColumn")){
			throw NoIdentityColumnError;
		}

		if(settings.hasOwnProperty("columns")){
			if(!Array.isArray(settings.columns)){
				throw ColumnsNotArrayError;
			}
		}else{
			throw ColumnsNotSuppliedError;
		}

		if(settings.hasOwnProperty("filters")){
			//create filters if filters have been passed to it

			//create top div with filters listed in it
			parent.append("<div id='filter-div'></div>");
			filterDiv = $("body #filter-div");

			if(!Array.isArray(settings['filters'])){
				throw FiltersNotArrayError;
			}else{
				//do this if the filters that are passed to it is an array
				for(var i = 0, k = settings.filters.length; i < k; i++){
					var control = "<div class='filter-element-div'>";
					control += "<select id='"+ settings.filters[i]['name'];
					control += "'><option>--filter--</option></select></div>";
					filterDiv.append(control);
				}
			}
		}

		//create div for holding data
		parent.append("<div id='data-div></div>");
		dataDiv = $("body #data-div");

		return this;
	};

}(jQuery));