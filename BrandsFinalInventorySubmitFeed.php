<?php

// Require_once for Feeds API PHP Library Dependencies
 require_once('vendor/MarketplaceWebService/Model.php');
 require_once('C:/xampp/htdocs/Brands_Amazon_API_Project/vendor/MarketplaceWebService/Client.php');
 require_once('vendor/MarketplaceWebService/Interface.php');
 require_once('vendor/MarketplaceWebService/Exception.php');
require_once('vendor/MarketplaceWebService/Model/SubmitFeedRequest.php');

// Include_once for File Parsing functions and configuration constants.
include_once ('includes/ParseInventoryFile.php');
include_once ('config/.config.inc.php');

/************************************************************************
* Uncomment to configure the client instance. Configuration settings
* are:
*
* - MWS endpoint URL
* - Proxy host and port.
* - MaxErrorRetry.
***********************************************************************/
// IMPORTANT: Uncomment the approiate line for the country you wish to
// sell in:
// United States:
$serviceUrl = "https://mws.amazonservices.com";
// United Kingdom
//$serviceUrl = "https://mws.amazonservices.co.uk";
// Canada
//$serviceUrl = "https://mws.amazonservices.ca";


// Configuration Array for the MarketplaceWebService_Client object
$config = array (
  'ServiceURL' => $serviceUrl,
  'ProxyHost' => null,
  'ProxyPort' => -1,
  'MaxErrorRetry' => 3,
);

//MWS_Client Object with the AWS credentials passed in the parameters
 $service = new MarketplaceWebService_Client(
     AWS_ACCESS_KEY_ID, 
     AWS_SECRET_ACCESS_KEY, 
     $config,
     APPLICATION_NAME,
     APPLICATION_VERSION);
 
// File path passed into the arguments on the CLI
$inboundFile = $argv[1];

// Remove Leading Zeros - SEE ParseInventoryFile.php for more info.
parseAndReplace($inboundFile);

// ID of the marketplace Brands Cycle is in.
$marketplaceIdArray = array("Id" => array('***********'));
     

 
// Open the file we want to send to Amazon
$feedHandle = fopen($inboundFile, 'r');


/************************************************************************
 * Setup request parameters 
 ***********************************************************************/

$parameters = array (
 'Merchant' => MERCHANT_ID,
 'MarketplaceIdList' => $marketplaceIdArray,
 'FeedType' => '_POST_FLAT_FILE_INVLOADER_DATA_',
 'FeedContent' => $feedHandle,
 'PurgeAndReplace' => false,
 'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true))
);

rewind($feedHandle);
 // @TODO: set request. Action can be passed as MarketplaceWebService_Model_SubmitFeedRequest
$request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);

// Invokes SubmitFeed function to send the request to amazon and print the responses.
invokeSubmitFeed($service, $request);

@fclose($feedHandle);
                                        
/**
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_SubmitFeed or array of parameters
  */
  function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request) 
  {
      try {
              $response = $service->submitFeed($request);

                // Write the FeedSubmissionID we get back from the SubmitFeedRequest into our "cache" which is a .txt file
                $feedIdFile = fopen('c:/xampp/htdocs/Brands_Amazon_API_Project/inbound/FeedSubmissionID.txt', 'w');

                fwrite($feedIdFile, $response->getSubmitFeedResult()->getFeedSubmissionInfo()->getFeedSubmissionId());
                fclose($feedIdFile);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        SubmitFeedResponse\n");
                if ($response->isSetSubmitFeedResult()) { 
                    echo("            SubmitFeedResult\n");
                    $submitFeedResult = $response->getSubmitFeedResult();
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                        echo("                FeedSubmissionInfo\n");
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                            echo("                    FeedSubmissionId\n");
                            echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedType()) 
                        {
                            echo("                    FeedType\n");
                            echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetSubmittedDate()) 
                        {
                            echo("                    SubmittedDate\n");
                            echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                        {
                            echo("                    FeedProcessingStatus\n");
                            echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                        {
                            echo("                    StartedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                        {
                            echo("                    CompletedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                    } 
                } 
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     } catch (MarketplaceWebService_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }
      