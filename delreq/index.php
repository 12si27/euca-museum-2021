<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>일괄 삭제 요청 - 애유갤 박물관</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body>

        <!-- Responsive navbar-->
        <?php require('../src/nav.php') ?>

        <div>
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded my-5">
                                <div class="card-header"><h3 class="font-weight-light m-2">일괄 삭제 요청</h3></div>

                                <div class="btn-group mx-4 mt-4" role="group">
                                    <input class="btn-check" id="b1">
                                    <label class="btn btn-outline-secondary active" for="b1">시작</label>

                                    <input class="btn-check" id="b2" disabled>
                                    <label class="btn btn-outline-secondary" for="b2">정보 입력</label>

                                    <input class="btn-check" id="b3" disabled>
                                    <label class="btn btn-outline-secondary" for="b3">인증 진행</label>

                                    <input class="btn-check" id="b4" disabled>
                                    <label class="btn btn-outline-secondary" for="b4">삭제 확인</label>

                                    <input class="btn-check" id="b5" disabled>
                                    <label class="btn btn-outline-secondary" for="b5">완료</label>
                                </div>
                                
                                <div class="card-body m-2">
                                    <h5 class="text-secondary">일괄 삭제 요청은...</h5>
                                    일괄 삭제 요청은 애유갤 박물관에서 자신의 모든 글, 댓글의 전시를 원치 않을 경우</br>전체를 한방에 삭제할 수 있는 기능입니다.</br></br>
                                    글과 댓글의 게시자임을 증명해야 하기 때문에 갤로그 방명록을 통한</br>인증 절차를 거쳐 삭제를 진행하게 됩니다.</br>
                                    (즉, 유동이 아닌 가입 회원만 일괄 삭제 기능을 사용하실 수 있습니다)</br></br>
                                    <span style="color: #DD6372; font-size: 8pt; font-weight:bold;">글의 양이 많거나 많은 사용자가 붐빌 경우 삭제가 실패할 수도 있습니다.</br>
                                    삭제 요청에 문제가 있는 경우 <a href="https://gallog.dcinside.com/12si27boon/guestbook" target="_blank">운영자 갤로그에 글을 남겨주시면</a> 삭제해 드리도록 하겠습니다</span>
                                    <div class="d-flex flex-row-reverse">
                                        <img src="dbmo.png" width="150px">
                                    </div>
                                </div>
                                
                                <div class="card-footer d-flex flex-row-reverse">
                                    <button type="button" class="btn btn-secondary" onclick="location.href='./auth'">다음</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
    </body>
</html>