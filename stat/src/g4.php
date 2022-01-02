<?php

# 글별 랭킹

$input = $_GET['input'];
$valid = ($input != '');
require('../src/dbconn.php');

# ==== 작업 시작 ====

$sqlq = '';
$nicks = array();
$key = mysqli_real_escape_string($conn, trim($input));

# 순서값이 1~4가 아니면 1로 처리
if ($_GET['order'] == 1 or $_GET['order'] == 2 or $_GET['order'] == 3 or $_GET['order'] == 4) {
    $order = $_GET['order'];
} else {
    $order = 1;
}

$limit = 50;

$rank_list = array();
$id_list = array();
$title_list = array();
$uv_list = array();
$dv_list = array();
$cmt_list = array();
$view_list = array();


$sqlq = "SELECT postid, title, upvotes, downvotes, comments, views FROM `euca_gall_posts_2021`";


if ($valid) {
    $sqlq .= " WHERE ipid = '$key' ";
}


switch ($order) {
    case 1: # 개추순
        $sqlq .= "ORDER BY upvotes DESC";
        break;
    
    case 2: # 비추순
        $sqlq .= "ORDER BY downvotes DESC";
        break;
        
    case 3: # 댓글순
        $sqlq .= "ORDER BY comments DESC";
        break;

    case 4: # 조회수순
        $sqlq .= "ORDER BY views DESC";
        break;
}

$sqlq .= " LIMIT ".$limit;

$results = mysqli_query($conn, $sqlq);

while ($result = mysqli_fetch_array($results)) {

    array_push($id_list, $result['postid']);
    array_push($title_list, $result['title']);
    array_push($uv_list, $result['upvotes']);
    array_push($dv_list, $result['downvotes']);
    array_push($cmt_list, $result['comments']);
    array_push($view_list, $result['views']);

}


if ($valid) {

    if (count($id_list) == 0) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. 올바른 ID(IP)인지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        $valid = false;
    }

    $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$key."' GROUP BY nickname");
    while ($result = mysqli_fetch_array($nickresult)) {
        array_push($nicks, $result['nickname']);
    }
}

?>

<form class="" method="GET">
    <input hidden name="stype" value="g4">  
    <div class="d-flex justify-content-center">
        <div class="col input-group mb-3" style="max-width: 600px">
            <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
            <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
        </div>
    </div>
    <input hidden name="order" value="<?=($_GET['order']==''?'1':$_GET['order'])?>">
</form>


<form class='' method='GET'>
    <div class="d-flex justify-content-center">
        <input hidden name="stype" value="g4">
        <input hidden name="input" value="<?=$input?>">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="order" id="btnradio1" autocomplete="off" value="1" checked onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio1">개추순</label>

            <input type="radio" class="btn-check" name="order" id="btnradio2" autocomplete="off" value="2" <?=($_GET['order']==2?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio2">비추순</label>

            <input type="radio" class="btn-check" name="order" id="btnradio3" autocomplete="off" value="3" <?=($_GET['order']==3?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio3">댓글순</label>

            <input type="radio" class="btn-check" name="order" id="btnradio4" autocomplete="off" value="4" <?=($_GET['order']==4?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio4">조회수순</label>
        </div>
    </div>
</form>
 
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="shadow p-3 mt-3 mb-3 bg-white backpanel">
            <div class="fs-4 mb-2 text-secondary text-center">
                <?php

                

                if (count($nicks)>0) {
                    echo join(', ', $nicks)."님의 ";
                } else {
                    echo "전체 ";
                }
                

                switch ($order) {
                    case 1: # 개추순
                        echo '개추 글 랭킹';
                        break;
                    
                    case 2: # 비추순
                        echo '비추 글 랭킹';
                        # code...
                        break;
                        
                    case 3: # 댓글순
                        echo '최다 댓글 랭킹';
                        # code...
                        break;

                    case 4: # 조회수순
                        echo '최다 조회수 랭킹';
                        # code...
                        break;
                }

                ?> TOP <?=count($id_list)?>
            </div>

            <div class="d-flex justify-content-center my-3">
                <table class="table table-hover mx-2">
                    <thead>
                        <tr>
                        <th scope="col">순위</th>
                        <th scope="col">제목</th>
                        <th scope="col"><?php
                        switch ($order) {
                            case 1: # 개추순
                                echo '개추수'; break;                            
                            case 2: # 비추순
                                echo '비추수'; break;                                
                            case 3: # 댓글순
                                echo '댓글수'; break;
                            case 4: # 조회수순
                                echo '조회수';  break;
                        }
                        ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $j = 1;
                    $prevrank = -1;
                    $currval = -1;
                    $prevval = -1;

                    for ($i=0; $i<count($id_list); $i++) {

                        switch ($order) {
                            case 1: # 개추순
                                $currval = $uv_list[$i]; break;                            
                            case 2: # 비추순
                                $currval = $dv_list[$i]; break;                                
                            case 3: # 댓글순
                                $currval = $cmt_list[$i]; break;
                            case 4: # 조회수순
                                $currval = $view_list[$i]; break;
                        }

                        ?>
                        <tr onclick="window.open('../post?id=<?=$id_list[$i]?>');">
                            <th scope="row"><?php

                            if ($currval == $prevval) {
                                echo $prevrank;
                            } else {
                                echo $j;
                                $prevrank = $j;
                            }

                            $prevval = $currval;
                            $j++;
                            
                            ?></th>
                            <td><?=$title_list[$i]?></td>
                            <td><?=$currval?></td>
                        </tr>
                        <?php
                    }

                    ?> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="card">
  <div class="card-body">
  <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
  <div style="font-size:small; color: gray;">
  최대 50개까지의 항목을 출력합니다</br>
  글이 삭제 요청으로 인해 삭제 처리가 된 경우, 해당 글과 해당 글에 달린 댓글 전체가 집계에서 제외됩니다.</div>
  </div>
</div>

<script>
    var loadMd = new bootstrap.Modal(document.getElementById('loadMd'));
</script>