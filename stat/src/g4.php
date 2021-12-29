<?php

$input = $_GET['input'];
$valid = ($input != '');
$conn = mysqli_connect('', '', '', '');


if ($valid) {

    # ==== 작업 시작 ====

    $sqlq = '';

    $upvote_sum = 0;
    $downvote_sum = 0;

    $upvote_id_arr = array();
    $upvote_title_arr = array();
    $upvote_uv_count_arr = array();
    $upvote_dv_count_arr = array();

    $downvote_id_arr = array();
    $downvote_title_arr = array();
    $downvote_uv_count_arr = array();
    $downvote_dv_count_arr = array();

    $nicks = array();

    $key = mysqli_real_escape_string($conn, trim($input));


    # 개추, 비추 합계 불러오기
    $results = mysqli_query($conn, "SELECT sum(upvotes) AS uv, sum(downvotes) AS dv FROM `euca_gall_posts_2021` WHERE ipid = '$key'");

    while ($result = mysqli_fetch_array($results)) {
        $upvote_sum = $result['uv'];
        $downvote_sum = $result['dv'];
    }


    # 개추 탑 25
    $sqlq = "SELECT postid, title, upvotes, downvotes FROM `euca_gall_posts_2021` WHERE ipid = '$key' ORDER BY upvotes DESC LIMIT 25";
    
    $results = mysqli_query($conn, $sqlq);

    while ($result = mysqli_fetch_array($results)) {

        array_push($upvote_id_arr, $result['postid']);
        array_push($upvote_title_arr, $result['title']);
        array_push($upvote_uv_count_arr, $result['upvotes']);
        array_push($upvote_dv_count_arr, $result['downvotes']);

    }


    # 비추 탑 25
    $sqlq = "SELECT postid, title, upvotes, downvotes FROM `euca_gall_posts_2021` WHERE ipid = '$key' ORDER BY downvotes DESC LIMIT 25";
    
    $results = mysqli_query($conn, $sqlq);

    while ($result = mysqli_fetch_array($results)) {

        array_push($downvote_id_arr, $result['postid']);
        array_push($downvote_title_arr, $result['title']);
        array_push($downvote_uv_count_arr, $result['upvotes']);
        array_push($downvote_dv_count_arr, $result['downvotes']);

    }


    if (count($upvote_id_arr) == 0 and count($downvote_id_arr) == 0 ) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. 올바른 ID(IP)인지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        $valid = false;
    } else {

        $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$key."' GROUP BY nickname");
        while ($result = mysqli_fetch_array($nickresult)) {
            array_push($nicks, $result['nickname']);
        }
    }
} else {

}

?>

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="g4">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>

<?php
if ($valid) {
    ?>
 
<div class="row">
    <div class="shadow p-3 mt-3 mb-3 bg-white backpanel">
        <div class="fs-4 mb-2 text-secondary text-center">
            <?=join(',',$nicks)?>님의 개추·비추수 비율
        </div>
        <script src="./chartjs/chart.min.js"></script>
        <div>
            <canvas id="pie-chart" style="max-height: 500px;"></canvas>
            <script>
            new Chart(document.getElementById("pie-chart"), {
                type: 'pie',
                data: {
                    labels: ['개추수','비추수'],
                    datasets: [{
                        backgroundColor: ['#2196F3','#F44336'],
                        data: [<?=$upvote_sum?>,<?=$downvote_sum?>]
                    }]
                }
            });
            </script>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="shadow p-3 mt-3 mb-3 bg-white backpanel">
            <div class="fs-4 mb-2 text-secondary text-center">
                개추 글 랭킹 TOP <?=count($upvote_title_arr)?>
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
                    for ($i=0; $i<count($upvote_id_arr); $i++) {
                        ?>
                        <tr>
                            <th scope="row"><?=$i+1?></th>
                            <td><?=$upvote_title_arr[$i]?></td>
                            <td><?=$upvote_uv_count_arr[$i]?></td>
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
                비추 글 랭킹 <?=count($downvote_title_arr)?>
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
                    for ($i=0; $i<count($downvote_id_arr); $i++) {
                        ?>
                        <tr>
                            <th scope="row"><?=$i+1?></th>
                            <td><?=$downvote_title_arr[$i]?></td>
                            <td><?=$downvote_dv_count_arr[$i]?></td>
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
