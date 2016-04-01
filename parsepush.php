<?php

require 'autoload.php';

$app_id = "IY38xjKRMweSOAngmu6ePm8EMBYyYwEdpqVUBqAv";
$rest_key = "cuYIVxyJMs2x1ntYz6A49NpjoHmjNd248MjAFtx9";
$master_key = "2WGuzkAnUYBCY2wrtkXmcGCINXzZonjCv9nnQpXA";

ParseClient::initialize( $app_id, $rest_key, $master_key );

use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;
use Parse\ParseClient;

echo "abc";

?>