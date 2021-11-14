<?php
    $starttime = microtime(true);

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

    $sql="SELECT postid, title, nickname, ipid, date_format(date,'%y.%m.%d %H:%i') as date,
    views, upvotes, gonicupvotes, downvotes, comments, recommended, mobile, hasaccount, postdata FROM euca_gall_posts_2021 WHERE postid=".$_GET['id'];
    $result=mysqli_query($conn,$sql);

    if (mysqli_num_rows($result) < 1){
        echo "<script>alert('존재하지 않는 게시글입니다.');</script>";
        exit();
    }

    $postdata = mysqli_fetch_array($result);

    $sql = "SELECT cmtpostid, cmtid, c_nickname, c_ipid, date_format(c_date,'%y.%m.%d %H:%i') as c_date, c_hasaccount, isreply, refcid, isdccon, c_postdata FROM euca_gall_cmts_2021 WHERE cmtpostid=".$_GET['id'];
    $result = mysqli_query($conn,$sql);
    $commentcnt = mysqli_num_rows($result);

?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php echo $postdata['title']." - "."애유갤 박물관 2021"; ?></title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <meta name="description" content="애유갤 보존 프로젝트" />
        
        <style> img {max-width: 100%;}</style>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="../js/scripts.js"></script>

    </head>
    <body>

        
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="../"><i class="fas fa-landmark"></i> 애유갤 박물관 <small>2021</small> <span style="font-size: x-small;">v0.2 beta</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                    <div class="collapse navbar-collapse" id="navbarScroll">
                        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 150px;">
                        <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../">메인</a>
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
            
            <div class="row">
                <!-- 글 영역 -->
                <div class="col-lg-6">

                    <div class="shadow-lg p-3 mt-3 mb-3 bg-white backpanel">
                        <div class="form-group shadow rounded p-3 mb-2">
                            <h3><?php if ($postdata['recommended']) {echo "<i class=\"fas fa-medal\"></i> "; }
                            if ($_GET['squery'] != '' and ($_GET['stype'] == 1 or $_GET['stype'] == 2))
                            { echo str_replace($_GET['squery'],"<mark>".$_GET['squery']."</mark>", $postdata['title']); }
                            else { echo $postdata['title']; } ?></h3>
                            <div class="d-flex bd-highlight">
                                <div class="flex-fill bd-highlight">
                                    <div><i class="far fa-clock"></i> <?php echo $postdata['date'] ?>
                                    <span class="badge rounded-pill bg-secondary">댓글 <?php 
                                    echo $postdata['comments'] ?></span>
                                    <span class="badge rounded-pill bg-secondary">개추 <?php
                                    echo $postdata['upvotes'] ?></span>
                                    <span class="badge rounded-pill bg-secondary">비추 <?php
                                    echo $postdata['downvotes'] ?></span></div>
                                </div>
                                <div class="flex bd-highlight justify-content-end">
                                    <div><?php echo $postdata['nickname'] ?>
                                    <span class="badge rounded-pill bg-secondary"><?php
                                    echo $postdata['ipid'] ?></span></div>
                                </div>
                            </div>
                        </div>

                        <div class="shadow rounded">

                            <div class="flex-fill bd-highlight px-3 py-4">
                                <?php 
                                if ($_GET['squery'] != '' and $_GET['stype'] == 2)
                                { echo str_replace($_GET['squery'],"<mark>".$_GET['squery']."</mark>",$postdata['postdata']); }
                                else { echo $postdata['postdata']; }
                                ?>
                            </div>

                            <div class="d-flex justify-content-center pt-2">
                                <button type="button" class="btn btn-primary btn-lg">개추 <?php echo "<b>".$postdata['upvotes']."</b> (".$postdata['gonicupvotes'].")" ?></button>
                                <button type="button" class="btn btn-secondary btn-lg ms-2">비추 <b><?php echo $postdata['downvotes'] ?></b></button>
                            </div>
                            <div class="d-flex justify-content-center pt-2 pb-4">
                                <a type="button" class="btn btn-outline-primary btn-sm" href="https://gall.dcinside.com/euca/<?php echo $postdata['postid']; ?>" target="_blank"> 원본 글 링크</a>
                                <a type="button" class="btn btn-outline-danger btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#delcfMd" target="_blank"> 삭제 요청</a>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- 댓글 영역 -->
                <div class="col-lg-6">
                    <div class="shadow-lg p-3 mt-3 mb-3 bg-white backpanel">
                        <div class="form-group shadow rounded p-3 mb-2">
                            <span style="font-size: x-large;">댓글  </span><span>총 <?php echo $commentcnt ?>개</span>
                        </div>

                        <div class="flex">
                        <?php

                            function print_cmt($data, $pd, $isreply) {
                                echo "<div class=\"shadow rounded my-3 ";
                                if ($isreply) {echo "ms-3";}
                                echo "\" ";
                                if ($pd == $data['c_ipid']) { echo "style='background-color:#E2ECF7;'"; }
                                echo ">
                                    <div class=\"p-3\">
                                        <div class=\"d-flex bd-highlight\">
                                            <div class=\"flex-fill bd-highlight\">
                                                <div>";
                                echo $data['c_nickname'];
                                echo " <span class=\"badge rounded-pill bg-secondary\">";
                                echo $data['c_ipid'];
                                echo "</span></div>
                                            </div>
                                            <div class=\"flex bd-highlight justify-content-end\">
                                                <div><i class=\"far fa-clock\"></i>";
                                echo $data['c_date']."</div></div></div><div class='pt-2'>";

                                if ($data['isdccon']) {
                                    echo "<img src=".$data['c_postdata'].">";
                                } else {
                                    echo $data['c_postdata'];
                                }
                                echo "</div></div></div>";
                            }

                            $comm = array();
                            $rply = array();

                            while($board = mysqli_fetch_array($result)){
                                if ($board['isreply']) {
                                    array_push($rply, $board);
                                } else {
                                    array_push($comm, $board);
                                }
                            }

                            // 배열 다시 읽어서 순서 정리

                            for ($i=0; $i<count($comm); $i++) {

                                // 일반 댓글 출력
                                print_cmt($comm[$i], $postdata['ipid'], 0);
                                
                                for ($j=0; $j<count($rply); $j++) {

                                    if ($rply[$j]['refcid'] == $comm[$i]['cmtid']) {
                                        print_cmt($rply[$j], $postdata['ipid'], 1);
                                        //array_splice($rply, $j, 1);
                                    }

                                    // array_splice($rply, $j, 1); // 해당 배열 제거

                                    // 댓글마다 답글 존재 여부 체크
                                }

                            }

                        ?>
                        </div>
                    </div>   
                </div>
            </div>
        </div>
        <?php
        
        $postloadtime = microtime(true) - $starttime;
        
        // 목록 비활성화 옵션 활성화일 경우 -> 여기서 끝
        if ($nolist) {
            ?><div class="text-end text-secondary text-center" style="font-size: x-small; font-color:gray;">
            <p>저장 DB: 2021.01.01~2021.10.31*</br><?php
            echo "글 로드 시간:".round($postloadtime, 4)."</p>";
            echo "<p>* 트래픽 최소화를 위해 본문 이미지는 링크되어 있으므로<br>PC 환경에서는 사진이 표시되지 않을 수 있습니다.</p></div></body></html>";
            exit();
        }

        $starttime = microtime(true);

        ?>


        <!-- 글 목록 시작 -->

        <div class="container shadow-lg p-3 mt-2 mb-5 bg-white backpanel">

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
                <form class="flex bd-highlight justify-content-end align-items-center text-end" name="monthForm" action="../">
                    <?php
                        # if ($_GET['page'] != "") { echo "<input hidden value=\"".$_GET['page']."\""." name=\"page\">"; }
                        if ($_GET['squery'] != "") { echo "<input hidden value=\"".$_GET['squery']."\""." name=\"squery\">"; }
                        if ($_GET['stype'] != "") { echo "<input hidden value=\"".$_GET['stype']."\""." name=\"stype\">"; }
                        if ($recom == 1) { echo "<input hidden value=\"".$_GET['recom']."\""." name=\"recom\">"; } ?>
                    <select class="form-select form-select-sm" id="dateGroupSelect" aria-label="select" name="month" onchange="javascript:document.monthForm.submit();">
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

                    
                    while($board = mysqli_fetch_array($result)){
                
                        echo "<div class=\"shadow my-3 postzoom\" data-bs-toggle=\"modal\" data-bs-target=\"#loadMd\" onclick=\"location.href='?id=";
                        echo $board['postid']."&page=".$pg;
                        if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                        if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                        if ($recom==1) { echo "&recom=1"; }
                        if ($month!='') { echo "&month=".$month; }
                        echo "';\" ";
                        if ($board['postid'] == $_GET['id']) {
                            echo "style='background-color:#E2ECF7;'";
                        }
                        echo ">";
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
                        echo "<img src=\"../assets/gonic.gif\" width=14px height=14px>";} else { echo "(".$board['ipid'].")"; }
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
                            <a class="page-link" href="../?page=1<?php
                            if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                            if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                            if ($recom==1) { echo "&recom=1"; }
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
                                            echo "\" href=\"../?page=".($i + 1);
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
                                            echo "\" href=\"../?page=".($pg + $i - 2);
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
                            <a class="page-link" href="<?php echo "../?page=".$totalpages;
                                if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                if ($_GET['stype']!='') { echo "&stype=".$_GET['stype']; }
                                if ($recom==1) { echo "&recom=1"; }
                                if ($month!='') { echo "&month=".$month; }?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">끝</span>
                            </a>
                            </li>
                        </ul>
                    </nav>

                </div>

                <form action='../' method='get'>
                    <div class="d-flex justify-content-center">
                        
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

            </div>

            <!-- 로딩 Modal -->
            <div class="modal fade" id="loadMd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
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

            <!-- 체크&삭제 Modal -->
            <div class="modal fade" id="delchkMd" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="chkModalLabel">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="loader">
                            <img src="../assets/loading.gif" height="100px" width="100px">
                        </div>
                        <div clas="loader-txt">
                        <p>게시글 상태를 확인하고 있습니다<br>
                        <small>잠시만 기다려 주세요...</small></p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- 삭제요청 Modal -->
            <div class="modal fade" id="delcfMd" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form action='./chk_post.php' method='post'>
                        <input type="hidden" id="postid" name="postid" value="<?php echo $postdata['postid'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">삭제 요청</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <p>해당 게시글의 전시를 원치 않는 경우 원본 글을 지우고 삭제 요청을 하면 박물관 서버가 글의 삭제 유무를 확인하여 삭제 처리합니다.</p>
                                <p><b>정말로 삭제요청을 보내시겠습니까?</b></br>(이 작업은 되돌릴 수 없습니다.)</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                            <input type='submit' class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#delchkMd" value='네, 보내겠습니다' />
                        </div>
                    </form>
                </div>
            </div>
        </div>

            <div class="text-end text-secondary text-center" style="font-size: x-small; font-color:gray;">
            <p>저장 DB: 2021.01.01~2021.10.31</br>

            <?php $endtime = microtime(true);
            echo "글 로드 시간:".round($postloadtime, 4)."</br>";
            echo "목록 로드 시간:".round($endtime - $starttime, 4)."</p>";
            ?>
            
            <p>트래픽 최소화를 위해 본문 콘텐츠는 링크되어 있으므로<br>PC 환경에서는 제대로 표시되지 않을 수 있습니다.</p></div>
            <p class="text-secondary text-center">by 1227</p>

        </div>
    </body>
</html>
