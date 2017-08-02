#!/bin/bash
# Variables holding the file paths for the PHP scripts used
# you shouldn't have to change unlesss xamp is installed on another harddrive
extractInventoryFromDB=C:/xampp/htdocs/Brands_Amazon_API_Project/mssqldb/ExtractInventoryForAmazon.php
finalInventoryFeed=C:/xampp/htdocs/Brands_Amazon_API_Project/BrandsFinalInventorySubmitFeed.php
getFeedResults=C:/xampp/htdocs/Brands_Amazon_API_Project/BrandsGetFeedSubmissionResult.php
#Variables susceptible to change if database table changes, or location / name of the tab
#delimited .txt file changes
inventoryFileWrittenTo=C:/xampp/htdocs/Brands_Amazon_API_Project/inbound/InvPreData.txt
DBTable=vi_amazon_inv_all
# if Statement to verify the File is there
if [ -f $finalInventoryFeed ]; then
	#Run the ExtractFromDB script passing the DBTable Variable and InventoryFileWrittenTo arguments
	php $extractInventoryFromDB $DBTable $inventoryFileWrittenTo
	#Sleep the client the give time for processing
	sleep 1m
	#Run the SubmitFeed with the path file the inventory data is located passed into the arguments
	php $finalInventoryFeed $inventoryFileWrittenTo
	#Sleep the client for 8 minutes to allow amazon to process the feed in order to produce the result
	sleep 8m
	#Runs the php script to create the API request that retrieves the result of the submission feed
	php $getFeedResults
fi