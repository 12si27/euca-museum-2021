<?php

# 갤럼간 댓글 랭킹

$input = $_GET['input'];
$valid = ($input != '');
require('../src/dbconn.php');


$res1_name = array();
$res1_no = array();
$res1_val = array();
$res1_rank = array();

$res2_name = array();
$res2_no = array();
$res2_val = array();
$res2_rank = array();

if ($valid) {
    
    $key = mysqli_real_escape_string($conn, trim($input));

    // 나 -> 타갤럼
    $sqlq1 = "SELECT
                ipid, GROUP_CONCAT(DISTINCT nickname) AS nick, hasaccount,
                count(*) AS total
            FROM
                `euca_gall_posts_2021`
            RIGHT OUTER JOIN
                euca_gall_cmts_2021
            ON
                postid = cmtpostid
            WHERE
                c_ipid = '$key' AND NOT ipid = '$key'
            GROUP BY ipid
            ORDER BY total DESC
            LIMIT 15";

    $results = mysqli_query($conn, $sqlq1);

    $i = 1; $prevrank = -1; $prevval = -1;

    while ($result = mysqli_fetch_array($results)) {
        array_push($res1_name, $result['nick']." (".($result['hasaccount']?substr($result['ipid'],0,4).'****':$result['ipid']).")");
        array_push($res1_no, $result['nick']);
        array_push($res1_val, $result['total']);

        if ($result['total'] == $prevval) {
            array_push($res1_rank, $prevrank);
        } else {
            array_push($res1_rank, $i);
            $prevrank = $i;
        }

        $prevval = $result['total'];
        $i++;
    }

    // 타갤럼 -> 나
    $sqlq2 = "SELECT
                c_ipid, GROUP_CONCAT(DISTINCT c_nickname) AS c_nick, c_hasaccount,
                count(*) AS total
            FROM
                `euca_gall_posts_2021`
            RIGHT OUTER JOIN
                euca_gall_cmts_2021
            ON
                postid = cmtpostid
            WHERE
                ipid = '$key' AND NOT c_ipid = '$key'
            GROUP BY c_ipid
            ORDER BY total DESC
            LIMIT 15";

    $results = mysqli_query($conn, $sqlq2);

    /*
    while ($result = mysqli_fetch_array($results)) {
        array_push($res2_name, $result['c_nick']." (".$result['c_ipid'].")");
        array_push($res2_no, $result['c_nick']);
        array_push($res2_val, $result['total']);
    }
    */

    $i = 1; $prevrank = -1; $prevval = -1;

    while ($result = mysqli_fetch_array($results)) {
        array_push($res2_name, $result['c_nick']." (".($result['c_hasaccount']?substr($result['c_ipid'],0,4).'****':$result['c_ipid']).")");
        array_push($res2_no, $result['c_nick']);
        array_push($res2_val, $result['total']);

        if ($result['total'] == $prevval) {
            array_push($res2_rank, $prevrank);
        } else {
            array_push($res2_rank, $i);
            $prevrank = $i;
        }

        $prevval = $result['total'];
        $i++;
    }


    $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$key."' GROUP BY nickname");
    while ($result = mysqli_fetch_array($nickresult)) {
        array_push($nicks, $result['nickname']);
    }


    if (count($res1_name) == 0 and count($res2_name) == 0) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. ID(IP)를 정확히 입력하였는지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        $valid = false;
    }

}



?>

<script src="./chartjs/chart.min.js"></script>


<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-tag-cloud.min.js"></script>

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="g5">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>

<?php
if ($valid) {
    ?>
 
<div class="row">
    <div class="col-lg-6">
        <div class="shadow p-3 mt-3 mb-3 bg-white backpanel">
            <div class="fs-4 mb-2 text-secondary text-center">
                나(<?=$key?>) → 타갤럼 댓글 랭킹
            </div>
            <div>
                <canvas id="chart1" style="max-height: 500px;"></canvas>
                <script>
                    new Chart(document.getElementById("chart1"), {
                        type: 'pie',
                        data: {
                            labels: [<?="'".join("','", $res1_name),"'"?>],
                            datasets: [{
                                backgroundColor: ['#2196F3','#03A9F4','#00BCD4','#009688','#4CAF50','#8BC34A','#CDDC39','#FFEB3B','#FF9800','#FF5722','#795548','#F44336','#E91E63','#673AB7','#3F51B5','#9C27B0','#9E9E9E','#607D8B'],
                                data: [<?="'".join("','", $res1_val),"'"?>]
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                </script>
            </div>
            <div class="chart-area" style="height:300px;">
                <div id="wc-1" style="width:100%; height:100%;"></div>
            </div>

            <div class="d-flex justify-content-center my-3">
                <table class="table" style="max-width: 600px;">
                    <thead>
                        <tr>
                        <th scope="col">순위</th>
                        <th scope="col">닉네임(IPID)</th>
                        <th scope="col">댓글쓴수</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i=0; $i<count($res1_name); $i++) {
                        ?>
                        <tr>
                            <th scope="row"><?=$res1_rank[$i]?></th>
                            <td><?=$res1_name[$i]?></td>
                            <td><?=number_format($res1_val[$i])?></td>
                        </tr>
                        <?php
                    }
                    ?> 
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="col-lg-6">
        <div class="shadow p-3 mt-3 mb-3 bg-white backpanel">
            <div class="fs-4 mb-2 text-secondary text-center">
                타갤럼 → 나(<?=$key?>) 댓글 랭킹
            </div>
            <div>
                <canvas id="chart2" style="max-height: 500px;"></canvas>
                <script>
                    new Chart(document.getElementById("chart2"), {
                        type: 'pie',
                        data: {
                            labels: [<?="'".join("','", $res2_name),"'"?>],
                            datasets: [{
                                backgroundColor: ['#2196F3','#03A9F4','#00BCD4','#009688','#4CAF50','#8BC34A','#CDDC39','#FFEB3B','#FF9800','#FF5722','#795548','#F44336','#E91E63','#673AB7','#3F51B5','#9C27B0','#9E9E9E','#607D8B'],
                                data: [<?="'".join("','", $res2_val),"'"?>]
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                </script>
            </div>
            <div class="chart-area-2" style="height:300px;">
                <div id="wc-2" style="width:100%; height:100%;"></div>
            </div>

            <div class="d-flex justify-content-center my-3">
                <table class="table" style="max-width: 600px;">
                    <thead>
                        <tr>
                        <th scope="col">순위</th>
                        <th scope="col">닉네임(IPID)</th>
                        <th scope="col">댓글받은수</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i=0; $i<count($res2_name); $i++) {
                        ?>
                        <tr>
                            <th scope="row"><?=$res2_rank[$i]?></th>
                            <td><?=$res2_name[$i]?></td>
                            <td><?=number_format($res2_val[$i])?></td>
                        </tr>
                        <?php
                    }
                    ?> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script> 
anychart.onDocumentReady(function() {
    var data = [<?php for ($i=0; $i<count($res1_name); $i++) { echo '{"x": "'.$res1_no[$i].'", "value": '.$res1_val[$i].'}, ';} ?>];
    var chart1 = anychart.tagCloud(data);
    data = [<?php for ($i=0; $i<count($res1_name); $i++) { echo '{"x": "'.$res2_no[$i].'", "value": '.$res2_val[$i].'}, ';} ?>];
    var chart2 = anychart.tagCloud(data);

    chart1.angles([0]);
    chart2.angles([0]);

    chart1.container("wc-1");
    chart2.container("wc-2");
    // chart.getCredits().setEnabled(false);

    chart1.draw();
    chart2.draw();
});
</script>

<?php } ?>

<div class="card">
    <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
    <div style="font-size:small; color: gray;">
    본 결과는 글과 댓글 작성 수를 기준으로 집계합니다.</br>
    글이 삭제 요청으로 인해 삭제 처리가 된 경우, 해당 글과 해당 글에 달린 댓글 전체가 집계에서 제외됩니다.</br>
    <span style="color: #DD6372; font-weight: bold;">애유갤에 통계 결과를 올리지 마세요! 닉언질로 차단되어도 책임 못집니다</span></div>
    </div>
</div>

