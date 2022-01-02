<?php

// 갤럼 요약

$input = $_GET['input'];
$valid = ($input != '');
require('../src/dbconn.php');



if ($valid) {

    $post_cutline = 2;
    $cmt_cutline = 6;


    $post_total = 0;
    $post_gtotal = 0;

    $haspost = false;
    $hascmt = false;

    $nicks = array();
    
    $key = mysqli_real_escape_string($conn, trim($input));

    $results = mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(hasaccount) AS gtotal FROM euca_gall_posts_rank_2021 WHERE post > $post_cutline");

    # 총 글작성 회원수, 고닉수 구하기
    while ($result = mysqli_fetch_array($results)) {
        $post_total = $result['total'];
        $post_gtotal = $result['gtotal'];
    }

    $results = mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(hasaccount) AS gtotal FROM euca_gall_cmts_rank_2021 WHERE cmt_posted > $cmt_cutline");

    # 총 댓작성 회원수, 고닉수 구하기
    while ($result = mysqli_fetch_array($results)) {
        $cmt_total = $result['total'];
        $cmt_gtotal = $result['gtotal'];
    }


    $results = mysqli_query($conn, "SELECT * FROM euca_gall_posts_rank_2021 WHERE ipid = '$key' AND post >= $post_cutline");

    # 현재 대상의 글값 구하기
    while ($result = mysqli_fetch_array($results)) {
        $haspost = true;

        $post = $result['post'];
        $cmt_recieved = $result['cmt_recieved'];
        $upvote = $result['upvote'];
        $downvote = $result['downvote'];
        $recommended = $result['recommended'];
    }

    $results = mysqli_query($conn, "SELECT * FROM euca_gall_cmts_rank_2021 WHERE ipid = '$key' AND cmt_posted >= $cmt_cutline");

    # 현재 대상의 댓글값 구하기
    while ($result = mysqli_fetch_array($results)) {
        $hascmt = true;

        $cmt_posted = $result['cmt_posted'];
        $dccon_posted = $result['dccon_posted'];
        $reply_posted = $result['reply_posted'];
    }




    # ================= 글 랭킹 구하기 =================

    $results = mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(hasaccount) AS gtotal FROM euca_gall_posts_rank_2021 WHERE post > '$post'");

    while ($result = mysqli_fetch_array($results)) {
        $post_grade = $result['total'] + 1;
        $post_ggrade = $result['gtotal'] + 1;
        $post_rank = $post_grade / $post_total * 100;
        $post_grank = $post_ggrade / $post_gtotal * 100;
    }

    # ================= 댓글 랭킹 구하기 =================

    $results = mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(hasaccount) AS gtotal FROM euca_gall_cmts_rank_2021 WHERE cmt_posted > '$cmt_posted'");

    while ($result = mysqli_fetch_array($results)) {
        $cmt_grade = $result['total'] + 1;
        $cmt_ggrade = $result['gtotal'] + 1;
        $cmt_rank = $cmt_grade / $cmt_total * 100;
        $cmt_grank = $cmt_ggrade / $cmt_gtotal * 100;
    }

    # ================= 념글 랭킹 구하기 =================

    $results = mysqli_query($conn, "SELECT COUNT(*) AS total FROM euca_gall_posts_rank_2021 WHERE recommended > '$recommended'");

    while ($result = mysqli_fetch_array($results)) {
        $rec_grade = $result['total'] + 1;
        $rec_rank = $rec_grade / $post_total * 100;
    }


    $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$key."' GROUP BY nickname");
    while ($result = mysqli_fetch_array($nickresult)) {
        array_push($nicks, $result['nickname']);
    }



    # ================= 최애 디시콘 =================

    $results = mysqli_query($conn, "SELECT c_postdata, COUNT(*) AS c FROM euca_gall_cmts_2021 WHERE c_ipid = '$key' AND isdccon = 1 GROUP BY c_postdata ORDER BY c DESC LIMIT 1");

    while ($result = mysqli_fetch_array($results)) {
        $dccon_pick = $result['c_postdata'];
    }


    if (count($nicks) == 0) {
        $valid = false;
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. ID(IP)를 정확히 입력하였는지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    } else {
        if ($hascmt == false and $haspost == false) {
            $valid = false;
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>순위권 밖</strong></br>죄송합니다만, 글 또는 댓글의 집계 기준 커트라인을 넘지 못해 통계를 낼 수 없습니다. 다른 ID(IP)를 입력해 주세요.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
        }
    }

}
?>


<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="g1">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>

<?php
if ($valid) { ?>

<style>

.panel {
   border-radius: 10pt;
   padding-top: 20px;
   padding-bottom: 20px;
   padding-left: 30px;
   padding-right: 30px;
}

.paneltitle {
    font-size: 18pt;
    font-weight: 300;
}

.panelsep {
    width: 100%;
    height: 1px;
    background-color: #c1c1c1;
    margin-top: 5px;
    margin-bottom: 5px;
}

.res-text {
    font-size: 35pt;
    font-family: 'Spoqa Han Sans Neo';
    font-weight: 300;
    color: #3a3a3a;
}

.res-text-2 {
    font-size: 25pt;
    font-family: 'Spoqa Han Sans Neo';
    font-weight: 400;
    color: #3a3a3a;
}

.res-sub-text {
    font-size: 15pt;
    font-weight: 500;
}

</style>


<div class="container">

    <div class="fs-4 mt-2 mb-1 text-secondary text-center">
        <?=join(', ', $nicks)?> 님의 애유갤 통계 요약
    </div>

    <script src="./chartjs/chart.min.js"></script>
    <div class="row">

        <div class="col-lg-4">
            <div class="shadow mt-3 mb-3 bg-white panel">
                <div class="paneltitle">
                    글 작성
                </div>
                <div class="panelsep"></div>
                <div class="d-flex">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        총 게시수
                    </div>
                    <div class="text-end flex-grow-1 res-text">
                        <?=number_format($post)?><span class="res-sub-text">개</span>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        전체 상위
                    </div>
                    <div class="text-end flex-grow-1 res-text">
                        <?php
                        if ($haspost) {
                            ?> <span style="font-size: 12pt; font-weight: 400; color: gray;">(<?=$post_grade?>등)</span>
                            <?=($post_rank>=10?round($post_grank, 1):round($post_rank, 2))?><span style="font-size: 20pt; font-weight: 400;">%</span> <?php
                        } else {
                            ?>
                            <span style="font-size: 22pt; color: gray;">(순위권 밖)</span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        고닉 상위
                    </div>
                    <div class="text-end flex-grow-1 res-text">
                        <?php
                        if ($haspost) {
                            ?> <span style="font-size: 12pt; font-weight: 400; color: gray;">(<?=$post_ggrade?>등)</span>
                            <?=($post_grank>=10?round($post_grank, 1):round($post_grank, 2))?><span style="font-size: 20pt; font-weight: 400;">%</span> <?php
                        } else {
                            ?>
                            <span style="font-size: 22pt; color: gray;">(순위권 밖)</span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="shadow mt-3 mb-3 bg-white panel">
                <div class="paneltitle">
                    댓글 작성
                </div>
                <div class="panelsep"></div>
                <div class="d-flex">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        총 게시수
                    </div>
                    <div class="text-end flex-grow-1 res-text">
                        <?=number_format($cmt_posted)?><span class="res-sub-text">개</span>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        전체 상위
                    </div>
                    <div class="text-end flex-grow-1 res-text">
                        <?php
                        if ($hascmt) {
                            ?> <span style="font-size: 12pt; font-weight: 400; color: gray;">(<?=$cmt_grade?>등)</span>
                            <?=($cmt_rank>=10?round($cmt_rank, 1):round($cmt_rank, 2))?><span style="font-size: 20pt; font-weight: 400;">%</span> <?php
                        } else {
                            ?>
                            <span style="font-size: 22pt; color: gray;">(순위권 밖)</span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        고닉 상위
                    </div>
                    <div class="text-end flex-grow-1 res-text">
                        <?php
                        if ($hascmt) {
                            ?> <span style="font-size: 12pt; font-weight: 400; color: gray;">(<?=$cmt_ggrade?>등)</span>
                            <?=($cmt_grank>=10?round($cmt_grank, 1):round($cmt_grank, 2))?><span style="font-size: 20pt; font-weight: 400;">%</span> <?php
                        } else {
                            ?>
                            <span style="font-size: 22pt; color: gray;">(순위권 밖)</span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="shadow mt-3 mb-3 bg-white panel">
                <div class="paneltitle">
                    글댓비
                </div>
                <div class="panelsep"></div>
                <div>
                        <canvas id="post-ratio-chart" style="width: 100%; height: 155px; min-height: 155px;"></canvas>
                        <script>
                        new Chart(document.getElementById("post-ratio-chart"), {
                            type: 'bar',
                            data: {
                                labels: ['글','댓글'],
                                datasets: [{
                                    backgroundColor: ['rgb(255, 99, 132)', 'rgb(75, 192, 192)'],
                                    data: [<?=$post?>, <?=$cmt_posted?>]
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
                <div class="d-flex">
                    <div class="text-center flex-grow-1 res-text" style="font-size: 20pt; font-weight: 500;">
                        <?php
                        if ($post <= $cmt_posted) {
                            echo "1 : ";
                            echo round($cmt_posted/$post, 4);
                        } else {
                            echo round($post/$cmt_posted, 4);
                            echo " : 1";
                        }
                        ?>
                    </div>
                </div>
                <div class="text-center" style="font-size: 6pt;">
                        *1:3이 가장 평범한 비율입니다
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="shadow mt-3 mb-3 bg-white panel">
                <div class="paneltitle">
                    개추:비추
                </div>
                <div class="panelsep"></div>
                <div>
                        <canvas id="vote-ratio-chart" style="width: 100%; height: 155px; min-height: 155px;"></canvas>
                        <script>
                        new Chart(document.getElementById("vote-ratio-chart"), {
                            type: 'bar',
                            data: {
                                labels: ['개추','비추'],
                                datasets: [{
                                    backgroundColor: ['#0090F2', '#D84052'],
                                    data: [<?=$upvote?>,<?=$downvote?>]
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
                        <?=number_format($upvote)?><span class="res-sub-text">(<?=round($upvote/($upvote+$downvote)*100,1)?>%)</span>
                    </div>
                    <div style="font-size:25pt; font-weight:400; margin: 10pt 3pt 3pt; color:#c1c1c1;">/</div>
                    <div style="font-weight: 500; color: #D84052;">
                        <?=number_format($downvote)?><span class="res-sub-text">(<?=round($downvote/($upvote+$downvote)*100,1)?>%)</span>
                    </div>
                </div>

                
                <div class="d-flex">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        평균 개추율
                    </div>
                    <div class="text-end flex-grow-1 res-text">
                        <?php
                            if ($post > $upvote) {
                                #echo '<span class="res-sub-text">글</span> ';
                                echo round($post / $upvote, 1);
                                echo '<span class="res-sub-text">글/1개추</span>';
                            } else {
                                #echo '<span class="res-sub-text">글 하나당</span>';
                                echo round($upvote / $post, 1);
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
                            if ($post > $downvote) {
                                #echo '<span class="res-sub-text">글</span> ';
                                echo round($post / $downvote, 1);
                                echo '<span class="res-sub-text">글/1비추</span>';
                            } else {
                                #echo '<span class="res-sub-text">글 한개당</span>';
                                echo round($downvote / $post, 1);
                                echo '<span class="res-sub-text">개/1글</span>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="shadow mt-3 mb-3 bg-white panel">
                <div class="paneltitle">
                    기타 정보
                </div>
                <div class="panelsep"></div>
                <div class="d-flex my-1">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        닉변횟수
                    </div>
                    <div class="text-end flex-grow-1 res-text-2">
                        <?=count($nicks)-1?><span class="res-sub-text">회</span>
                    </div>
                </div>
                <div class="d-flex my-1">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        념글수
                    </div>
                    <div class="text-end flex-grow-1 res-text-2">
                        <?=number_format($recommended)?><span class="res-sub-text">개 <span style="color: gray;">(<?=$rec_grade?>등, <?=round($rec_rank, 2)?>%)</span></span>
                    </div>
                </div>
                <div class="d-flex my-1">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        받은 댓글 수<sub>(본인 포함)</sub>
                    </div>
                    <div class="text-end flex-grow-1 res-text-2">
                        <?=number_format($cmt_recieved)?><span class="res-sub-text">개</span>
                    </div>
                </div>
                <div class="d-flex my-1">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        답글 쓴 수
                    </div>
                    <div class="text-end flex-grow-1 res-text-2">
                        <?=number_format($reply_posted)?><span class="res-sub-text">개 <span style="color: gray;">(<?=round($reply_posted/$cmt_posted*100,1)?>%)</span></span>
                    </div>
                </div>
                <div class="d-flex my-1">
                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                        디시콘 쓴 수
                    </div>
                    <div class="text-end flex-grow-1 res-text-2">
                        <?=number_format($dccon_posted)?><span class="res-sub-text">개 <span style="color: gray;">(<?=round($dccon_posted/$cmt_posted*100,1)?>%)</span></span>
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <div class="align-self-end mb-2 flex-grow-1" style="font-size: 12pt;">
                        최다사용 디시콘
                    </div>
                    <div>
                        <img src="<?=$dccon_pick?>">
                    </div>
                </div>
            </div>
            
        </div>

        


    </div>
</div>

<?php } ?>

<div class="card">
    <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
    <div style="font-size:small; color: gray;">본 결과에서 나오는 등수와 백분율은 다중 고닉, 통피, 극단값 등을 고려하지 않은 수치로 실제 집계와는 결과가 다를 수 있습니다.</br>
    글이 삭제 요청으로 인해 삭제 처리가 된 경우, 해당 글과 해당 글에 달린 댓글 전체가 집계에서 제외됩니다.</br>
    순위권 커트라인: 글 <?=$post_cutline?>개, 댓글 <?=$cmt_cutline?>개 이상</div>
    </div>
</div>
