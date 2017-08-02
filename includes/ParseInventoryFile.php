<?php
	// used for testing purposes	
	//parseAndReplace("../inbound/RegInvPreData.txt");

	//Function used the in the BrandsFinalInventorySubmitFeed.php file to remove leading zeros in price column
	function parseAndReplace($filein) {

		// Open the file we are reading from the insert into the DB
			$file = fopen($filein,"r");
			$i = 0;

			// Fill an array with all the records in the status.csv file
			while(!feof($file)){
				$thisLine = fgets($file);
				$personData[$i] = explode("\t" , $thisLine);

				if(isset($personData[$i][0]) && !empty($personData[$i][0]))
				{
					// Remove the leading zeros in the decimal by casting (float) to the converted float value that's extracted from the file
					if($i != 0){
						$personData[$i][1] = floatval((float)$personData[$i][1]);
					}
					


					 if(isset($personData[$i][3]) && !empty($personData[$i][3])){
						$personData[$i][3] = trim($personData[$i][3]);
					 } 

				}
				$i++;
			}
			var_dump($personData);
			//rewind($file);
			// open a new file stream to rewrite all the data that we filled into the array ( for remove leading zeros) back to the same file we read from ( because it's all the same data just removing leading zero from the Price Column)
			$file = fopen($filein,"w");
			//fwrite($file, "sku\t"."price\t"."quantity\t"."leadtime-to-ship\n");

			// For loop to wrtie the contents from the array back into the file.
			for( $x = 0; $x < sizeof($personData) - 1  ; $x++){
				var_dump($personData[$x]);
				//fputcsv($file, $personData[$x], "\t");
				$data = buildString($personData[$x],$x,sizeof($personData));
				fwrite($file, $data);
			}
			//close resources
			fclose($file);
	}

	// used to build the appropriate string for a new line in the file.
	function buildString($array, $offset, $size) {
		$string = $array[0] . "\t" . $array[1] . "\t" .$array[2]."\t";
		// if(!isset($array[0]) OR empty($array[0]) OR !isset($array[1]) OR empty($array[1]) OR !isset($array[2]) OR empty($array[2])  ){
		// 	return false;
		// }
		if(isset($array[3]) AND !empty($array[3])){
			$string .= $array[3];
		}
		if($offset == ($size - 1) ){
			return $string;
		} else { 
			return $string .="\n";
		}
	}



?>