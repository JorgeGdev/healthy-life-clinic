<?php
include 'config.php';

$specialization = mysqli_real_escape_string($conn, $_GET['specialization']);


// TO BE COMPLETED - Add a query to search a provider based on the specilaization 

$result = mysqli_query($conn, $query);

$available_providers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $available_providers[] = $row;
}

echo json_encode($available_providers);

mysqli_close($conn);
?>
