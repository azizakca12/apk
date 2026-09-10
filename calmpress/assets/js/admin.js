(function () {
	'use strict';

	var form = document.querySelector('.calmpress-admin__form');
	if (!form) {
		return;
	}

	var dirty = false;
	var fields = form.closest('.calmpress-admin').querySelectorAll('input, textarea, select');
	Array.prototype.forEach.call(fields, function (field) {
		field.addEventListener('input', function () {
			dirty = true;
		});
		field.addEventListener('change', function () {
			dirty = true;
		});
	});
	form.addEventListener('submit', function () {
		dirty = false;
	});
	window.addEventListener('beforeunload', function (event) {
		if (!dirty) {
			return;
		}
		event.preventDefault();
		event.returnValue = (window.calmpressAdmin && window.calmpressAdmin.confirmLeave) || '';
		return event.returnValue;
	});

	var color = document.querySelector('#calmpress_accent_color');
	var swatch = document.querySelector('[data-cp-swatch="calmpress_accent_color"]');
	var updateSwatch = function () {
		if (color && swatch) {
			swatch.style.backgroundColor = color.value;
		}
	};
	if (color) {
		color.addEventListener('input', updateSwatch);
		updateSwatch();
	}

	Array.prototype.forEach.call(document.querySelectorAll('[data-cp-count-for]'), function (counter) {
		var target = document.getElementById(counter.getAttribute('data-cp-count-for'));
		if (!target) {
			return;
		}
		var updateCount = function () {
			counter.textContent = target.value.length + ' karakter';
		};
		target.addEventListener('input', updateCount);
		updateCount();
	});

	Array.prototype.forEach.call(document.querySelectorAll('[data-cp-preview]'), function (button) {
		var placement = button.getAttribute('data-cp-preview');
		var preview = document.querySelector('[data-cp-preview-content="' + placement + '"]');
		var textarea = document.getElementById('calmpress_ad_' + placement + '_html');
		if (!preview || !textarea) {
			return;
		}
		button.addEventListener('click', function () {
			var isHidden = preview.hasAttribute('hidden');
			preview.textContent = textarea.value || 'Önizlenecek HTML yok.';
			if (isHidden) {
				preview.removeAttribute('hidden');
				button.textContent = 'Önizlemeyi kapat';
			} else {
				preview.setAttribute('hidden', 'hidden');
				button.textContent = 'Önizlemeyi aç';
			}
		});
	});
}());
