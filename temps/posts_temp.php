<div class="chat-page">
	<div>
	<?php if(isset($_SESSION["loginStatus"]) && $_SESSION["loginStatus"] !== "passed") {
		echo "Login status:" . $_SESSION["loginStatus"];
		unset($_SESSION["loginStatus"]);
	} 
	if (isset($_SESSION["registerStatus"])) {
		echo "Register status: " . $_SESSION["registerStatus"];
		unset($_SESSION["registerStatus"]);
		} ?>
	</div>

<div class="reply-form">
	<form action="" method="POST" id="post-input">
		<div class="additional-post-info">
			<?php if ($_SESSION["loginStatus"] !== "passed"): ?>
				<input value="<?php if(!empty($_COOKIE['rememberedName'])) {
									echo $_COOKIE['rememberedName'];
								} else{
									echo '';
								}; ?>" type="text" name="username" placeholder="Name" class="username-input" />
			<?php else: ?>
				<input value="<?php echo $_SESSION['username']; ?>" type="text" name="username" class="username-input logged-username" readonly>
			<?php endif; ?>
			<input type="text" name="title" class="title-input" placeholder="Subject">
			<input type="email" name="email" class = "email-input" placeholder="Email" />
			<input accesskey="s" type="submit" value="Send message" class="post-button post-submit">
			<?php if ($_SESSION["loginStatus"] !== "passed"): ?>
				<button type="button" class="post-button user-panel login-button">Login</button>
			<?php else: ?>
				<a href="/chatbox/includes/login.php?logout=1"><button type="button" class="post-button user-panel">Logout</button></a>
				<?php if (count($allPMs) > $lastPM): ?>
					<a href="/chatbox/pm.php"><button type="button" class="post-button user-panel">PM<?php echo " + " . (count($allPMs) - $lastPM) . " new"; ?></button></a>
				<?php else: ?>
					<a href="/chatbox/pm.php"><button type="button" class="post-button user-panel">PM</button></a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</form>

	<?php if ($_SESSION["loginStatus"] !== "passed"): ?>
		<div class="login-form">
			<form action="/chatbox/includes/login.php" method="POST" id="login-area">
				Username: <input type="text" name="login-username"><br>
				Password: <input type="password" name="login-password"><br>
				Keep logged: <input type="checkbox" name="keep-logged" value="true"><br>
				<span class="register-anchor">register</span>
				<input type="submit" value="Login">
			</form>
			<form action="/chatbox/includes/register.php" method="POST" id="register-area">
				Username: <input type="text" name="username"><br>
				Password: <input type="password" name="password"><br>
				Confirm password: <input type="password" name="confirm-password"><br>
				<div id="capcha"></div><br>
				<span class="register-anchor">login</span>
				<input type="submit" value="Register">
			</form>
		</div>
	<?php endif; ?>
	<textarea id="message" form="post-input" class="content-input" name="content" maxlength="1000" placeholder="Message" required autofocus></textarea>
</div>

<div class="additional-menu-button">Additional menu</div>
<div class="additional-menu">
	<span class="main-smiles">
		<?php require "includes/parser/smiles.php"; ?>
		<?php foreach ($mainSmiles as $smile): ?>
			<img alt="<?php echo $smile['code'];?>" src="<?php echo $smile['src'];?>" title="<?php echo $smile['title'];?>"/>
		<?php endforeach; ?>
	</span>
	<span class="BB-codes">
		<img src="/chatbox/temps/includes/img.png" title="image" alt="img" id="img-button">
		<img src="/chatbox/temps/includes/url.png" title="url" alt="url" id="url-button">
		<img src="/chatbox/temps/includes/quote.png" title="quote" alt="> " id="quote-button">
		<img src="/chatbox/temps/includes/spoiler.png" title="spoiler" alt="spoiler" id="spoiler-button">
	</span>
</div>

<div class="alerts"></div>
<div class="all-posts">
<?php foreach (array_reverse($posts, true) as $post): ?>
	<?php $post = $this->processIndex($post); ?>
	<div class="post">
		<div class="post-info">
			<span class="post-username<?php echo ($this->guestStatus ? ' guest-username' : '') ?><?php echo ($post['isAuthorised'])? ' authorised': '' ?>"><?php echo (!empty($post['email']) ? "<a href=mailto:{$post['email']}>".$post['username']."</a>" : $post['username']); ?></span>
			<span class="post-title"><?php echo $post['title']; ?></span>
			<span class="post-right-align">
				<?php if ($this->isAdmin): ?>
					<span class="post-delete"><a href="/chatbox/index.php?deletePost=<?php echo $post['id']; ?>">Delete</a></span>
					<span class="post-ip"><?php echo $post["ip"]; ?></span>
				<?php endif; ?>
				<span class="post-time"><?php echo !empty($post["modTime"])? $post["modTime"]->format("H:i:s d-m-Y"): ""; ?></span>
				<span class="post-id"><?php echo $post['id']; ?></span>
				<div class="hide-drop-down-menu">
					<img class="hide-post" src="/chatbox/temps/includes/hide_post_icon.png" title="hide post"><br>
					<div class="additional-drop-down-menu"><span class="hide-nickname">hide nickname</span></div>
				</div>
			</span>
		</div>
		<div class="post-content"><?php echo $post['content']; ?></div>
	</div>
<?php endforeach; ?>
</div>
</div>