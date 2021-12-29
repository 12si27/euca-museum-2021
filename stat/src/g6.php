<?php
$input = trim($_POST['input']);
?>

<form class="d-flex justify-content-center" method="POST" action="./?stype=g6">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="ID(IP)를 입력하세요" aria-label="id" name="input" aria-describedby="idinput"  value="<?=$input?>">
        <input class="btn btn-primary" type="submit" id="button-submit" value="입력" onclick="loadMd2.show();">
    </div>
</form>

<?php
$valid = ($input != '');
$conn = mysqli_connect('', '', '', '');

$key = mysqli_real_escape_string($conn, $input);

if (!$valid) {
} elseif (mb_strlen($input, 'UTF-8') < 2) {
    $valid = false;
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>잘못된 입력</strong></br>2자 이상의 키워드를 입력해 주세요.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
}

if ($valid) {

    function wordstrToFreqDict($wordstr) {
        $ws = $wordstr;

        $result = array();
        $history = array('너무', '진짜', '이거', '근데', '님들', '오늘', '오늘은',
                        '솔직히', '존나', '내가', '아니', '애니', '요즘',
                        '그냥', '사실', '이제', '뭔가', 'ㅋㅋ', '자꾸',
                        '지금', '이게', '여기', '있음', '있다', '없음',
                        '없다', '뭔데', '왤케', '완전', '하는', 'ㄹㅇ',
                        '나도', 'vs', '시발', '씨발', '이런', '이유',
                        '이렇게', '은근', '보고', '보면', '많이', '가장',
                        '있는', '아님', '누가', '요새', '하고', '에서',
                        '같은', '같음'. '하는거', '보니', '하나', '하면',
                        '까지', '해서');

        foreach (array_filter(explode(" ", $wordstr)) as $w) {
            if (mb_strlen($w, 'UTF-8') < 2) continue;
            if (in_array($w, $history)) continue;
            # if (strpos($w, $key) !== false) continue;

            array_push($history, $w);
            $count = substr_count($ws, " ".$w." ");
            if ($count > 1) {
                $result[$w] = $count;
            }
        }

        arsort($result);

        return array_slice($result, 0, 100);
    }

    $sqlq = "select title from euca_gall_posts_2021 WHERE ipid = '".$key."' AND comments > 0";

    # $sqlq = "select title from euca_gall_posts_2021 WHERE title LIKE '%".$key."%'";
    
    $results = mysqli_query($conn, $sqlq);
    $nicks = array();
    $data = ' ';

    while ($result = mysqli_fetch_array($results)) {
        $data .= $result['title']." ";
    }

    $replacestr = array('.','!','?','(',')','/','<','>','~',',','"',"'",'\\','`','[',']','+','{','}','=', 'ㅋ');

    foreach ($replacestr as $c) {
        $data = str_replace($c, " ", $data);
    }

    $dict = wordstrToFreqDict($data);

    if (count($dict) == 0) {
        $valid = false;
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. 올바른 ID(IP)인지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    } else {
        $nickresult = mysqli_query($conn, "SELECT nickname FROM `euca_gall_posts_2021` WHERE ipid = '".$key."' GROUP BY nickname");
        while ($result = mysqli_fetch_array($nickresult)) {
            array_push($nicks, $result['nickname']);
        }
    }

}


if ($valid) {

    $res_name = array();
    $res_val = array();

    foreach ($dict as $kw => $freq) {
        array_push($res_name, $kw);
        array_push($res_val, $freq);
    }

?>

<div class="fs-4 mt-2 text-secondary text-center">
    <?=join(',',$nicks)?>님의 워드클라우드
</div>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-tag-cloud.min.js"></script>

<div class="chart-area" style="height: 70vh;">
    <div id="wc-1" style="width:100%; height:100%;"></div>
</div>

<script> 
anychart.onDocumentReady(function() {
    var data = [<?php for ($i=0; $i<count($res_name); $i++) { echo '{"x": "'.$res_name[$i].'", "value": '.$res_val[$i].'}, ';} ?>];
    var chart1 = anychart.tagCloud(data);

    chart1.angles([0]);
    chart1.container("wc-1");
    chart1.draw();
});
</script>

<?php
}

?>
<div class="card">
    <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
    <div style="font-size:small; color: gray;">
    본 결과는 글 제목 내용을 기준으로 집계합니다.</br>
    매 요청마다 실시간으로 집계하기 때문에 글을 많이 썼던 경우 처리가 느려질 수 있습니다.</br>
    키워드에 마우스를 올리면 언급 빈도(frequency)를 확인할 수 있습니다.</br></div>
    </div>
</div>


<!-- 로딩 Modal 2 -->
<div class="modal fade" id="loadMd2" name="loadMd2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-body text-center">
            <div class="loader">
                <img src="../assets/loading.gif" height="100px" width="100px">
            </div>
            <div clas="loader-txt">
            <p>데이터 조회중입니다<br>
            <span style="color: #DD6372; font-weight: bold;"><small>글 양이 많으면 오래 걸릴 수 있으므로</br>창을 닫지 말아주세요!</small><span></p>
            </div>
        </div>
        </div>
    </div>
</div>
<script>
    var loadMd2 = new bootstrap.Modal(document.getElementById('loadMd2'));
</script>