<?php
    $pg = (int)$_GET['page'];
    $recom = $_GET['recom'];
    $month = $_GET['month'];

    if ($pg == '' or $pg == 0) { $pg = 1; }

    $postcount = 15;
    $sk = "WHERE 1";

    # 월별
    if (!($month == '' or $month == '0')) { $sk .= " AND MONTH(date)=".$month; }

    # 념글
    if ($recom) { $sk .= " AND recommended=1"; }

    # 서치쿼리
    if ($_GET['squery'] != '') {
        $sk .= " AND ";
        
        if ($_GET['stype'] > 0 and $_GET['stype'] < 5) {

            $sqr = $_GET['squery'];

            if ($_GET['stype'] == 1) {
                $sk .= "(INSTR(title, '".$sqr."'))";
            } elseif ($_GET['stype'] == 2) {
                $sk .= "(INSTR(title, '".$sqr."') OR INSTR(postdata, '".$sqr."'))";
            } elseif ($_GET['stype'] == 3) {
                $sk .= "(INSTR(nickname, '".$sqr."'))";
            } elseif ($_GET['stype'] == 4) {
                $sk .= "(INSTR(ipid, '".$sqr."'))";
            }
        }
    }

    $conn= mysqli_connect('', '', '', '');
    $sql="SELECT postid, title, nickname, ipid, date_format(date,'%y.%m.%d %H:%i') as date2,
    views, upvotes, gonicupvotes, downvotes, comments, recommended, mobile, hasaccount
    FROM euca_gall_posts_2021 ".$sk." ORDER BY postid DESC LIMIT ".($postcount*($pg-1)).", ".$postcount;

    $result = mysqli_query($conn,$sql);

    $cnt = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM euca_gall_posts_2021 ".$sk));
    $totalposts = $cnt['cnt'];

    
    $totalpages = (int)($totalposts/$postcount);

    if (($totalposts%$postcount)>1) {
        $totalpages++;
    }

    //echo "<script>alert('$totalpages')</script>"

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
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="./"><i class="fas fa-landmark"></i> 애유갤 박물관 <small>2021</small> <span style="font-size: x-small;">v0.2 beta</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                    <div class="collapse navbar-collapse" id="navbarScroll">
                        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 150px;">
                        <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./">메인</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="#">통계 (준비중)</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="#">정보 (준비중)</a>
                        </li>
                        </ul>
                </div>
            </div>
        </nav>

        <!-- Page content-->
        <div class="container">
            <div class="mt-5 mx-4">
                <h1>애유갤 박물관<small> #2021</small></h1>
                <div class="lead mb-5">귀중한 애유갤의 역대 글 자료를 자유롭게 열람하세요</div>
            </div>
        </div>

            <div class="container shadow-lg p-3 mt-2 mb-5 bg-white backpanel">
                <?php

                    if ($_GET['squery']!='') {
                        echo "<div class=\"ms-1 mt-1 mb-1\"><span style='font-size: x-large;'>\"".$_GET['squery']."\" ";
                        if ($_GET['stype'] == 1) { echo "제목"; }
                        elseif ($_GET['stype'] == 2) { echo "제목+내용"; }
                        elseif ($_GET['stype'] == 3) { echo "닉네임"; }
                        elseif ($_GET['stype'] == 4) { echo "IP,ID"; }
                        echo " 검색 결과</span>";
                        echo " (총 ".$totalposts."개)</div>";
                    } ?>

                    <div class="d-flex">

                        <form class="flex-fill bd-highlight align-items-center">
                            <div class="form-check form-switch ms-1 mt-1">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" onclick="<?php 

                                    echo "window.location='./?recom=";
                                    if ($recom == 1) { echo "0"; } else { echo "1"; } 
                                    if ($_GET['page'] != "") { echo "&page=".$_GET['page']; }
                                    if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                    if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                                    if ($month!='') { echo "&month=".$month; }
                                    
                                ?>';" <?php if ($recom == 1) { echo " checked"; } ?> >
                                <label class="form-check-label" for="flexSwitchCheckDefault">개념글만 보기</label>
                            </div>
                        </form>
                    
                        <form class="flex bd-highlight justify-content-end align-items-center text-end" name="monthForm" id="monthForm">
                            <?php
                            # if ($_GET['page'] != "") { echo "<input hidden value=\"".$_GET['page']."\""." name=\"page\">"; }
                            if ($_GET['squery'] != "") { echo "<input hidden value=\"".$_GET['squery']."\""." name=\"squery\">"; }
                            if ($_GET['stype'] != "") { echo "<input hidden value=\"".$_GET['stype']."\""." name=\"stype\">"; }
                            if ($recom == 1) { echo "<input hidden value=\"".$_GET['recom']."\""." name=\"recom\">"; } ?>
                            <select class="form-select form-select-sm" id="dateGroupSelect" aria-label="select" name="month" onchange="javascript:document.monthForm.submit(); loadMd.show();">
                                <option <?php if ($month==0) { echo "selected"; } ?> value="0">전체</option>
                                <?php
                                    for ($m=1; $m<=12; $m++) {
                                        echo "<option ";
                                        if ($month == $m) { echo "selected "; }
                                        echo "value=\"".$m."\">2021-".$m."</option>\n";
                                    }
                                ?>
                            </select>
                        </form>
                                        
                    </div>

                    

                        <?php

                            while($board = mysqli_fetch_array($result)){
                        
                                echo "<div class=\"shadow my-3 postzoom\" data-bs-toggle=\"modal\" data-bs-target=\"#loadMd\" onclick=\"location.href='post?id=";
                                echo $board['postid']."&page=".$pg;
                                if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                                if ($recom==1) { echo "&recom=1"; }
                                if ($month!='') { echo "&month=".$month; }
                                echo "';\">";
                                echo "    <div class=\"p-3\">";
                                echo "        <div class=\"d-flex bd-highlight align-items-center\">";
                                echo "            <div class=\"flex-fill bd-highlight\">";
                                echo "                <div>";
                                if ($board['recommended']) {echo "<i class=\"fas fa-medal\"></i> "; }
                                if ($_GET['squery'] != '' and ($_GET['stype'] == 1 or $_GET['stype'] == 2))
                                { echo str_replace($_GET['squery'],"<mark>".$_GET['squery']."</mark>",$board['title']); }
                                else { echo $board['title']; }
                                echo "</div>";
                                echo "                <span class=\"badge rounded-pill bg-secondary\">".$board['comments']."</span>";
                                echo "                <span class=\"badge rounded-pill bg-primary\">".$board['upvotes']."</span>";
                                echo "                <span class=\"badge rounded-pill bg-danger\">".$board['downvotes']."</span>";
                                echo "            </div>";
                                echo "            <div class=\"flex bd-highlight justify-content-end align-items-center text-end fs-6\">";
                                if ($_GET['squery'] != '' and $_GET['stype'] == 3) { echo str_replace($_GET['squery'],"<mark>".$_GET['squery']."</mark>",$board['nickname']); }
                                else { echo $board['nickname']; }
                                if ($board['hasaccount']) {
                                echo "<img src=\"assets/gonic.gif\" width=14px height=14px>";} else { echo "(".$board['ipid'].")"; }
                                echo "                <div><i class=\"far fa-clock\"></i> ".$board['date2']."</div>";
                                echo "            </div>";
                                echo "        </div>";
                                echo "    </div>";
                                echo "</div>";
                            }

                        ?>

                <!-- 페이지 버튼 영역 -->

                <div class="d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                            <a class="page-link" href="?page=1<?php
                            if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                            if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                            if ($recom==1) { echo "&recom=1"; }
                            if ($month!='') { echo "&month=".$month; }
                            ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">처음</span>
                            </a>
                            </li>

                            <?php
                                
                                $i = 0;
                                $pg = (int)$pg;

                                if ($pg < 4) {
                                    while($i < 5) {
                                        if (!(($i + 1) > $totalpages) or $i==0) {
                                            echo "<li class=\"page-item";
                                            if (($i + 1) == $pg) { echo " active"; }
                                            echo "\"><a class=\"page-link";
                                            echo "\" href=\"?page=".($i + 1);
                                            if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                            if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                                            if ($recom==1) { echo "&recom=1"; }
                                            if ($month!='') { echo "&month=".$month; }
                                            echo "\">";
                                            echo ($i + 1);
                                            echo "</a></li>";
                                        }
                                        $i++;
                                    }

                                } else {
                                    while($i < 5) {
                                        if (!(($pg + $i - 2) > $totalpages) or $i==0) {
                                            echo "<li class=\"page-item";
                                            if (($pg + $i - 2) == $pg) { echo " active"; }
                                            echo "\"><a class=\"page-link";
                                            echo "\" href=\"?page=".($pg + $i - 2);
                                            if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                            if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                                            if ($recom==1) { echo "&recom=1"; }
                                            if ($month!='') { echo "&month=".$month; }
                                            echo "\">";
                                            echo ($pg + $i - 2);
                                            echo "</a></li>";
                                        }

                                        $i++;
                                    }
                                }

                            ?>
                            <li class="page-item">
                            <a class="page-link" href="<?php echo "?page=".$totalpages;
                                if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                                if ($recom==1) { echo "&recom=1"; }
                                if ($month!='') { echo "&month=".$month; } ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">끝</span>
                            </a>
                            </li>
                        </ul>
                    </nav>

                </div>

                <form action='./' method='get'>
                <div class="d-flex justify-content-center">
                        <?php if ($recom==1) { echo "\n<input type=\"hidden\" name=\"recom\" value=\"1\">\n"; } ?>
                        <div class="col-lg-1">
                            <select class="form-select" id="inputGroupSelect03" aria-label="Example select with button addon" name="stype" required>
                                <option <?php if ($_GET['stype']==1) { echo "selected"; } ?> value="1">제목</option>
                                <option <?php if ($_GET['stype']==2) { echo "selected"; } ?> value="2">제목+내용</option>
                                <option <?php if ($_GET['stype']==3) { echo "selected"; } ?> value="3">닉네임</option>
                                <option <?php if ($_GET['stype']==4) { echo "selected"; } ?> value="4">ID,IP</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <?php if ($recom == 1) { echo "<input hidden value=\"".$_GET['recom']."\""." name=\"recom\">"; }
                            if (!($month == '' or $month == 0)) { echo "<input hidden value=\"".$month."\""." name=\"month\">"; } ?>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="squery" placeholder="검색어 입력" aria-label="검색어 입력" aria-describedby="button-addon2" value="<?php echo $_GET['squery']; ?>">
                                <input class="btn btn-outline-secondary" type="submit" id="button-addon2" data-bs-toggle="modal" data-bs-target="#loadMd" value="검색">
                            </div>
                        </div>
                </div>
                </form>

                <!-- 로딩 Modal -->
                <div class="modal fade" id="loadMd" name="loadMd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                        <div class="modal-content">
                        <div class="modal-body text-center">
                            <div class="loader">
                                <img src="./assets/loading.gif" height="100px" width="100px">
                            </div>
                            <div clas="loader-txt">
                            <p>데이터 조회중입니다<br>
                            <small>잠시만 기다려 주세요...</small></p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="text-end text-secondary text-center" style="font-size: x-small; font-color:gray;">
        <p>저장 DB: 2021.01.01~2021.10.31<br><?php echo "총 ".number_format($totalposts)."개의 글 검색됨" ?></p>
        <p>트래픽 최소화를 위해 본문 콘텐츠는 링크되어 있으므로<br>PC 환경에서는 제대로 표시되지 않을 수 있습니다.</p></div>
        <p class="text-secondary text-center">by 1227</p>
    </body>

    <script>
        var loadMd = new bootstrap.Modal(document.getElementById('loadMd'));
    </script>
</html>
