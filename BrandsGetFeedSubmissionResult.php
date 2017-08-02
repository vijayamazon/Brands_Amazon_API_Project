<?php


/**
 * Brands Cycle Get Feed Submission Result
 */
// Require_once for all Feeds API PHP Library dependencies
 require_once('vendor/MarketplaceWebService/Model.php');
 require_once('C:/xampp/htdocs/Brands_Amazon_API_Project/vendor/MarketplaceWebService/Client.php');
 require_once('vendor/MarketplaceWebService/Model/GetFeedSubmissionResultRequest.php');


 require_once('vendor/MarketplaceWebService/Interface.php');
 require_once('vendor/MarketplaceWebService/Exception.php');

// Holds configuration constants
include_once ('config/.config.inc.php'); 

/************************************************************************
* Uncomment to configure the client instance. Configuration settings
* are:
*
* - MWS endpoint URL
* - Proxy host and port.
* - MaxErrorRetry.
***********************************************************************/
// IMPORTANT: Uncomment the appropriate line for the country you wish to
// sell in:
// United States:
$serviceUrl = "https://mws.amazonservices.com";


$config = array (
  'ServiceURL' => $serviceUrl,
  'ProxyHost' => null,
  'ProxyPort' => -1,
  'MaxErrorRetry' => 3,
);

/************************************************************************
 * Instantiate Implementation of MarketplaceWebService
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 ***********************************************************************/
 $service = new MarketplaceWebService_Client(
     AWS_ACCESS_KEY_ID, 
     AWS_SECRET_ACCESS_KEY, 
     $config,
     APPLICATION_NAME,
     APPLICATION_VERSION);
 
// Opens the temporary cache we used to save the FeedSubmissionID from the SubmitFeed Script
$feedIdFile = fopen('c:/xampp/htdocs/Brands_Amazon_API_Project/inbound/FeedSubmissionID.txt', 'r');
$value = fgets($feedIdFile, 13);
fclose($feedIdFile);


// Builds the FeedSubmissionResultRequest object
$request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
$request->setMerchant(MERCHANT_ID);
$request->setFeedSubmissionId(trim($value));
// Append it to M:/Amazon/php_Log/Submission_Log
$request->setFeedSubmissionResult( fopen('M:/Amazon/php_Log/Submission_Log'.time()."", 'w') );   //fopen('c:/xampp/htdocs/Brands_Amazon_API_Project/results/text', 'w'));
//$request->setMWSAuthToken('<MWS Auth Token>'); // Optional
     
invokeGetFeedSubmissionResult($service, $request);

/**
  * Get Feed Submission Result Action Sample
  * retrieves the feed processing report
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interfacepy
  * @param mixed $request MarketplaceWebService_Model_GetFeedSubmissionResult or array of parameters
  */
  function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request) 
  {
      try {
              $response = $service->getFeedSubmissionResult($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetFeedSubmissionResultResponse\n");
                if ($response->isSetGetFeedSubmissionResultResult()) {
                  $getFeedSubmissionResultResult = $response->getGetFeedSubmissionResultResult(); 
                  echo ("            GetFeedSubmissionResult");
                  
                  if ($getFeedSubmissionResultResult->isSetContentMd5()) {
                    echo ("                ContentMd5");
                    echo ("                " . $getFeedSubmissionResultResult->getContentMd5() . "\n");
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
?>
                              
