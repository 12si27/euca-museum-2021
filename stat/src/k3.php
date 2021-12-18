<?php

$input = $_GET['input'];
$valid = ($input != '');
$conn = mysqli_connect('','','','');

if ($valid) {

    $sqlq = '';

    $keyname = array();
    $keyname2 = array();
    $keyvalue = array();

    $key = mysqli_real_escape_string($conn, trim($input));

    $sqlq = "SELECT
                nickname,
                ipid,
                COUNT(*) AS count
            FROM
                euca_gall_posts_2021 RIGHT OUTER JOIN euca_gall_cmts_2021 ON postid = cmtpostid
            WHERE
                title LIKE '%".$key."%' OR c_postdata LIKE '%".$key."%'
            GROUP BY
                ipid,
                nickname
            ORDER BY
                count
            DESC
            LIMIT 15
            ";
    $results = mysqli_query($conn, $sqlq);

    while ($result = mysqli_fetch_array($results)) {
        array_push($keyname, $result['nickname']);
        array_push($keyname2, $result['ipid']);
        array_push($keyvalue, $result['count']);
    }
}

?>

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="분석할 키워드를 입력하세요 (하나만)" aria-label="id" name="input" aria-describedby="idinput"  value="<?=$_GET['input']?>">
        <input hidden name="stype" value="k3">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
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
            <th scope="col">언급 수 (글제목+댓글)</th>
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
  <div style="font-size:small; color: gray;">본 결과는 글 제목과 댓글 내용을 기준으로 집계합니다</br>
  하나의 글, 댓글에서 한번 이상 언급된 것은 한번 언급된 것으로 취급합니다</br>
  문맥과는 맞지 않는 언급도 포함되기 때문에 뜻이 많이 겹치는 단어의 경우 실제보다 더 많이 집계될 수 있습니다.</div>
  </div>
</div>

<?php

echo '';

?>
