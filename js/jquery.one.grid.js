(function($){

	$.fn.grid = function(option){
		
		if(option == undefined || option == null){
			option = {};
		}

		var parent = this,
			filterDiv,
			dataDiv,
			dataFooterDiv,
			settings = $.extend({
				offset: 0, 
				limit: 100 }, option),
			FiltersNotArrayError = "Filters must be an array",
			ColumnsNotArrayError = "Columns must be an array",
			ColumnsNotSuppliedError = "Columns to display haven't been supplied",
			NoIdentityColumnError = "No Column has been specified to be used as the unique identifier for a row",
			NoDataListUrlError = "Url for getting a list of data hasn't been specified",
			NoDataCountUrlError = "Url for counting the number of data row hasn't been specified",
			ExtraControlsNotArrayError = "The contorls specified in the extraControls parameter wasn't specified inf an Array";

		var validateSettings = function(settings){
			if(settings.extraControls != null || settings.extraControls != undefined){
				if(!Array.isArray(settings.extraControls)){
					throw ExtraControlsNotArrayError;
				}
			}

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
		}
		
		//validation arguments to the plugin
		validateSettings(settings);

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
					var filterName = settings.filters[i];
					var control = "<div class='filter-element-div'><label>" + filterName;
					control += "<select id='"+ filterName;
					control += "'><option>--filter--</option></select></label></div>";
					filterDiv.append(control);
				}
			}
		}

		//create div for holding data
		parent.append("<div id='data-div'></div>");
		dataDiv = $("body #data-div");
		
		//create hidden field in footer that contains the names of the filter
		//controls
		parent.append("<div id='data-footer-div'></div>");
		dataFooterDiv = $("body #data-footer-div");
		
		var filterNamesControl = "<input type='hidden' id='filterNames' value='" +
			settings.filters.toString() +"'/>";
		dataFooterDiv.append(filterNamesControl);
		
		//function for getting the values from the filters
		var getFilters = function(){
			var filters = {};
			var filterNames = $("#data-footer-div #filterNames").val();
			if(filterNames !== ""){
				filterNames = filterNames.split(',');
			}
			if(Array.isArray(filterNames)){
				for(var i = 0, k = filterNames; i < k; i++){
					var filterName = filterNames[i];
					var filterControlValue = $('#'+ filterName).val();
					filters[filterName] = (filterControlValue === '--filter--' ? 
						null : filterControlValue);
				}
			}
			return filters;
		};

		//create table header
		var createTableHeader = function(settings){
			var columns = settings.columns,
				tableHeader,
				theaderRow = "",
				extraControls = settings.extraControls;

			$("body #data-div").append(("<table id='grid-proper'><thead></thead>"+
				"<tbody></tbody><tfoot></tfoot></table>"));
			tableHeader = $("body #grid-proper thead");

			for(var i = 0, k = columns.length; i < k; i++){
				theaderRow += ("<th>" + columns[i].name + "</th>");
			}
			if(extraControls != null && extraControls != undefined){
				theaderRow += ("<th>Operations</th>");
			}
			tableHeader.append(theaderRow);
		};
		
		createTableHeader(settings);
		
		//create the table for the data to be displayed
		var loadGridData = function(settings){
			var dataListUrl = settings.dataListUrl;
			var args = {index: settings.offset, limit: settings.limit};
			var columns = settings.columns;
			var extraControls = settings.extraControls;
			var tableBody = $("body #grid-proper tbody");
			$.get(dataListUrl, args, function(data){
				$.each(data, function(index, value){
					var tableRow = "<tr>";
					for(var i = 0, k = columns.length; i < k; i++){
						tableRow += ("<td>" + value[columns[i]["name"]] + "</td>");
					}

					for(var i = 0, k = extraControls.length; i < k; i++){
						tableRow += ("<td>" + extraControls[i] + "</td>");
					}
					
					tableRow += "</tr>";
					tableBody.append(tableRow);
				});
			},'json');
		};
		
		loadGridData(settings);
		
		//create pagination controls
		$.get(settings.dataCountUrl, function(data){
		
		},"json");
		
		//adding the dataListUrl and dataCountUrl to the footer div for later
		//calls to the api during pagination
		var createFooterForListAndCountUrl = function(settings, dataFooterDiv){
			var dataListUrlControl = "<input type='hidden' id='dataListUrl' value='" +
				settings.dataListUrl + "'/>";
			var dataCountUrlControl = "<input type='hidden' id='dataCountUrl' value='" +
				settings.dataCountUrl + "'/>";
		
			dataFooterDiv.append(dataListUrlControl).append(dataCountUrlControl);
		};
		
		createFooterForListAndCountUrl(settings,dataFooterDiv);
		
		return this;
	};

}(jQuery));