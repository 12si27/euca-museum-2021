<?php
$id = $_GET['id'];
$code = $_GET['code'];

if ($id == '' or $code == '') {
    echo "<script>alert('잘못된 요청입니다.');history.back();</script>";
    exit();
} else {
    

    $conn = mysqli_connect('', '', '', '');

    $id = mysqli_escape_string($conn, $id);
    $nicks = array();

    $sql="SELECT COUNT(*) AS c, SUM(comments) AS c2 FROM `euca_gall_posts_2021` WHERE ipid = '$id'";
    $results = mysqli_query($conn,$sql);

    while ($result = mysqli_fetch_array($results)) {
        $posts = $result['c'];
        $r_cmts = $result['c2'];
    }

    $sql="SELECT COUNT(*) AS c FROM `euca_gall_cmts_2021` WHERE c_ipid = '$id'";
    $results = mysqli_query($conn,$sql);

    while ($result = mysqli_fetch_array($results)) {
        $cmts = $result['c'];
    }

    $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$id."' LIMIT 1");
    while ($result = mysqli_fetch_array($nickresult)) {
        array_push($nicks, $result['nickname']);
    }
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>일괄 삭제 요청 - 애유갤 박물관</title>
        <link href="../../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    </head>
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
    <body>
        <!-- Responsive navbar-->
        <?php require('../../src/nav.php') ?>

        <div>
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <?php if ($_GET['fail']) {
                                ?>
                                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                    <strong>삭제 실패</strong></br>내부 DB 오류로 삭제에 실패하였습니다. 잠시 후 다시 시도해보시고 여전히 문제가 있다면 <a href="https://gallog.dcinside.com/12si27boon/guestbook">운영자 갤로그</a>에 연락을 남겨주세요.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php
                            } ?>
                            
                            <div class="card shadow-lg border-0 rounded my-5">
                                <div class="card-header"><h3 class="font-weight-light m-2">일괄 삭제 요청</h3></div>

                                <div class="btn-group mx-4 mt-4" role="group">
                                    <input class="btn-check" id="b1" disabled>
                                    <label class="btn btn-outline-secondary active" for="b1">시작</label>

                                    <input class="btn-check" id="b2" disabled>
                                    <label class="btn btn-outline-secondary active" for="b2">정보 입력</label>

                                    <input class="btn-check" id="b3" disabled>
                                    <label class="btn btn-outline-secondary active" for="b3">인증 진행</label>

                                    <input class="btn-check" id="b4">
                                    <label class="btn btn-outline-secondary active" for="b4">삭제 확인</label>

                                    <input class="btn-check" id="b5" disabled>
                                    <label class="btn btn-outline-secondary" for="b5">완료</label>
                                </div>
                                
                                <div class="card-body m-2 mb-4">
                                    <div class="text-center">
                                        <h5>아래는 해당되는 글과 댓글입니다.</h5>
                                        <h6>애유갤 박물관(2021)에서 <?=$nicks[0]?>님이 쓴 내용을 모두 삭제하시겠습니까?</h6>
                                    </div>

                                    <div class="row">
                                        <div class="col">
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
                                                        <?=number_format($posts)?><span class="res-sub-text">개</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="align-self-end mb-2" style="font-size: 12pt;">
                                                        쓴 글에 포함된 댓글수
                                                    </div>
                                                    <div class="text-end flex-grow-1 res-text">
                                                        <?=number_format($r_cmts)?><span class="res-sub-text">개</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
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
                                                        <?=number_format($cmts)?><span class="res-sub-text">개</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                
                                <div class="card-footer d-flex flex-row-reverse">
                                    <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#delcfMd">네, 싹 다 지워주세요</button>
                                    <button type="button" class="btn btn-secondary" onclick="location.href='../'">다시 생각해볼게요</button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

        <!-- 체크&삭제 Modal -->
        <div class="modal fade" id="loadMd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="loader">
                        <img src="destroy.gif" height="100px" width="100px">
                    </div>
                    <div clas="loader-txt">
                    <p>일괄 삭제 진행 중입니다<br>
                    <span style="color: #DD6372; font-weight: bold;"><small>시간이 꽤나 걸리므로</br>창을 닫지 말아주세요!</small><span></p>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- 삭제요청 Modal -->
        <div class="modal fade" id="delcfMd" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form action='./dodel.php' method='post'>
                        <input type="hidden" name="id" value="<?=$id?>">
                        <input type="hidden" name="code" value="<?=$code?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">마지막 확인</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <p>일괄 삭제를 진행하게 되면 박물관 DB에서 <?=$nicks[0]?>님의 글, 댓글이 즉시 삭제됩니다.</p>
                                <p>또한 앞으로 통계 페이지에 있는 각종 키워드, 갤러 분석에서 제외되어 본인과 관련된 결과를 볼 수 없게 됩니다.</p>
                                <p><b>정말로 일괄 삭제 요청을 보내시겠습니까?</b></br>(이 작업은 되돌릴 수 없습니다.)</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">조금만 더 생각해볼게요</button>
                            <input type='submit' class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#loadMd" value='후회 안하니깐 빨랑 지워줘요!!' />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            var loadMd = new bootstrap.Modal(document.getElementById('loadMd'));
        </script>
    </body>
</html>