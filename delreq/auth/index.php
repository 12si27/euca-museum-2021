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
    <body>

        <!-- Responsive navbar-->
        <?php require('../../src/nav.php') ?>

        <div>
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                        <?php if ($_GET['fail'] == 1) {
                            ?>
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <strong>오류 발생</strong></br>오류가 발생했습니다. ID를 올바르게 입력하였는지 확인해 주세요.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php
                        } elseif ($_GET['fail'] == 2) {
                            ?>
                            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                <strong>오류 발생</strong></br>DB에 해당 ID가 존재하지 않습니다. ID를 올바르게 입력하였는지 확인해 주세요.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php
                        } ?>
                            <form action="./doauth.php" method="POST" onsubmit="loadMd.show();">
                                <div class="card shadow-lg border-0 rounded my-5">
                                    <div class="card-header"><h3 class="font-weight-light m-2">일괄 삭제 요청</h3></div>

                                    <div class="btn-group mx-4 mt-4" role="group">
                                        <input class="btn-check" id="b1" disabled>
                                        <label class="btn btn-outline-secondary active" for="b1">시작</label>

                                        <input class="btn-check" id="b2">
                                        <label class="btn btn-outline-secondary active" for="b2">정보 입력</label>

                                        <input class="btn-check" id="b3" disabled>
                                        <label class="btn btn-outline-secondary" for="b3">인증 진행</label>

                                        <input class="btn-check" id="b4" disabled>
                                        <label class="btn btn-outline-secondary" for="b4">삭제 확인</label>

                                        <input class="btn-check" id="b5" disabled>
                                        <label class="btn btn-outline-secondary" for="b5">완료</label>
                                    </div>
                                    
                                    <div class="card-body m-2 mb-4">
                                        <label for="basic-url" class="form-label">삭제를 원하는 고닉 아이디를 입력해 주세요.</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon3">https://gallog.dcinside.com/</span>
                                            <input type="text" class="form-control" name="id" required>
                                        </div>
                                        
                                        <div class="card">
                                            <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted">주의</h6>
                                            <div style="font-size:small; color: gray;">로그인이 가능하며 갤로그 진입이 가능한 계정이어야 합니다.</br>
                                            이미 인증 코드가 발급된 경우 새로 코드가 발급되지 않습니다.</br>
                                            코드 재발급을 원하시는 경우 방명록에 있는 코드를 삭제한 후 다시 요청하시면 됩니다.</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer d-flex flex-row-reverse">
                                        <input type="submit" class="btn btn-secondary ms-2" value="다음">
                                        <button type="button" class="btn btn-outline-secondary" onclick="location.href='../';">뒤로</button>
                                    </div>
                                </div>
                            </form>
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
                        <img src="../../assets/loading.gif" height="100px" width="100px">
                    </div>
                    <div clas="loader-txt">
                    <p>잠시만 기다려 주세요<br>
                    <small>약 10~20초 정도 소요됩니다</small></p>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <script>
            var loadMd = new bootstrap.Modal(document.getElementById('loadMd'));
        </script>
    </body>
</html>