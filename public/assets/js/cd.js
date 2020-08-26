// Clientdetails Page JS Document

$(document).ready(function() {

	$('.slide-panel').hide();
	
	if ($('#pb:checked').val() == 'on') {
		$("#ban-duration").hide();
	}
	$('#pb').click(function(){
		if ($('#pb:checked').val() == 'on') {
			$("#ban-duration").fadeTo("fast" , 0.5);
			$("#ban-duration").children().attr('disabled', 'true');
		} else {
			$("#ban-duration").fadeTo("fast" , 1);
			$("#ban-duration").children().removeAttr('disabled');
		}
	});
	
	// Tabs
	$("a.cd-tab").click(function() {
		if( $(this).hasClass('active') )
			return;

		$("#actions").find('.active').removeClass("active");
		
		$(this).addClass("active");

		var content_show = $(this).attr("rel");
		$(".act-slide").not("#content_show").hide();
		$("#"+content_show).show();
	  
	});

	// TODO: Remove with plugin update
	$('.cd-slide').click(function() {
	
		var slideName = $(this).attr("id");
		
		var slideArea = slideName + '-table';
		
		$('#'+slideArea).toggle();
		
	});
	
	// Chats Tabs
	$("a.chat-tab").click(function() {
		
		$(".chat-active").removeClass("chat-active");
		
		$(this).parent().addClass("chat-active");
		
		$(".chat-content").slideUp();
		
		var content_show = $(this).attr("rel");
		$("#"+content_show).slideDown('slow');
	  
	});
	
	if ($('#eb-pb:checked').val() == 'on') {
		$("#eb-ban-duration").hide();
	}
	$('#eb-pb').click(function(){
		editBanCheck();
	});

	// new and pretty shit.
	var actionNav = $("#actionNav"),
		actionContent = $("#actionContent");

	actionNav.find('li').on('click', function () {
		actionNav.find('li.active').removeClass('active');
		$(this).addClass('active');

		actionContent.find("[data-relation]").addClass('hidden');
		$("[data-relation='"+$(this).find('a').attr('data-relation')+"']").removeClass('hidden');
	});
	  
});

function editBanBox(thisItem) {
	var ban_id = $(thisItem).attr('rel');
	$.colorbox({href:"app/views/editban.php?banid="+ ban_id});
}

function editBanCheck() {
	if ($('#eb-pb:checked').val() == 'on') {
		$("#eb-ban-duration").slideUp();
	} else {
		$("#eb-ban-duration").slideDown();
	}
}