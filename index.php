<?php
    $mode = 'main';
    require('./src/dbconn.php');

    $cookie_name = 'user';
    $pg = (int)$_GET['page'];
    $recom = $_GET['recom'];
    $month = $_GET['month'];
    $squery = $_GET['squery'];
    $asc = $_GET['asc'];
    $ascc = $_COOKIE['asc'];

    $hidenick = $_GET['hidenick'];
    if ($hidenick == 1) { require("./src/hasher.php"); }

    $stype = $_GET['stype']; # 검색방식

    if ($asc == null) { # get asc 값 없을때
        if ($ascc == null) { # 쿠키값도 없을때
            $asc = 0; setcookie('asc', 0, time()+3600*24*365, '/');
        } else { $asc = $ascc; }
    } else { setcookie('asc', $asc, time()+3600*24*365, '/'); }

    if ($pg == '' or $pg == 0) { $pg = 1; }

    $postcount = 15;
    $sk = "";

    # 월별
    # if (!($month == '' or $month == '0')) { $sk .= " AND MONTH(date)=".$month; }
    if (!($month == '' or $month == '0')) { 
        if ($stype > 4 and $stype < 8) {
            $sk .= ' AND (c_date ';
        } else {
            $sk .= ' AND (date ';
        }

        if ($month == '12') {
            $sk .= 'BETWEEN date("2021-12-01") AND date("2022-1-1")-1)';
        } else {
            $sk .= 'BETWEEN date("2021-'.$month.'-1") AND date("2021-'.($month+1).'-1")-1)';
        }
        
    }

    # 념글
    if ($recom and !($stype > 4 and $stype < 8)) { $sk .= " AND recommended=1"; }

    # 서치쿼리
    if ($squery != '') {
        $sk .= " AND ";
        
        if ($stype > 0 and $stype < 8) {

            // 검색쿼리 추출
            $sqr = mysqli_real_escape_string($conn, $squery);

            // 명령어 탐색
            $cmd = explode(" ", $sqr);
            for ($i=0; $i < count($cmd); $i++) {
                if (strpos($cmd[$i], "IPID:") !== false) {
                    $findnick = trim(substr($cmd[$i], strpos($cmd[$i], "IPID:")+5));

                    if ($stype < 5) {
                        $sk .= "(ipid = '".trim($findnick)."') AND ";
                    } else {
                        $sk .= "(c_ipid = '".trim($findnick)."') AND ";
                    }
                    
                    $sqr = str_replace(substr($cmd[$i], strpos($cmd[$i], "IPID:")), "", $sqr);
                    $squery = trim(str_replace(substr($cmd[$i], strpos($cmd[$i], "IPID:")), "", $squery));
                    break;
                }
            }

            $sq2 = trim(str_replace("%", "\\%", $sqr));

            // 검색 쿼리가 공란이면
            if ($sq2 == '') {
                $sk .= '1';
            } else {
                if ($stype == 1) {
                    $sk .= "(title LIKE '%".$sq2."%')";
                } elseif ($stype == 2) {
                    $sk .= "(title LIKE '%".$sq2."%' OR postdata LIKE '%".$sq2."%')";
                } elseif ($stype == 3) {
                    $sk .= "(nickname LIKE '%".$sq2."%')";
                } elseif ($stype == 4) {
                    $sk .= "(ipid LIKE '%".$sq2."%')";
                } elseif ($stype == 5) { # 댓글-내용
                    $sk .= "(c_postdata LIKE '%".$sq2."%')";
                } elseif ($stype == 6) { # 댓글-닉네임
                    $sk .= "(c_nickname LIKE '%".$sq2."%')";
                } elseif ($stype == 7) { # 댓글-IPID
                    $sk .= "(c_ipid LIKE '%".$sq2."%')";
                }
            }
        }
    }

    $order = ($asc==1 ? "ASC" : "DESC");

    if ($stype > 4 and $stype < 8) {

        # 댓글 검색이므로 -> 댓글 출력
        $sql = "SELECT cmtpostid, cmtid, c_nickname, c_ipid, date_format(c_date,'%y.%m.%d %H:%i') as date2, 
        c_hasaccount, isreply, refcid, isdccon, c_postdata, title 
        FROM euca_gall_cmts_2021 LEFT OUTER JOIN euca_gall_posts_2021 
        ON euca_gall_cmts_2021.cmtpostid = euca_gall_posts_2021.postid WHERE 1".$sk." 
        ORDER BY cmtid ".$order." LIMIT ".($postcount*($pg-1)).", ".$postcount;

        $result = mysqli_query($conn,$sql);

        $cnt = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM euca_gall_cmts_2021 WHERE 1".$sk));
        $totalposts = $cnt['cnt'];

    } else {
        
        $sql="SELECT postid, title, nickname, ipid, date_format(date,'%y.%m.%d %H:%i') as date2,
        views, upvotes, gonicupvotes, downvotes, comments, recommended, mobile, hasaccount
        FROM euca_gall_posts_2021 WHERE 1".$sk." ORDER BY postid ".$order." LIMIT ".($postcount*($pg-1)).", ".$postcount;

        $result = mysqli_query($conn,$sql);

        $cnt = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM euca_gall_posts_2021 WHERE 1".$sk));
        $totalposts = $cnt['cnt'];

    }

    $totalpages = (int)($totalposts/$postcount);
    if (($totalposts%$postcount)>0) { $totalpages++; }

    //echo "<script>alert('$totalpages')</script>"

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
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css?rev=0.1" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <!-- Responsive navbar-->
        <?php require('./src/nav.php') ?>

        <!-- Page content-->
        <div class="container">

        <?php 
        // 삭제 요청 성공 확인 팝업창 표시
        if ($_GET['delsucc']) {
            ?>
            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </symbol>
            </svg>

            <div class="alert alert-success alert-dismissible fade show mt-3 d-flex" role="alert">
                <div width="24"><svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg></div>
                <div><strong>삭제 처리됨</strong></br>요청한 글과 포함 댓글이 성공적으로 삭제되었습니다.</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
        } elseif ($_GET['delfail']) {
            ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <strong>삭제 요청 실패</strong></br>DB 내부 오류로 삭제에 실패했습니다. 잠시 후 다시 시도해 주세요.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
        } ?>

        
            <div class="mt-5 mx-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <h1>애유갤 박물관<small> #2021</small></h1>
                        <div class="fs-6 text-secondary mb-5">귀중한 2021년 애유갤의 역대 글 자료를 자유롭게 열람하세요</div>
                    </div>
                    <div class="mt-2 ms-3">
                        <span class="fs-1"><i class="fas fa-landmark"></i></span>
                    </div>
                </div>
            </div>
        </div>

            <div class="container shadow-lg p-3 mt-2 mb-5 bg-white backpanel">
                <?php

                    if ($squery!='') {

                        echo "<div class=\"ms-1 mt-1 mb-1\"><span style='font-size: x-large; word-break: break-all;'>\"";
                        
                        if (strlen($squery) > 30) { echo substr($squery,0,30)."..."; }
                        else { echo $squery; }

                        echo "\" ";
                        
                        if ($stype == 1) { echo "제목"; }
                        elseif ($stype == 2) { echo "제목+내용"; }
                        elseif ($stype == 3) { echo "닉네임"; }
                        elseif ($stype == 4) { echo "IP,ID"; }
                        elseif ($stype == 5) { echo "댓글 내용"; }
                        elseif ($stype == 6) { echo "댓글 닉네임"; }
                        elseif ($stype == 7) { echo "댓글 IP,ID"; }
                        echo " 검색 결과</span>";
                        echo " (총 ".$totalposts."개)</div>";
                    } ?>

                    <div class="d-flex">

                        <!-- 념글만 스위치 -->
                        <form class="flex-fill bd-highlight align-items-center">
                            <div class="form-check form-switch ms-1 mt-1">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" onclick="<?php 

                                    echo "window.location='./?recom=";
                                    if ($recom == 1) { echo "0"; } else { echo "1"; } 
                                    if ($_GET['page'] != "") { echo "&page=".$_GET['page']; }
                                    if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                    if ($stype!='') { echo "&stype=".$stype; }
                                    if ($month!='') { echo "&month=".$month; }
                                    if ($hidenick==1) { echo "&hidenick=1"; }
                                    
                                    ?>';" <?php if ($recom == 1) { echo " checked"; } if ($stype > 4 and $stype < 8) { echo " disabled"; } ?> >
                                <label class="form-check-label" for="flexSwitchCheckDefault">개념글만 보기</label>
                            </div>
                        </form>
                    
                        <!-- 정렬 버튼 -->
                        <form class="me-2" name="sortBt" id="sortBt" action="./">
                            <input hidden name="asc" value="<?=($asc==1?0:1)?>">
                            <?php
                            if ($_GET['page'] != "") { echo "<input hidden value=\"".$_GET['page']."\""." name=\"page\">"; }
                            if ($_GET['squery'] != "") { echo "<input hidden value=\"".$_GET['squery']."\""." name=\"squery\">"; }
                            if ($stype != "") { echo "<input hidden value=\"".$stype."\""." name=\"stype\">"; }
                            if ($recom == 1) { echo "<input hidden value=\"1\" name=\"recom\">"; }
                            if ($_GET['month'] != "") { echo "<input hidden value=\"".$_GET['month']."\""." name=\"month\">"; }
                            if ($hidenick == 1) { echo "<input hidden value=\"1\" name=\"hidenick\">"; } ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="javascript:document.sortBt.submit(); loadMd.show();">
                            <?php if ($asc=='1') { echo "<i class=\"fas fa-sort-numeric-down\"></i>"; }
                                  else { echo "<i class=\"fas fa-sort-numeric-down-alt\"></i>"; } ?>
                            </button>
                        </form>
                        
                        <!-- 월별 콤보박스 -->
                        <form class="flex bd-highlight justify-content-end align-items-center text-end" name="monthForm" id="monthForm">
                            <?php
                            # if ($_GET['page'] != "") { echo "<input hidden value=\"".$_GET['page']."\""." name=\"page\">"; }
                            if ($_GET['squery'] != "") { echo "<input hidden value=\"".$_GET['squery']."\""." name=\"squery\">"; }
                            if ($stype != "") { echo "<input hidden value=\"".$stype."\""." name=\"stype\">"; }
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

                            if ($stype > 4 and $stype < 8) { #댓글일 경우

                                while($board = mysqli_fetch_array($result)){
                                ?>
                                    <div class="shadow my-3 postzoom" data-bs-toggle="modal" data-bs-target="#loadMd" onclick=<?php 
                                        echo "\"location.href='post?id=".$board['cmtpostid']."&page=".$pg;
                                        if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                        if ($stype!='') { echo "&stype=".$stype; }
                                        if ($recom==1) { echo "&recom=1"; }
                                        if ($month!='') { echo "&month=".$month; }
                                        if ($hidenick==1) { echo "&hidenick=1"; }
                                        echo "&cmtid=".$board['cmtid']."';\"";

                                        if ($_GET['pcmtid']==$board['cmtid']) {
                                            echo " id=\"targetpost\" style='background-color:#FAEFCA;'";
                                        } elseif ($_GET['previd']==$board['cmtpostid']) {
                                            echo " style='background-color:#E2ECF7;'";
                                        }

                                        ?>>
                                        <div class="p-3">
                                            <div class="d-flex bd-highlight align-items-center">
                                                <div class="flex-fill bd-highlight">
                                                    <div style="font-size:small; color:gray;"><i class="fas fa-quote-left"></i> <?=$board['title']?></div>
                                                    <div><i class="fas fa-comment-alt"></i> <?php
                                                    if ($board['isdccon']) {
                                                    echo "<img src=\"".$board['c_postdata']."\">";
                                                    } elseif ($_GET['squery'] != '' and ($stype == 5))
                                                    { echo str_replace($squery,"<mark>".$squery."</mark>",$board['c_postdata']); }
                                                    else { echo $board['c_postdata']; } ?></div>
                                                </div>
                                                <div class="flex bd-highlight justify-content-end align-items-center text-end fs-6"><?php
                                                        if ($hidenick) {
                                                            echo explode(' #', eucahash($board['c_ipid']))[0];
                                                            if ($board['c_hasaccount']) { echo "<img src=\"assets/gonic.gif\" width=14px height=14px>";}
                                                        } else {
                                                            if ($squery != '' and $stype == 6)
                                                            { echo str_replace($squery,"<mark>".$squery."</mark>",$board['c_nickname']); }
                                                            else { echo $board['c_nickname']; }
                                                            if ($board['c_hasaccount']) { echo "<img src=\"assets/gonic.gif\" width=14px height=14px>";}
                                                            else { echo "<sub>(".$board['c_ipid'].")</sub>"; }
                                                        }
                                                        ?>
                                                        <div><i class="far fa-clock"></i> <?=$board['date2']?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }

                            } else { # 일반 글일 경우

                                while($board = mysqli_fetch_array($result)){
                                    ?>
                                        <div class="shadow my-3 postzoom" data-bs-toggle="modal" data-bs-target="#loadMd" onclick=<?php 
                                        echo "\"location.href='post?id=".$board['postid']."&page=".$pg;
                                        if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                                        if ($stype!='') { echo "&stype=".$stype; }
                                        if ($recom==1) { echo "&recom=1"; }
                                        if ($month!='') { echo "&month=".$month; }
                                        if ($hidenick==1) { echo "&hidenick=1"; }
                                        echo "';\"";
                                        ?> <?=($_GET['previd']==$board['postid'] ? "id=\"targetpost\" style='background-color:#E2ECF7;'":"")?>>
                                            <div class="p-3">
                                                <div class="d-flex bd-highlight align-items-center">
                                                    <div class="flex-fill bd-highlight">
                                                        <div><?php
                                                        if ($board['recommended']) {echo "<i class=\"fas fa-medal\"></i> "; }
                                                        if ($squery != '' and ($stype == 1 or $stype == 2))
                                                        { echo str_replace($squery,"<mark>".$squery."</mark>",$board['title']); }
                                                        else { echo $board['title']; } ?></div>
                                                        <span class="badge rounded-pill bg-secondary"><?=$board['comments']?></span>
                                                        <span class="badge rounded-pill bg-primary"><?=$board['upvotes']?></span>
                                                        <span class="badge rounded-pill bg-danger"><?=$board['downvotes']?></span>
                                                    </div>
                                                    <div class="flex bd-highlight justify-content-end align-items-center text-end fs-6"><?php
                                                        if ($hidenick == 1) {
                                                            echo explode(' #', eucahash($board['ipid']))[0];
                                                            if ($board['hasaccount']) { echo "<img src=\"assets/gonic.gif\" width=14px height=14px>";}
                                                        } else {
                                                            if ($squery != '' and $stype == 3) {
                                                                echo str_replace($squery,"<mark>".$squery."</mark>",$board['nickname']); 
                                                            } else {
                                                                echo $board['nickname'];
                                                            }
                                                            if ($board['hasaccount']) { echo "<img src=\"assets/gonic.gif\" width=14px height=14px>";}
                                                            else { echo "<sub>(".$board['ipid'].")</sub>"; } 
                                                        }
                                                        ?>
                                                        <div><i class="far fa-clock"></i> <?=$board['date2']?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                }
                                
                            }

                        ?>

                <!-- 페이지 버튼 영역 -->

                <div class="d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination" onclick="loadMd.show();">
                            <li class="page-item">
                            <a class="page-link" href="?page=1<?php
                            if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                            if ($stype!='') { echo "&stype=".$stype; }
                            if ($recom==1) { echo "&recom=1"; }
                            if ($month!='') { echo "&month=".$month; }
                            if ($hidenick==1) { echo "&hidenick=1"; }
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
                                            if ($stype!='') { echo "&stype=".$stype; }
                                            if ($recom==1) { echo "&recom=1"; }
                                            if ($month!='') { echo "&month=".$month; }
                                            if ($hidenick==1) { echo "&hidenick=1"; }
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
                                            if ($stype!='') { echo "&stype=".$stype; }
                                            if ($recom==1) { echo "&recom=1"; }
                                            if ($month!='') { echo "&month=".$month; }
                                            if ($hidenick==1) { echo "&hidenick=1"; }
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
                                if ($stype!='') { echo "&stype=".$stype; }
                                if ($recom==1) { echo "&recom=1"; }
                                if ($month!='') { echo "&month=".$month; }
                                if ($hidenick==1) { echo "&hidenick=1"; } ?>" aria-label="Next">
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
                                <option <?php if ($stype==1) { echo "selected"; } ?> value="1">제목</option>
                                <option <?php if ($stype==2) { echo "selected"; } ?> value="2">제목+내용</option>
                                <option <?php if ($stype==3) { echo "selected"; } ?> value="3">닉네임</option>
                                <option <?php if ($stype==4) { echo "selected"; } ?> value="4">ID,IP</option>
                                <option <?php if ($stype==5) { echo "selected"; } ?> value="5">댓글-내용</option>
                                <option <?php if ($stype==6) { echo "selected"; } ?> value="6">댓글-닉네임</option>
                                <option <?php if ($stype==7) { echo "selected"; } ?> value="7">댓글-ID,IP</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <?php if ($recom == 1) { echo "<input hidden value=\"1\" name=\"recom\">"; }
                            if ($hidenick == 1) { echo "<input hidden value=\"1\" name=\"hidenick\">"; }
                            if (!($month == '' or $month == 0)) { echo "<input hidden value=\"".$month."\""." name=\"month\">"; }?>
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
        <p>저장 DB: 2021.01.01~2021.12.31<br><?php echo "총 ".number_format($totalposts)."개의 글 검색됨" ?></p>
        <p>트래픽 최소화를 위해 본문 콘텐츠는 링크되어 있으므로<br>PC 환경에서는 제대로 표시되지 않을 수 있습니다.</p></div>
        <p class="text-secondary text-center">by 1227</p>
    </body>
    <script src="js/scripts.js?rev=0.4"></script>
    <?php # if ($_GET['previd'] != '') { echo "<script>document.getElementById('targetpost').scrollIntoView({behavior: \"smooth\", block: \"center\"});</script>"; } ?>
</html>
