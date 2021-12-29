<?php

$input = $_GET['input'];
$valid = ($input != '');
$conn = mysqli_connect('', '', '', '');

if ($valid) {

    $sqlq = '';

    $keyname = array();
    $keyvalue = array();

    $key = mysqli_real_escape_string($conn, trim($input));

    $sqlq = "SELECT DATE_FORMAT(date,'%Y-%m') AS m, COUNT(*) AS count FROM euca_gall_posts_2021 WHERE title LIKE '%".$key."%' OR postdata LIKE '%".$key."%' GROUP BY m";
    $results = mysqli_query($conn, $sqlq);

    while ($result = mysqli_fetch_array($results)) {

        array_push($keyname, $result['m']);
        array_push($keyvalue, $result['count']);
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

<form class="d-flex justify-content-center" method="GET">
    <div class="input-group mb-3" style="max-width: 600px">
        <input type="text" class="form-control" placeholder="분석할 키워드를 입력하세요 (하나만)" aria-label="id" name="input" aria-describedby="idinput"  value="<?=$_GET['input']?>">
        <input hidden name="stype" value="k2">
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
        type: 'line',
        data: {
            labels: [<?="'".join("','", $keyname),"'"?>],
            datasets: [{
                label: "<?=trim($input)?>",
                backgroundColor: ['rgb(13, 110, 253)'],
                data: [<?=join(",", $keyvalue)?>]
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: '월별 "<?=trim($input)?>" 언급 횟수'
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
            <th scope="col">#</th>
            <th scope="col">년-월</th>
            <th scope="col">언급 수 (제목+내용)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $i<count($keyname); $i++) {
            ?>
            <tr>
                <th scope="row"><?=$i+1?></th>
                <td><?=$keyname[$i]?></td>
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
    본 결과는 제목과 글 내용을 기준으로 집계합니다.</br>
    글에서 해당 키워드가 여러번 언급되더라도 글당 1회로 집계합니다.</br>
    문맥과는 맞지 않는 언급도 포함되기 때문에 뜻이 많이 겹치는 단어의 경우 실제보다 더 많이 집계될 수 있습니다.<br>
    공앱으로 첨부한 꼬릿말에도 키워드가 있을 경우 집계에 포함됩니다.</div>
    </div>
</div>

<?php

echo '';

?>
