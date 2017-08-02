#!/bin/bash
extractInventoryFromDB=C:/xampp/htdocs/Brands_Amazon_API_Project/mssqldb/ExtractInventoryForAmazon.php
DBTable=vi_amazon_inv_all
inventoryFileWrittenTo=C:/xampp/htdocs/Brands_Amazon_API_Project/inbound/InvPreData.txt
regInventoryFeed=C:/xampp/htdocs/Brands_Amazon_API_Project/BrandsFinalInventorySubmitFeed.php
getFeedResults=C:/xampp/htdocs/Brands_Amazon_API_Project/BrandsGetFeedSubmissionResult.php
if [ -f $regInventoryFeed ]; then
		
	php $extractInventoryFromDB $DBTable $inventoryFileWrittenTo
	unset DBTable
	unset inventoryFileWrittenTo
	sleep 1m
	php $regInventoryFeed debug
	unset regInventoryFeed
	sleep 10m
	php $getFeedResults
fi