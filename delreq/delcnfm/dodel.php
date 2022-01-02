<script> <?php
$id = $_POST['id'];
$code = $_POST['code'];

require('../../src/dbconn.php');

$id = mysqli_escape_string($conn, $id);
$code = mysqli_escape_string($conn, $code);

$sql="SELECT COUNT(*) AS c FROM `euca_museum_auth` WHERE id = '$id' AND code = '$code'";

$results = mysqli_query($conn,$sql);

$valid = false;

while ($result = mysqli_fetch_array($results)) {
    if ($result['c'] == 1) {
        $valid = true;
        break;
    }
}


if ($valid) {
    # 인증에 성공했으므로 -> 모두 제거하기
    $err_occured = false;

    # 댓글 지우기
    $sql="DELETE FROM euca_gall_cmts_2021 WHERE c_ipid = '$id'";
    $del=mysqli_query($conn,$sql);

    # 글 크기 파악
    $sql="SELECT COUNT(*) AS c FROM  `euca_gall_posts_2021` WHERE `ipid` = '$id'";
    $results=mysqli_query($conn,$sql);
    while ($result = mysqli_fetch_array($results)) {
        $posts = $result['c'];
    }

    # 글이 있을 때에만
    if ($posts > 0) {

        # 글의 하위댓글 지우기
        $sql="DELETE FROM euca_gall_cmts_2021 WHERE cmtid IN (SELECT cmtid FROM `euca_gall_posts_2021` LEFT OUTER JOIN euca_gall_cmts_2021 ON postid = cmtpostid WHERE ipid = '$id')";
        $del=mysqli_query($conn,$sql);


        # 게시글 지우기
        $sql="DELETE FROM `euca_gall_posts_2021` WHERE `ipid` = '$id'; ";
        $del=mysqli_query($conn,$sql);

    }

    # 글랭킹 지우기
    $sql="DELETE FROM `euca_gall_posts_rank_2021` WHERE `ipid` = '$id'; ";
    $del=mysqli_query($conn,$sql);

    # 댓글랭킹 지우기
    $sql="DELETE FROM `euca_gall_cmts_rank_2021` WHERE `ipid` = '$id'; ";
    $del=mysqli_query($conn,$sql);

    echo 'window.location.href="../../?delsucc=1";';

} else {
    echo "window.location = document.referrer + '&fail=1';";
}




?> </script>