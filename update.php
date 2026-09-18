<?php
require 'db_koneksi.php';

$id = $_POST['id'];
$title = $_POST['title'];
$content = $_POST['content'];

$query = "UPDATE notes SET title = '$title', content = '$content' WHERE id = $id";
mysqli_query($koneksi, $query);

header("Location: index.php?status=update");
exit;
