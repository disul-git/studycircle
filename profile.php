<?php
require_once __DIR__.'/config.php'; require_login();
$pageTitle='Profile settings'; $errors=[]; $userId=(int)current_user()['id'];
if (is_post()) {
 verify_csrf();
 $name=posted_string('name',120); $email=strtolower(posted_string('email',190));
 $current=(string)($_POST['current_password']??''); $new=(string)($_POST['new_password']??'');
 if(mb_strlen($name)<2)$errors[]='Name must contain at least 2 characters.';
 if(!filter_var($email,FILTER_VALIDATE_EMAIL))$errors[]='Enter a valid email address.';
 $stmt=$pdo->prepare('SELECT password_hash, profile_picture FROM users WHERE id=?'); $stmt->execute([$userId]); $account=$stmt->fetch();
 if($new!==''&&(!password_verify($current,$account['password_hash'])||strlen($new)<8))$errors[]='To change your password, enter your current password and a new password of at least 8 characters.';
 $check=$pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?'); $check->execute([$email,$userId]); if($check->fetch())$errors[]='That email is already in use.';
 $picture=$account['profile_picture']??null;
 if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error']!==UPLOAD_ERR_NO_FILE){
  $f=$_FILES['profile_picture'];
  if($f['error']!==UPLOAD_ERR_OK)$errors[]='The picture upload failed. Please try again.';
  elseif($f['size']>3*1024*1024)$errors[]='Choose an image smaller than 3 MB.';
  else { $info=@getimagesize($f['tmp_name']); $mime=$info['mime']??''; $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;
   if(!$ext)$errors[]='Use a JPG, PNG, or WebP image.';
   else { $dir=__DIR__.'/uploads/avatars'; if(!is_dir($dir)&&!mkdir($dir,0755,true))$errors[]='Could not prepare the profile picture folder.';
    if(!$errors){$filename='user_'.$userId.'_'.bin2hex(random_bytes(6)).'.'.$ext; if(move_uploaded_file($f['tmp_name'],$dir.'/'.$filename))$picture='uploads/avatars/'.$filename; else $errors[]='Could not save the picture. Check folder permissions.';}
   }
  }
 }
 if(!$errors){
  if($new!=='')$pdo->prepare('UPDATE users SET name=?,email=?,password_hash=?,profile_picture=? WHERE id=?')->execute([$name,$email,password_hash($new,PASSWORD_DEFAULT),$picture,$userId]);
  else $pdo->prepare('UPDATE users SET name=?,email=?,profile_picture=? WHERE id=?')->execute([$name,$email,$picture,$userId]);
  $_SESSION['user']['name']=$name; $_SESSION['user']['email']=$email; $_SESSION['user']['profile_picture']=$picture;
  set_flash('success','Your profile is looking good — changes saved!'); redirect('profile.php');
 }
}
$pic=current_user()['profile_picture']??null;
require __DIR__.'/includes/header.php'; ?>
<section class="page-hero profile-hero"><div class="container"><span class="eyebrow">YOUR SPACE, YOUR STYLE ✨</span><h1>Make it feel like you.</h1><p>Update your profile, choose a picture, and keep your StudyCircle account fresh.</p></div></section>
<div class="page-content profile-page"><div class="container profile-settings-layout">
<form class="profile-settings-card" method="post" enctype="multipart/form-data"><?= csrf_field() ?>
<?php if($errors): ?><div class="alert alert-error"><ul><?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="profile-card-banner"><div class="profile-picture-wrap"><div class="profile-picture-preview" id="profilePicturePreview"><?php if($pic): ?><img src="<?= e($pic) ?>" alt="Your profile picture"><?php else: ?><span><?= e(mb_strtoupper(mb_substr(current_user()['name'],0,1))) ?></span><?php endif; ?></div><label class="picture-edit" for="profile_picture" title="Choose a profile picture">✎</label></div><div><span class="eyebrow">STUDYCIRCLE MEMBER</span><h2><?= e(current_user()['name']) ?></h2><p><?= e(current_user()['email']) ?></p><label class="btn btn-outline btn-small picture-button" for="profile_picture">📷 Change picture</label><input class="visually-hidden-file" id="profile_picture" name="profile_picture" type="file" accept="image/png,image/jpeg,image/webp"><div class="form-help">JPG, PNG or WebP · up to 3 MB</div></div></div>
<div class="profile-form-body"><div class="profile-section-title"><span>01</span><div><h3>Personal details</h3><p>Make sure your classmates can recognise you.</p></div></div>
<div class="form-group"><label for="name">Full name</label><input id="name" name="name" value="<?= e($_POST['name']??current_user()['name']) ?>" maxlength="120" required></div>
<div class="form-group"><label for="email">Email address</label><input id="email" type="email" name="email" value="<?= e($_POST['email']??current_user()['email']) ?>" required></div>
<div class="separator"></div><div class="profile-section-title"><span>02</span><div><h3>Security check-up</h3><p>Leave these fields blank if you don't want to change your password.</p></div></div>
<div class="form-group"><label for="current_password">Current password</label><input id="current_password" type="password" name="current_password" autocomplete="current-password" placeholder="Enter current password"></div>
<div class="form-group"><label for="new_password">New password</label><input id="new_password" type="password" name="new_password" minlength="8" autocomplete="new-password" placeholder="At least 8 characters"></div>
<div class="profile-submit-row"><span>🌱 Small updates, better study space.</span><button class="btn btn-primary" type="submit">Save my changes <span>→</span></button></div></div></form>
<aside class="profile-side-note"><div class="profile-note-icon">🌈</div><span class="eyebrow">A LITTLE REMINDER</span><h3>Your learning journey is yours.</h3><p>A friendly profile helps your study circle recognise you when you share notes, join groups, and help classmates.</p><div class="profile-note-pill">📚 Learn · Share · Grow</div></aside>
</div></div>
<script>
document.getElementById('profile_picture')?.addEventListener('change', function(){const file=this.files?.[0];if(!file)return;if(file.size>3*1024*1024){alert('Choose an image smaller than 3 MB.');this.value='';return;}const reader=new FileReader();reader.onload=()=>{document.getElementById('profilePicturePreview').innerHTML='<img src="'+reader.result+'" alt="Preview of your profile picture">';};reader.readAsDataURL(file);});
</script>
<?php require __DIR__.'/includes/footer.php'; ?>