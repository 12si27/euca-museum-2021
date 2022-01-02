<?php

# 전체 랭킹

require('../src/dbconn.php');

# ==== 작업 시작 ====

$sqlq = '';

# 순서값이 1~4가 아니면 1로 처리
if ($_GET['order'] == 1 or $_GET['order'] == 2 or $_GET['order'] == 3 or $_GET['order'] == 4 or $_GET['order'] == 5) {
    $order = $_GET['order'];
} else {
    $order = 1;
}

$limit = 100;

$rank_list = array();
$nick_list = array();
$ipid_list = array();
$hacc_list = array();
$val_list = array();


$sqlq = "SELECT nickname, ipid, hasaccount, ";


switch ($order) {
    case 1: # 글싼 랭킹
        $sqlq .= "post FROM `euca_gall_posts_rank_2021` ORDER BY post";
        break;
    
    case 2: # 댓글 쓴 랭킹
        $sqlq .= "cmt_posted FROM `euca_gall_cmts_rank_2021` ORDER BY cmt_posted";
        break;
        
    case 3: # 댓글 받은 랭킹
        $sqlq .= "cmt_recieved FROM `euca_gall_posts_rank_2021` ORDER BY cmt_recieved";
        break;

    case 4: # 개추랭킹
        $sqlq .= "upvote FROM `euca_gall_posts_rank_2021` ORDER BY upvote";
        break;
    
    case 5: # 비추랭킹
        $sqlq .= "downvote FROM `euca_gall_posts_rank_2021` ORDER BY downvote";
        break;
}

$sqlq .= " DESC LIMIT ".$limit;

$results = mysqli_query($conn, $sqlq);

while ($result = mysqli_fetch_array($results)) {

    array_push($nick_list, $result['nickname']);
    array_push($ipid_list, $result['ipid']);
    array_push($hacc_list, $result['hasaccount']);

    switch ($order) {
        case 1: # 글싼 랭킹
            array_push($val_list, $result['post']); break;
        
        case 2: # 댓글 쓴 랭킹
            array_push($val_list, $result['cmt_posted']); break;
            
        case 3: # 댓글 받은 랭킹
            array_push($val_list, $result['cmt_recieved']); break;
    
        case 4: # 개추랭킹
            array_push($val_list, $result['upvote']); break;
        
        case 5: # 비추랭킹
            array_push($val_list, $result['downvote']); break;
    }

}

?>

<form class='' method='GET'>
    <div class="d-flex justify-content-center">
        <input hidden name="stype" value="g0">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="order" id="btnradio1" autocomplete="off" value="1" checked onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio1">글싼 랭킹</label>

            <input type="radio" class="btn-check" name="order" id="btnradio2" autocomplete="off" value="2" <?=($_GET['order']==2?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio2">댓글 (쓴) 랭킹</label>

            <input type="radio" class="btn-check" name="order" id="btnradio3" autocomplete="off" value="3" <?=($_GET['order']==3?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio3">댓글 (받은) 랭킹</label>

            <input type="radio" class="btn-check" name="order" id="btnradio4" autocomplete="off" value="4" <?=($_GET['order']==4?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio4">개추랭킹</label>

            <input type="radio" class="btn-check" name="order" id="btnradio5" autocomplete="off" value="5" <?=($_GET['order']==5?'checked':'')?> onclick="loadMd.show(); this.form.submit();">
            <label class="btn btn-outline-primary" for="btnradio5">비추랭킹</label>
        </div>
    </div>
</form>
 
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="shadow p-3 mt-3 mb-3 bg-white backpanel">
            <div class="fs-4 mb-2 text-secondary text-center">
                애유갤 전체 
                <?php
                switch ($order) {
                    case 1: 
                        echo '글싼 랭킹'; break;
                    
                    case 2: 
                        echo '댓글 쓴 랭킹'; break;
                        
                    case 3:
                        echo '댓글 받은 랭킹'; break;
                
                    case 4:
                        echo '개추랭킹'; break;
                    
                    case 5:
                        echo '비추랭킹'; break;
                }

                ?> TOP <?=$limit?>
            </div>

            <div class="d-flex justify-content-center my-3">
                <table class="table table-hover table-sm mx-2">
                    <thead>
                        <tr>
                        <th scope="col">순위</th>
                        <th scope="col">유저명 (IPID)</th>
                        <th scope="col"><?php
                        switch ($order) {
                            case 1: 
                                echo '글싼수'; break;
                            case 2: 
                                echo '댓글쓴수'; break;
                            case 3:
                                echo '댓글받은수'; break;
                            case 4:
                                echo '개추수'; break;
                            case 5:
                                echo '비추수'; break;
                        }
                        ?></th>
                        </tr>
                    </thead>
                    <tbody onclick="loadMd.show()">
                    <?php
                    $j = 1;
                    $prevrank = -1;
                    $prevval = -1;

                    for ($i=0; $i<count($val_list); $i++) {

                        ?>
                        <tr onclick="location.href='./?input=<?=$ipid_list[$i]?>&stype=g1#statres';"<?php
                        if ($i < $limit * 0.01) {
                            echo ' class="table-danger"';
                        } elseif ($i < $limit * 0.1) {
                            echo ' class="table-warning"';
                        } elseif ($i < $limit * 0.33) {
                            echo ' class="table-light"';
                        }
                        ?>>
                            <th scope="row"><?php

                            if ($val_list[$i] == $prevval) {
                                echo $prevrank;
                            } else {
                                echo $j;
                                $prevrank = $j;
                            }

                            $prevval = $val_list[$i];
                            $j++;
                            
                            ?></th>
                            <td><?=$nick_list[$i]." (".($hacc_list[$i]?substr($ipid_list[$i],0,4).'****':$ipid_list[$i]).")"?></td>
                            <td><?=$val_list[$i]?></td>
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
  최대 <?=$limit?>개까지의 항목을 출력합니다</br>
  삭제 요청을 통해 삭제되더라도 테이블 최적화를 위해 실시간으로 값이 반영되지 않습니다.<span style="font-size:xx-small">(단, 일괄 삭제 요청을 통해 유저를 통째로 삭제한 경우 결산에서 제외됩니다)</span></br>
  고닉계정의 닉네임은 최적화를 위해 하나만 출력됩니다.</br>
  <b>다중고닉이나 통피 합산은 고려되지 않은 목록으로, 참고용으로만 보시기 바라며 해당 조건을 모두 반영한 순위는 '애유갤 2021 총결산'을 참고하시면 됩니다.</b></div>
  </div>
</div>

<script>
    var loadMd = new bootstrap.Modal(document.getElementById('loadMd'));
</script>