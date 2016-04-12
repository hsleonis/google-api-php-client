<?php
 require_once 'google-api-php-client/src/Google_Client.php';
    require_once 'google-api-php-client/src/contrib/Google_YouTubeService.php';

    $client = new Google_Client();
    $client->setApplicationName('Application Name');
    $client->setClientId('CLIENT_ID');
    $client->setClientSecret('CLIENT_SECRET_CODE');
    $client->setRedirectUri('REDIRECT_URI');
    $client->setDeveloperKey('DEVELOPER_KEY');

    $youtube = new Google_YouTubeService($client);
    if (isset($_GET['code'])) {
        $client->authenticate();
        $_SESSION['token'] = $client->getAccessToken();
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    }

    if (isset($_SESSION['token'])){
        $client->setAccessToken($_SESSION['token']);
    }

    if ($client->getAccessToken()){
        $path_to_video_to_upload = $_FILES["video"]["tmp_name"];
        $mime_type = $_FILES["video"]["type"];
        $snippet = new Google_VideoSnippet();
        $snippet->setTitle('Highlights');
        $snippet->setDescription('Description Of Video');
        $snippet->setTags(array('training', 'Soccer'));
        $snippet->setCategoryId(17);

        $status = new Google_VideoStatus();
        $status->privacyStatus = "private";

        $video = new Google_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        try {
            $obj = $youtube->videos->insert("status,snippet", $video,
            array("data"=>file_get_contents($path_to_video_to_upload), 
            "mimeType" => $mime_type));
            } catch(Google_ServiceException $e) {
            print "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage()." <br>";
            }

    $_SESSION['token'] = $client->getAccessToken();
    }

    else{
            $authUrl = $client->createAuthUrl();
            print "<a href='$authUrl'>Connect Me!</a>";
    }
