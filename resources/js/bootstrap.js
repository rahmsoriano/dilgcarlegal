/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const globalLoaderId = 'global-loading-overlay';
const globalLoader = {
    count: 0,
    showTimer: null,
    hideTimer: null,
    shownAt: 0,
};

const getGlobalLoaderEl = () => document.getElementById(globalLoaderId);

const setGlobalLoaderVisible = (visible) => {
    const el = getGlobalLoaderEl();
    if (!el) return;

    el.classList.toggle('hidden', !visible);
    el.setAttribute('aria-hidden', visible ? 'false' : 'true');
};

const globalLoaderStart = (options = {}) => {
    globalLoader.count += 1;
    if (globalLoader.count !== 1) return;

    if (globalLoader.hideTimer) {
        clearTimeout(globalLoader.hideTimer);
        globalLoader.hideTimer = null;
    }

    if (globalLoader.showTimer) clearTimeout(globalLoader.showTimer);
    const immediate = options && options.immediate === true;
    const delay = immediate ? 0 : 160;

    globalLoader.showTimer = setTimeout(() => {
        globalLoader.shownAt = Date.now();
        setGlobalLoaderVisible(true);
    }, delay);
};

const globalLoaderStop = () => {
    globalLoader.count = Math.max(0, globalLoader.count - 1);
    if (globalLoader.count !== 0) return;

    if (globalLoader.showTimer) {
        clearTimeout(globalLoader.showTimer);
        globalLoader.showTimer = null;
    }

    const minVisibleMs = 220;
    const elapsed = globalLoader.shownAt ? Date.now() - globalLoader.shownAt : 0;
    const delay = globalLoader.shownAt ? Math.max(0, minVisibleMs - elapsed) : 0;

    if (globalLoader.hideTimer) clearTimeout(globalLoader.hideTimer);
    globalLoader.hideTimer = setTimeout(() => {
        setGlobalLoaderVisible(false);
        globalLoader.shownAt = 0;
    }, delay);
};

window.__globalLoaderStart = globalLoaderStart;
window.__globalLoaderStop = globalLoaderStop;
window.__setGlobalLoading = (isLoading) => (isLoading ? globalLoaderStart() : globalLoaderStop());

const shouldTrackRequest = (method, url, headers) => {
    const m = String(method || 'get').toLowerCase();
    if (m === 'get') return false;

    const h = headers || {};
    const skip = h['X-Loader-Skip'] || h['x-loader-skip'];
    if (skip) return false;

    const u = String(url || '');
    if (/\/(messages|conversations)(\/|$)/.test(u)) return false;

    return true;
};

window.axios.interceptors.request.use((config) => {
    if (shouldTrackRequest(config.method, config.url, config.headers)) {
        globalLoaderStart();
        config.__globalLoaderTracked = true;
    }
    return config;
});

window.axios.interceptors.response.use(
    (response) => {
        if (response?.config?.__globalLoaderTracked) globalLoaderStop();
        return response;
    },
    (error) => {
        if (error?.config?.__globalLoaderTracked) globalLoaderStop();
        return Promise.reject(error);
    }
);

if (window.fetch) {
    const originalFetch = window.fetch.bind(window);
    window.fetch = async (...args) => {
        const input = args[0];
        const init = args[1] || {};
        const url = typeof input === 'string' ? input : input?.url;
        const method = init.method || (typeof input !== 'string' && input?.method) || 'get';
        const headers = init.headers || (typeof input !== 'string' && input?.headers) || {};
        const tracked = shouldTrackRequest(method, url, headers);

        if (tracked) globalLoaderStart();

        try {
            return await originalFetch(...args);
        } finally {
            if (tracked) globalLoaderStop();
        }
    };
}

const ensureConfirmOverlay = () => {
    let overlay = document.getElementById('confirm-overlay');
    if (overlay) return overlay;

    overlay = document.createElement('div');
    overlay.id = 'confirm-overlay';
    overlay.className = 'fixed inset-0 hidden items-center justify-center bg-slate-900/40 px-4';
    overlay.style.zIndex = '2147483300';
    overlay.innerHTML = `
        <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white/95 ring-1 ring-slate-900/10 backdrop-blur-xl shadow-[0_24px_70px_rgba(15,23,42,0.18)]">
            <div class="px-8 pt-7 pb-4">
                <div class="text-base font-black tracking-tight text-slate-900" data-confirm-title></div>
                <div class="mt-2 text-sm font-medium text-slate-600" data-confirm-message></div>
            </div>
            <div class="flex items-center justify-end gap-2 px-8 pb-7">
                <button type="button" class="h-10 rounded-2xl border border-slate-900/10 bg-white/60 px-4 text-xs font-black uppercase tracking-[0.18em] text-slate-700 hover:bg-white transition" data-confirm-cancel>Cancel</button>
                <button type="button" class="h-10 rounded-2xl bg-rose-600 px-5 text-xs font-black uppercase tracking-[0.18em] text-white hover:bg-rose-500 transition" data-confirm-ok>Delete</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    return overlay;
};

const confirmDialog = ({ title, message, okText, cancelText } = {}) => {
    const overlay = ensureConfirmOverlay();
    const titleEl = overlay.querySelector('[data-confirm-title]');
    const messageEl = overlay.querySelector('[data-confirm-message]');
    const okBtn = overlay.querySelector('[data-confirm-ok]');
    const cancelBtn = overlay.querySelector('[data-confirm-cancel]');

    if (!titleEl || !messageEl || !okBtn || !cancelBtn) {
        return Promise.resolve(false);
    }

    titleEl.textContent = String(title || 'Confirm');
    messageEl.textContent = String(message || '');
    okBtn.textContent = String(okText || 'Delete');
    cancelBtn.textContent = String(cancelText || 'Cancel');

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');

    return new Promise((resolve) => {
        let resolved = false;

        const cleanup = () => {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            overlay.removeEventListener('click', onOverlayClick);
            document.removeEventListener('keydown', onKeyDown);
            okBtn.removeEventListener('click', onOk);
            cancelBtn.removeEventListener('click', onCancel);
        };

        const finish = (value) => {
            if (resolved) return;
            resolved = true;
            cleanup();
            resolve(value);
        };

        const onOk = (e) => {
            e.preventDefault();
            finish(true);
        };

        const onCancel = (e) => {
            e.preventDefault();
            finish(false);
        };

        const onOverlayClick = (e) => {
            if (e.target === overlay) finish(false);
        };

        const onKeyDown = (e) => {
            if (e.key === 'Escape') finish(false);
        };

        okBtn.addEventListener('click', onOk);
        cancelBtn.addEventListener('click', onCancel);
        overlay.addEventListener('click', onOverlayClick);
        document.addEventListener('keydown', onKeyDown);

        cancelBtn.focus();
    });
};

window.__confirmDialog = confirmDialog;

const isDeleteForm = (form) => {
    if (form.hasAttribute('data-confirm-skip')) return false;
    const override = form.getAttribute('data-confirm');
    if (override && override.toLowerCase() === 'delete') return true;
    if (form.hasAttribute('data-confirm-delete')) return true;
    const method = form.querySelector('input[name="_method"]')?.value || '';
    return String(method).toUpperCase() === 'DELETE';
};

document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!isDeleteForm(form)) return;

    if (form.dataset.confirmed === '1') {
        delete form.dataset.confirmed;
        return;
    }

    e.preventDefault();

    const ok = await confirmDialog({
        title: form.getAttribute('data-confirm-title') || 'Confirm',
        message: form.getAttribute('data-confirm-message') || 'Delete this item?',
        okText: form.getAttribute('data-confirm-ok') || 'Delete',
        cancelText: form.getAttribute('data-confirm-cancel') || 'Cancel',
    });

    if (!ok) return;
    form.dataset.confirmed = '1';
    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
    } else {
        HTMLFormElement.prototype.submit.call(form);
    }
}, true);

document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (e.defaultPrevented) return;
    if (form.hasAttribute('data-loader-skip')) return;
    if (isDeleteForm(form) && form.dataset.confirmed !== '1') return;
    globalLoaderStart({ immediate: true });
}, true);

document.addEventListener('click', (e) => {
    const target = e.target;
    if (!(target instanceof Element)) return;

    const link = target.closest('a');
    if (!link) return;
    if (link.hasAttribute('data-loader-skip')) return;
    if (link.target && link.target !== '_self') return;
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    if (!link.href || link.href.startsWith('javascript:') || link.href === '#') return;

    try {
        const linkUrl = new URL(link.href, window.location.href);
        if (linkUrl.origin !== window.location.origin) return;
        if (link.closest('#opinions-results') && linkUrl.pathname === window.location.pathname && linkUrl.searchParams.has('page')) {
            return;
        }
    } catch {
        return;
    }

    globalLoaderStart({ immediate: true });
}, true);

const authModal = {
    el: null,
    panel: null,
    lastFocus: null,
    isOpen: false,
    keydownHandler: null,
};

const getAuthModalEls = () => {
    if (!authModal.el) authModal.el = document.getElementById('auth-login-modal');
    if (authModal.el && !authModal.panel) authModal.panel = authModal.el.querySelector('.auth-modal-panel');
    return { el: authModal.el, panel: authModal.panel };
};

const getFocusable = (root) => {
    const nodes = Array.from(root.querySelectorAll('a[href],button:not([disabled]),textarea,input,select,[tabindex]:not([tabindex="-1"])'));
    return nodes.filter((el) => el.offsetParent !== null && !el.hasAttribute('disabled') && el.getAttribute('aria-hidden') !== 'true');
};

const initAuthViews = (scopeEl) => {
    const roots = Array.from(scopeEl.querySelectorAll('[data-auth-views-root]'));
    roots.forEach((root) => {
        const views = Array.from(root.querySelectorAll('[data-view]'));
        const show = (mode) => {
            views.forEach((v) => v.setAttribute('aria-hidden', v.getAttribute('data-view') === mode ? 'false' : 'true'));
        };
        show(root.getAttribute('data-initial-mode') || 'login');
        root.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-auth-switch]');
            if (!btn) return;
            e.preventDefault();
            show(btn.getAttribute('data-auth-switch'));
        });
    });
};

const openAuthModal = () => {
    const { el, panel } = getAuthModalEls();
    if (!el || !panel) return;
    if (authModal.isOpen) return;

    authModal.isOpen = true;
    authModal.lastFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;

    el.classList.remove('hidden');
    el.setAttribute('aria-hidden', 'false');
    document.body.classList.add('auth-modal-open');

    initAuthViews(el);

    requestAnimationFrame(() => {
        el.classList.add('is-open');
        const email = el.querySelector('input[name="email"]');
        if (email instanceof HTMLElement) {
            email.focus();
        } else {
            const focusables = getFocusable(panel);
            focusables[0]?.focus();
        }
    });

    authModal.keydownHandler = (e) => {
        if (!authModal.isOpen) return;

        if (e.key === 'Escape') {
            e.preventDefault();
            closeAuthModal();
            return;
        }

        if (e.key === 'Tab') {
            const focusables = getFocusable(panel);
            if (focusables.length === 0) return;
            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            const active = document.activeElement;

            if (e.shiftKey) {
                if (active === first || !panel.contains(active)) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                if (active === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    };

    document.addEventListener('keydown', authModal.keydownHandler, true);
};

const closeAuthModal = () => {
    const { el } = getAuthModalEls();
    if (!el) return;
    if (!authModal.isOpen) return;

    authModal.isOpen = false;
    el.classList.remove('is-open');
    el.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('auth-modal-open');

    if (authModal.keydownHandler) {
        document.removeEventListener('keydown', authModal.keydownHandler, true);
        authModal.keydownHandler = null;
    }

    const cleanup = () => {
        el.classList.add('hidden');
        if (authModal.lastFocus) authModal.lastFocus.focus();
        authModal.lastFocus = null;
    };

    window.setTimeout(cleanup, 180);
};

document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-open-login-modal]');
    if (trigger) {
        e.preventDefault();
        openAuthModal();
        return;
    }

    const { el } = getAuthModalEls();
    if (!el || el.classList.contains('hidden')) return;

    const closeBtn = e.target.closest('[data-auth-modal-close]');
    if (closeBtn) {
        e.preventDefault();
        closeAuthModal();
    }
}, true);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
