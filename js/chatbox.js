$(function() {

$(".main-smiles img").click(function(){
	var start = document.getElementById("message").selectionStart;
	var end = document.getElementById("message").selectionEnd;
	var value = $("#message").val();
	$("#message").val(value.substring(0, start) + " " + $(this).attr("alt") + value.substring(end));
	$(".content-input").focus();
	document.getElementById("message").setSelectionRange(end+1+$(this).attr("alt").length, end+1+$(this).attr("alt").length - (end-start));
});
$("#quote-button").click(function(){
	var start = document.getElementById("message").selectionStart;
	var end = document.getElementById("message").selectionEnd;
	var value = $("#message").val();
	$("#message").val(value.substring(0, start) + $(this).attr("alt") + value.substring(end));
	$(".content-input").focus();
	document.getElementById("message").setSelectionRange(end+$(this).attr("alt").length, end+$(this).attr("alt").length - (end-start));
});

$(".BB-codes").click(function(event) {
	if (event.target.className == "format-button") {
		var codeName = $(event.target).attr("alt");
		var start = document.getElementById("message").selectionStart;
		var end = document.getElementById("message").selectionEnd;
		var value = $("#message").val();
		$("#message").val(value.substring(0, start) + "[" + codeName + "]" + value.substring(start, end) + "[/" + codeName + "]" + value.substring(end));
		$("#message").focus();
		if (start == end) {
			document.getElementById("message").setSelectionRange(start+2+codeName.length, start+2+codeName.length);
		} else {
			document.getElementById("message").setSelectionRange(start, end+5+codeName.length*2);
		}
	} else if (event.target.tagName == "OPTION" && event.target.value.length) {
		var codeName = $(event.target).parent().attr("id");
		var attrValue = $(event.target).attr("value");
		var start = document.getElementById("message").selectionStart;
		var end = document.getElementById("message").selectionEnd;
		var value = $("#message").val();
		$("#message").val(value.substring(0, start) + "[" + codeName + "=" + attrValue + "]" + value.substring(start, end) + "[/" + codeName + "]" + value.substring(end));
		$("#message").focus();
		if (start == end) {
			document.getElementById("message").setSelectionRange(start+3+codeName.length+attrValue.length, start+3+codeName.length+attrValue.length);
		} else {
			document.getElementById("message").setSelectionRange(start, end+6+attrValue.length+codeName.length*2);
		}
	}
});

$(".login-button").click(function() {
	$(".login-form").slideToggle("fast");
});

$(".additional-menu-button").click(function() {
		$(".additional-menu").slideToggle("fast");
});

function getLastPostId() {
	var allPostId = [];
	$(".post .post-id").each(function() {
  	allPostId.push($(this).html());
	});
	if (!allPostId) {
		return 0;
	} else {
		var lastPostId = Math.max.apply(Math, allPostId);
		return lastPostId;
	}
}
lastPostId = getLastPostId();
var isRefreshing = false;
function refreshPosts() {
	if (isRefreshing) {return 0}
	isRefreshing = true;
	$.ajax({
		url: "/chatbox/includes/refresh_posts.php",
		data: {
			lastPostId: lastPostId
		},
		type: "POST",
		dataType: "html",
 		success: function(html) {
 			if ($(html).filter(".post").length) {
 				$(html).filter(".post").each(function() {
 					if ($(this).find(".post-id").html() <= $(".post").first().find(".post-id")) {
 						$(this).remove();
 					}
 				});
 				$(".all-posts").prepend(html);
   				lastPostId = getLastPostId();
 			}
 			$(".waiting").remove();
 			isRefreshing = false;
 			//hidePostsId();
 			//hideNickname();
 		},
 		error: function() {isRefreshing = false;}
	});
}
setInterval(refreshPosts, 20000);

$(".register-anchor").click(function() {
	$("#login-area, #register-area").toggle();
	getReCapcha();
});

function getReCapcha() {
	Recaptcha.create("6LcqNP8SAAAAAISgHaumQLEMa9qGzZ4SIDf8gdVH", "capcha",
    	{
     		theme: "black",
     		callback: Recaptcha.focus_response_field
   		}
	);
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
} 
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
}

$(document).on("click", ".hide-post", function() {
	var hiddenPosts = getCookie("hiddenPosts");
	var hidePostButton = $(this);
	var hiddenPostsArr = hiddenPosts.split(" ");
	var trigger = true;
	var hiddenNicknames = getCookie("hiddenNicknames");
	$.each(hiddenNicknames.split("\n"), function(key, nickname) {
		if (hidePostButton.parent().parent().parent().find(".post-username").html() == nickname) {
			hidePostButton.parent().parent().parent().parent().find(".post-content").toggle();
			trigger = false;
		}
	})
	$.each(hiddenPostsArr, function(key, postId) {
		if (hidePostButton.parent().prev().html() == postId) {
			delete hiddenPostsArr[key];
			setCookie("hiddenPosts", hiddenPostsArr.join(" "), 60);
			hidePostButton.parent().parent().parent().parent().find(".post-content").show();
			trigger = false;
		}
	});
	if (trigger) {
		if (hiddenPosts.length > 0) {
			var hiddenPosts = hiddenPosts.split(" ");
			hiddenPosts.push($(this).parent().prev().html());
		} else {
			hiddenPosts = [$(this).parent().prev().html()];
		}
		setCookie("hiddenPosts", hiddenPosts.join(" "), 60);
		hidePostsId();
	}
});

$(document).on("click", ".hide-nickname", function() {
	var hiddenNicknames = getCookie("hiddenNicknames");
	var hideNickButton = $(this);
	var hiddenNicknamesArr = hiddenNicknames.split("\n");
	var hideNickTrigger = true;
	$.each(hiddenNicknamesArr, function(key, postNick) {
		if (hideNickButton.parent().parent().parent().parent().find(".post-username").html() == postNick) {
			delete hiddenNicknamesArr[key];
			setCookie("hiddenNicknames", hiddenNicknamesArr.join("\n"), 60);
			showNickname(postNick);
			hideNickTrigger = false;
		}
	});
	if (hideNickTrigger) {
		if (hiddenNicknames.length > 0) {
			var hiddenNicknames = hiddenNicknames.split("\n");
			hiddenNicknames.push($(this).parent().parent().parent().parent().find(".post-username").html());
		} else {
			hiddenNicknames = [$(this).parent().parent().parent().parent().find(".post-username").html()]
		}
		setCookie("hiddenNicknames", hiddenNicknames.join("\n"), 60);
		hideNickname();
	}
});

function hidePostsId() {
	$(".post-id").each(function() {
		hiddenPosts = getCookie("hiddenPosts");
		post = $(this);
		$.each(hiddenPosts.split(" "), function(key, postId) {
			if (post.html() == postId) {
				post.parent().parent().parent().find(".post-content").hide();
			}
		})
	})
}
hidePostsId();

function hideNickname() {
	$(".post-username").each(function() {
		hiddenNicknames = getCookie("hiddenNicknames");
		postNick = $(this);
		$.each(hiddenNicknames.split("\n"), function(key, nick) {
			if (postNick.html() == nick) {
				postNick.parent().parent().find(".post-content").hide();
			}
		})
	})
}
hideNickname();

function showNickname(nickname) {
	$(".post-username").each(function() {
		if ($(this).html() == nickname) {
			$(this).parent().parent().find(".post-content").show();
		}
	})
}

$(document).on({
	mouseenter: function() {
		$(this).children(".additional-drop-down-menu").css("display", "initial");
	},
	mouseleave: function() {
		$(this).children(".additional-drop-down-menu").css("display", "none");
	}
} , ".hide-drop-down-menu");

var postCooldown = false;
$("#post-input").submit(function(e) {
	e.preventDefault();
	if (postCooldown) {
		$(".alerts").prepend("<div class='post-like cooldown' style='display:inline-block;'>Too fast posting</div><br class='cooldown'>");
	} else {
		postCooldown = true;
		var username = $(".username-input").val();
		var title = $(".title-input").val();
		var email = $("email-input").val();
		var content = $(".content-input").val();
		$(".title-input").val("");
		$("email-input").val("");
		$(".content-input").val("");
		if (!$(".waiting").length) $(".alerts").prepend("<div class='post-like waiting' style='display:inline-block;'>Sending...</div>");
		$.ajax({
			url: "/chatbox/index.php",
			data: {
				username: username,
				title: title,
				email: email,
				content: content
			},
			type: "POST",
			dataType: "html",
			success: function() {
				refreshPosts();
			},
			complete: function() {
				var cooldownInterval = setInterval(function() {
					postCooldown = false;
					$(".cooldown").remove();
					clearInterval(cooldownInterval);
				}, 5000);
			}
		});
	}
});

$(this).on("keydown", function(e) {
	if ($(".content-input").is(":focus")) {
		if (e.which == "13" && !e.shiftKey && !e.ctrlKey) {
			e.preventDefault();
			$("#post-input").trigger("submit");
		}
	}
});

$(this).on("keydown", function(e) {
	if ($(".content-input").is(":focus")) {
		if (e.which == "13" && e.ctrlKey) {
			e.preventDefault();
			var start = document.getElementById("message").selectionStart;
			var end = document.getElementById("message").selectionEnd;
			var value = $("#message").val();
			$("#message").val(value.substring(0, start) + "\n" + value.substring(end));
			$(".content-input").focus();
		}
	}
});

var isGettingNewPosts = false;
var getOlderPosts = setInterval(function() {
	if ($(".post").eq(-1).find(".post-id").html() == "1") {
		clearInterval(getOlderPosts);
	} else if ($(".post").eq(-3).is(":in-viewport")) {
		if (isGettingNewPosts) {return 0}
		isGettingNewPosts = true;
		var lowestPostId = $(".post").eq(-1).find(".post-id").html();
		$(".all-posts").append("<div class='post-like waiting' style='display:inline-block;'>Loading...</div>");
		$.ajax({
			url: "/chatbox/includes/get_older_posts.php",
			data: {
				lowestPostId: lowestPostId
			},
			type: "POST",
			dataType: "html",
			success: function(html) {
				$(".waiting").remove();
				if ($(html).filter(".post").length) $(".all-posts").append(html);
				isGettingNewPosts = false;
			},
			error: function() {
				isGettingNewPosts = false;
				$(".waiting").remove();
				$(".all-posts").append("<div class='post-like waiting' style='display:inline-block;'>Some error!</div>");
			}
		});
	}
}, 1000)

$(document).on('click', '.post-content .spoiler-head', function () {
	$(this).parent().find(".spoiler-content").eq(0).slideToggle("fast");
});

$(document).on("click", ".post-username", function() {
	var start = document.getElementById("message").selectionStart;
	var end = document.getElementById("message").selectionEnd;
	var value = $("#message").val();
	$("#message").val(value.substring(0, start) + "[b]" + $(this).html() + ",[/b] " + value.substring(end));
	$(".content-input").focus();
})

});

//expiryTime = new Date();
//expiryTime.setTime(expiryTime.getTime() + 30*1000*60*60*24);
//expiryTime;
//document.cookie = "testotaoe=valuae; expires=" + expiryTime.toUTCString();