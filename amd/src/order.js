/*define(['jquery'], function($) {
    return {
      init: function() {
        var table = new Tabulator("#example-table", {
            data:tabledata,           //load row data from array
            layout:"fitColumns",      //fit columns to width of table
            responsiveLayout:"hide",  //hide columns that dont fit on the table
            tooltips:true,            //show tool tips on cells
            addRowPos:"top",          //when adding a new row, add it to the top of the table
            history:true,             //allow undo and redo actions on the table
            pagination:"local",       //paginate the data
            paginationSize:7,         //allow 7 rows per page of data
            movableColumns:true,      //allow column order to be changed
            resizableRows:true,       //allow row order to be changed
            initialSort:[             //set the initial sort order of the data
                {column:"name", dir:"asc"},
            ],
            columns: [
                { title: "Name", field: "name" },
                { title: "Progress", field: "progress", sorter: "number" },
                { title: "Gender", field: "gender" },
                { title: "Rating", field: "rating" },
                { title: "Favourite Color", field: "col" },
                { title: "Date Of Birth", field: "dob", align: "center" },
            ]      
        });
        
        var tabledata = [
            { id: 1, name:"Oli", progress: "Oli Bob", gender: "12", rating: "red", col: "10", dob: "" }];
        
        table.setData(tabledata);
      }
    }
});
*/
define(['jquery'], function($) {
    return {
        init: function(returnData) {
            // Make sure the window has loaded before we perform processing.
            $(window).ready(function() {
                var table = new Tabulator("#example-table", {
                    data:tabledata,           //load row data from array
                    layout:"fitColumns",      //fit columns to width of table
                    responsiveLayout:"hide",  //hide columns that dont fit on the table
                    tooltips:true,            //show tool tips on cells
                    addRowPos:"top",          //when adding a new row, add it to the top of the table
                    history:true,             //allow undo and redo actions on the table
                    pagination:"local",       //paginate the data
                    paginationSize:7,         //allow 7 rows per page of data
                    movableColumns:true,      //allow column order to be changed
                    resizableRows:true,       //allow row order to be changed
                    initialSort:[             //set the initial sort order of the data
                        {column:"name", dir:"asc"},
                    ],
                    columns: [
                        { title: "Name", field: "name" },
                        { title: "Progress", field: "progress", sorter: "number" },
                        { title: "Gender", field: "gender" },
                        { title: "Rating", field: "rating" },
                        { title: "Favourite Color", field: "col" },
                        { title: "Date Of Birth", field: "dob", align: "center" },
                    ]      
                });
                
                var tabledata = [
                    { id: 1, name:"Oli", progress: "Oli Bob", gender: "12", rating: "red", col: "10", dob: "" }];
                
                table.setData(tabledata);
 
                
            });
        }
    };
});