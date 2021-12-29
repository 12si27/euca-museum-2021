<script> <?php

$id = $_POST['id'];
$code = $_POST['code'];

$conn = mysqli_connect('', '', '', '');

$id = mysqli_escape_string($conn, $id);
$code = strtoupper(mysqli_escape_string($conn, $code));

$sql="SELECT COUNT(*) AS c FROM `euca_museum_auth` WHERE id = '$id' AND code = '$code'";

$results = mysqli_query($conn,$sql);

$valid = false;

while ($result = mysqli_fetch_array($results)) {
    if ($result['c'] == 1) {
        $valid = true;
        break;
    }
}


if ($valid) {
    echo 'window.location.href="../delcnfm/?id='.$id.'&code='.$code.'";';
} else {
    echo "window.location = document.referrer + '&fail=1';";
}




?> </script>