<script> <?php
$id = $_POST['id'];
$id = str_replace(' ', '', $id);

if (ctype_alnum($id) == false) {

    echo "window.location = document.referrer + '?fail=1';";

} elseif($id != '') {

    require('../../src/dbconn.php');

    $id = strtolower($id);
    $id = mysqli_escape_string($conn, $id);
    
    # 우선 ID가 박물관에 존재하는지 체크

    $sql="SELECT COUNT(*) AS c FROM `euca_gall_posts_2021` WHERE ipid = '$id'";
    $results = mysqli_query($conn,$sql);

    while ($result = mysqli_fetch_array($results)) {
        $posts = $result['c'];
    }

    $sql="SELECT COUNT(*) AS c FROM `euca_gall_cmts_2021` WHERE c_ipid = '$id'";
    $results = mysqli_query($conn,$sql);

    while ($result = mysqli_fetch_array($results)) {
        $cmts = $result['c'];
    }

    if ($posts == 0 and $cmts == 0) {
        echo "window.location = document.referrer + '?fail=2';";

    } else {
        exec('python3 ../src/auth.py '.$id, $arr);
        $result = implode("\n", $arr)."\n";
        if (strpos($result, '<state>ERROR</state>') !== false) {
            echo "window.location = document.referrer + '?fail=1';";
        } else {
            echo 'window.location.href="../authchk/?id='.$id.'";';
        }

    }



    

} else {

    echo "alert('잘못된 요청입니다.');history.back();";

}

?> </script>