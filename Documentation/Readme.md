# Brands Cycle Workaround the AMTU using the Brands Feed API

This project is used as a way for Brands Cycle and Fitness to submit large inventory feeds to Amazon marketplace without having to use the AMTU (Amazon Merchant Transport Utility) as the AMTU is slow and is known to freeze or timeout in the middle of the processes. Instead we send the feeds through our own Shell/PHP Script which makes the process alot quicker and adaptable to changes. 


## Getting Started

Start off by visting the Download Links section and install all needed drivers / software. Than download the file into the xampp/htdocs directory.


### Prerequisites

The Microsoft SQL Server and ODBC Driver must be installed on the computer the application/script is running on. Also need the sqlsrv driver for PHP in order for PHP to interact with the Microsoft SQL Server in Brands Cycle and Fitness. Needs to be ran on a web server, e.g. Apache, etc. Will most likely be ran locally as there's no need for the application to be ran on a live network or host.(* I used xampp*).



### Files Needed 

*.config.inc.php* - Configuration File for Access Keys, Merchant ID's, etc
*RunAmazonInventory.sh* - Shell script that runs the 2 php scripts which send the inventory feed to Amazon, and log the results.
*BrandsFinalInventorySubmitFeed.php* - PHP Script used to build the API request (using the feeds API library) that sends the Feed to Amazon.
*BrandsGetFeedSubmissionResult.php* - PHP Script used the build the API Request to log the result of the Feed Submission request prior *impotant: shell must sleep 8-10 mins because of the buffer between the request to send a feed submission and recieve the result.  
*ExtractInventoryForAmazon.php* - Script used to query the Microsoft SQL Server Database and write the inventory (as well as the attributes needed for the API request) into a tab delimeted .txt file.
*ParseInventoryFile.php* - Used in the BrandsFInalInventorySubmitFeed.php to remove leading zero in the price column of the .txt file.


### Files Created

*InvPreData.txt* - Text file created by ExtractInventoryForAmazon.php which containts 30,000+ inventory records to send to Amazon.
*FeedSubmissionID.txt* - Acts as a temporary cache for the FeedSubmissionID recieved from the SUbmit request and needed for retrieving the result in BrandsGetFeedSubmissionResult.php
*M:/Amazon/php_Log/Submission_Log* - Where the results of the BrandsGetFeedSubmissionResult.php are written to.


## Download links for dependencies needed by the scripts

* [Amazon_Feeds_API_PHP_Library](https://developer.amazonservices.com/doc/bde/feeds/v20090101/php.html) - The php library used
* [Microsoft_SQL_Server](https://www.microsoft.com/en-us/sql-server/sql-server-2016) - DBMS I interacted with
* [XAMPP_Download](https://www.apachefriends.org/download.html) - AMP Stack used to host the server
* [Microsoft_Drivers_For_PHP_SQLServer](https://github.com/Microsoft/msphpsql/releases) - Binaries for the PHP SQLServer driver, simply download the zip and place the 2 files into xampp/php/tmp
* [MIcrosoft_ODBC_Driver_13](https://www.microsoft.com/en-us/download/details.aspx?id=50420) - ODBC Driver for SQL Server


### Configuration / Installation

Once the files are placed on the computer you'd like to run the shell script on, double check all paths in the application files to verify everything is in place. Please try to use c:/xampp/htdocs/Brands_amazon_API_Project or you will have to modify paths in the shell script (RunAmazonInventory.sh). Make sure that a SQL Server instance is up and running and your Web Server is running (*please try using xampp as I built the application around the xampp stack*), also make sure that the sqlsrv driver is installed into PHP. To run the script cd into the directory the RunAmazonInventoryFeed.sh is, which should be Brands_Amazon_API_Project/cli/RunAmazonInventoryFeed.sh.

If the User would like to change values go into the shell script and change the paths of the $InventoryFileWrittenTo to change the paths of the files written to and opened by the SubmitFeed script. Or if the DB Table changes you can also change the $DBTable variable. I provided these for the user of the script to be able to configure the script to the environment it's ran in.



### How It Works

Open a command prompt and cd into the cli directory and run the shell script
**Will most likely be run on a task schedule or CRON job**

```
> cd C:/xampp/htdocs/Brands_Amazon_API_Project/cli
> sh RunAmazonInventoryFeed.sh 
```

This will invoke the shell script which initiates the flow of events as follows.

First the inventory is extracted from the vi_amazon_inv_all table in the Microsoft SQL Database to create the file that holds the 30,000+ records to be updated in amazon.

```
> php $extractInventoryFromDB $DBTable $InventoryFileWrittenTo
> sleep 1m
```
Than the Feed is parsed (*to remove leading zeros in decimals*) API request is built and sent to Amazon to integrate the inventory. in the following lines take in an argument in case the user of the app would like to change the inventory file that the query results or written to and opened for submission of api request

```
> php $finalInventoryFeed $InventoryFileWrittenTo
> sleep 8m
```
The script sleeps for 8 minutes in order to provide a buffer in between the getFeedResults script and the finalInventoryFeed Script. (*it usually takes about 6-10 minutes for amazon to send back the result*). The getFeedResults script is than ran to see the result of our submission (*if it was successful 
or not*) and the results are written to M:/Amazon/php_Log/text

```
> php $getFeedResults
```

Once the process is completed the inventory is processed and Amazon is now synchronised with our inventory.


## Running the tests

To Test the  application please try and modify the ExtractInventoryForAmazon.php to only get the top 10 sku, price, quantity, leadtime-to-ship so you can send a small request with a couple products to update and see the response you get back.

In the $sql variable in ExtractInventroyForAmazon.php for testing purposes (*Smaller feeds than 30,000*) Change the query to be

```
SELECT top 10 sku, price, quantity, leadtime FROM vi_amazon_inv_all ORDER BY quantity
```


### Break down into end to end tests

you can also test the scripts by cd'ing into the directory the BrandsGetFeedSubmissionResult, BrandsFinalInventorySubmit feed are located and running it straight from the command line

```
> php BrandsGetFeedSubmissionResult.php
```
or
```
> php BrandsFinalInventorySubmitFeed.php
```
You can also test the other scripts used in the two files above independently , for example mssqldb/ExtractInventoryForAmazon.php takes in command line arguments so you can cd into the directory and run the script with two arguments example:
```
> cd c:/xampp/htdocs/Brands_Amazon_API_Project/mssqldb
> php ExtractInventoryForAmazon.php vi_amazon_inv_all c:/xampp/htdocs/inbound/InvPreData.txt
```

  
## Deployment

Should be deployed locally as there is no need for this script to be live. XAMPP is the preffered local stack to use when using the application.


## Authors

* **James McCarthy** - *Initial work* - [jmccarthy92](https://github.com/jmccarthy92)


## Acknowledgments

* Rob O for initiating and creating the project idea/ Also creating the Microsoft SQL tables making scripting **alot** easier.
* Dan S for hiring me at Brands Cycle and Fitness.
* Amazon for making a wicked awesome easy to work with PHP Library for the Feeds API
