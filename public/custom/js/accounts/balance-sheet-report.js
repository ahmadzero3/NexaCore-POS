$(function() {
	"use strict";

    $(document).ready(function() {
      // Select the tables
      var table1 = $('.assetTree tbody tr');
      var table2 = $('.equityLiabilityTree tbody tr');

      // Find the longer table
      var longerTable = table1.length > table2.length ? table1 : table2;

      // Create the new table
      var newTable = $('<table id="tester-report"></table>');
      newTable.append('<thead><tr><th>Name</th><th>Value</th><th>Name</th><th>Value</th></tr></thead>');


      // Loop through the longer table (ensures all rows are included)
      longerTable.each(function() {

        //var row1 = $(this).clone(); // Clone the current row

        var row1 = table1.eq($(this).index());

        // Get corresponding row from the shorter table (if it exists)
        var row2 = table2.eq($(this).index());

        // Create new cells for missing data (if shorter)
        if (!row2.length) {
          row2 = $('<tr><td></td><td></td><td></td><td></td></tr>');
        }

        // Combine the rows into a new row
        var newRow = $('<tr></tr>');
        newRow.append('<td>' + row1.find('td').eq(0).text() + '</td>'); // Name from table1
        newRow.append('<td>' + row1.find('td').eq(1).text() + '</td>'); // Value from table1
        newRow.append('<td>' + row2.find('td').eq(0).text() + '</td>'); // Name from table1
        newRow.append('<td>' + row2.find('td').eq(1).text() + '</td>'); // Value from table1
        // Add the new row to the table
        newTable.append(newRow);
      });

      // Insert the new table into a container (optional)
      $('#combined-table').append(newTable);
    });


    $("#btnExport").click(function () {
      // Get the table element with the specific ID
      var table = $("#tester-report");

      // Ensure the table exists before proceeding
      if (table.length > 0) {
        // Convert the table to Excel using TableToExcel library
        TableToExcel.convert(table[0], {
          name: `UserManagement.xlsx`,
          sheet: {
            name: 'Usermanagement'
          }
        });
      } else {
        // Handle scenario where table with ID "test-table" is not found
        console.error("Table with ID 'test-table' not found!");
      }
    });
    
});//main function
