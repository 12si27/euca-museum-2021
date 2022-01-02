<?php

// 디시콘 랭킹

$input = $_GET['input'];
$valid = ($input != '');
require('../src/dbconn.php');

$dccons = array();
$counts = array();
$rank = array();

$nicks = array();

if ($valid) {

    $key = mysqli_real_escape_string($conn, trim($input));

    $sqlq = "SELECT c_postdata, count(*) as count FROM euca_gall_cmts_2021 WHERE isdccon = 1 AND c_ipid = '".$key."' GROUP BY c_postdata ORDER BY count DESC LIMIT 25";

    $results = mysqli_query($conn, $sqlq);

    $i = 1;
    $prevrank = -1;
    $prevcount = -1;
    while ($result = mysqli_fetch_array($results)) {
        array_push($dccons, $result['c_postdata']);
        array_push($counts, $result['count']);

        // 이전 카운트와 동일할시
        if ($result['count'] == $prevcount) {
            array_push($rank, $prevrank);
        } else {
            array_push($rank, $i);
            $prevrank = $i;
        }

        $prevcount = $result['count'];
        $i++;
    }

    $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$key."' GROUP BY nickname");
    while ($result = mysqli_fetch_array($nickresult)) {
        array_push($nicks, $result['nickname']);
    }

} else {
    // 비어있을시 -> 전체 랭킹으로

    $sqlq = "SELECT c_postdata, count(*) as count FROM euca_gall_cmts_2021 WHERE isdccon = 1 GROUP BY c_postdata ORDER BY count DESC LIMIT 25";
    $results = mysqli_query($conn, $sqlq);

    $i = 1;
    $prevrank = -1;
    $prevcount = -1;
    while ($result = mysqli_fetch_array($results)) {
        array_push($dccons, $result['c_postdata']);
        array_push($counts, $result['count']);

        // 이전 카운트와 동일할시
        if ($result['count'] == $prevcount) {
            array_push($rank, $prevrank);
        } else {
            array_push($rank, $i);
            $prevrank = $i;
        }

        $prevcount = $result['count'];
        $i++;
    }

    array_push($nicks, "전체");
}

if (count($dccons) == 0) {
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. ID(IP)를 정확히 입력하였는지, 사용한 디시콘이 존재하는지 확인하세요.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    $valid = false;
}

?>

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="g3">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>


    <div class="fs-5 text-secondary text-center">
            <?=join(", ", $nicks)?> 님의 디시콘 랭킹 top <?=count($dccons)?>
        </div>
    <div class="d-flex flex-wrap justify-content-center">
        <?php

            for ($i=0; $i<count($dccons); $i++) {

                $sq = str_replace('https://dcimg5.dcinside.com/dccon.php?no=','',$dccons[$i]);
                if ($valid) { $sq = "IPID:".$key." ".$sq; }

                ?>

                <div class="shadow p-3 m-2 bg-body rounded text-center postzoom" onclick="loadMd.show(); window.location='../?stype=5&squery=<?=$sq?>'">
                    <img src="<?=$dccons[$i]?>">
                    <div class="fs-5"><?=$rank[$i]?>위</div>
                    <span class="badge rounded-pill bg-secondary"><?=$counts[$i]?>회 사용</span>
                </div>

                <?php


            }

        ?>
    </div>
    

<div class="card">
    <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
    <div style="font-size:small; color: gray;">
    같은 이미지더라도 따로 판매되는 콘의 경우 별개로 취급됩니다.</br>
    현재 판매중지된 디시콘은 공란으로 표시됩니다.</br>
    댓글로 사용한 디시콘만 집계되며, 본문에 첨부한 디시콘은 집계되지 않습니다.</br>
    디시콘을 달았던 글이 삭제 요청으로 삭제 처리 된 경우, 해당 디시콘은 집계에서 제외됩니다.</div>
    </div>
</div>

<?php

echo '';

?>
