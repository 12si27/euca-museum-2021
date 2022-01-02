<?php

$input = $_GET['input'];
$valid = ($input != '');
require('../src/dbconn.php');

if ($valid) {

    # ==== 작업 시작 ====

    $sqlq = '';

    $uv = 0;
    $dv = 0;
    $post = 0;

    
    $uv_id_arr = array();
    $uv_title_arr = array();
    $uv_uv_count_arr = array();
    $uv_dv_count_arr = array();
    $uv_rank_arr = array();

    $dv_id_arr = array();
    $dv_title_arr = array();
    $dv_uv_count_arr = array();
    $dv_dv_count_arr = array();
    $dv_rank_arr = array();

    $nicks = array();

    $key = mysqli_real_escape_string($conn, trim($input));


    # 개추, 비추 합계 불러오기
    $results = mysqli_query($conn, "SELECT sum(upvotes) AS uv, sum(downvotes) AS dv, COUNT(*) AS c FROM `euca_gall_posts_2021` WHERE (upvotes > 0 OR downvotes > 0) AND title LIKE '%$key%'");

    while ($result = mysqli_fetch_array($results)) {
        $uv = $result['uv'];
        $dv = $result['dv'];
        $post = $result['c'];
    }


    # 개추 탑 25
    $sqlq = "SELECT postid, title, upvotes, downvotes FROM `euca_gall_posts_2021` WHERE upvotes > 0 AND title LIKE '%$key%' ORDER BY upvotes DESC LIMIT 25";
    
    $results = mysqli_query($conn, $sqlq);

    $i = 1; $prevrank = -1; $prevval = -1;

    while ($result = mysqli_fetch_array($results)) {

        array_push($uv_id_arr, $result['postid']);
        array_push($uv_title_arr, $result['title']);
        array_push($uv_uv_count_arr, $result['upvotes']);
        array_push($uv_dv_count_arr, $result['downvotes']);

        if ($result['upvotes'] == $prevval) {
            array_push($uv_rank_arr, $prevrank);
        } else {
            array_push($uv_rank_arr, $i);
            $prevrank = $i;
        }

        $prevval = $result['upvotes'];
        $i++;

    }


    # 비추 탑 25
    $sqlq = "SELECT postid, title, upvotes, downvotes FROM `euca_gall_posts_2021` WHERE downvotes > 0 AND title LIKE '%$key%' ORDER BY downvotes DESC LIMIT 25";
    
    $results = mysqli_query($conn, $sqlq);

    $i = 1; $prevrank = -1; $prevval = -1;

    while ($result = mysqli_fetch_array($results)) {

        array_push($dv_id_arr, $result['postid']);
        array_push($dv_title_arr, $result['title']);
        array_push($dv_uv_count_arr, $result['upvotes']);
        array_push($dv_dv_count_arr, $result['downvotes']);

        if ($result['downvotes'] == $prevval) {
            array_push($dv_rank_arr, $prevrank);
        } else {
            array_push($dv_rank_arr, $i);
            $prevrank = $i;
        }

        $prevval = $result['downvotes'];
        $i++;
    }


    if (count($uv_id_arr) == 0 and count($dv_id_arr) == 0 ) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. 올바른 ID(IP)인지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        $valid = false;
    }
} else {

}

?>

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="분석할 키워드를 입력하세요 (하나만)" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="k4">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>

<?php
if ($valid) {
    ?>
 
<style>

    .res-text {
        font-size: 35pt;
        font-family: 'Spoqa Han Sans Neo';
        font-weight: 300;
        color: #3a3a3a;
    }

    .res-sub-text {
        font-size: 15pt;
        font-weight: 500;
    }

</style>

<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="shadow bg-white backpanel p-4">
            <script src="./chartjs/chart.min.js"></script>
            <div class="fs-4 mb-2 text-secondary text-center">
                '<?=$key?>' 키워드 개추:비추 요약
            </div>
            <div>
                    <canvas id="vote-ratio-chart" style="width: 100%; height: 155px; min-height: 155px;"></canvas>
                    <script>
                    new Chart(document.getElementById("vote-ratio-chart"), {
                        type: 'bar',
                        data: {
                            labels: ['개추','비추'],
                            datasets: [{
                                backgroundColor: ['#0090F2', '#D84052'],
                                data: [<?=$uv?>,<?=$dv?>]
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                    </script>
            </div>
            <div class="d-flex flex-wrap res-text justify-content-center">
                <div style="font-weight: 500; color: #0090F2;">
                    <?=number_format($uv)?><span class="res-sub-text">(<?=round($uv/($uv+$dv)*100,1)?>%)</span>
                </div>
                <div style="font-size:25pt; font-weight:400; margin: 10pt 3pt 3pt; color:#c1c1c1;">/</div>
                <div style="font-weight: 500; color: #D84052;">
                    <?=number_format($dv)?><span class="res-sub-text">(<?=round($dv/($uv+$dv)*100,1)?>%)</span>
                </div>
            </div>

            
            <div class="d-flex">
                <div class="align-self-end mb-2" style="font-size: 12pt;">
                    평균 개추율
                </div>
                <div class="text-end flex-grow-1 res-text">
                    <?php
                        if ($post > $uv) {
                            echo round($post / $uv, 1);
                            echo '<span class="res-sub-text">글/1개추</span>';
                        } else {
                            echo round($uv / $post, 1);
                            echo '<span class="res-sub-text">개/1글</span>';
                        }
                    ?>
                </div>
            </div>
            <div class="d-flex">
                <div class="align-self-end mb-2" style="font-size: 12pt;">
                    평균 비추율
                </div>
                <div class="text-end flex-grow-1 res-text">
                    <?php
                        if ($post > $dv) {
                            echo round($post / $dv, 1);
                            echo '<span class="res-sub-text">글/1비추</span>';
                        } else {
                            echo round($dv / $post, 1);
                            echo '<span class="res-sub-text">개/1글</span>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>


    


    <div class="col-lg-6">
        <div class="shadow p-3 mt-3 mb-3 bg-white backpanel">
            <div class="fs-4 mb-2 text-secondary text-center">
                개추 글 랭킹 TOP <?=count($uv_title_arr)?>
            </div>

            <div class="d-flex justify-content-center my-3">
                <table class="table" style="max-width: 600px;">
                    <thead>
                        <tr>
                        <th scope="col">순위</th>
                        <th scope="col">제목</th>
                        <th scope="col">개추수</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i=0; $i<count($uv_id_arr); $i++) {
                        ?>
                        <tr>
                            <th scope="row"><?=$uv_rank_arr[$i]?></th>
                            <td><?=$uv_title_arr[$i]?></td>
                            <td><?=$uv_uv_count_arr[$i]?></td>
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
                비추 글 랭킹 TOP <?=count($dv_title_arr)?>
            </div>

            <div class="d-flex justify-content-center my-3">
                <table class="table" style="max-width: 600px;">
                    <thead>
                        <tr>
                        <th scope="col">순위</th>
                        <th scope="col">제목</th>
                        <th scope="col">비추수</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i=0; $i<count($dv_id_arr); $i++) {
                        ?>
                        <tr>
                            <th scope="row"><?=$dv_rank_arr[$i]?></th>
                            <td><?=$dv_title_arr[$i]?></td>
                            <td><?=$dv_dv_count_arr[$i]?></td>
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

<?php } ?>

<div class="card">
  <div class="card-body">
  <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
  <div style="font-size:small; color: gray;">
  본 결과는 글 제목<?=($cmt=='1'?'과 댓글':'')?> 내용을 기준으로 집계합니다</br>
  하나의 글, 댓글에서 한번 이상 언급된 것은 한번 언급된 것으로 취급합니다</br>
  문맥과는 맞지 않는 언급도 포함되기 때문에 뜻이 많이 겹치는 단어의 경우 실제보다 더 많이 집계될 수 있습니다.</div>
  </div>
</div>
