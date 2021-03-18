
<?php

$servername = "localhost";
$username = "root";
$password = "root";
$key = 'AIzaSyD4ZPQzcafhwaTueXrNNXb28zQsU9eEUrc';
$base_url = 'https://www.googleapis.com/youtube/v3/';
$max_vid = 60;
$channel_user = 'NBA';

$get_channel_details = $base_url . 'channels?part=snippet,brandingSettings&forUsername='.$channel_user.'&key='.$key;
$channel_details =  json_decode( file_get_contents($get_channel_details));

if($channel_details){
    $profile_pic = $channel_details->items[0]->snippet->thumbnails->high->url;
    $name = $channel_details->items[0]->snippet->title;
    $description = addslashes($channel_details->items[0]->snippet->description);
    $channel_id = $channel_details->items[0]->id;
    $channel_banner = $channel_details->items[0]->brandingSettings->image->bannerExternalUrl;

    $conn = new mysqli($servername, $username, $password, 'sociablekit');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $sql1 = "INSERT INTO youtube_channels (profile_pic, name, description,banner_url) VALUES ('".$profile_pic."', '".$name."', '".$description."', '".$channel_banner."')";

    if ($conn->query($sql1) === TRUE) {
        $last_id = $conn->insert_id;

        $get_channel_vid = $base_url . 'activities?part=snippet,contentDetails&channelId='.$channel_id.'&maxResults='.$max_vid.'&key='.$key;
        $channel_videos = json_decode( file_get_contents($get_channel_vid));
        // echo '<pre>';
        // print_r($channel_videos);die();
        if($channel_videos){
            $video_data = [];
            foreach($channel_videos->items as $key => $item){
                $video_data[$key] = array(
                    'channel_id' => $last_id,
                    'video_link' => 'https://www.youtube.com/watch?v='. $item->contentDetails->upload->videoId,
                    'title' => addslashes($item->snippet->title),
                    'description' => addslashes($item->snippet->description),
                    'thumbnail' => $item->snippet->thumbnails->high->url
                );
            }

            $sql2 = "INSERT INTO youtube_channel_videos_2 (channel_id, video_link, title, description, thumbnail) VALUES";
            $arr = [];
            foreach($video_data as $key => $item){
                $arr[] = '("'.$item["channel_id"].'", "'.$item["video_link"].'", "'.$item["title"].'", "'.$item["description"].'", "'.$item["thumbnail"].'")';
            }
            $sql2 .= implode(',', $arr);

            if ($conn->query($sql2) === TRUE) {
                echo "Channel Details and Videos saved in Database!";
            }else {
                echo '<pre>';
                echo "Error: " . $sql2 . "<br>" . $conn->error;
            }
        }
    } else {
        echo '<pre>';
        echo "Error: " . $sql1 . "<br>" . $conn->error;
    }

    $conn->close();

}

?>