<?php 

$servername = "localhost";
$username = "root";
$password = "root";

if(isset($_GET['channel_id'])){
    $channel_id = $_GET['channel_id'];

    $conn = new mysqli($servername, $username, $password, 'sociablekit');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $data = [];
    $sql1 = "SELECT * FROM youtube_channels WHERE id =".$channel_id;
    $result = $conn->query($sql1)->fetch_assoc();
    if($result){
        $sql2 = "SELECT * FROM youtube_channel_videos WHERE channel_id =".$result['id'];
        $videos = $conn->query($sql2)->fetch_all(MYSQLI_ASSOC);
        
        $data['channel'] = $result;
        $data['video_list'] = $videos;
        echo json_encode($data);
    }else{
        echo 'No Channel Saved';
    }
}else{
    echo 'No ID Passed!';
}
?>