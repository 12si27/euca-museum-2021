<?php
    $mode = 'stat';
    $type = $_GET['stype'];

    //if ($type == '') $type = 'k1';

?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>애유갤 박물관 2021 v0.2</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="../css/styles.css?rev=0.1" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <!-- Responsive navbar-->
        <?php require('../src/nav.php') ?>

        <!-- Page content-->
        <div class="container">
            <div class="mt-5 mx-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <h1>통계<small> #2021</small></h1>
                        <div class="fs-6 text-secondary mb-5">지난 2021년 애유갤의 데이터를 분석하여 통계로 되돌아보세요</div>
                    </div>
                    <div class="mt-2 ms-3">
                        <span class="fs-1"><i class="fas fa-chart-bar"></i></span>
                    </div>
                </div>
            </div>
        </div>

            <div class="container shadow-lg p-3 mt-2 mb-5 bg-white backpanel">

                <div class="d-flex flex-wrap bd-highlight mb-3">
                    <div class="m-2">
                        <div class="fs-5 text-secondary mb-2"><i class="fas fa-comment-dots"></i> 키워드 분석</div>
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='k1'?'active':'')?>" href="./?stype=k1" >언급 비율</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='k2'?'active':'')?>" href="./?stype=k2">월별 언급 빈도</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='k3'?'active':'')?>" href="./?stype=k3">최다 언급 갤럼</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='k4'?'active':'')?>" href="./?stype=k4">개추:비추</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='k5'?'active':'')?>" href="./?stype=k5">워드클라우드</a>
                            </li>
                        </ul>
                    </div>

                    <div class="m-2">
                        <div class="fs-5 text-secondary mb-2"><i class="fas fa-user"></i> 갤러 분석</div>
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='g0'?'active':'')?>" href="./?stype=g0" onclick="loadMd.show();">전체</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='g1'?'active':'')?>" href="./?stype=g1">개인</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='g2'?'active':'')?>" href="./?stype=g2" onclick="loadMd.show();">활동 횟수</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='g3'?'active':'')?>" href="./?stype=g3" onclick="loadMd.show();">디시콘 랭킹</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='g4'?'active':'')?>" href="./?stype=g4" onclick="loadMd.show();">글별 랭킹</a>
                            </li>
                            <?php /*<li class="nav-item">
                                <a class="nav-link <?=($type=='g5'?'active':'')?>" href="./?stype=g5">갤럼간 댓글 랭킹</a>
                            </li> */?>
                            <li class="nav-item">
                                <a class="nav-link <?=($type=='g6'?'active':'')?>" href="./?stype=g6">워드클라우드</a>
                            </li>
                        </ul>
                    </div>
                </div>
                    
                <div class="container shadow-lg p-3 mt-2 mb-5 bg-white backpanel" id="statres">
                    <?php
                    switch ($type) {
                        case 'k1':
                            require("./src/k1.php");
                            break;

                        case 'k2':
                            require("./src/k2.php");
                            break;

                        case 'k3':
                            require("./src/k3.php");
                            break;
                        
                        case 'k4':
                            require("./src/k4.php");
                            break;

                        case 'k5':
                            require("./src/k5.php");
                            break;

                        case 'g0':
                            require("./src/g0.php");
                            break;

                        case 'g1':
                            require("./src/g1.php");
                            break;

                        case 'g2':
                            require("./src/g2.php");
                            break;

                        case 'g3':
                            require("./src/g3.php");
                            break;

                        case 'g4':
                            require("./src/g4.php");
                            break;
                        /*        
                        case 'g5':
                            require("./src/g5.php");
                            break; */

                        case 'g6':
                            require("./src/g6.php");
                            break;
                        default:
                        ?>
                            <div class="fs-4 text-center text-secondary p-3">
                                <img class="mt-3" src="../assets/bmo_anal_m.png" style="max-height:300px; max-width: 100%;">
                                <div class="my-3">분석할 항목을 선택해 주세요</div>
                            </div>
                        <?php
                    }
                    ?>
                    
                </div>
            

            </div>


        <div class="text-end text-secondary text-center" style="font-size: x-small;">
        <p>저장 DB: 2021.01.01~2021.12.31</p></div>
        <p class="text-secondary text-center">by 1227</p>

        <!-- 로딩 Modal -->
        <div class="modal fade" id="loadMd" name="loadMd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loader">
                        <img src="../assets/loading.gif" height="100px" width="100px">
                    </div>
                    <div clas="loader-txt">
                    <p>데이터 조회중입니다<br>
                    <small>잠시만 기다려 주세요...</small></p>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </body>
    <script src="../js/scripts.js?rev=0.4"></script>
    <?php # if ($_GET['previd'] != '') { echo "<script>document.getElementById('targetpost').scrollIntoView({behavior: \"smooth\", block: \"center\"});</script>"; } ?>
</html>
