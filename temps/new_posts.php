<?php foreach (array_reverse($newPosts, true) as $post): ?>
<?php $post = $Posts->processIndex($post); ?>
	<div class="post">
		<div class="post-info">
			<span class="post-username<?php echo ($Posts->guestStatus ? ' guest-username' : '') ?><?php echo ($post['isAuthorised'])? ' authorised': '' ?>"><?php echo (!empty($post['email']) ? "<a href=mailto:{$post['email']}>".$post['username']."</a>" : $post['username']); ?></span>
			<span class="post-title"><?php echo $post['title']; ?></span>
			<span class="post-right-align">
				<?php if ($Posts->getAdminStatus()): ?>
					<span class="post-delete"><a href="/chatbox/index.php?deletePost=<?php echo $post['id']; ?>">Delete</a></span>
					<span class="post-ip"><?php echo $post["ip"]; ?></span>
				<?php endif; ?>
				<span class="post-time"><?php echo !empty($post["modTime"])? $post["modTime"]->format("H:i:s d-m-Y"): ""; ?></span>
				<span class="post-id"><?php echo $post['id']; ?></span>
				<div class="hide-drop-down-menu">
					<img class="hide-post" src="/chatbox/temps/includes/hide_post_icon.png" title="hide post"><br>
					<div class="additional-drop-down-menu">hide nickname</div>
				</div>
			</span>
		</div>
		<div class="post-content"><?php echo $post['content']; ?></div>
	</div>
<?php endforeach; ?>