<?php

$input = $_GET['input'];
$cmt = $_GET['cmt'];
$valid = ($input != '');
require('../src/dbconn.php');

if ($valid) {

    $sqlq = '';

    $resultname = array();
    $resultname2 = array();
    $resulthacc = array();
    $resultvalue = array();

    $key = mysqli_real_escape_string($conn, trim($input));
    

    # 글 작성 상위 100 목록을 불러오기

    $sqlq = "SELECT
                nickname as n,
                ipid as i,
                hasaccount as h,
                COUNT(*) AS c
            FROM
                euca_gall_posts_2021
            WHERE
                title LIKE '%".$key."%'
            GROUP BY
                ipid,
                nickname
            ORDER BY
                c
            DESC
            LIMIT 20";
    
    $results = mysqli_query($conn, $sqlq);

    while ($result = mysqli_fetch_array($results)) {
        array_push($resultname, $result['n']);
        array_push($resultname2, $result['i']);
        array_push($resulthacc, $result['h']);
        array_push($resultvalue, $result['c']);
    }

    if ($cmt == '1') {

        # 댓글 작성 상위 100 목록을 불러오기

        $sqlq = "SELECT
                    c_nickname as n,
                    c_ipid as i,
                    c_hasaccount as h,
                    COUNT(*) AS c
                FROM
                    euca_gall_cmts_2021
                WHERE
                    c_postdata LIKE '%".$key."%'
                GROUP BY
                    c_ipid,
                    c_nickname
                ORDER BY
                    c
                DESC
                LIMIT 20";
        
        $results = mysqli_query($conn, $sqlq);

        while ($result = mysqli_fetch_array($results)) {

            if (array_search($result['i'], $resultname2) === false) {
                array_push($resultname, $result['n']);
                array_push($resultname2, $result['i']);
                array_push($resulthacc, $result['h']);
                array_push($resultvalue, $result['c']);
            } else {
                $resultvalue[array_search($result['i'], $resultname2)] += $result['c'];
            }
        }

        array_multisort($resultvalue, SORT_DESC, $resultname, $resultname2, $resulthacc);

    }

    if (count($resultname) == 0) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. 올바른 키워드인지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        $valid = false;
    }

    array_slice($resultname, 0, 15);
    array_slice($resultname2, 0, 15);
    array_slice($resulthacc, 0, 15);
    array_slice($resultvalue, 0, 15);

}

?>

<form method="GET">
    <div class="d-flex justify-content-center">
        <div class="input-group" style="max-width: 600px">
            <input type="text" class="form-control" placeholder="분석할 키워드를 입력하세요 (하나만)" aria-label="id" name="input" aria-describedby="idinput"  value="<?=$_GET['input']?>">
            <input hidden name="stype" value="k3">
            <input class="btn btn-primary" type="submit" id="button-submit" onclick="loadMd.show();" value="입력">
        </div>
    </div>
    <div class="d-flex justify-content-center my-3">
        <div class="form-check" style="max-width: 600px">
            <input class="form-check-input" type="checkbox" name="cmt" value="1" id="flexCheck" <?=($cmt=='1'?'checked':'')?>>
            <label class="form-check-label" for="flexCheck">
                댓글도 포함하여 검색하기
            </label>
        </div>
    </div>
</form>

<?php
if ($valid) {
    ?>
<script src="./chartjs/chart.min.js"></script>
<div>
    <canvas id="chart" style="max-height: 500px;"></canvas>
    <script>
    new Chart(document.getElementById("chart"), {
        type: 'pie',
        data: {
            labels: [<?="'".join("','", $resultname),"'"?>],
            datasets: [{
                label: "<?=trim($input)?>",
                backgroundColor: ['#2196F3','#03A9F4','#00BCD4','#009688','#4CAF50','#8BC34A','#CDDC39','#FFEB3B','#FF9800','#FF5722','#795548','#F44336','#E91E63','#673AB7','#3F51B5','#9C27B0','#9E9E9E','#607D8B'],
                data: [<?=join(",", $resultvalue)?>]
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: '"<?=trim($input)?>" 언급 갤러 순위'
                }
            }
        }
    });
    </script>
</div>

<div class="d-flex justify-content-center my-3">
    <table class="table" style="max-width: 600px;">
        <thead>
            <tr>
            <th scope="col">순위</th>
            <th scope="col">닉네임(IPID)</th>
            <th scope="col">언급 수 (글제목<?=($cmt=='1'?'+댓글':'')?>)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $i<count($resultname); $i++) {
            ?>
            <tr>
                <th scope="row"><?=$i+1?></th>
                <td><?=$resultname[$i]."(".($resulthacc[$i]?substr($resultname2[$i],0,4).'****':$resultname2[$i]).")"?></td>
                <td><?=$resultvalue[$i]?></td>
            </tr>
            <?php
        }

        ?> 
        </tbody>
    </table>
</div><?php
}
?>

<div class="card">
  <div class="card-body">
  <h6 class="card-subtitle mb-2 text-muted">참고사항</h6>
  <div style="font-size:small; color: gray;">
  본 결과는 글 제목<?=($cmt=='1'?'과 댓글':'')?> 내용을 기준으로 집계합니다</br>
  하나의 글, 댓글에서 한번 이상 언급된 것은 한번 언급된 것으로 취급합니다</br>
  문맥과는 맞지 않는 언급도 포함되기 때문에 뜻이 많이 겹치는 단어의 경우 실제보다 더 많이 집계될 수 있습니다.</div>
  </div>
</div>

