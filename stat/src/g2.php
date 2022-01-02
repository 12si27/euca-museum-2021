<?php

// 월별 활동

$input = $_GET['input'];
$valid = ($input != '');
require('../src/dbconn.php');

$keyname = array();
$keyvalue = array();
$keyvalue2 = array();
$keyvalue3 = array();


# 1=월별, 2=시간별, 3=요일별
# 순서값이 1~3이 아니면 1로 처리
if ($_GET['gtype'] == 1 or $_GET['gtype'] == 2 or $_GET['gtype'] == 3) {
    $gtype = $_GET['gtype'];
} else {
    $gtype = 1;
}

switch ($gtype) {
    case 1:
        $gname = "년-월";
        $df = '%Y-%m';
        break;
    case 2:
        $gname = "시간(24시)";
        $df = '%H';
        break;
    case 3:
        $gname = "요일";
        $df = '%a';
        break;
}

$nicks = array();

if ($valid) {
    
    $key = mysqli_real_escape_string($conn, trim($input));

    $sqlq = "SELECT
                COALESCE(m,m2) as m, posts, cmts
            FROM
                (
                SELECT
                    DATE_FORMAT(DATE, '$df') AS m,
                    COUNT(*) AS posts
                FROM
                    euca_gall_posts_2021
                WHERE
                    ipid = '$key'
                GROUP BY
                    m
            ) a
            LEFT JOIN
                (
                SELECT
                    DATE_FORMAT(c_date, '$df') AS m2,
                    COUNT(*) AS cmts
                FROM
                    euca_gall_cmts_2021
                WHERE
                    c_ipid = '$key'
                GROUP BY
                    m2
            ) b
            ON
                a.m = b.m2
            UNION
            SELECT
                COALESCE(m,m2) as m, posts, cmts
            FROM
                (
                SELECT
                    DATE_FORMAT(DATE, '$df') AS m,
                    COUNT(*) AS posts
                FROM
                    euca_gall_posts_2021
                WHERE
                    ipid = '$key'
                GROUP BY
                    m
            ) a
            RIGHT JOIN
                (
                SELECT
                    DATE_FORMAT(c_date, '$df') AS m2,
                    COUNT(*) AS cmts
                FROM
                    euca_gall_cmts_2021
                WHERE
                    c_ipid = '$key'
                GROUP BY
                    m2
            ) b
            ON
                a.m = b.m2";

    $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$key."' GROUP BY nickname");
    while ($result = mysqli_fetch_array($nickresult)) {
        array_push($nicks, $result['nickname']);
    }

} else {

    $sqlq = "SELECT
                *
            FROM
                (
                SELECT
                    DATE_FORMAT(DATE, '$df') AS m,
                    COUNT(*) AS posts
                FROM
                    euca_gall_posts_2021
                GROUP BY
                    m
            ) a
            NATURAL JOIN
                (
                SELECT
                    DATE_FORMAT(c_date, '$df') AS m,
                    COUNT(*) AS cmts
                FROM
                    euca_gall_cmts_2021
                GROUP BY
                    m
            ) b";
}

if ($gtype == 3) {
    $sqlq .= " ORDER BY CASE
                WHEN m = 'Mon' THEN 1
                WHEN m = 'Tue' THEN 2
                WHEN m = 'Wed' THEN 3
                WHEN m = 'Thu' THEN 4
                WHEN m = 'Fri' THEN 5
                WHEN m = 'Sat' THEN 6
                WHEN m = 'Sun' THEN 7
            END ASC";
}

$results = mysqli_query($conn, $sqlq);

while ($result = mysqli_fetch_array($results)) {

    if ($gtype == 3) {
        switch ($result['m']) {
            case 'Mon':
                $kn = '월요일'; break;
            case 'Tue':
                $kn = '화요일'; break;
            case 'Wed':
                $kn = '수요일'; break;
            case 'Thu':
                $kn = '목요일'; break;
            case 'Fri':
                $kn = '금요일'; break;
            case 'Sat':
                $kn = '토요일'; break;
            case 'Sun':
                $kn = '일요일'; break;
        }
    } else {
        $kn = $result['m'];
    }

    array_push($keyname, $kn);

    $p = intval($result['posts']);
    $c = intval($result['cmts']);

    array_push($keyvalue, $p);
    array_push($keyvalue2, $c);
    array_push($keyvalue3, $p + $c);
}


if (count($keyname) == 0) {
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. ID(IP)를 정확히 입력하였는지 확인하세요.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    $valid = false;
}

?>

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="g2">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
    <input hidden name="gtype" value="<?=($_GET['gtype']==''?'1':$_GET['gtype'])?>">
</form>

<form class='' method='GET'>
    <div class="d-flex justify-content-center">
        <input hidden name="stype" value="g2">
        <input hidden name="input" value="<?=$input?>">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="gtype" id="btnradio1" autocomplete="off" value="1" checked onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio1">월별</label>

            <input type="radio" class="btn-check" name="gtype" id="btnradio2" autocomplete="off" value="2" <?=($_GET['gtype']==2?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio2">시간별</label>

            <input type="radio" class="btn-check" name="gtype" id="btnradio3" autocomplete="off" value="3" <?=($_GET['gtype']==3?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio3">요일별</label>
        </div>
    </div>
</form>

<script src="./chartjs/chart.min.js"></script>
<div>
    <div class="fs-4 m-2 text-secondary text-center">
        <?php
        switch ($gtype) {
            case 1:
                echo '월';
                break;
            case 2:
                echo '시간';
                break;
            case 3:
                echo '요일';
                break;
        }
        echo '별 ';

        if (count($nicks) > 0) {
            echo join(', ', $nicks)."님의";
        } else {
            echo '전체';
        }

        echo ' 활동 횟수';
        ?>
    </div>
    <canvas id="chart" style="max-height: 500px;"></canvas>
    <script>
    new Chart(document.getElementById("chart"), {
        type: 'line',
        data: {
            labels: [<?="'".join("','", $keyname),"'"?>],
            datasets: [{
                label: "글",
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgb(13, 110, 253)',
                data: [<?=join(",", $keyvalue)?>]
            },{
                label: "댓글",
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgb(255, 99, 132)',
                data: [<?=join(",", $keyvalue2)?>]
            },{
                label: "총합",
                borderColor: 'rgb(129, 129, 129)',
                backgroundColor: 'rgb(129, 129, 129)',
                data: [<?=join(",", $keyvalue3)?>]
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
        }
        }
    });
    </script>
</div>

<div class="d-flex justify-content-center my-3">
    <table class="table" style="max-width: 600px;">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col"><?=$gname?></th>
            <th scope="col">글쓴수</th>
            <th scope="col">댓글쓴수</th>
            <th scope="col">총합</th>
            </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $i<count($keyname); $i++) {

            

            ?>
            <tr>
                <th scope="row"><?=$i+1?></th>
                <td><?=$keyname[$i]?></td>
                <td><?=number_format($keyvalue[$i])?></td>
                <td><?=number_format($keyvalue2[$i])?></td>
                <td><?=number_format($keyvalue3[$i])?></td>
            </tr>
            <?php
        }

        ?> 
        </tbody>
    </table>
</div>

<div class="card">
    <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
    <div style="font-size:small; color: gray;">본 결과는 글과 댓글 작성 수를 기준으로 집계합니다.</br>
    글이 삭제 요청으로 인해 삭제 처리가 된 경우, 해당 글과 해당 글에 달린 댓글 전체가 집계에서 제외됩니다.</div>
    </div>
</div>

<script>
    var loadMd = new bootstrap.Modal(document.getElementById('loadMd'));
</script>