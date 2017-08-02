#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
extractInventoryFromDB=$DIR/../mssqldb/ExtractInventoryForAmazon.php
DBTable=vi_amazon_inv
inventoryFileWrittenTo=$DIR/../inbound/RegInvPreData.txt
regInventoryFeed=$DIR/../BrandsRegInventorySubmitFeed.php
getFeedResults=$DIR/../BrandsGetFeedSubmissionResult.php
if [ -f $regInventoryFeed ]; then
	for i in {1..2}
	do
		php $extractInventoryFromDB $DBTable $inventoryFileWrittenTo
		unset DBTable
		unset inventoryFileWrittenTo
		sleep 2m
		php $regInventoryFeed debug
		unset regInventoryFeed
		sleep 10m
		php $getFeedResults
		unset getFeedResults
		sleep 2m
		DBTable=vi_amazon_grid_inv
		regInventoryFeed=$DIR/BrandsGridInventorySubmitFeed.php 
		inventoryFileWrittenTo=$DIR/../inbound/GridInvPreData.txt
	done
fi