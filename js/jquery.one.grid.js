(function($){

	$.fn.grid = function(option){
		
		if(option == undefined || option == null){
			option = {};
		}

		var parent = this,
			filterDiv,
			dataDiv,
			settings = $.extend({
				offset: 0, 
				limit: 100 }, option),
			defaultReloadInterval = 40000,
			filterPrefix = "cmbo",
			NoFilterUrlError = "The url for getting the values for the filters hasn't been provided",
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
					throw FilterControlsNotArrayError;
				}
				if(!settings.hasOwnProperty('filterUrl')){
					throw NoFilterUrlError;
				}
			}

			defaultReloadInterval = (settings.reloadInterval != null && 
				settings.reloadInterval ?  settings.reloadInterval : 
				defaultReloadInterval);
		}
		
		//validation arguments to the plugin
		validateSettings(settings);

		if(settings.hasOwnProperty("filterControls") && settings.filterControls != null){
			//create Filter Controls if filterControls have been passed to it

			//create top div with filterControls listed in it
			parent.append("<div id='filter-div'></div>");
			filterDiv = $("body #filter-div");

			if(!Array.isArray(settings['filterControls'])){
				throw FilterControlsNotArrayError;
			}else{
				//do this if the filterControls that are passed to it is an array
				for(var i = 0, k = settings.filterControls.length; i < k; i++){
					var controlLabel = settings.filterControls[i]['label'],
						controlName = settings.filterControls[i]['name'],
						control = "";
					control = "<div class='filter-element-div'><label>" + controlLabel;

					control += "<select id='"+ (filterPrefix + controlName);
					control += "'><option>--filter--</option></select></label></div>";
					filterDiv.append(control);
				}

				filterDiv.append("<button id='search'>Search</button>");
			}
		}

		//create div for holding data
		parent.append("<div id='data-div'></div>");
		
		//create hidden field in footer that contains the names of the filter
		//controls
		parent.append("<div id='data-footer-div'></div>");
		
		//function for getting the values from the FilterControls
		var getFilterControlsAndValues = function(){
			var filterName, 
				filterControlValue, 
				filterControlsAndValues = {},
				filterNames = $("#data-footer-div #filterNames").val();

			if(filterNames != undefined && filterNames != null){
				filterNames = filterNames.split(',');
				if(Array.isArray(filterNames)){
					for(var i = 0, k = filterNames.length; i < k; i++){
						filterName = filterNames[i];
						filterControlValue = $('#' + filterPrefix + filterName).val();
						filterControlsAndValues[filterName] = (filterControlValue === '--filter--' ? 
							null : filterControlValue);
					}
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

			$("body #data-div").append(("<table id='grid-proper' " +
				"class='table table-striped table-bordered table-hover" +
				" table-condensed'><thead></thead>"+
				"<tbody></tbody><tfoot></tfoot></table>"));
			tableHeader = $("body #grid-proper thead");

			for(var i = 0, k = columns.length; i < k; i++){
				var columnName = null;
				if(columns[i]['label'] != null && columns[i]['label'] != undefined){
					columnName = columns[i]['label'];
				}else{
					columnName = columns[i]['name'];
				}
				theaderRow += ("<th>" + columnName + "</th>");
			}
			if(extraControls != null && extraControls != undefined){
				theaderRow += ("<th>Operations</th>");
			}
			tableHeader.append(theaderRow);
		};
		
		//create the table for the data to be displayed
		var loadGridData = function(settings){
			var tableRow,
				columnName,
				dataListUrl = settings.dataListUrl,
				args = {index: settings.offset, limit: settings.limit},
				columns = settings.columns,
				extraControls = settings.extraControls,
				tableBody = $("body #grid-proper tbody"),
				//add the values in the filter to the arguments passed to the server
				filterControlsAndValues = getFilterControlsAndValues();

			if(filterControlsAndValues != null && filterControlsAndValues != {}){
				for(var name in filterControlsAndValues){
					args[name] = filterControlsAndValues[name];
				}
			}
			tableBody.html("");
			$.get(dataListUrl, args, function(data){
				$.each(data, function(index, value){
					tableRow = "<tr id='" + value[settings.identityColumn] + "' class='grid-row'>";
					for(var i = 0, k = columns.length; i < k; i++){
						columnName = columns[i]['name'];
						tableRow += ("<td>" + value[columnName] + "</td>");
					}

					if(extraControls != null && extraControls != undefined && Array.isArray(extraControls)){
						tableRow += "<td>";
						for(var i = 0, k = extraControls.length; i < k; i++){
							tableRow += extraControls[i];
						}
						tableRow += "</td>";
					}

					tableRow += "</tr>";
					tableBody.append(tableRow);
				});
			},'json');
		};
		
		//create pagination controls
		var createPaginationControls = function(settings){
			var cols, 
				DictName, 
				args, 
				startingPoint, 
				pageSize, 
				page, 
				resultCount, 
				numberOfPages, 
				paginationDiv, 
				pageNumber, 
				filterControlsAndValues = getFilterControlsAndValues(),
				dataFooterDiv = $("body #data-footer-div");

			$.get(settings.dataCountUrl,filterControlsAndValues, function(data){
				resultCount = data['count'];
				numberOfPages = (Math.floor(resultCount / settings.limit) + 
					(resultCount % settings.limit > 0 ? 1: 0));
				paginationDiv = "<div class='pagination'><ul>";
				for(var i = 0; i < numberOfPages; i++){
					pageNumber = i + 1;
					paginationDiv += "<li><a href='"+pageNumber+"' class='page'>"+pageNumber+"</li>";
				}
				paginationDiv += "</ul></div>";
				dataFooterDiv.append(paginationDiv);
				dataFooterDiv.append(("<input id='limit' type='hidden' value= '"+settings.limit +"' />"));
			},"json");

			$("body").off('click', '.page');
			$("body").on("click", ".page", function(event){
				event.preventDefault();
				page = Number($(this).attr("href"));
				pageSize = Number($("#limit").val());
				startingPoint = (pageSize * (page-1));
				args = getFilterControlsAndValues();
				args['offset'] = startingPoint;
				args['limit'] = pageSize;
				args['dataListUrl'] =  $('body #dataListUrl').val();
				args['dataCountUrl'] = $('body #dataCountUrl').val();
				args['columns'] = '';
				DictName = [];
				cols = $('body #columns').val().toString().split(',');
				for (var i = 0, k = cols.length; i < k; i ++){
					DictName.push({'name': cols[i]});
				}
				args['columns'] = DictName;
				args['identityColumn'] = $('body #identityColumn').val();
				args['extraControls'] = $.jStorage.get('extraControls', undefined); //$('body #extraControls').val();
				loadGridData(args);
			});
		};

		//adding the dataListUrl and dataCountUrl to the footer div for later
		//calls to the api during pagination
		var createFooterForUrls = function(settings){
			var filterNamesControl,
				dataListUrlControl, 
				dataCountUrlControl, 
				cols, 
				columns, 
				columnsControl,
				identityColumnControl,
				dataFooterDiv = $("body #data-footer-div");
			
			
			if(settings.filterControls != undefined && settings.filterControls != null){
				var filterCtrl = [];
				for(var i = 0, k = settings.filterControls.length; i < k; i++){
					filterCtrl.push(settings.filterControls[i]['name']);
				}
				filterNamesControl = "<input type='hidden' id='filterNames' value='" +
					filterCtrl.toString() +"'/>";
				
				dataFooterDiv.append(filterNamesControl);	
			}
			
			dataListUrlControl = "<input type='hidden' id='dataListUrl' value='" +
				settings.dataListUrl + "'/>";
			dataCountUrlControl = "<input type='hidden' id='dataCountUrl' value='" +
				settings.dataCountUrl + "'/>";

			cols = settings.columns;
			columns = [];
			for(var i = 0, k = cols.length; i < k; i++){
				columns.push(cols[i]['name']);
			}
			columnsControl = "<input type='hidden' id='columns' value='"+ 
				columns.toString() + "'/>";
			identityColumnControl = "<input type='hidden' id='identityColumn' value='" +
				settings.identityColumn +"' />";

			dataFooterDiv.append(dataListUrlControl).append(
				dataCountUrlControl).append(columnsControl
				).append(identityColumnControl);

			if(settings.hasOwnProperty('extraControls')){
				$.jStorage.set('extraControls', settings.extraControls);
			}
		};
		
		//This method is responsible for getting the values that have been selected
		//in the filter combo boxes
		var getValuesForFilters = function(settings){
			var args,
				contrl,
				filterString = "",
				tmp = [],
				controls = settings.filterControls,
				url = settings.filterUrl;

			if(controls != undefined && controls != null){
				for(var i = 0, k = controls.length; i < k; i++){
					tmp.push(controls[i]['name']);
				}
				filterString = tmp.toString();

				args = {'filters': filterString};

				$.get(url, args, function(data){
					for(var index in controls){
						contrl = $(("#" + filterPrefix + controls[index]['name']));
						for(var i = 0, k = data[controls[index]['name']].length; i < k; i++){
							contrl.append(("<option>"+ data[controls[index]['name']][i] +"</option>"));
						}
					}
				});
			}
		};
		
		$("body").on("click", "#search", function(event){
			event.preventDefault();

			var settings = {},
				columns,
				DictName = [];

			columns = $("#columns").val();
			columns = columns.toString().split(',');
			for(var i = 0, k = columns.length; i < k; i++){
				DictName.push({'name': columns[i]});
			}

			settings = {
				'dataListUrl': $("#dataListUrl").val(),
				'dataCountUrl': $("#dataCountUrl").val(),
				'columns': DictName,
				'identityColumn': $("#identityColumn").val(),
				'extraControls': $.jStorage.get('extraControls', undefined),
				'limit': $("#limit").val(),
				'offset': 0
			};

			loadGridData(settings);
		});

		//timeout for resetting the values in the grid
		setInterval(function(){
			console.log('redrawing grid');
			var settings = {},
				columns,
				DictName = [];

			columns = $("#columns").val();
			columns = columns.toString().split(',');

			for(var i = 0, k = columns.length; i < k; i++){
				DictName.push({'name': columns[i]});
			}

			settings = {
				'dataListUrl': $("#dataListUrl").val(),
				'dataCountUrl': $("#dataCountUrl").val(),
				'columns': DictName,
				'identityColumn': $("#identityColumn").val(),
				'extraControls': $.jStorage.get('extraControls', undefined),
				'limit': $("#limit").val(),
				'offset': 0
			};

			loadGridData(settings);
		}, defaultReloadInterval);
		
		createTableHeader(settings);
		createFooterForUrls(settings);
		loadGridData(settings);
		createPaginationControls(settings);
		getValuesForFilters(settings);

		return this;
	};

}(jQuery));