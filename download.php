<?php
require_once __DIR__.'/config.php';require_login();$id=valid_id($_GET['id']??0);$stmt=$pdo->prepare("SELECT id,file_path,title FROM resources WHERE id=? AND status='approved'");$stmt->execute([$id]);$r=$stmt->fetch();if(!$r){http_response_code(404);exit('Resource not found.');}
$path=realpath(__DIR__.'/'.$r['file_path']);$base=realpath(__DIR__.'/uploads/resources');if(!$path||!$base||!str_starts_with($path,$base.DIRECTORY_SEPARATOR)||!is_file($path)){http_response_code(404);exit('File not found.');}
$pdo->prepare('INSERT INTO downloads(resource_id,user_id) VALUES(?,?)')->execute([$id,current_user()['id']]);
$mime=(new finfo(FILEINFO_MIME_TYPE))->file($path) ?: 'application/octet-stream';header('Content-Type: '.$mime);header('Content-Length: '.filesize($path));header('Content-Disposition: attachment; filename="'.preg_replace('/[^A-Za-z0-9._-]/','_',basename($path)).'"');readfile($path);exit;
