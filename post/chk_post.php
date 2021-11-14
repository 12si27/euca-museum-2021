<script> <?php
$postid = $_POST['postid'];

if($postid != '' and is_numeric($postid)) {
    exec('python3 ../src/dcchk.py '.$postid, $arr);
    $result = implode("\n", $arr)."\n";
    if (strpos($result, '<state>DELETED</state>') !== false) {
        
        // DB에서 삭제하기 시작
        $conn=mysqli_connect('', '', '', '');

        $sql="DELETE FROM `euca_gall_cmts_2021` WHERE `cmtpostid` = ".$postid.";";
        $del=mysqli_query($conn,$sql);

        $sql="DELETE FROM `euca_gall_posts_2021` WHERE `postid` = ".$postid."; ";
        $del=mysqli_query($conn,$sql);

        if ($del) {
            echo "alert('삭제 처리 되었습니다.');";
            echo 'window.location.href="../";';
        } else {
            echo 'alert("삭제에 실패하였습니다");';
        }

    } else {
        echo "alert('게시글이 게시된 상태입니다. 삭제 후 다시 시도하세요.');history.back();";
    }
} else {
    echo "alert('잘못된 요청입니다.');history.back();";
}

?> </script>