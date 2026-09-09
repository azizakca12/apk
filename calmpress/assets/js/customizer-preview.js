(function () {
	'use strict';

	if (!window.wp || !wp.customize) {
		return;
	}

	var root = document.documentElement;
	var spacing = {
		compact: 'clamp(2rem, 4vw, 3rem)',
		comfortable: 'clamp(3rem, 7vw, 6rem)',
		spacious: 'clamp(4rem, 9vw, 8rem)'
	};
	var fonts = {
		system: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
		readable: 'Atkinson Hyperlegible, "Segoe UI", Helvetica, Arial, sans-serif'
	};
	var headings = {
		system: '-apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", sans-serif',
		readable: 'Atkinson Hyperlegible, "Segoe UI", Helvetica, Arial, sans-serif'
	};

	function bind(setting, callback) {
		if (wp.customize(setting)) {
			wp.customize(setting, function (value) {
				value.bind(callback);
				callback(value());
			});
		}
	}

	bind('calmpress_accent_color', function (value) {
		root.style.setProperty('--cp-accent', value);
	});
	bind('calmpress_accent_hover', function (value) {
		root.style.setProperty('--cp-accent-hover', value);
	});
	bind('calmpress_accent_contrast', function (value) {
		root.style.setProperty('--cp-accent-contrast', value);
	});
	bind('calmpress_focus_color', function (value) {
		root.style.setProperty('--cp-focus', value);
	});
	bind('calmpress_container_width', function (value) {
		root.style.setProperty('--cp-content-width', parseInt(value, 10) + 'px');
	});
	bind('calmpress_cards_per_row', function (value) {
		root.style.setProperty('--cp-card-columns', parseInt(value, 10));
	});
	bind('calmpress_border_radius', function (value) {
		root.style.setProperty('--cp-radius', parseInt(value, 10) + 'px');
	});
	bind('calmpress_density', function (value) {
		root.style.setProperty('--cp-density', value);
		root.style.setProperty('--cp-density-space', value === 'compact' ? '.85rem' : value === 'spacious' ? '1.2rem' : '1rem');
	});
	bind('calmpress_body_font', function (value) {
		root.style.setProperty('--cp-font-body', fonts[value] || fonts.system);
	});
	bind('calmpress_heading_font', function (value) {
		root.style.setProperty('--cp-font-heading', headings[value] || headings.system);
	});
	bind('calmpress_section_spacing', function (value) {
		document.querySelectorAll('.section-spaced').forEach(function (element) {
			element.style.marginTop = spacing[value] || spacing.comfortable;
		});
	});
}());
