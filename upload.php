<?php
require_once __DIR__ . '/config.php'; require_login();
$pageTitle='Upload a study resource';$errors=[];$subjects=$pdo->query('SELECT id,name FROM subjects ORDER BY name')->fetchAll();$categories=$pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
if(is_post()){verify_csrf();$title=posted_string('title',180);$description=posted_string('description',5000);$subject=valid_id($_POST['subject_id']??0);$category=valid_id($_POST['category_id']??0);
if(mb_strlen($title)<4)$errors[]='Title must be at least 4 characters.';if(mb_strlen($description)<15)$errors[]='Please provide a useful description (at least 15 characters).';
if(!$subject||!$category)$errors[]='Choose a subject and category.';
$file=$_FILES['resource_file']??null;$allowed=['pdf'=>'application/pdf','doc'=>'application/msword','docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','ppt'=>'application/vnd.ms-powerpoint','pptx'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation','txt'=>'text/plain'];
if(!$file||$file['error']===UPLOAD_ERR_NO_FILE)$errors[]='Choose a study file to upload.';
elseif($file['error']!==UPLOAD_ERR_OK)$errors[]='The file could not be uploaded.';
elseif($file['size']>MAX_UPLOAD_BYTES)$errors[]='Maximum file size is 10 MB.';
else{$ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));$mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);if(!isset($allowed[$ext])||$mime!==$allowed[$ext])$errors[]='Allowed file types: PDF, DOC, DOCX, PPT, PPTX, TXT.';}
if(!$errors){$dir=__DIR__.'/uploads/resources';if(!is_dir($dir))mkdir($dir,0750,true);$stored=bin2hex(random_bytes(18)).'.'.$ext;$target=$dir.'/'.$stored;if(!move_uploaded_file($file['tmp_name'],$target))$errors[]='Could not save the uploaded file.';
else{$stmt=$pdo->prepare("INSERT INTO resources(title,description,file_path,file_type,subject_id,category_id,uploader_id,status) VALUES(?,?,?,?,?,?,?,'pending')");$stmt->execute([$title,$description,'uploads/resources/'.$stored,$ext,$subject,$category,(int)current_user()['id']]);set_flash('success','Resource submitted for admin review. It will appear in Browse after approval.');redirect('my-uploads.php');}}}
require __DIR__.'/includes/header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow">SHARE WHAT YOU KNOW</span><h1>Upload a study resource</h1><p>Help another student understand a topic. Your resource will be reviewed by an admin before it appears in the public library.</p></div></section>
<div class="page-content"><div class="container"><form class="form-card" method="post" enctype="multipart/form-data"><?= csrf_field() ?><?php if($errors): ?><div class="alert alert-error"><ul><?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="form-group"><label for="title">Resource title</label><input id="title" name="title" maxlength="180" value="<?= e($_POST['title']??'') ?>" required placeholder="e.g. Week 4 — Data Structures Summary"></div>
<div class="form-group"><label for="description">Description</label><textarea id="description" name="description" maxlength="5000" required placeholder="What does this resource cover? Who might find it useful?"><?= e($_POST['description']??'') ?></textarea></div>
<div class="grid-2"><div class="form-group"><label for="subject_id">Subject</label><select id="subject_id" name="subject_id" required><option value="">Choose a subject</option><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div><div class="form-group"><label for="category_id">Category</label><select id="category_id" name="category_id" required><option value="">Choose a category</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div></div>
<div class="form-group"><label for="resource_file">Study file</label><input id="resource_file" name="resource_file" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required><div class="form-help">PDF, DOC, DOCX, PPT, PPTX, or TXT. Maximum 10 MB. Do not upload copyrighted or private material without permission.</div></div>
<button class="btn btn-primary" type="submit">Submit for review →</button> <a class="btn btn-outline" href="my-uploads.php">Cancel</a></form></div></div>
<?php require __DIR__.'/includes/footer.php'; ?>
