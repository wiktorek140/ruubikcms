<?php if (basename($_SERVER['REQUEST_URI']) == 'form.php') die ('Access denied'); ?>
<form method="post" action="login/login.php">
	<div class="loginText"><?php echo USERNAME;?></div> 						
	<div class="loginInput"><input type="text" name="username" id="username" /></div>
	<div class="loginText"><?php echo PASSWORD;?></div>
	<div class="loginInput"><input type="password" name="passwd" /></div>
	<div class="loginbtn"><input type="submit" value="<?php echo LOGIN;?>" class="inputbtn-off" /></div>
</form>