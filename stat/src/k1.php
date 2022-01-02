<?php

$input = $_GET['input'];
$valid = ($input != '');
require('../src/dbconn.php');

if ($valid) {

    $keys = explode(",", $input);

    if (count($keys) > 5) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>키워드 수 추과</strong></br>입력한 키워드가 5개가 넘습니다! 5개 이하의 키워드를 검색해 주세요.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        $valid = false;
    }

    for($i=0; $i<count($keys); $i++) {
        if ($keys[$i]=='') {
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>잘못된 입력</strong></br>잘못된 키워드 입력이 있습니다. 확인 후 다시 시도하세요.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            $valid = false;
            break;
        }
    }
}

if ($valid) {

    $sqlq = '';

    $keyname = array();
    $keyvalue = array();

    for($i=0; $i<count($keys); $i++) {
        $key = mysqli_real_escape_string($conn, trim($keys[$i]));
        $sqlq .= "(SELECT '".$key."' as kw, count(*) as count FROM `euca_gall_posts_2021` WHERE title LIKE '%".$key."%')";

        if ($i<count($keys)-1) {
            $sqlq .= "\nUNION\n";
        }
    }

    $results = mysqli_query($conn, $sqlq." ORDER BY count DESC");

    while ($result = mysqli_fetch_array($results)) {
        array_push($keyname, $result['kw']);
        array_push($keyvalue, $result['count']);
    }

    if (count($keyname) == 1 and $keyvalue[0] == 0) {
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
        <input type="text" class="form-control" placeholder="분석할 키워드를 입력하세요 (쉼표로 구분)" aria-label="id" name="input" aria-describedby="idinput" value="<?=$_GET['input']?>">
        <input hidden name="stype" value="k1">
        <input class="btn btn-primary" type="submit" id="button-submit" data-bs-toggle="modal" data-bs-target="#loadMd" value="입력">
    </div>
</form>

<?php
if ($valid) {
    ?>
<script src="./chartjs/chart.min.js"></script>
<div>
    <canvas id="pie-chart" style="max-height: 500px;"></canvas>
    <script>
    new Chart(document.getElementById("pie-chart"), {
        type: 'pie',
        data: {
            labels: [<?="'".join("','", $keyname),"'"?>],
            datasets: [{
                label: "Population (millions)",
                backgroundColor: ['rgb(255, 99, 132)',
                                    'rgb(75, 192, 192)',
                                    'rgb(255, 205, 86)',
                                    'rgb(201, 203, 207)',
                                    'rgb(54, 162, 235)'],
                data: [<?="'".join("','", $keyvalue),"'"?>]
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: '<?=count($keys)?>개 키워드 비교'
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
            <th scope="col">키워드</th>
            <th scope="col">언급 수 (제목)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $i<count($keys); $i++) {
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
    본 결과는 제목을 기준으로 집계합니다.</br>
    문맥과는 맞지 않는 언급도 포함되기 때문에 뜻이 많이 겹치는 단어의 경우 실제보다 더 많이 집계될 수 있습니다.</div>
    </div>
</div>




<?php

echo '';

?>
