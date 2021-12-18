<?php

$input = $_GET['input'];
$valid = ($input != '');
$conn = mysqli_connect('','','','');

$keyname = array();
$keyvalue = array();
$keyvalue2 = array();
$keyvalue3 = array();

$nicks = array();

if ($valid) {
    
    $key = mysqli_real_escape_string($conn, trim($input));

    $sqlq = "SELECT
                COALESCE(m,m2) as m, posts, cmts
            FROM
                (
                SELECT
                    DATE_FORMAT(DATE, '%Y-%m') AS m,
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
                    DATE_FORMAT(c_date, '%Y-%m') AS m2,
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
                    DATE_FORMAT(DATE, '%Y-%m') AS m,
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
                    DATE_FORMAT(c_date, '%Y-%m') AS m2,
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
                    DATE_FORMAT(DATE, '%Y-%m') AS m,
                    COUNT(*) AS posts
                FROM
                    euca_gall_posts_2021
                GROUP BY
                    m
            ) a
            NATURAL JOIN
                (
                SELECT
                    DATE_FORMAT(c_date, '%Y-%m') AS m,
                    COUNT(*) AS cmts
                FROM
                    euca_gall_cmts_2021
                GROUP BY
                    m
            ) b";

    
    array_push($nicks, '전체');
}

$results = mysqli_query($conn, $sqlq);

while ($result = mysqli_fetch_array($results)) {
    array_push($keyname, $result['m']);
    $p = intval($result['posts']);
    $c = intval($result['cmts']);

    array_push($keyvalue, $p);
    array_push($keyvalue2, $c);
    array_push($keyvalue3, $p + $c);
}

?>

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="g1">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>

<script src="./chartjs/chart.min.js"></script>
<div>
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
            plugins: {
                title: {
                    display: true,
                    text: '월별 <?=join(",", $nicks)?>님의 활동 횟수'
                }
            },
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
            <th scope="col">년-월</th>
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
  <div style="font-size:small; color: gray;">본 결과는 제목과 글 내용을 기준으로 집계합니다</br>
  글에서 해당 키워드가 여러번 언급되더라도 글당 1회로 집계합니다</br>
  문맥과는 맞지 않는 언급도 포함되기 때문에 뜻이 많이 겹치는 단어의 경우 실제보다 더 많이 집계될 수 있습니다.</div>
  </div>
</div>

<?php

echo '';

?>
