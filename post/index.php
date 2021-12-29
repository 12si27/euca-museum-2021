<?php
    $starttime = microtime(true);
    $nolist = 1; #$_GET['nolist'];

    $conn= mysqli_connect('', '', '', '');

    $cookie_name = 'user';
    $pg = (int)$_GET['page'];
    $recom = $_GET['recom'];
    $month = $_GET['month'];
    $squery = $_GET['squery'];
    $asc = $_GET['asc'];
    $ascc = $_COOKIE['asc'];

    $hidenick = $_GET['hidenick'];
    if ($hidenick == 1) { require("../src/hasher.php"); }

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
        $sk .= 'BETWEEN date("2021-0'.$month.'-01") AND date("2021-'.($month+1).'-01")-1)';
    }

    # 념글
    if ($recom) { $sk .= " AND recommended=1"; }

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
                        $sk .= "(ipid LIKE '%".trim($findnick)."%') AND ";
                    } else {
                        $sk .= "(c_ipid LIKE '%".trim($findnick)."%') AND ";
                    }
                    
                    $sqr = trim(str_replace(substr($cmd[$i], strpos($cmd[$i], "IPID:")), "", $sqr));
                    $squery = trim(str_replace(substr($cmd[$i], strpos($cmd[$i], "IPID:")), "", $squery));
                    break;
                }
            }

            $sq2 = str_replace("%", "\\%", $sqr);

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
    

    $sql="SELECT postid, title, nickname, ipid, date_format(date,'%y.%m.%d %H:%i') as date,
    views, upvotes, gonicupvotes, downvotes, comments, recommended, mobile, hasaccount, postdata FROM euca_gall_posts_2021 WHERE postid=".$_GET['id'];
    $result=mysqli_query($conn,$sql);

    if (mysqli_num_rows($result) < 1){
        echo "<script>alert('존재하지 않는 게시글입니다.');history.back();</script>";
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
        <meta name="robots" content="noindex"> <!-- 크롤링 방지용 메타태그 -->
        <title><?php echo $postdata['title']." - "."애유갤 박물관 2021"; ?></title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="../css/styles.css?rev=0.1" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <meta name="description" content="애유갤 보존 프로젝트" />
        
        <style> img {max-width: 100%;}</style>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="../js/scripts.js"></script>
        <script type="application/javascript">setUserAgent(window, "Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Mobile Safari/537.36");</script>

    </head>
    <body>

        
        <!-- Responsive navbar-->
        <?php require('../src/nav.php') ?>

        <!-- Page content-->
        <div class="container">

        <?php if ($_GET['delfail']) {
            ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <strong>삭제 요청 거부됨</strong></br>아직 원본 글이 삭제되지 않았습니다. 삭제 후 다시 시도해 주세요.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
        } ?>
            
            <div class="row">
                <!-- 글 영역 -->
                <div class="col-lg-6">

                    <div class="shadow-lg p-3 mt-3 mb-3 bg-white backpanel">
                        <div class="form-group shadow rounded p-3 mb-2 bg-light">
                            <h3><?php if ($postdata['recommended']) {echo "<i class=\"fas fa-medal\"></i> "; }
                            if ($squery != '' and ($_GET['stype'] == 1 or $_GET['stype'] == 2))
                            { echo str_replace($squery,"<mark>".$squery."</mark>", $postdata['title']); }
                            else { echo $postdata['title']; } ?></h3>
                            <div class="d-flex bd-highlight">
                                <div class="flex-fill bd-highlight">
                                    <i class="far fa-clock"></i> <?=$postdata['date']?>
                                    <span class="badge rounded-pill bg-secondary">댓글 <?=$postdata['comments']?></span>
                                    <span class="badge rounded-pill bg-secondary">개추 <?=$postdata['upvotes']?></span>
                                    <span class="badge rounded-pill bg-secondary">비추 <?=$postdata['downvotes']?></span>
                                </div>
                                <div class="flex bd-highlight justify-content-end">
                                    <?php
                                        if ($hidenick) {
                                            $annick = explode(' #', eucahash($postdata['ipid']));
                                            ?><div><?=$annick[0]?>
                                            <span class="badge rounded-pill bg-secondary"><?="#".$annick[1]?></span></div><?php
                                        } else {
                                            ?><div><?=$postdata['nickname']?>
                                            <span class="badge rounded-pill bg-secondary"><?=$postdata['ipid']?></span></div><?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="shadow rounded">

                            <div class="flex-fill bd-highlight px-3 py-4" style="word-break: break-all;">
                                <?php 
                                if ($squery != '' and $_GET['stype'] == 2)
                                { echo str_replace($squery,"<mark>".$squery."</mark>",$postdata['postdata']); }
                                else { echo $postdata['postdata']; }
                                ?>
                            </div>

                            <div class="d-flex justify-content-center pt-2">
                                <button type="button" class="btn btn-primary btn-lg">개추 <b><?=$postdata['upvotes']?></b> (<?=$postdata['gonicupvotes']?>)</button>
                                <button type="button" class="btn btn-secondary btn-lg ms-2">비추 <b><?=$postdata['downvotes']?></b></button>
                            </div>
                            <div class="d-flex justify-content-center pt-2 pb-4">
                                <a type="button" class="btn btn-outline-primary btn-sm" href="https://gall.dcinside.com/euca/<?=$postdata['postid']?>" target="_blank"> 원본 글 링크</a>
                                <a type="button" class="btn btn-outline-danger btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#delcfMd" target="_blank"> 삭제 요청</a>
                            </div>

                        </div>
                    </div>

                    <div class="shadow rounded-pill my-3 postzoom" onclick=<?php
                    echo "\"location.href='../?previd=".$_GET['id']."&page=".$pg;
                    if ($_GET['cmtid']!='') { echo "&pcmtid=".$_GET['cmtid']; }
                    if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                    if ($stype!='') { echo "&stype=".$stype; }
                    if ($recom==1) { echo "&recom=1"; }
                    if ($month!='') { echo "&month=".$month; }
                    if ($hidenick==1) { echo "&hidenick=1"; }
                    echo "#".$_GET['id'];

                    ?>'" style="position: relative; z-index: 1;">
                        <div class="p-3">
                            <div class="d-flex bd-highlight align-items-center">
                                <div class="flex-fill bd-highlight text-center">
                                    <div class="fs-5"><i class="fas fa-list"></i>&nbsp;목록으로</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 댓글 영역 -->
                <div class="col-lg-6">
                    <div class="shadow-lg p-3 mt-3 mb-1 bg-white backpanel">
                        <div class="form-group shadow rounded p-3 mb-2">
                            <span style="font-size: x-large;">댓글  </span><span>총 <?php echo $commentcnt ?>개</span>
                        </div>

                        <div class="flex">
                        <?php

                            function print_cmt($data, $pd, $isreply, $hidenick) {
                                echo "<div class=\"shadow rounded my-3 ";
                                if ($isreply) {echo "ms-3";}
                                echo "\" ";
                                if ($data['cmtid'] == $_GET['cmtid']) { # 선택한 댓글 강조표시
                                    echo "style='background-color:#FAEFCA; word-break: break-all;' id='targetpost'";
                                } elseif ($pd == $data['c_ipid']) { echo "style='background-color:#E2ECF7; word-break: break-all;'"; }
                                echo ">
                                    <div class=\"p-3\">
                                        <div class=\"d-flex bd-highlight\">
                                            <div class=\"flex-fill bd-highlight\">
                                                <div>";
                                
                                if ($hidenick) { $annick = explode(' #', eucahash($data['c_ipid'])); }

                                if ($hidenick) {
                                    echo $annick[0];
                                } else {
                                    echo $data['c_nickname'];
                                }
                                
                                echo " <span class=\"badge rounded-pill bg-secondary\">";

                                if ($hidenick) {
                                    echo "#".$annick[1];
                                } else {
                                    echo $data['c_ipid'];
                                }

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
                                print_cmt($comm[$i], $postdata['ipid'], 0, $hidenick);
                                
                                for ($j=0; $j<count($rply); $j++) {

                                    if ($rply[$j]['refcid'] == $comm[$i]['cmtid']) {
                                        print_cmt($rply[$j], $postdata['ipid'], 1, $hidenick);
                                        //array_splice($rply, $j, 1);
                                    }

                                    // array_splice($rply, $j, 1); // 해당 배열 제거

                                    // 댓글마다 답글 존재 여부 체크
                                }

                            }

                        ?>
                        </div>
                    </div>

                    <?php if (count($comm) > 5) { // 댓글이 5개를 넘을때만 버튼 출력 ?>

                        <div class="shadow rounded-pill my-3 postzoom" onclick=<?php
                        echo "\"location.href='../?previd=".$_GET['id']."&page=".$pg;
                        if ($_GET['cmtid']!='') { echo "&pcmtid=".$_GET['cmtid']; }
                        if ($_GET['squery']!='') { echo "&squery=".$_GET['squery']; }
                        if ($stype!='') { echo "&stype=".$stype; }
                        if ($recom==1) { echo "&recom=1"; }
                        if ($month!='') { echo "&month=".$month; }
                        if ($hidenick==1) { echo "&hidenick=1"; }
                        echo "#".$_GET['id'];

                        ?>'" style="position: relative; z-index: 1;">
                            <div class="p-3">
                                <div class="d-flex bd-highlight align-items-center">
                                    <div class="flex-fill bd-highlight text-center">
                                        <div class="fs-5"><i class="fas fa-list"></i>&nbsp;목록으로</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    
                    
                </div>
            </div>


            
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
                        <input type="hidden" id="postid" name="postid" value="<?=$postdata['postid']?>">
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

            
            <?php
            $postloadtime = microtime(true) - $starttime;?>
            
        <div class="text-end text-secondary text-center mt-3" style="font-size: x-small;">
            <p>저장 DB: 2021.01.01~2021.10.31*</br>
            
            <?php
                echo "글 로드 시간:".round($postloadtime, 4)."</p>";
                echo "<p>* 트래픽 최소화를 위해 본문 이미지는 링크되어 있으므로<br>PC 환경에서는 사진이 표시되지 않을 수 있습니다.</p></div></body></html>";
            ?>
            <p class="text-secondary text-center">by 1227</p>

        </div>
    </body>
    <script src="../js/scripts.js?rev=0.4"></script>
    <?php # if ($_GET['cmtid'] != '') { echo "<script>document.getElementById('targetpost').scrollIntoView();</script>"; } ?>
</html>
