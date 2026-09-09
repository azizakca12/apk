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

	var configured = window.calmpressTheme && window.calmpressTheme.defaultTheme;
	var fallback = configured === 'light' || configured === 'dark' ? configured : 'system';
	var theme = stored === 'light' || stored === 'dark' ? stored : fallback;
	root.setAttribute('data-theme', theme);

	var config = window.calmpressTheme || {};
	var labels = config.labels || { system: 'Sistem', light: 'Açık', dark: 'Koyu' };
	var activate = config.activate || 'Değiştirmek için etkinleştirin.';
	var shareCopied = config.shareCopied || 'Bağlantı kopyalandı.';
	var sharePrompt = config.sharePrompt || 'Bağlantıyı kopyalayın:';
	var label = toggle ? toggle.querySelector('.theme-toggle__label') : null;

	function updateButton() {
		if (!toggle) {
			return;
		}
		toggle.setAttribute('aria-label', labels[theme] + '. ' + activate);
		toggle.setAttribute('title', labels[theme] + '. ' + activate);
		if (label) {
			label.textContent = labels[theme];
		}
	}

	if (toggle) {
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
	}

	var backToTop = document.querySelector('[data-back-to-top]');
	if (backToTop) {
		var toggleBackToTop = function () {
			backToTop.classList.toggle('is-visible', window.scrollY > 500);
		};
		window.addEventListener('scroll', toggleBackToTop, { passive: true });
		toggleBackToTop();
		backToTop.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: root.classList.contains('calmpress-reduced-motion') ? 'auto' : 'smooth' });
		});
	}

	document.querySelectorAll('[data-share-native], [data-share-copy]').forEach(function (button) {
		var tools = button.closest('[data-share-title]');
		if (!tools) {
			return;
		}
		var status = tools.querySelector('[data-share-status]');
		button.addEventListener('click', function () {
			var title = tools.getAttribute('data-share-title') || document.title;
			var url = tools.getAttribute('data-share-url') || window.location.href;
			if (button.hasAttribute('data-share-native') && navigator.share) {
				navigator.share({ title: title, url: url }).catch(function () {});
				return;
			}
			if (button.hasAttribute('data-share-copy') && navigator.clipboard) {
				navigator.clipboard.writeText(url).then(function () {
					if (status) {
						status.textContent = shareCopied;
					}
				});
			} else {
				window.prompt(sharePrompt, url);
			}
		});
	});
}());
