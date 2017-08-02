<?php
	
	runInventoryQueryOn($argv[1],$argv[2]);

	/*
	* Function used the run the query on the Micorosft SQL Server Database to create the tab delimited .txt file
	* used to submit to Amazon.
	* @param - $table-  Table the query is ran on.
	* @param - $filePath- File write the results too.
	*/
	function runInventoryQueryOn($table, $filePath){
	
		// Database Credentials
		$server = "************";
		$connectionConfig = array(
			"Database" => "*********",
			"Uid" => "*********",
			"PWD" => "*********"
		);
		$dbTable = $table;

		// Connection Variable
		$conn = sqlsrv_connect($server, $connectionConfig);

		// Error catcher incase connection failed.
		if( !$conn ){
			echo formatErrors(sqlsrv_errors());
		}

		// Query used to extract the data needed from the Microsoft SQL Server DB
		$sql = "SELECT sku, price, quantity, leadtime FROM ".$dbTable." ORDER BY quantity";
		//$sql = "SELECT TOP 5 sku, price, quantity, leadtime FROM ".$dbTable." ORDER BY quantity";
		// $sql = "SELECT sku, price, quantity, leadtime 
		// 		FROM vi_amazon_inv
		// 		WHERE sku in ('856-0546', '856-0548-B','9973272','9973274')";

		// Runs the Query
		$query = sqlsrv_query($conn, $sql);
		// Checks if the query had erros
		if( !$query )
			echo formatErrors(sqlsrv_errors());

		// Retreieve the row count for later
		$rowCount = sqlsrv_num_rows($query);
		$i = 0;
		
		// Open the file stream to write the result too
	    $file = fopen($filePath, 'w');
	   	fwrite($file, "sku\t"."price\t"."quantity\t"."leadtime-to-ship\n");

	   	// Retrieve the results from the query and write them into the $filePaht file
		while($row = sqlsrv_fetch_array( $query, SQLSRV_FETCH_ASSOC)){


			$string = $row['sku'] . "\t" . $row['price'] . "\t" .$row['quantity'] . "\t" ;//. $row['leadtime-to-ship'] ;
			
			// Incase leadtime has a value of null		
			if(!empty($row['leadtime']) && isset($row['leadtime']) ){
				$string .= $row['leadtime'] . "\n";
			} else {
				
				if($i == ($rowCount - 1)){} 
				else {
					$string .= "\n";
				}
			}

			fwrite($file, $string);

			$i++;
		}

		//Free resources
		sqlsrv_free_stmt( $query);
		fclose($file);
	}



	// Function used to format the errors printed by the sqlsrv_errors() function
	function FormatErrors( $errors )  
	{  
	    /* Display errors. */  
	    echo "Error information: \n";  
	  
	    foreach ( $errors as $error )  
	    {  
	        echo "SQLSTATE: ".$error['SQLSTATE']."\n";  
	        echo "Code: ".$error['code']."\n";  
	        echo "Message: ".$error['message']."\n";  
	    }  
	}  
?>