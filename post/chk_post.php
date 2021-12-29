<script> <?php
$postid = $_POST['postid'];

if($postid != '' and is_numeric($postid)) {
    exec('python3 ../src/dcchk.py '.$postid, $arr);
    $result = implode("\n", $arr)."\n";
    if (strpos($result, '<state>DELETED</state>') !== false) {
        
        // DB에서 삭제하기 시작
        $conn = mysqli_connect('', '', '', '');

        $postipid = '';     # 작성자 IDIP
        $ipids = array();   # 댓글단사람 IDIP

        $results=mysqli_query($conn,"SELECT ipid FROM euca_gall_posts_2021 WHERE `postid` = ".$postid);
        while($result = mysqli_fetch_array($results)) {
            $postipid = $result['ipid'];
        }

        $results=mysqli_query($conn, "SELECT DISTINCT c_ipid FROM euca_gall_cmts_2021 WHERE `cmtpostid` = ".$postid);
        while($result = mysqli_fetch_array($results)) {
            array_push($ipids, $result['c_ipid']);
        }

        $sql="DELETE FROM `euca_gall_cmts_2021` WHERE `cmtpostid` = ".$postid.";";
        $del=mysqli_query($conn,$sql);

        $sql="DELETE FROM `euca_gall_posts_2021` WHERE `postid` = ".$postid."; ";
        $del=mysqli_query($conn,$sql);



        # 글쓴이 글씀랭킹 업데이트

        # 우선 글이 있긴 한지 체크
        $sql="SELECT count(*) AS c FROM `euca_gall_posts_2021` WHERE `ipid` = '$postipid'; ";
        $results = mysqli_query($conn,$sql);
        while($result = mysqli_fetch_array($results)) {
            $postcnt = $result['c'];
        }

        if ($postcnt > 0) {

            # 있으면 업데이트
            $sql = "REPLACE
                    INTO
                        euca_gall_posts_rank_2021(
                            ipid,
                            nickname,
                            hasaccount,
                            post,
                            cmt_recieved,
                            upvote,
                            downvote,
                            recommended
                        )
                    SELECT
                        ipid,
                        nickname,
                        hasaccount,
                        COUNT(*) AS post,
                        SUM(comments) AS cmt_revieved,
                        SUM(upvotes) AS upvote,
                        SUM(downvotes) AS downvote,
                        SUM(recommended) AS recommended
                    FROM
                        `euca_gall_posts_2021` WHERE ipid = '$postipid'
                    GROUP BY
                        ipid";
            mysqli_query($conn,$sql);

        } else {
            # 아예 없음 지우기
            $sql="DELETE FROM `euca_gall_posts_rank_2021` WHERE `ipid` = '$postipid'; ";
            $del=mysqli_query($conn,$sql);
        }


        # 댓글도 각각 업데이트

        foreach ($ipids as $i) {

            # 댓글 없는지 체크
            $sql="SELECT count(*) AS c FROM `euca_gall_cmts_2021` WHERE `c_ipid` = '$i'; ";
            $results = mysqli_query($conn,$sql);
            while($result = mysqli_fetch_array($results)) {
                $cmtcnt = $result['c'];
            }

            if ($cmtcnt > 0) {
                $sql = "REPLACE
                        INTO
                            euca_gall_cmts_rank_2021(
                                ipid,
                                nickname,
                                hasaccount,
                                cmt_posted,
                                dccon_posted,
                                reply_posted
                            )
                        SELECT
                            c_ipid,
                            c_nickname,
                            c_hasaccount,
                            COUNT(*) AS cmt_posted,
                            SUM(isdccon) as dccon_posted,
                            SUM(isreply) as reply_posted
                        FROM
                            `euca_gall_cmts_2021` WHERE c_ipid = '$i'
                        GROUP BY
                            c_ipid";
                mysqli_query($conn,$sql);

            } else {
                # 아예 없음 지우기
                $sql="DELETE FROM `euca_gall_cmts_rank_2021` WHERE `ipid` = '$i'; ";
                $del=mysqli_query($conn,$sql);
            }
        }

        if ($del) {
            # echo "alert('삭제 처리 되었습니다.');";
            echo 'window.location.href="../?delsucc=1";';
        } else {
            # echo 'alert("삭제에 실패하였습니다");';
            echo 'window.location.href="../?delfail=1";';
        }

    } else {
        # echo "alert('게시글이 게시된 상태입니다. 삭제 후 다시 시도하세요.');";
        echo "window.location = document.referrer + '&delfail=1';";
    }
} else {
    echo "alert('잘못된 요청입니다.');history.back();";
}

?> </script>