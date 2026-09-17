<?php
require 'db_koneksi.php';

$title = $_POST['title'];
$content = $_POST['content'];

$query = "INSERT INTO notes (title, content) VALUES ('$title', '$content')";
mysqli_query($koneksi, $query);

header("Location: index.php?status=tambah");
exit;
