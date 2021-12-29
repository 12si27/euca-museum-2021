<?php

$input = $_GET['input'];
$cmt = $_GET['cmt'];
$valid = ($input != '');
$conn = mysqli_connect('', '', '', '');



if ($valid) {

    $sqlq = '';

    $keyname = array();
    $keyname2 = array();
    $keyvalue = array();


    $key = mysqli_real_escape_string($conn, trim($input));
    

    if ($cmt == '1') {

        $sqlq = "SELECT
                b.c_ipid as i,
                b.c_nickname as n,
                c1 + c2 as c
            FROM
                (
                SELECT
                    ipid, nickname, COUNT(*) AS c1
                FROM
                    euca_gall_posts_2021
                WHERE
                    title LIKE '%".$key."%'
                GROUP BY
                    ipid
            ) a
            RIGHT OUTER JOIN
                (
                SELECT
                    c_ipid, c_nickname, COUNT(*) AS c2
                FROM
                    euca_gall_cmts_2021
                WHERE
                    c_postdata LIKE '%".$key."%'
                GROUP BY
                    c_ipid
            ) b ON a.ipid = b.c_ipid
            ORDER BY c DESC
            LIMIT 15";
    } else {

        $sqlq = "SELECT
                    nickname as n,
                    ipid as i,
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
                LIMIT 15";
        
    }
    
    $results = mysqli_query($conn, $sqlq);

    while ($result = mysqli_fetch_array($results)) {
        array_push($keyname, $result['n']);
        array_push($keyname2, $result['i']);
        array_push($keyvalue, $result['c']);
    }

    if (count($keyname) == 0) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>결과 없음</strong></br>DB에 일치하는 결과가 없습니다. 올바른 키워드인지 확인하세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        $valid = false;
    }
}

?>

<form method="GET">
    <div class="d-flex justify-content-center">
        <div class="input-group" style="max-width: 600px">
            <input type="text" class="form-control" placeholder="분석할 키워드를 입력하세요 (하나만)" aria-label="id" name="input" aria-describedby="idinput"  value="<?=$_GET['input']?>">
            <input hidden name="stype" value="k3">
            <input class="btn btn-primary" type="submit" id="button-submit" onclick="if (document.getElementById('flexCheck').checked == true) {loadMd2.show();} else {loadMd.show();}" value="입력">
        </div>
    </div>
    <div class="d-flex justify-content-center my-3">
        <div class="form-check" style="max-width: 600px">
            <input class="form-check-input" type="checkbox" name="cmt" value="1" id="flexCheck" <?=($cmt=='1'?'checked':'')?> onclick="if (document.getElementById('flexCheck').checked == true) {alert('댓글 포함 검색시 상당 시간이 소요되므로 (30~40초) 입력 후 완료할 때까지 창을 닫지 말고 기다려주세요.')}">
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
            labels: [<?="'".join("','", $keyname),"'"?>],
            datasets: [{
                label: "<?=trim($input)?>",
                backgroundColor: ['#2196F3','#03A9F4','#00BCD4','#009688','#4CAF50','#8BC34A','#CDDC39','#FFEB3B','#FF9800','#FF5722','#795548','#F44336','#E91E63','#673AB7','#3F51B5','#9C27B0','#9E9E9E','#607D8B'],
                data: [<?=join(",", $keyvalue)?>]
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
        for ($i=0; $i<count($keyname); $i++) {
            ?>
            <tr>
                <th scope="row"><?=$i+1?></th>
                <td><?=$keyname[$i]."(".$keyname2[$i].")"?></td>
                <td><?=$keyvalue[$i]?></td>
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
            <span style="color: #DD6372; font-weight: bold;"><small>오래 걸리는 작업이므로 창을 닫지 말아주세요!</small><span></p>
            </div>
        </div>
        </div>
    </div>
</div>
<script>
    var loadMd2 = new bootstrap.Modal(document.getElementById('loadMd2'));
</script>
