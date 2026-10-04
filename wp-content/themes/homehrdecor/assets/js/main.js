/**
 * homehrdecor — front-end behaviour.
 *
 * Small, dependency-free modules. Everything degrades gracefully: with
 * JavaScript disabled the markup is still complete and readable.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var root = document.documentElement;

	/* Signal that scripting is available so CSS can opt into reveals. */
	root.classList.add('js');

	/**
	 * Run a callback once the DOM is parsed.
	 *
	 * @param {Function} fn Callback.
	 */
	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	/* ----------------------------------------------------------------------
	   Header: solid on scroll, hidden when scrolling down past the fold.
	   ---------------------------------------------------------------------- */
	function initHeader() {
		var header = document.querySelector('.site-header');
		if (!header) {
			return;
		}

		var lastY = window.scrollY;
		var ticking = false;

		function update() {
			var y = window.scrollY;
			var threshold = header.classList.contains('site-header--dark') ? 40 : 24;

			header.classList.toggle('is-stuck', y > threshold);

			// Only hide on the way down, and never while the mobile menu is open.
			if (!document.body.classList.contains('nav-open')) {
				if (y > 420 && y > lastY + 4) {
					header.classList.add('is-hidden');
				} else if (y < lastY - 4 || y < 420) {
					header.classList.remove('is-hidden');
				}
			}

			lastY = y;
			ticking = false;
		}

		window.addEventListener(
			'scroll',
			function () {
				if (!ticking) {
					ticking = true;
					window.requestAnimationFrame(update);
				}
			},
			{ passive: true }
		);

		update();
	}

	/* ----------------------------------------------------------------------
	   Mobile navigation.
	   ---------------------------------------------------------------------- */
	function initMobileNav() {
		var toggle = document.querySelector('.nav-toggle');
		var panel = document.getElementById('mobile-nav');
		if (!toggle || !panel) {
			return;
		}

		var focusableSelector = 'a[href], button:not([disabled])';

		function open() {
			panel.classList.add('is-open');
			panel.removeAttribute('inert');
			toggle.setAttribute('aria-expanded', 'true');
			document.body.classList.add('nav-open');

			var first = panel.querySelector(focusableSelector);
			if (first) {
				first.focus();
			}
		}

		function close(returnFocus) {
			panel.classList.remove('is-open');
			panel.setAttribute('inert', '');
			toggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('nav-open');
			if (returnFocus) {
				toggle.focus();
			}
		}

		toggle.addEventListener('click', function () {
			if (panel.classList.contains('is-open')) {
				close(true);
			} else {
				open();
			}
		});

		panel.addEventListener('click', function (event) {
			if (event.target.closest('a')) {
				close(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && panel.classList.contains('is-open')) {
				close(true);
			}
		});

		// Keep focus inside the panel while it is open.
		panel.addEventListener('keydown', function (event) {
			if (event.key !== 'Tab') {
				return;
			}
			var items = Array.prototype.filter.call(panel.querySelectorAll(focusableSelector), function (el) {
				return el.offsetParent !== null;
			});
			if (!items.length) {
				return;
			}
			var first = items[0];
			var last = items[items.length - 1];

			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		});

		// Reset when resizing back up to the desktop layout.
		window.addEventListener('resize', function () {
			if (window.innerWidth > 1080 && panel.classList.contains('is-open')) {
				close(false);
			}
		});

		close(false);
	}

	/* ----------------------------------------------------------------------
	   Scroll reveals.
	   ---------------------------------------------------------------------- */
	function initReveals() {
		var targets = document.querySelectorAll('.reveal, .reveal-media, .reveal-line, .process-step');
		if (!targets.length) {
			return;
		}

		if (reduceMotion || !('IntersectionObserver' in window)) {
			Array.prototype.forEach.call(targets, function (el) {
				el.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -12% 0px', threshold: 0.12 }
		);

		Array.prototype.forEach.call(targets, function (el) {
			observer.observe(el);
		});
	}

	/* ----------------------------------------------------------------------
	   Stagger children of a group so reveals cascade instead of popping.
	   ---------------------------------------------------------------------- */
	function initStagger() {
		var groups = document.querySelectorAll('[data-stagger]');
		Array.prototype.forEach.call(groups, function (group) {
			var step = parseInt(group.getAttribute('data-stagger'), 10) || 90;
			Array.prototype.forEach.call(group.children, function (child, index) {
				child.style.setProperty('--reveal-delay', index * step + 'ms');
			});
		});
	}

	/* ----------------------------------------------------------------------
	   Animated counters for the studio statistics.
	   ---------------------------------------------------------------------- */
	function initCounters() {
		var counters = document.querySelectorAll('[data-count-to]');
		if (!counters.length) {
			return;
		}

		var persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

		function toPersian(value) {
			return String(value).replace(/\d/g, function (digit) {
				return persian[parseInt(digit, 10)];
			});
		}

		function run(el) {
			var target = parseInt(el.getAttribute('data-count-to'), 10) || 0;

			if (reduceMotion) {
				el.textContent = toPersian(target);
				return;
			}

			var duration = 1500;
			var start = null;

			function tick(timestamp) {
				if (start === null) {
					start = timestamp;
				}
				var progress = Math.min((timestamp - start) / duration, 1);
				// easeOutExpo keeps the count lively at the start and calm at the end.
				var eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
				el.textContent = toPersian(Math.round(eased * target));
				if (progress < 1) {
					window.requestAnimationFrame(tick);
				}
			}

			window.requestAnimationFrame(tick);
		}

		if (!('IntersectionObserver' in window)) {
			Array.prototype.forEach.call(counters, run);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						run(entry.target);
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.5 }
		);

		Array.prototype.forEach.call(counters, function (el) {
			observer.observe(el);
		});
	}

	/* ----------------------------------------------------------------------
	   Services: an image preview that follows the pointer.
	   ---------------------------------------------------------------------- */
	function initServicePreview() {
		var list = document.querySelector('[data-service-list]');
		var preview = document.querySelector('.service-preview');
		if (!list || !preview || window.matchMedia('(max-width: 782px)').matches) {
			return;
		}

		var images = preview.querySelectorAll('img');
		var current = null;
		var pointer = { x: -999, y: -999 };
		var position = { x: -999, y: -999 };
		var raf = null;

		function render() {
			// Ease the preview toward the pointer for a weighted, premium feel.
			position.x += (pointer.x - position.x) * 0.14;
			position.y += (pointer.y - position.y) * 0.14;
			preview.style.transform =
				'translate3d(' + (position.x + 28) + 'px,' + (position.y - 130) + 'px,0) scale(1)';

			if (Math.abs(pointer.x - position.x) > 0.3 || Math.abs(pointer.y - position.y) > 0.3) {
				raf = window.requestAnimationFrame(render);
			} else {
				raf = null;
			}
		}

		function schedule() {
			if (raf === null) {
				raf = window.requestAnimationFrame(render);
			}
		}

		list.addEventListener('pointermove', function (event) {
			pointer.x = event.clientX;
			pointer.y = event.clientY;
			schedule();
		});

		Array.prototype.forEach.call(list.querySelectorAll('[data-preview]'), function (row) {
			function activate() {
				var key = row.getAttribute('data-preview');
				Array.prototype.forEach.call(images, function (img) {
					img.classList.toggle('is-current', img.getAttribute('data-key') === key);
				});
				preview.classList.add('is-active');
				current = row;
			}

			function deactivate() {
				if (current === row) {
					preview.classList.remove('is-active');
					current = null;
				}
			}

			row.addEventListener('pointerenter', activate);
			row.addEventListener('focusin', activate);
			row.addEventListener('pointerleave', deactivate);
			row.addEventListener('focusout', deactivate);
		});

		list.addEventListener('pointerleave', function () {
			preview.classList.remove('is-active');
		});
	}

	/* ----------------------------------------------------------------------
	   Before / after comparison slider.
	   ---------------------------------------------------------------------- */
	function initCompare() {
		var compares = document.querySelectorAll('[data-compare]');

		Array.prototype.forEach.call(compares, function (compare) {
			var range = compare.querySelector('.compare__range');
			if (!range) {
				return;
			}

			function apply(value) {
				compare.style.setProperty('--compare-pos', value + '%');
				range.value = String(value);
			}

			function fromEvent(event) {
				var rect = compare.getBoundingClientRect();
				var ratio = (event.clientX - rect.left) / rect.width;
				// The handle follows the pointer in visual order, so the
				// percentage is mirrored in RTL.
				var value = document.dir === 'rtl' ? (1 - ratio) * 100 : ratio * 100;
				apply(Math.max(0, Math.min(100, Math.round(value))));
			}

			range.addEventListener('input', function () {
				apply(parseFloat(range.value));
			});

			var dragging = false;

			compare.addEventListener('pointerdown', function (event) {
				dragging = true;
				compare.classList.add('is-dragging');
				fromEvent(event);
				if (compare.setPointerCapture) {
					compare.setPointerCapture(event.pointerId);
				}
			});

			compare.addEventListener('pointermove', function (event) {
				if (dragging) {
					fromEvent(event);
				}
			});

			function stop() {
				dragging = false;
				compare.classList.remove('is-dragging');
			}

			compare.addEventListener('pointerup', stop);
			compare.addEventListener('pointercancel', stop);
			compare.addEventListener('pointerleave', function () {
				if (dragging) {
					stop();
				}
			});

			apply(50);
		});
	}

	/* ----------------------------------------------------------------------
	   Accordion (FAQ).
	   ---------------------------------------------------------------------- */
	function initAccordion() {
		var triggers = document.querySelectorAll('.accordion__trigger');

		Array.prototype.forEach.call(triggers, function (trigger) {
			var panel = document.getElementById(trigger.getAttribute('aria-controls'));
			if (!panel) {
				return;
			}

			function setOpen(open) {
				trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
				panel.style.height = open ? panel.scrollHeight + 'px' : '0px';
			}

			setOpen(trigger.getAttribute('aria-expanded') === 'true');

			trigger.addEventListener('click', function () {
				setOpen(trigger.getAttribute('aria-expanded') !== 'true');
			});

			// Keep an open panel correctly sized when the viewport changes.
			window.addEventListener('resize', function () {
				if (trigger.getAttribute('aria-expanded') === 'true') {
					panel.style.height = panel.scrollHeight + 'px';
				}
			});
		});
	}

	/* ----------------------------------------------------------------------
	   Project filters.
	   ---------------------------------------------------------------------- */
	function initFilters() {
		var bar = document.querySelector('[data-filters]');
		if (!bar) {
			return;
		}

		var cards = document.querySelectorAll('[data-category]');
		var countEl = document.querySelector('[data-filter-count]');
		var empty = document.querySelector('[data-filter-empty]');

		bar.addEventListener('click', function (event) {
			var button = event.target.closest('.filter-btn');
			if (!button) {
				return;
			}

			var filter = button.getAttribute('data-filter');

			Array.prototype.forEach.call(bar.querySelectorAll('.filter-btn'), function (btn) {
				btn.setAttribute('aria-pressed', btn === button ? 'true' : 'false');
			});

			var visible = 0;
			Array.prototype.forEach.call(cards, function (card) {
				var match = filter === 'all' || card.getAttribute('data-category') === filter;
				card.classList.toggle('is-hidden', !match);
				if (match) {
					visible++;
				}
			});

			if (countEl) {
				var persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
				countEl.textContent = String(visible).replace(/\d/g, function (d) {
					return persian[parseInt(d, 10)];
				});
			}

			if (empty) {
				empty.hidden = visible !== 0;
			}
		});
	}

	/* ----------------------------------------------------------------------
	   Public submission forms.

	   The markup is untouched: the fields, their names and the layout are the
	   ones the templates already render. FIELD_MAP below is the bridge from
	   those names to the REST parameters.
	   ---------------------------------------------------------------------- */
	function initPublicForms() {
		var data = window.hhdData || {};
		if (!data.restUrl) {
			return;
		}

		/** Must match HHD_HONEYPOT_FIELD in inc/rest.php. */
		var HONEYPOT = 'hhd_website';

		var i18n = data.i18n || {};
		var forms = document.querySelectorAll('[data-contact-form], [data-consultation-form], [data-review-form]');

		Array.prototype.forEach.call(forms, function (form) {
			var endpoint = 'contact';
			if (form.hasAttribute('data-consultation-form')) {
				endpoint = 'consultation';
			} else if (form.hasAttribute('data-review-form')) {
				endpoint = 'reviews';
			}

			injectHoneypot(form);

			var feedback = document.createElement('p');
			feedback.className = 'form__feedback';
			feedback.setAttribute('role', 'status');
			feedback.setAttribute('aria-live', 'polite');
			form.appendChild(feedback);

			form.addEventListener('submit', function (event) {
				event.preventDefault();
				sendForm(form, endpoint, feedback);
			});
		});

		/**
		 * A field no human can see. Bots fill everything they find, so a
		 * non-empty value is discarded server-side without a row being
		 * written.
		 */
		function injectHoneypot(form) {
			if (form.querySelector('[name="' + HONEYPOT + '"]')) {
				return;
			}

			var wrap = document.createElement('div');
			wrap.className = 'form__trap';
			wrap.setAttribute('aria-hidden', 'true');

			var input = document.createElement('input');
			input.type = 'text';
			input.name = HONEYPOT;
			input.tabIndex = -1;
			input.autocomplete = 'off';

			wrap.appendChild(input);
			form.appendChild(wrap);
		}

		/**
		 * Field name -> API parameter.
		 *
		 * Written out rather than derived from the name. Two of the public
		 * field names do not line up with what the API expects — the project
		 * type is "contact-type"/"consult-type" in the markup but
		 * `project_type` over the wire, and the call window is "consult-time"
		 * but `call_time` — and the public forms are fixed, so the bridge
		 * belongs here. Anything absent from this map is ignored.
		 */
		var FIELD_MAP = {
			'contact-name': 'name',
			'contact-phone': 'phone',
			'contact-type': 'project_type',
			'contact-area': 'area',
			'contact-city': 'city',
			'contact-message': 'message',
			'consult-name': 'name',
			'consult-phone': 'phone',
			'consult-type': 'project_type',
			'consult-area': 'area',
			'consult-city': 'city',
			'consult-time': 'call_time',
			'consult-message': 'message',
			'review-project': 'project',
			'review-name': 'name',
			'review-rating': 'rating',
			'review-message': 'message'
		};

		/**
		 * Map the form's fields onto the API payload.
		 */
		function collect(form) {
			var payload = {};

			Array.prototype.forEach.call(form.elements, function (field) {
				var key = FIELD_MAP[field.name];

				if (!key) {
					return;
				}

				if (field.type === 'radio' && !field.checked) {
					return;
				}

				payload[key] = field.value;
			});

			// The honeypot is sent on purpose. It only catches anything if the
			// value a bot filled in actually reaches the server, so excluding
			// it here — the obvious reading of "don't submit the trap" — would
			// make the server-side check unreachable and the field decorative.
			var trap = form.querySelector('[name="' + HONEYPOT + '"]');
			if (trap) {
				payload[HONEYPOT] = trap.value;
			}

			return payload;
		}

		/**
		 * The required fields, checked before the round trip so an obvious
		 * omission does not cost a request.
		 */
		function validate(form, feedback) {
			var required = form.querySelectorAll('[required]');

			for (var i = 0; i < required.length; i++) {
				if (!required[i].value.trim()) {
					show(feedback, i18n.invalid || 'لطفاً فیلدهای لازم را کامل کنید.', 'error');
					required[i].focus();
					return false;
				}
			}

			return true;
		}

		function show(feedback, message, state) {
			feedback.textContent = message;
			feedback.className = 'form__feedback is-' + state;
		}

		function sendForm(form, endpoint, feedback) {
			if (!validate(form, feedback)) {
				return;
			}

			var button = form.querySelector('[type="submit"]');
			var label = button ? button.querySelector('.btn__label') : null;
			var original = label ? label.textContent : '';

			if (button) {
				button.disabled = true;
			}
			if (label) {
				label.textContent = i18n.sending || '…';
			}
			feedback.className = 'form__feedback';
			feedback.textContent = '';

			window
				.fetch(data.restUrl.replace(/\/+$/, '') + '/' + endpoint, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': data.restNonce || ''
					},
					body: JSON.stringify(collect(form))
				})
				.then(function (response) {
					return response
						.json()
						.catch(function () {
							return null;
						})
						.then(function (body) {
							if (!response.ok) {
								throw new Error((body && body.message) || i18n.error || '');
							}
							return body;
						});
				})
				.then(function (body) {
					form.reset();
					show(feedback, (body && body.message) || '', 'success');
				})
				.catch(function (error) {
					show(feedback, error.message || i18n.error, 'error');
				})
				.then(function () {
					if (button) {
						button.disabled = false;
					}
					if (label) {
						label.textContent = original;
					}
				});
		}
	}

	/* ----------------------------------------------------------------------
	   Boot.
	   ---------------------------------------------------------------------- */
	ready(function () {
		initHeader();
		initMobileNav();
		initStagger();
		initReveals();
		initCounters();
		initServicePreview();
		initCompare();
		initAccordion();
		initFilters();
		initPublicForms();
	});
})();
