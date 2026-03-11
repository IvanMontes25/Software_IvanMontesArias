<?php
$servername = "localhost";
$uname = "root";
$pass = "";
$db = "gymbodytraining";

$db = mysqli_connect($servername, $uname, $pass, $db);

if (!$db) {
    die("Connection Failed");
}

// ===============================
// INGRESOS REALES DEL SISTEMA
// ===============================
$sql = "
  SELECT COALESCE(SUM(amount), 0) AS total_ingresos
  FROM payments
  WHERE status = 'pagado'
";

$result = mysqli_query($db, $sql);

if (!$result) {
    die(mysqli_error($db));
}

$row = mysqli_fetch_assoc($result);

// Salida (tal como lo usas en el dashboard)
echo number_format((float)$row['total_ingresos'], 2, '.', ',');
?>
