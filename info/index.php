<?php
    $mode = 'info';
?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="robots" content="noindex"> <!-- 크롤링 방지용 메타태그 -->
        <title>애유갤 박물관 2021 v0.2</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="../css/styles.css?rev=0.1" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            .panel {
                border-radius: 10pt;
                padding-top: 20px;
                padding-bottom: 20px;
                padding-left: 30px;
                padding-right: 30px;
            }

            .paneltitle {
                font-size: 20pt;
                font-weight: 500;
                margin-top: 2px;
            }

            .panelsep {
                width: 100%;
                height: 2px;
                background-color: #a1a1a1;
                margin-top: 15px;
                margin-bottom: 15px;
            }
            
            /* unvisited link */
            a:link {
            color: rgb(78, 78, 78);
            }

            /* visited link */
            a:visited {
            color: rgb(114, 114, 114);
            }

            /* mouse over link */
            a:hover {
            color: rgb(88, 88, 88);
            }

            /* selected link */
            a:active {
            color: rgb(44, 44, 44);
            }
        </style>
    </head>
    <body>
        <!-- Responsive navbar-->
        <?php require('../src/nav.php') ?>

        <!-- Page content-->
        <div class="container">
            <div class="mx-4 my-5">
                <div class="d-flex justify-content-between">
                    <div>
                        <h1 class="mt-1">정보<small> #2021</small></h1>
                    </div>
                    <div class="">
                        <span class="fs-1"><i class="fas fa-info-circle"></i></span>
                    </div>
                </div>
            </div>
        
            <div class="row shadow-lg p-5 mt-2 mb-3 backpanel" style="background-image: url('./assets/info-bg1.jpg'); background-size: cover;">
                <div class="col-lg-5">
                    <div class="shadow mt-3 mb-3 bg-white panel">
                        <div class="paneltitle">애유갤 박물관은...</div>
                        <div class="panelsep"></div>
                        <div>
                            &nbsp;2020년 초부터 매번 수작업으로 결산을 내 오다가 겨울왕국 갤러리에서 <a href="https://frozen2020.netlify.app" target="_blank">웹 페이지를 통해 보기 좋게 통계를 알아볼 수 있는 페이지</a>를 발견하고
                            이거다 싶어서 한번 만들어본 아카이브 겸 분석 사이트입니다.</br></br>
                            사실 21년 2학기중에 DB랑 웹 개발 관련 강의를 들었었는데 당시 웹에 대해 아는게 하도 없어서 좀 갖고놀면서 익혀보기나 하자 싶어서 만든것이기도 합니다</br></br>
                            <small class="text-secondary">근데 정작 학기 끝나고 성적은 영 좋지 못했음</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-end shadow-lg p-5 mt-2 mb-4 backpanel" style="background-image: url('./assets/info-bg2.jpg'); background-size: cover;">
                <div class="col-lg-5">
                    <div class="shadow mt-3 mb-3 bg-white panel">
                        <div class="paneltitle text-end">사용한 솔루션</br>& 프로젝트</div>
                        <div class="panelsep"></div>
                        <div class="p-2" style="word-wrap: break-word;";>
                            <div class="fs-5 text-primary mb-2">
                                서버 환경
                            </div>
                            <div class="mb-4">
                                <ul>
                                    <li>Raspberry Pi 4</li>
                                    <li>Debian / Linux 10</li>
                                    <li>Lighttpd</li>
                                    <li>PHP 7.x</li>
                                    <li>MariaDB w/ InnoDB</li>
                                    <li>Python 3.x</li>
                                </ul>
                            </div>

                            <div class="fs-5 text-primary mb-2">
                                사용한 프로젝트
                            </div>
                            <div class="mb-4">
                                <ul>
                                    <li>Chart.js</br><a href="https://www.chartjs.org" target="_blank">https://www.chartjs.org</a></li>
                                    <li>Bootstrap 5</br><a href="https://getbootstrap.com/" target="_blank">https://getbootstrap.com/</a></li>
                                    <li>gallreader</br><a href="https://github.com/pdjdev/gallreader" target="_blank">https://github.com/pdjdev/gallreader</a></li>
                                    <li>Spoqa Han Sans</br><a href="https://spoqa.github.io/spoqa-han-sans/" target="_blank">https://spoqa.github.io/spoqa-han-sans/</a></li>
                                </ul>
                            </div>

                            <div class="fs-5 text-primary mb-2">
                                리포지토리
                            </div>
                            <div class="mb-4">
                                <ul>
                                    <li>euca-museum-2021</br><a href="https://github.com/12si27/euca-museum-2021" target="_blank">https://github.com/12si27/euca-museum-2021</a></li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <p class="text-secondary text-center">by 1227</p>
        <div class="text-end text-secondary text-center" style="font-size: x-small; color:gray;">
        <p><a href="../../privacy">개인정보처리방침</a></span>   |   <a href="../delreq">일괄 삭제 요청</a></p></div>
    </body>
    <script src="../js/scripts.js?rev=0.4"></script>
</html>
