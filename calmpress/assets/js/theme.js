(function () {
	'use strict';

	var root = document.documentElement;
	var toggle = document.querySelector('[data-theme-toggle]');
	var stored = null;

	try {
		stored = window.localStorage.getItem('calmpress-theme');
	} catch (error) {
		stored = null;
	}

	var theme = stored === 'light' || stored === 'dark' ? stored : 'system';
	root.setAttribute('data-theme', theme);

	if (!toggle) {
		return;
	}

	var label = toggle.querySelector('.theme-toggle__label');
	var config = window.calmpressTheme || {};
	var labels = config.labels || { system: 'Sistem', light: 'Açık', dark: 'Koyu' };
	var activate = config.activate || 'Değiştirmek için etkinleştirin.';

	function updateButton() {
		toggle.setAttribute('aria-label', labels[theme] + '. ' + activate);
		toggle.setAttribute('title', labels[theme] + '. ' + activate);
		if (label) {
			label.textContent = labels[theme];
		}
	}

	toggle.addEventListener('click', function () {
		theme = theme === 'system' ? 'light' : theme === 'light' ? 'dark' : 'system';
		root.setAttribute('data-theme', theme);
		try {
			window.localStorage.setItem('calmpress-theme', theme);
		} catch (error) {
			// Theme still works for this page when storage is unavailable.
		}
		updateButton();
	});

	updateButton();
}());
