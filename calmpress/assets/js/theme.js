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
	var shareOpened = config.shareOpened || 'Paylaşım penceresi açıldı.';
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
				navigator.share({ title: title, url: url }).then(function () {
					if (status) {
						status.textContent = shareOpened;
					}
				}).catch(function () {});
				return;
			}
			if (button.hasAttribute('data-share-copy') && navigator.clipboard) {
				navigator.clipboard.writeText(url).then(function () {
					if (status) {
						status.textContent = shareCopied;
					}
				}).catch(function () {
					window.prompt(sharePrompt, url);
				});
			} else {
				window.prompt(sharePrompt, url);
			}
		});
	});

	var modal = document.querySelector('[data-search-modal]');
	var modalOpeners = document.querySelectorAll('[data-search-open]');
	var modalClosers = modal ? modal.querySelectorAll('[data-search-close]') : [];
	var lastFocus = null;
	var focusableSelector = 'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';

	function closeSearch() {
		if (!modal) {
			return;
		}
		modal.hidden = true;
		document.body.classList.remove('search-modal-open');
		if (lastFocus) {
			lastFocus.focus();
		}
	}

	function openSearch() {
		if (!modal) {
			return;
		}
		lastFocus = document.activeElement;
		modal.hidden = false;
		document.body.classList.add('search-modal-open');
		var input = modal.querySelector('input[type="search"]');
		if (input) {
			input.focus();
		}
	}

	modalOpeners.forEach(function (button) {
		button.addEventListener('click', openSearch);
	});
	Array.prototype.forEach.call(modalClosers, function (button) {
		button.addEventListener('click', closeSearch);
	});
	if (modal) {
		modal.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeSearch();
				return;
			}
			if (event.key !== 'Tab') {
				return;
			}
			var focusable = modal.querySelectorAll(focusableSelector);
			if (!focusable.length) {
				return;
			}
			var first = focusable[0];
			var last = focusable[focusable.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		});
	}

	var mobileMenu = document.querySelector('[data-mobile-menu]');
	if (mobileMenu) {
		mobileMenu.addEventListener('click', function () {
			var nav = document.querySelector('.primary-navigation');
			if (!nav) {
				return;
			}
			var expanded = mobileMenu.getAttribute('aria-expanded') === 'true';
			mobileMenu.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			nav.classList.toggle('is-mobile-open', !expanded);
		});
	}

	var campaign = document.querySelector('[data-campaign]');
	var dismissCampaign = campaign ? campaign.querySelector('[data-campaign-dismiss]') : null;
	if (campaign && dismissCampaign) {
		var campaignKey = campaign.getAttribute('data-campaign-key');
		try {
			if (campaignKey && window.localStorage.getItem('calmpress-campaign-' + campaignKey) === 'dismissed') {
				campaign.hidden = true;
			}
		} catch (error) {
			// The notification remains usable when storage is unavailable.
		}
		dismissCampaign.addEventListener('click', function () {
			campaign.hidden = true;
			try {
				window.localStorage.setItem('calmpress-campaign-' + campaignKey, 'dismissed');
			} catch (error) {
				// Dismissal still applies for this page.
			}
		});
	}
}());
