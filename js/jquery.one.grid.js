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
			FilterControlsNotArrayError = "Filters must be an array",
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

			if(settings.hasOwnProperty("filters")){
				if(!Array.isArray(settings.filters)){

				}
			}
		}
		
		//validation arguments to the plugin
		validateSettings(settings);

		if(settings.hasOwnProperty("filterControls")){
			//create Filter Controls if filterControls have been passed to it

			//create top div with filterControls listed in it
			parent.append("<div id='filter-div'></div>");
			filterDiv = $("body #filter-div");

			if(!Array.isArray(settings['filterControls'])){
				throw FilterControlsNotArrayError;
			}else{
				//do this if the filterControls that are passed to it is an array
				for(var i = 0, k = settings.filterControls.length; i < k; i++){
					var filterName = settings.filterControls[i];
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
			settings.filterControls.toString() +"'/>";
		dataFooterDiv.append(filterNamesControl);
		
		//function for getting the values from the FilterControls
		var getFilterControlsAndValues = function(){
			var filterControlsAndValues = {};
			var filterNames = $("#data-footer-div #filterNames").val();
			if(filterNames !== ""){
				filterNames = filterNames.split(',');
			}
			if(Array.isArray(filterNames)){
				for(var i = 0, k = filterNames; i < k; i++){
					var filterName = filterNames[i];
					var filterControlValue = $('#'+ filterName).val();
					filterControlsAndValues[filterName] = (filterControlValue === '--filter--' ? 
						null : filterControlValue);
				}
			}
			return filterControlsAndValues;
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
			//add the values in the filter to the arguments passed to the server
			var filterControlsAndValues = getFilterControlsAndValues();

			if(filterControlsAndValues != null && filterControlsAndValues != {}){
				for(var name in filterControlsAndValues){
					args[name] = filterControlsAndValues[name];
				}
			}
			tableBody.html("");
			$.get(dataListUrl, args, function(data){
				$.each(data, function(index, value){
					var tableRow = "<tr id='" + value[settings.identityColumn] + "'>";
					for(var i = 0, k = columns.length; i < k; i++){
						tableRow += ("<td>" + value[columns[i]["name"]] + "</td>");
					}

					if(extraControls != null && extraControls != undefined && Array.isArray(extraControls)){
						for(var i = 0, k = extraControls.length; i < k; i++){
							tableRow += ("<td>" + extraControls[i] + "</td>");
						}
					}

					tableRow += "</tr>";
					tableBody.append(tableRow);
				});
			},'json');
		};
		
		loadGridData(settings);

		//create pagination controls
		var createPaginationControls = function(settings){
			var filterControlsAndValues = getFilterControlsAndValues();
			$.get(settings.dataCountUrl,filterControlsAndValues, function(data){
				var resultCount = data['count'];
				var numberOfPages = (Math.floor(resultCount / settings.limit) + 
					(resultCount % settings.limit > 0 ? 1: 0));
				var paginationDiv = "<div class='pagination'><ul>";
				for(var i = 0; i < numberOfPages; i++){
					var pageNumber = i + 1;
					paginationDiv += "<li><a href='"+pageNumber+"' class='page'>"+pageNumber+"</li>";
				}
				paginationDiv += "</ul></div>";
				dataFooterDiv.append(paginationDiv);
				dataFooterDiv.append(("<input id='limit' type='hidden' value= '"+settings.limit +"' />"));
			},"json");

			$("body").off('click', '.page');
			$("body").on("click", ".page", function(event){
				event.preventDefault();
				var page = Number($(this).attr("href"));
				var pageSize = Number($("#limit").val());
				var startingPoint = (pageSize * (page-1));
				var args = getFilterControlsAndValues();
				args['offset'] = startingPoint;
				args['limit'] = pageSize;
				args['dataListUrl'] =  $('body #dataListUrl').val();
				args['dataCountUrl'] = $('body #dataCountUrl').val();
				args['columns'] = '';
				var DictName = [];
				var cols = $('body #columns').val().toString().split(',');
				for (var i = 0, k = cols.length; i < k; i ++){
					DictName.push({'name': cols[i]});
				}
				args['columns'] = DictName;
				args['identityColumn'] = $('body #identityColumn').val();
				args['extraControls'] = $('body #extraControls').val();
				loadGridData(args);
			});
		};
		
		createPaginationControls(settings);

		//adding the dataListUrl and dataCountUrl to the footer div for later
		//calls to the api during pagination
		var createFooterForListAndCountUrl = function(settings, dataFooterDiv){
			var dataListUrlControl = "<input type='hidden' id='dataListUrl' value='" +
				settings.dataListUrl + "'/>";
			var dataCountUrlControl = "<input type='hidden' id='dataCountUrl' value='" +
				settings.dataCountUrl + "'/>";

			var cols = settings.columns;
			var columns = [];
			for(var i = 0, k = cols.length; i < k; i++){
				columns.push(cols[i]['name']);
			}
			var columnsControl = "<input type='hidden' id='columns' value='"+ 
				columns.toString() + "'/>";
			var identityColumnControl = "<input type='hidden' id='identityColumn' value='" +
				settings.identityColumn +"' />";

			dataFooterDiv.append(dataListUrlControl).append(
				dataCountUrlControl).append(columnsControl
				).append(identityColumnControl);

			// if(settings.hasOwnProperty('extraControls')){
			// 	var control = "<input type='hidden' id='extraControls' value='" +
			// 		settings.extraControls.toString().trim() +"' />";
			// 	console.log(control);
			// 	dataFooterDiv.append(control);
			// }
		};
		
		createFooterForListAndCountUrl(settings,dataFooterDiv);
		
		return this;
	};

}(jQuery));