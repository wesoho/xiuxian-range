<?php
// 修复：剥离 NTFS 流
$name = preg_replace('/:.*$/', '', $_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $name);