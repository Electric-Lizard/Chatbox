<div class="chat-page">
<div class="reply-form">
	<form action="" method="POST">
		<div class="additional-post-info">
		<input value="<?php if(!empty($_COOKIE['username'])) {
								echo $_COOKIE['username'];
							} else{
								echo '';
							}; ?>" type="text" name="username" placeholder="Pozornik" class="username-input" /><input type="text" name="title" class="title-input" placeholder="Name of a instance of the pozor"><input type="email" name="email" class = "email-input" placeholder="pzr@mail.com" /><input accesskey="s" type="image" src="/chatbox/temps/includes/pozor.gif" title="Send pozor" class="post-submit">
		</div>
		<textarea class="content-input" name="content" maxlength="7000" placeholder=":pozor:" required autofocus></textarea>
	</form>
</div>

<div class="all-posts">
<?php foreach (array_reverse($posts, true) as $post): ?>
	<?php $post = $this->processIndex($post); ?>
	<div class="post">
		<div class="post-info">
			<span class="post-username<?php echo ($this->guestStatus ? ' guest-username' : '') ?>"><?php echo (!empty($post['email']) ? "<a href=mailto:{$post['email']}>".$post['username']."</a>" : $post['username']); ?></span> <span class="post-title"><?php echo $post['title']; ?></span> <span class="post-right-align"><span class="edit-url"><a href="/chatbox/admin/index.php?deletePost=<?php echo $post['id']; ?>">Delete</a></span> <span class="post-hash-name"><?php echo htmlspecialchars($post['hashname']); ?></span> <span class="post-time"><?php echo $post['explodedTime'][1]."\t".$post['explodedTime'][0][2].".".$post['explodedTime'][0][1].".".$post['explodedTime'][0][0]; ?></span> <span class="post-ip"><?php echo $post['ip']; ?></span> <span class="post-id"><?php echo $post['id']; ?></span></span>
		</div>
		<div class="post-content"><?php echo $post['content']; ?></div>
	</div>
<?php endforeach; ?>
</div>
</div>