<?php
$input = trim($_POST['input']);
?>

<form class="d-flex justify-content-center" method="POST" action="./?stype=k5">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="분석할 키워드를 입력하세요 (하나만)" aria-label="id" name="input" aria-describedby="idinput"  value="<?=$input?>">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>

<?php
$valid = ($input != '');
require('../src/dbconn.php');

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

    function wordstrToFreqDict($wordstr, $key) {
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
            if (strpos($w, $key) !== false) continue;

            array_push($history, $w);
            $count = substr_count($ws, " ".$w." ");
            if ($count > 1) {
                $result[$w] = $count;
            }
        }

        arsort($result);

        return array_slice($result, 0, 100);
    }

    # $sqlq = "select title from euca_gall_posts_2021 WHERE ipid = '".$key."' AND comments > 0";

    $sqlq = "select title from euca_gall_posts_2021 WHERE title LIKE '%".$key."%'";
    $results = mysqli_query($conn, $sqlq);
    $data = ' ';

    while ($result = mysqli_fetch_array($results)) {
        $data .= $result['title']." ";
    }

    $replacestr = array('.','!','?','(',')','/','<','>','~',',','"',"'",'\\','`','[',']','+','{','}','=', 'ㅋ');

    foreach ($replacestr as $c) {
        $data = str_replace($c, " ", $data);
    }

    $dict = wordstrToFreqDict($data, $key);

    if (count($dict) == 0) {
        $valid = false;
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. 올바른 키워드인지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
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
    "<?=$key?>" 키워드 워드클라우드
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
    매 요청마다 실시간으로 집계하기 때문에 언급이 많은 키워드는 처리가 느려질 수 있습니다.</br>
    검색 키워드가 포함된 결과는 가독성을 위해 제거됩니다.</br>
    (예: '제니' 검색시 '제니는', '제니가', '제니보고싶다' 제외)</div>
    </div>
</div>