<?php
require_once "includes/process_post.php";
$process = new ProcessPost; ?>
<div class="chat-page">
	<?php if (!empty($_SESSION["SentStatus"])) {
		echo "<p>${_SESSION['SentStatus']}</p>";
		unset($_SESSION["SentStatus"]);
	} ?>
	<form action"<?php echo __FILE__; ?>" method="POST">
	Receiver: <input type="text" name="receiver"> <input class="post-button post-submit" type="submit" value="Send"> <br>
	<textarea class="message" type="text" name="message" maxlength="10000" placeholder="Enter your message" required></textarea>
	</form>
	<?php switch ($_GET['which']): case 0: ?>
	<?php default: ?>
		<div class="pm-menu">Private messages [received|<a href="pm.php?which=1">sent</a>|<a href="pm.php?which=2">all</a>] (<a href="/chatbox/">back</a>)</div>
	<?php break; ?>
	<?php case 1: ?>
		<div class="pm-menu">Private messages [<a href="pm.php">received</a>|sent|<a href="pm.php?which=2">all</a>] (<a href="/chatbox/">back</a>)</div>
	<?php break; ?>
	<?php case 2: ?>
		<div class="pm-menu">Private messages [<a href="pm.php">received</a>|<a href="pm.php?which=1">sent</a>|all] (<a href="/chatbox/">back</a>)</div>
	<?php break; ?>
	<?php endswitch; ?>

	<?php if (!empty($messages)): ?>
	<?php foreach (array_reverse($messages, true) as $message): ?>
	<?php
	$message["time"] = $message["sendingTime"];
	$message["content"] = $message["message"];
	$message = $process->processIndex($message);
	$message["message"] = $message["content"];
	if ($_SESSION["username"] == $message["sender"]) {
	 $makeSenderBold = false;
	} else $makeSenderBold = true;
	?>
		<div class="post">
			<div class="post-info">
			<?php if ($makeSenderBold): ?>
				Author: <span class="post-username authorised"><?php echo $message["sender"] ?></span>
				<span style="margin-left: 8em;">Reseiver: <?php echo $message["receiver"]; ?></span>
			<?php else: ?>
				Author: <span><?php echo $message["sender"] ?></span>
				<span style="margin-left: 8em;">
					Receiver: <span class="post-username authorised"><?php echo $message["receiver"]; ?></span>
				</span>
			<?php endif; ?>
				<span class="post-right-align">
					<span class="post-time"><?php echo !empty($message["modTime"])? $message["modTime"]->format("H:i:s d-m-Y"): ""; ?></span>
				</span>
			</div>
			<div class="post-content"><?php echo $message['message']; ?></div>
		</div>
	<?php endforeach; ?>
	<?php else: ?>
	<p>There is not messages for you</p>
	<?php endif; ?>
</div>