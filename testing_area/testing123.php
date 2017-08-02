<?php

	$feedIdFile = fopen('../inbound/FeedSubmissionID.txt', 'w');
                fwrite($feedIdFile, "292292292292");
                fclose($feedIdFile);


	$feedIdFile = fopen('../inbound/FeedSubmissionID.txt', 'r');
				$value = fgets($feedIdFile, 13);
				echo $value;


?>

