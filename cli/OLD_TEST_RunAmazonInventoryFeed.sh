#!/bin/bash
extractInventoryFromDB=C:/xampp/htdocs/Brands_Amazon_API_Project/mssqldb/ExtractInventoryForAmazon.php
DBTable=vi_amazon_inv
inventoryFileWrittenTo=C:/xampp/htdocs/Brands_Amazon_API_Project/inbound/RegInvPreData.txt
regInventoryFeed=C:/xampp/htdocs/Brands_Amazon_API_Project/BrandsRegInventorySubmitFeed.php
getFeedResults=C:/xampp/htdocs/Brands_Amazon_API_Project/BrandsGetFeedSubmissionResult.php
if [ -f $regInventoryFeed ]; then
	for i in {1..2}
	do
		php $extractInventoryFromDB $DBTable $inventoryFileWrittenTo
		unset DBTable
		unset inventoryFileWrittenTo
		sleep 1m
		php $regInventoryFeed debug
		unset regInventoryFeed
		sleep 8m
		php $getFeedResults
		sleep 10m
		DBTable=vi_amazon_grid_inv
		regInventoryFeed=C:/xampp/htdocs/Brands_Amazon_API_Project/BrandsGridInventorySubmitFeed.php 
		inventoryFileWrittenTo=C:/xampp/htdocs/Brands_Amazon_API_Project/inbound/GridInvPreData.txt
	done
fi