/**
 * homehrdecor — admin dashboard behaviour.
 *
 * The screens are server-rendered on first paint; everything after that
 * (filtering, search, pagination, status changes, deletes, the notification
 * dropdown) round-trips through the hhd/v1 REST API and re-renders from
 * JSON. Rows are built as DOM nodes with textContent rather than HTML
 * strings, so submitted review text can never be interpreted as markup.
 *
 * Same shape as the front-end bundle: an IIFE, ES5, small init functions.
 */
(function () {
	'use strict';

	var config = window.hhdAdmin;
	if (!config) {
		return;
	}

	var i18n = config.i18n || {};

	/* ----------------------------------------------------------------------
	   Small helpers
	   ---------------------------------------------------------------------- */

	var PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

	function faNum(value) {
		return String(value).replace(/\d/g, function (digit) {
			return PERSIAN_DIGITS[parseInt(digit, 10)];
		});
	}

	function el(tag, className, text) {
		var node = document.createElement(tag);
		if (className) {
			node.className = className;
		}
		if (text !== undefined && text !== null) {
			node.textContent = String(text);
		}
		return node;
	}

	function debounce(fn, wait) {
		var timer = null;
		return function () {
			var args = arguments;
			var self = this;
			window.clearTimeout(timer);
			timer = window.setTimeout(function () {
				fn.apply(self, args);
			}, wait);
		};
	}

	function toast(message, isError) {
		var existing = document.querySelector('.hhd-toast');
		if (existing) {
			existing.parentNode.removeChild(existing);
		}

		var node = el('div', 'hhd-toast' + (isError ? ' hhd-toast--error' : ''), message);
		node.setAttribute('role', 'status');
		document.body.appendChild(node);

		window.setTimeout(function () {
			if (node.parentNode) {
				node.parentNode.removeChild(node);
			}
		}, 4000);
	}

	function closestElement(node, selector) {
		if (!node || node.nodeType !== 1) {
			return null;
		}
		return node.closest ? node.closest(selector) : null;
	}

	function parseSpec(raw, fallback) {
		if (!raw) {
			return fallback;
		}
		try {
			return JSON.parse(raw);
		} catch (error) {
			return fallback;
		}
	}

	/* ----------------------------------------------------------------------
	   REST client
	   ---------------------------------------------------------------------- */

	function api(method, path, data) {
		var base = config.restUrl.replace(/\/+$/, '');
		var url = base + '/' + String(path).replace(/^\/+/, '');
		var options = {
			method: method,
			credentials: 'same-origin',
			headers: {
				'X-WP-Nonce': config.nonce,
				'Content-Type': 'application/json'
			}
		};

		if (data) {
			options.body = JSON.stringify(data);
		}

		return window.fetch(url, options).then(function (response) {
			return response
				.json()
				.catch(function () {
					return null;
				})
				.then(function (body) {
					if (!response.ok) {
						throw new Error((body && body.message) || i18n.error);
					}
					return body;
				});
		});
	}

	/* ----------------------------------------------------------------------
	   Shared: which endpoint does this screen talk to?
	   ---------------------------------------------------------------------- */

	function endpoint() {
		if (config.screen === 'hhd-reviews') {
			return 'admin/reviews';
		}
		if (config.screen === 'hhd-notifications') {
			return 'admin/notifications';
		}
		return 'admin/requests';
	}

	/* ----------------------------------------------------------------------
	   Notification bell
	   ---------------------------------------------------------------------- */

	function initBell() {
		var bell = document.querySelector('[data-hhd-bell]');
		if (!bell) {
			return null;
		}

		var toggle = bell.querySelector('[data-hhd-bell-toggle]');
		var panel = bell.querySelector('.hhd-bell__panel');
		var list = bell.querySelector('[data-hhd-bell-list]');
		var count = bell.querySelector('[data-hhd-bell-count]');

		function setOpen(open) {
			if (!panel) {
				return;
			}
			panel.hidden = !open;
			if (toggle) {
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			}
		}

		/**
		 * Show the unread count.
		 *
		 * The bell badge and the dashboard's "unread notifications" counter
		 * are two views of one number, so both are set here rather than at
		 * each call site. They change from three places — a notification
		 * mutation, a bell refresh, and a list reload that carries stats —
		 * and setting them separately is how they drift apart.
		 */
		function setCount(unread) {
			if (typeof unread !== 'number') {
				return;
			}

			if (count) {
				count.textContent = faNum(unread);
				count.classList.toggle('is-empty', !unread);
			}

			var card = document.querySelector('[data-hhd-stat="notifications_unread"]');

			if (card) {
				card.textContent = faNum(unread);
			}
		}

		function refresh() {
			return api('GET', 'admin/notifications?per_page=8').then(function (body) {
				setCount(body.unread);
				if (list) {
					renderNotices(list, body.items, { compact: true });
				}
				return body;
			});
		}

		if (toggle) {
			toggle.addEventListener('click', function () {
				setOpen(panel.hidden);
			});
		}

		document.addEventListener('click', function (event) {
			if (!panel || panel.hidden) {
				return;
			}
			if (!bell.contains(event.target)) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && panel && !panel.hidden) {
				setOpen(false);
			}
		});

		return {
			refresh: refresh,
			setCount: setCount,
			setOpen: setOpen
		};
	}

	/* ----------------------------------------------------------------------
	   Notification rows
	   ---------------------------------------------------------------------- */

	function renderNotice(item) {
		var article = el('article', 'hhd-notice' + (item.is_read ? ' is-read' : ''));
		article.setAttribute('data-hhd-id', item.id);

		var body = el('div', 'hhd-notice__body');
		body.appendChild(el('p', 'hhd-notice__title', item.title));
		body.appendChild(el('p', 'hhd-notice__text', item.message));

		if (item.created_label) {
			body.appendChild(el('p', 'hhd-notice__date', item.created_label));
		}

		article.appendChild(body);

		var actions = el('div', 'hhd-notice__actions');

		if (item.url) {
			var link = el('a', 'hhd-btn hhd-btn--ghost hhd-btn--sm', 'مشاهده');
			link.href = item.url;
			actions.appendChild(link);
		}

		var toggle = el(
			'button',
			'hhd-btn hhd-btn--ghost hhd-btn--sm',
			item.is_read ? 'خوانده‌نشده' : 'خوانده شد'
		);
		toggle.type = 'button';
		toggle.setAttribute('data-hhd-action', item.is_read ? 'unread' : 'read');
		toggle.setAttribute('data-hhd-id', item.id);
		actions.appendChild(toggle);

		var remove = el('button', 'hhd-btn hhd-btn--ghost hhd-btn--sm hhd-btn--danger', 'حذف');
		remove.type = 'button';
		remove.setAttribute('data-hhd-action', 'delete');
		remove.setAttribute('data-hhd-id', item.id);
		actions.appendChild(remove);

		article.appendChild(actions);

		return article;
	}

	function renderNotices(container, items, options) {
		var settings = options || {};
		container.textContent = '';

		if (!items || !items.length) {
			var empty = el('div', 'hhd-empty');
			empty.appendChild(el('p', 'hhd-empty__title', settings.empty || i18n.empty));
			container.appendChild(empty);
			return;
		}

		items.forEach(function (item) {
			container.appendChild(renderNotice(item));
		});
	}

	/* ----------------------------------------------------------------------
	   Table cells and rows — mirror of hhd_admin_cell() in inc/admin-ui.php
	   ---------------------------------------------------------------------- */

	function renderCell(column, item, actions) {
		var cell = el('td');
		cell.setAttribute('data-hhd-key', column.key);

		var value = item[column.key];

		switch (column.type) {
			case 'badge':
				cell.appendChild(
					el(
						'span',
						'hhd-badge hhd-badge--' + value,
						item[column.key + '_label'] || value
					)
				);
				break;

			case 'ltr':
				var ltr = el('span', '', value);
				ltr.setAttribute('dir', 'ltr');
				cell.appendChild(ltr);
				break;

			case 'strong':
				cell.appendChild(el('strong', '', value));
				break;

			case 'actions':
				var group = el('div', 'hhd-row-actions');
				(actions || []).forEach(function (action) {
					var button = el(
						'button',
						'hhd-btn hhd-btn--' + (action.style || 'ghost') + ' hhd-btn--sm',
						action.label
					);
					button.type = 'button';
					button.setAttribute('data-hhd-action', action.action);
					button.setAttribute('data-hhd-status', action.status || '');
					button.setAttribute('data-hhd-id', item.id);
					group.appendChild(button);
				});
				cell.appendChild(group);
				break;

			default:
				cell.textContent = value === undefined || value === null ? '' : String(value);
		}

		return cell;
	}

	function renderRows(table, items, columns, actions) {
		var tbody = table.querySelector('[data-hhd-rows]');
		if (!tbody) {
			return;
		}

		tbody.textContent = '';

		if (!items || !items.length) {
			var row = el('tr', 'hhd-table__empty-row');
			var cell = el('td');
			cell.colSpan = columns.length;

			var empty = el('div', 'hhd-empty');
			empty.appendChild(el('p', 'hhd-empty__title', i18n.empty));
			empty.appendChild(el('p', 'hhd-empty__text', i18n.emptyText));
			cell.appendChild(empty);

			row.appendChild(cell);
			tbody.appendChild(row);
			return;
		}

		items.forEach(function (item) {
			var row = el('tr');
			row.setAttribute('data-hhd-id', item.id);

			columns.forEach(function (column) {
				row.appendChild(renderCell(column, item, actions));
			});

			tbody.appendChild(row);
		});
	}

	/* ----------------------------------------------------------------------
	   Pagination
	   ---------------------------------------------------------------------- */

	function renderPagination(container, page, perPage, total) {
		if (!container) {
			return;
		}

		container.textContent = '';

		var pages = Math.max(1, Math.ceil(total / perPage));

		if (total === 0) {
			return;
		}

		var wrapper = el('div', 'hhd-pagination');

		var prev = el('button', 'hhd-btn hhd-btn--ghost hhd-btn--sm', i18n.prev);
		prev.type = 'button';
		prev.setAttribute('data-hhd-page', String(page - 1));
		prev.disabled = page <= 1;
		wrapper.appendChild(prev);

		var info = i18n.page || 'صفحه %1$s از %2$s';
		wrapper.appendChild(
			el(
				'span',
				'hhd-pagination__info',
				info.replace('%1$s', faNum(page)).replace('%2$s', faNum(pages))
			)
		);

		var next = el('button', 'hhd-btn hhd-btn--ghost hhd-btn--sm', i18n.next);
		next.type = 'button';
		next.setAttribute('data-hhd-page', String(page + 1));
		next.disabled = page >= pages;
		wrapper.appendChild(next);

		container.appendChild(wrapper);
	}

	/* ----------------------------------------------------------------------
	   Modals
	   ---------------------------------------------------------------------- */

	function openModal(id) {
		var modal = document.getElementById(id);
		if (!modal) {
			return null;
		}
		if (typeof modal.showModal === 'function') {
			if (!modal.open) {
				modal.showModal();
			}
		} else {
			modal.setAttribute('open', '');
		}
		return modal;
	}

	function closeModal(modal) {
		if (!modal) {
			return;
		}
		if (typeof modal.close === 'function') {
			// close() on a dialog that is not open is a spec no-op, but the
			// check keeps a stray call from throwing before cleanup() reaches
			// its resolve().
			if (modal.open) {
				modal.close();
			}
		} else {
			modal.removeAttribute('open');
		}
	}

	/**
	 * Inspect a row.
	 *
	 * The values are read from the rendered cells rather than from a cached
	 * payload, so this works identically for server-rendered and
	 * JavaScript-rendered rows.
	 */
	function openViewModal(row, columns) {
		var modal = openModal('hhd-modal-view');
		if (!modal) {
			return;
		}

		var text = modal.querySelector('[data-hhd-modal-text]');
		if (!text) {
			return;
		}

		var list = el('dl', 'hhd-detail');

		columns.forEach(function (column) {
			if (column.type === 'actions' || !column.label) {
				return;
			}

			var cell = row.querySelector('[data-hhd-key="' + column.key + '"]');
			if (!cell) {
				return;
			}

			var value = (cell.textContent || '').trim();
			if (!value) {
				return;
			}

			var rowEl = el('div', 'hhd-detail__row');
			rowEl.appendChild(el('dt', 'hhd-detail__label', column.label));

			var dd = el('dd', 'hhd-detail__value', value);
			if (column.type === 'ltr') {
				dd.setAttribute('dir', 'ltr');
			}
			rowEl.appendChild(dd);

			list.appendChild(rowEl);
		});

		text.textContent = '';
		text.appendChild(list);

		// Dismissing needs no wiring here. hhd_admin_modals() renders the
		// inspector's one button as a submit inside the modal's
		// <form method="dialog">, so the browser itself closes it.
	}

	function confirmAction(message) {
		return new Promise(function (resolve) {
			var modal = openModal('hhd-modal-confirm');
			if (!modal) {
				resolve(false);
				return;
			}

			var text = modal.querySelector('[data-hhd-modal-text]');
			if (text) {
				text.textContent = message;
			}

			var confirm = modal.querySelector('[data-hhd-modal-confirm]');
			var cancel = modal.querySelector('[data-hhd-modal-cancel]');

			function cleanup(result) {
				if (confirm) {
					confirm.removeEventListener('click', onConfirm);
				}
				if (cancel) {
					cancel.removeEventListener('click', onCancel);
				}
				modal.removeEventListener('close', onDismiss);
				closeModal(modal);
				resolve(result);
			}

			function onConfirm() {
				cleanup(true);
			}

			function onCancel() {
				cleanup(false);
			}

			/**
			 * The dialog closed by some means other than a button — Escape, or
			 * the browser dismissing it.
			 *
			 * Settling here is not tidiness. A promise left pending keeps its
			 * listeners bound to the persistent modal, so the NEXT confirm
			 * fires this stale handler too: the reader cancels one delete with
			 * Escape, confirms another, and both rows are destroyed.
			 */
			function onDismiss() {
				cleanup(false);
			}

			if (confirm) {
				confirm.addEventListener('click', onConfirm);
			}
			if (cancel) {
				cancel.addEventListener('click', onCancel);
			}
			modal.addEventListener('close', onDismiss);
		});
	}

	/* ----------------------------------------------------------------------
	   Actions
	   ---------------------------------------------------------------------- */

	/**
	 * Endpoint a control should talk to.
	 *
	 * Resolved from the nearest ancestor that declares one, because the
	 * dashboard renders a request table and a review table side by side and
	 * a row action must reach the table it belongs to.
	 */
	function endpointFor(node) {
		var holder = closestElement(node, '[data-hhd-endpoint]');
		return holder ? holder.getAttribute('data-hhd-endpoint') : endpoint();
	}

	/**
	 * Column spec for the table a control lives in.
	 */
	function columnsFor(node) {
		var wrap = closestElement(node, '.hhd-table-wrap[data-hhd-columns]');
		return wrap ? parseSpec(wrap.getAttribute('data-hhd-columns'), []) : [];
	}

	function initActions(listController, bell) {
		/**
		 * Re-render after a successful mutation.
		 *
		 * The list is reloaded only when it is the list that changed. The
		 * dashboard renders a requests table and a reviews table at once and
		 * the controller is bound to the first of them, so refreshing it
		 * unconditionally would leave the mutated table showing stale rows.
		 * When the action belongs to another table the page reloads instead,
		 * which repaints every table and the counters together.
		 *
		 * Notification actions pass no reloadPage: when nothing in the list
		 * changed, refreshing the bell is the whole job.
		 */
		function afterChange(body, reloadPage, route) {
			if (bell) {
				if (body && typeof body.unread === 'number') {
					bell.setCount(body.unread);
				}
				bell.refresh().catch(function () {});
			}

			if (listController && listController.endpoint === route) {
				listController.reload();
			} else if (reloadPage || !bell) {
				window.location.reload();
			}
		}

		function run(trigger, action, id, status) {
			var route = endpointFor(trigger);

			if (action === 'view') {
				var row = trigger.closest('tr');
				if (row) {
					openViewModal(row, columnsFor(trigger));
				}
				return;
			}

			if (action === 'delete') {
				confirmAction(i18n.confirmDelete).then(function (confirmed) {
					if (!confirmed) {
						return;
					}
					api('DELETE', route + '/' + id)
						.then(function (body) {
							toast(i18n.saved);
							afterChange(body, true, route);
						})
						.catch(function (error) {
							toast(error.message, true);
						});
				});
				return;
			}

			if (action === 'read' || action === 'unread') {
				api('PATCH', route + '/' + id, { is_read: action === 'read' })
					.then(function (body) {
						afterChange(body, false, route);
					})
					.catch(function (error) {
						toast(error.message, true);
					});
				return;
			}

			if (action === 'read-all') {
				api('POST', route + '/read-all')
					.then(function (body) {
						toast(i18n.readAll);
						afterChange(body, false, route);
					})
					.catch(function (error) {
						toast(error.message, true);
					});
				return;
			}

			if (action === 'status' && status) {
				api('PATCH', route + '/' + id, { status: status })
					.then(function (body) {
						toast(i18n.saved);
						afterChange(body, true, route);
					})
					.catch(function (error) {
						toast(error.message, true);
					});
			}
		}

		// One delegated listener on the whole dashboard: it covers the
		// tables, the notification list and the dropdown, and each control
		// resolves its own endpoint from the DOM.
		document.addEventListener('click', function (event) {
			var trigger = closestElement(event.target, '[data-hhd-action]');
			if (!trigger) {
				return;
			}

			var action = trigger.getAttribute('data-hhd-action');

			if (action === 'read-all') {
				event.preventDefault();
			}

			run(
				trigger,
				action,
				parseInt(trigger.getAttribute('data-hhd-id'), 10),
				trigger.getAttribute('data-hhd-status')
			);
		});

		// "Mark all read" is also offered as a page-level button outside any
		// list, so give it its own hook.
		document.addEventListener('click', function (event) {
			var trigger = closestElement(event.target, '[data-hhd-read-all]');
			if (!trigger || trigger.hasAttribute('data-hhd-action')) {
				return;
			}
			event.preventDefault();
			run(trigger, 'read-all', 0, '');
		});

		/**
		 * Open the inspector for the row a notification points at.
		 *
		 * Notifications deep-link with ?view=<id>, which is what makes "a new
		 * review arrived" land on the review rather than merely on the right
		 * list. A no-op when the row is not on this page — a notification
		 * about a row that has since been deleted or paged away simply
		 * resolves to the list, which is where the reader was heading anyway.
		 */
		function openLinkedRow() {
			var id = parseInt(new URLSearchParams(window.location.search).get('view'), 10);

			if (!id) {
				return;
			}

			var row = document.querySelector('.hhd-table-wrap tr[data-hhd-id="' + id + '"]');

			if (row) {
				openViewModal(row, columnsFor(row));
			}
		}

		openLinkedRow();
	}

	/* ----------------------------------------------------------------------
	   List controller: filters, search, pagination
	   ---------------------------------------------------------------------- */

	function initList(bell) {
		// Only a table the server marked as a list is driven from here. The
		// dashboard's "recent requests" panels are previews of a fixed size,
		// so they carry no list marker and no pagination — actions taken on
		// them reload the page instead of trying to page a preview.
		var wrap = document.querySelector('.hhd-table-wrap[data-hhd-list]');
		var noticeList = document.querySelector('[data-hhd-notice-list]');

		if (!wrap && !noticeList) {
			return null;
		}

		var table = wrap ? wrap.querySelector('.hhd-table') : null;
		var columns = wrap ? parseSpec(wrap.getAttribute('data-hhd-columns'), []) : [];
		var actions = wrap ? parseSpec(wrap.getAttribute('data-hhd-actions'), []) : [];

		var filters = document.querySelector('[data-hhd-filters]');
		var pagination = document.querySelector('[data-hhd-pagination]');

		/**
		 * Page the server painted.
		 *
		 * The screens read ?paged= when they render the first paint, so
		 * assuming page 1 would mislabel the pagination — and make "next"
		 * step back to page 2 on a view already showing page 3.
		 */
		function startPage() {
			var paged = parseInt(new URLSearchParams(window.location.search).get('paged'), 10);

			return paged > 0 ? paged : 1;
		}

		var state = {
			page: startPage(),
			perPage: config.perPage || 20
		};

		function params() {
			var out = {
				page: state.page,
				per_page: state.perPage
			};

			if (config.requestType) {
				out.type = config.requestType;
			}

			if (filters) {
				var search = filters.querySelector('[data-hhd-search]');
				if (search && search.value.trim()) {
					out.search = search.value.trim();
				}

				Array.prototype.forEach.call(filters.querySelectorAll('[data-hhd-filter]'), function (control) {
					if (control.value) {
						out[control.getAttribute('data-hhd-filter')] = control.value;
					}
				});
			}

			return out;
		}

		/**
		 * Reflect the current view in the address bar.
		 *
		 * Merged into the existing query rather than replacing it: wp-admin
		 * addresses a screen by ?page=<slug>, so overwriting the query would
		 * send a refresh to the dashboard instead of back to this list.
		 *
		 * The API and the server spell two of these differently — the API
		 * takes ?page= and ?search=, while the screens read ?paged= and ?s=
		 * when they render the first paint. The URL is written in the
		 * server's spelling so a refresh reproduces the same view.
		 */
		var URL_KEYS = { page: 'paged', search: 's' };

		function syncUrl(query) {
			if (!window.history || !window.history.replaceState) {
				return;
			}

			var merged = new URLSearchParams(window.location.search);

			Object.keys(query).forEach(function (key) {
				var value = query[key];
				var name = URL_KEYS[key] || key;

				if (value === '' || value === null || value === undefined) {
					merged.delete(name);
				} else {
					merged.set(name, value);
				}
			});

			window.history.replaceState(null, '', window.location.pathname + '?' + merged.toString());
		}

		function render(body) {
			if (table) {
				renderRows(table, body.items, columns, actions);
			} else if (noticeList) {
				renderNotices(noticeList, body.items, {});
			}

			renderPagination(pagination, state.page, state.perPage, body.total);

			if (bell && body.stats && typeof body.stats.notifications_unread === 'number') {
				bell.setCount(body.stats.notifications_unread);
			}
		}

		function reload() {
			var query = params();

			if (wrap) {
				wrap.classList.add('hhd-loading');
			}

			return api('GET', endpoint() + '?' + new URLSearchParams(query).toString())
				.then(function (body) {
					render(body);
					syncUrl(query);
				})
				.catch(function (error) {
					toast(error.message, true);
				})
				.then(function () {
					if (wrap) {
						wrap.classList.remove('hhd-loading');
					}
				});
		}

		if (filters) {
			var search = filters.querySelector('[data-hhd-search]');
			if (search) {
				search.addEventListener(
					'input',
					debounce(function () {
						state.page = 1;
						reload();
					}, 350)
				);
			}

			Array.prototype.forEach.call(filters.querySelectorAll('[data-hhd-filter]'), function (control) {
				control.addEventListener('change', function () {
					state.page = 1;
					reload();
				});
			});
		}

		if (pagination) {
			pagination.addEventListener('click', function (event) {
				var button = closestElement(event.target, '[data-hhd-page]');
				if (!button || button.disabled) {
					return;
				}
				var page = parseInt(button.getAttribute('data-hhd-page'), 10);
				if (page > 0) {
					state.page = page;
					reload();
				}
			});
		}

		// The server rendered page one; controls are wired from here on.
		renderPagination(pagination, state.page, state.perPage, initialTotal());

		return {
			reload: reload,
			columns: columns,
			endpoint: endpoint()
		};
	}

	/**
	 * Total rows the server rendered with, read from the markup so
	 * pagination is correct before the first fetch.
	 */
	function initialTotal() {
		var holder =
			document.querySelector('.hhd-table-wrap[data-hhd-list][data-hhd-total]') ||
			document.querySelector('[data-hhd-pagination][data-hhd-total]');

		if (holder) {
			return parseInt(holder.getAttribute('data-hhd-total'), 10) || 0;
		}

		return 0;
	}

	/* ----------------------------------------------------------------------
	   Boot
	   ---------------------------------------------------------------------- */

	function boot() {
		var bell = initBell();
		var list = initList(bell);

		initActions(list, bell);
	}

	if (document.readyState !== 'loading') {
		boot();
	} else {
		document.addEventListener('DOMContentLoaded', boot);
	}
})();
