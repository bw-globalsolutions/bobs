(function () {
	"use strict";

	var treeviewMenu = $('.app-menu');

	// Toggle Sidebar
	$('[data-toggle="sidebar"]').click(function(event) {
		event.preventDefault();
		$('.app').toggleClass('sidenav-toggled');
	});

	// Activate sidebar treeview toggle
	$("[data-toggle='treeview']").click(function(event) {
		event.preventDefault();
		if(!$(this).parent().hasClass('is-expanded')) {
			treeviewMenu.find("[data-toggle='treeview']").parent().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
	});

	// Set initial active toggle
	$("[data-toggle='treeview.'].is-expanded").parent().toggleClass('is-expanded');

	//Activate bootstrip tooltips
	$("[data-toggle='tooltip']").tooltip();

	// Cambio de contraseña cada 60 dias
	let currentURL = window.location.href.split('/');
	if(typeof lastUpdPassword !== 'undefined' && currentURL[currentURL.length - 1] != 'perfil'){
		const passNotice = new Date();
		passNotice.setDate(lastUpdPassword.getDate() - 60);
		if(lastUpdPassword < passNotice){
			window.location = base_url + '/usuarios/perfil';
		}
	}

	// Back to top button
	$(window).scroll(function() {
		if ($(this).scrollTop() > 250) {
			$('.back-to-top').fadeIn('fast');
		} else {
			$('.back-to-top').fadeOut('fast');
		}
	});
	
	$('.back-to-top').click(function() {
		$('html, body').animate({
			scrollTop: 0
		}, 400);
		return false;
	});
})();
