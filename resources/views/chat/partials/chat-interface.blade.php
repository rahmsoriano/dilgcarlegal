@php
    $isPro = ($theme ?? '') === 'pro';
    $canUseDocumentReview = auth()->check();
    $canSelectOpinionForReview = auth()->check() && auth()->user()?->is_admin;
@endphp

<style>
    .chat-shell {
        background: {{ $isPro ? 'transparent' : 'radial-gradient(circle at top left, rgba(14, 165, 233, 0.14), transparent 28%), radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.14), transparent 32%), linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%)' }};
    }

    .chat-shell-with-lex {
        position: relative;
        isolation: isolate;
    }

    .chat-panel {
        position: relative;
        backdrop-filter: none;
        background: #ffffff;
        box-shadow: none;
        border: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pro-input-wrapper {
        background: rgba(248, 250, 252, 0.9);
        border: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .pro-input-wrapper:focus-within {
        background: rgba(255, 255, 255, 1);
        border-color: rgba(99, 102, 241, 0.45);
        box-shadow: 0 0 30px rgba(99, 102, 241, 0.14);
    }

    .message-bubble-user {
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
        box-shadow: 0 12px 30px -10px rgba(37, 99, 235, 0.35);
    }

    .message-bubble-ai {
        background: transparent;
        border: 0;
        backdrop-filter: none;
    }

    .chat-user-bubble {
        position: relative;
    }

    .chat-msg-tools {
        position: absolute;
        right: 10px;
        bottom: -20px;
        display: flex;
        align-items: center;
        gap: 6px;
        opacity: 0;
        transform: translateY(-2px);
        pointer-events: none;
        transition: opacity 180ms ease-in-out, transform 180ms ease-in-out;
        z-index: 5;
    }

    .chat-user-bubble:hover .chat-msg-tools {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .chat-msg-tools-btn {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        border: 1px solid rgba(15, 23, 42, 0.14);
        background: rgba(255, 255, 255, 0.92);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        cursor: pointer;
        position: relative;
    }

    .chat-msg-tools-btn svg {
        width: 18px;
        height: 18px;
        color: rgba(15, 23, 42, 0.72);
    }

    .chat-msg-tools-btn::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 50%;
        top: 100%;
        transform: translate(-50%, 8px);
        padding: 6px 10px;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.92);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.01em;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 140ms ease-in-out, transform 140ms ease-in-out;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
    }

    .chat-msg-tools-btn:hover::after {
        opacity: 1;
        transform: translate(-50%, 12px);
    }

    .chat-user-bubble.is-editing .chat-msg-tools {
        display: none;
    }

    .message-enter.is-editing-wide {
        max-width: none !important;
        width: 100% !important;
    }

    .chat-user-bubble.is-editing {
        width: min(1100px, 100%);
        margin-left: auto;
        background: rgba(0, 44, 118, 0.12) !important;
        border: 1px solid rgba(0, 44, 118, 0.18) !important;
        border-radius: 26px !important;
        padding: 18px 18px 16px !important;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.14) !important;
    }

    .chat-edit-textarea {
        width: 100%;
        min-height: 130px;
        resize: none;
        border: 1px solid rgba(0, 44, 118, 0.25);
        outline: none;
        background: rgba(255, 255, 255, 0.85);
        color: rgba(15, 23, 42, 0.95);
        font: inherit;
        line-height: 1.4;
        padding: 16px 18px;
        border-radius: 20px;
    }

    .chat-user-bubble.is-editing .chat-edit-textarea {
        background: rgba(255, 255, 255, 0.92);
    }

    .chat-edit-actions {
        margin-top: 14px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .chat-edit-action-btn {
        height: 38px;
        padding: 0 18px;
        border-radius: 9999px;
        border: 1px solid rgba(0, 44, 118, 0.22);
        background: rgba(255, 255, 255, 0.75);
        color: rgba(15, 23, 42, 0.92);
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
    }

    .chat-edit-action-btn.primary {
        background: rgba(0, 44, 118, 0.92);
        border-color: rgba(0, 44, 118, 0.92);
        color: #ffffff;
    }

    .ref-accordion {
        margin-top: 1px;
        padding: 6px 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 14px;
        cursor: pointer;
        outline: none;
    }

    .ref-accordion:hover {
        background: rgba(0, 44, 118, 0.06);
    }

    .ref-accordion:focus-visible {
        background: rgba(0, 44, 118, 0.06);
        box-shadow: 0 0 0 4px rgba(0, 44, 118, 0.14);
    }

    .ref-accordion-head {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ref-accordion-arrow {
        flex: 0 0 auto;
        color: rgba(15, 23, 42, 0.65);
    }

    .ref-accordion-title {
        flex: 1 1 auto;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ref-accordion-toggle,
    .ref-accordion-chevron {
        display: none !important;
    }

    .ref-accordion-body {
        margin-top: 4px;
        white-space: normal;
        color: rgba(15, 23, 42, 0.82);
        line-height: 1.35;
        overflow: hidden;
        max-height: calc(1 * 1.35em);
        transition: max-height 260ms ease-in-out;
        position: relative;
    }

    .ref-accordion:not(.is-open) .ref-accordion-body {
        white-space: nowrap;
        text-overflow: ellipsis;
        -webkit-mask-image: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 1) 0%,
            rgba(0, 0, 0, 1) 28%,
            rgba(0, 0, 0, 0.4) 52%,
            rgba(0, 0, 0, 0) 100%
        );
        mask-image: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 1) 0%,
            rgba(0, 0, 0, 1) 28%,
            rgba(0, 0, 0, 0.4) 52%,
            rgba(0, 0, 0, 0) 100%
        );
    }

    .ref-accordion.is-open .ref-accordion-body {
        max-height: 1200px;
        white-space: normal;
        -webkit-mask-image: none;
        mask-image: none;
    }

    .external-source-link {
        font-style: italic;
        color: #2563eb;
        text-decoration: underline;
        word-break: break-all;
    }

    hr.chat-section-divider {
        border: 0;
        border-top: 1px solid rgba(15, 23, 42, 0.10);
        margin: 14px 0;
    }

    .chat-scroll-bottom-btn {
        position: absolute;
        left: 50%;
        bottom: clamp(5.75rem, 8vw, 6.75rem);
        transform: translateX(-50%) translateY(10px);
        z-index: 80;
        width: 44px;
        height: 44px;
        border-radius: 9999px;
        border: 1px solid rgba(15, 23, 42, 0.10);
        background: rgba(255, 255, 255, 0.95);
        color: rgba(15, 23, 42, 0.82);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 220ms ease, transform 260ms ease, background-color 180ms ease, border-color 180ms ease;
        backdrop-filter: blur(10px);
    }

    .chat-scroll-bottom-btn.is-visible {
        opacity: 1;
        pointer-events: auto;
        transform: translateX(-50%) translateY(0);
    }

    .chat-scroll-bottom-btn:hover {
        background: rgba(255, 255, 255, 1);
        border-color: rgba(0, 44, 118, 0.25);
    }

    .chat-scroll-bottom-btn:active {
        transform: translateX(-50%) translateY(1px);
    }

    .chat-scroll-bottom-btn svg {
        width: 20px;
        height: 20px;
        display: block;
    }

    body.opinion-modal-open .chat-scroll-bottom-btn {
        opacity: 0 !important;
        pointer-events: none !important;
        transform: translateX(-50%) translateY(10px) !important;
    }

    .chat-suggestions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: opacity 220ms ease, transform 220ms ease, max-height 260ms ease, margin 260ms ease;
        max-height: 180px;
        overflow: hidden;
    }

    .chat-suggestions.is-fading {
        opacity: 0;
        transform: translateY(8px);
        pointer-events: none;
        max-height: 0;
        margin-bottom: 0 !important;
    }

    .chat-suggestions__title {
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        line-height: 1.2;
        color: #6f87bd;
    }

    .chat-suggestions__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(300px, 1fr));
        gap: 12px 16px;
        width: min(960px, 100%);
    }

    .chat-suggestion-btn {
        min-width: 0;
        min-height: 46px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 0 16px 0 20px;
        border-radius: 12px;
        border: 1px solid #cbd8ee;
        background: rgba(255, 255, 255, 0.88);
        color: #1e5fc8;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.25;
        text-align: left;
        box-shadow: 0 2px 7px rgba(15, 23, 42, 0.06);
        transition: background-color 160ms ease, border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
    }

    .chat-suggestion-btn:hover {
        border-color: #b7c8e5;
        background: #ffffff;
        box-shadow: 0 5px 14px rgba(37, 99, 235, 0.10);
        transform: translateY(-1px);
    }

    .chat-suggestion-btn:focus-visible {
        outline: none;
        border-color: rgba(37, 99, 235, 0.55);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
    }

    .chat-suggestion-btn svg {
        width: 17px;
        height: 17px;
        flex: 0 0 auto;
        color: #6f87bd;
        transition: transform 160ms ease;
    }

    .chat-suggestion-btn:hover svg {
        transform: translateX(2px);
    }

    .chat-suggestion-btn span {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    @media (max-width: 760px) {
        .chat-suggestions__grid {
            grid-template-columns: 1fr;
        }

        .chat-suggestions {
            max-height: 320px;
        }
    }

    @keyframes chat-fade-in {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-reply-fade-in {
        animation: chat-fade-in 260ms ease-out both;
    }

    @keyframes chat-dot-blink {
        0%, 80%, 100% { opacity: 0.25; }
        40% { opacity: 1; }
    }

    .chat-thinking-dots span {
        display: inline-block;
        width: 0.5em;
        text-align: center;
        animation: chat-dot-blink 1.2s infinite;
    }
    .chat-thinking-dots span:nth-child(2) { animation-delay: 0.2s; }
    .chat-thinking-dots span:nth-child(3) { animation-delay: 0.4s; }

    .chat-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .chat-scrollbar::-webkit-scrollbar-thumb {
        background: {{ $isPro ? 'rgba(15, 23, 42, 0.12)' : 'rgba(148, 163, 184, 0.45)' }};
        border-radius: 999px;
    }

    .chat-scrollbar::-webkit-scrollbar-thumb:hover {
        background: {{ $isPro ? 'rgba(15, 23, 42, 0.2)' : 'rgba(148, 163, 184, 0.6)' }};
    }

    @keyframes pulse-glow {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }

    .glow-dot {
        position: absolute;
        width: 4px;
        height: 4px;
        background: #2563eb;
        border-radius: 50%;
        filter: blur(2px);
        animation: pulse-glow 2s infinite;
    }

    .chat-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-faq-paragraph + .chat-faq-paragraph {
        margin-top: 0.9rem;
    }

    .chat-tool-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.9rem 1.35rem 1.1rem;
        border-top: 1px solid rgba(15, 23, 42, 0.06);
        background: linear-gradient(180deg, rgba(251, 253, 255, 0.88) 0%, rgba(247, 250, 255, 0.96) 100%);
    }

    .chat-tool-actions {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .chat-doc-review-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        border-radius: 9999px;
        border: 1px solid rgba(0, 44, 118, 0.10);
        background: rgba(255, 255, 255, 0.84);
        padding: 0.55rem 0.9rem;
        font-size: 0.78rem;
        font-weight: 800;
        color: #1f3b77;
        transition: border-color 180ms ease, background-color 180ms ease, color 180ms ease, box-shadow 180ms ease;
    }

    .chat-doc-review-chip:hover {
        border-color: rgba(37, 99, 235, 0.18);
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.08);
    }

    .chat-doc-review-chip[aria-expanded="true"] {
        background: rgba(0, 44, 118, 0.08);
        border-color: rgba(0, 44, 118, 0.18);
        color: #113273;
    }

    .chat-doc-review-copy {
        font-size: 0.8rem;
        line-height: 1.45;
        color: #64748b;
        flex: 1 1 auto;
        min-width: 0;
    }

    .chat-doc-review-panel {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        border-top: 1px solid transparent;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.84) 0%, rgba(255, 255, 255, 0.98) 100%);
        transition: max-height 260ms ease, opacity 220ms ease, border-color 220ms ease;
    }

    .chat-doc-review-panel.is-open {
        max-height: 420px;
        opacity: 1;
        border-top-color: rgba(15, 23, 42, 0.08);
    }

    .chat-doc-review-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr);
        gap: 0.9rem;
        padding: 1rem 1.35rem 1.2rem;
    }

    .chat-doc-review-grid--upload-only {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    }

    .chat-doc-review-field {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .chat-doc-review-label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #64748b;
    }

    .chat-doc-review-input {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(15, 23, 42, 0.10);
        background: rgba(255, 255, 255, 0.95);
        color: #0f172a;
        font-size: 0.9rem;
        line-height: 1.4;
        padding: 0.8rem 1rem;
    }

    .chat-doc-review-input:focus {
        outline: none;
        border-color: rgba(37, 99, 235, 0.35);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.10);
    }

    .chat-doc-review-upload-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        min-height: 48px;
        border-radius: 1rem;
        border: 1px dashed rgba(0, 44, 118, 0.18);
        background: rgba(255, 255, 255, 0.96);
        color: #173879;
        font-size: 0.92rem;
        font-weight: 700;
        transition: border-color 180ms ease, background-color 180ms ease, transform 180ms ease;
    }

    .chat-doc-review-upload-btn:hover {
        border-color: rgba(37, 99, 235, 0.35);
        background: #ffffff;
        transform: translateY(-1px);
    }

    .chat-doc-review-submit {
        min-height: 48px;
        border-radius: 1rem;
        background: linear-gradient(135deg, #002c76 0%, #1947a6 100%);
        color: #ffffff;
        font-size: 0.92rem;
        font-weight: 800;
        box-shadow: 0 18px 32px rgba(0, 44, 118, 0.16);
        transition: transform 180ms ease, box-shadow 180ms ease, opacity 180ms ease;
    }

    .chat-doc-review-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 36px rgba(0, 44, 118, 0.22);
    }

    .chat-doc-review-status {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(15, 23, 42, 0.06);
    }

    .chat-doc-review-status-text {
        font-size: 0.8rem;
        color: #475569;
        line-height: 1.45;
    }

    .chat-doc-review-clear {
        flex: 0 0 auto;
        border: 0;
        background: transparent;
        color: #1d4ed8;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    @media (max-width: 900px) {
        .chat-tool-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .chat-doc-review-grid {
            grid-template-columns: 1fr;
        }

        .chat-doc-review-status {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    .message-enter {
        animation: message-in 220ms ease-out;
    }

    @keyframes message-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .lex-safe-zone {
        padding-right: 0;
    }

    .lex-assistant {
        --lex-size: clamp(200px, 16vw, 250px);
        position: fixed;
        right: clamp(1rem, 2vw, 2rem);
        bottom: clamp(5.75rem, 8vw, 7.5rem);
        z-index: 60;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.85rem;
        pointer-events: none;
    }

    .lex-assistant__bubble {
        position: relative;
        max-width: 260px;
        padding: 1.15rem 1.15rem 1.05rem;
        border-radius: 1.75rem;
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 22px 44px rgba(15, 23, 42, 0.12);
        color: #0f172a;
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.5;
        text-align: center;
        backdrop-filter: blur(18px);
        opacity: 0;
        transform: translateY(12px) scale(0.96);
        transform-origin: bottom right;
        transition: opacity 220ms ease, transform 260ms cubic-bezier(0.2, 0.8, 0.2, 1);
        pointer-events: none;
    }

    .lex-assistant__bubble::after {
        content: '';
        position: absolute;
        right: 2.2rem;
        bottom: -0.68rem;
        width: 1.25rem;
        height: 1.25rem;
        background: inherit;
        border-right: 1px solid rgba(148, 163, 184, 0.18);
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        transform: rotate(45deg);
        border-bottom-right-radius: 0.3rem;
    }

    .lex-assistant__bubble.is-visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .lex-assistant__button {
        position: relative;
        width: var(--lex-size);
        display: flex;
        justify-content: center;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
        pointer-events: auto;
        outline: none;
        animation: lex-float 4.8s ease-in-out infinite;
    }

    .lex-assistant__button:focus-visible {
        border-radius: 999px;
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0.18);
    }

    .lex-assistant__figure {
        position: relative;
        width: 100%;
        transform-origin: center bottom;
        animation: lex-breathe 4.8s ease-in-out infinite;
        transition: transform 220ms ease, filter 220ms ease;
        will-change: transform;
        filter: drop-shadow(0 20px 28px rgba(15, 23, 42, 0.16));
    }

    .lex-assistant__button:hover .lex-assistant__figure,
    .lex-assistant__button:focus-visible .lex-assistant__figure {
        transform: scale(1.05);
        filter: drop-shadow(0 26px 34px rgba(15, 23, 42, 0.22));
    }

    .lex-assistant__button.is-idle .lex-assistant__figure {
        animation: lex-wave 900ms cubic-bezier(0.2, 0.8, 0.2, 1) 1;
    }

    .lex-assistant__image {
        display: block;
        width: 100%;
        height: auto;
        user-select: none;
        -webkit-user-drag: none;
        transform: translateX(2px);
    }

    .lex-assistant__shadow {
        width: calc(var(--lex-size) * 0.58);
        height: 1.25rem;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(148, 163, 184, 0.32) 0%, rgba(148, 163, 184, 0.14) 50%, rgba(148, 163, 184, 0) 78%);
        transform: translateY(-0.45rem);
        animation: lex-shadow 4.8s ease-in-out infinite;
        pointer-events: none;
    }

    .opinion-modal-overlay {
        background: radial-gradient(circle at 18% 20%, rgba(59, 130, 246, 0.22), transparent 45%),
            radial-gradient(circle at 84% 18%, rgba(99, 102, 241, 0.22), transparent 48%),
            rgba(15, 23, 42, 0.52);
        backdrop-filter: blur(10px);
    }

    .opinion-modal-panel {
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 40px 100px rgba(15, 23, 42, 0.30);
        border: 1px solid rgba(15, 23, 42, 0.10);
        will-change: transform, opacity;
        backface-visibility: hidden;
        transform: translateZ(0);
        contain: layout paint;
    }

    #opinion-modal {
        opacity: 0;
        pointer-events: none;
        transition: opacity 220ms ease;
    }

    #opinion-modal.is-open {
        opacity: 1;
        pointer-events: auto;
    }

    #opinion-modal.is-closing {
        opacity: 1;
        pointer-events: none;
    }

    #opinion-modal .opinion-modal-overlay {
        opacity: 0;
        transition: opacity 220ms ease;
    }

    #opinion-modal.is-open .opinion-modal-overlay {
        opacity: 1;
    }

    #opinion-modal .opinion-modal-panel {
        opacity: 0;
        transform: translateY(14px) scale(0.98);
        transition: opacity 240ms ease, transform 320ms cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    #opinion-modal.is-open .opinion-modal-panel {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    #opinion-modal.is-closing .opinion-modal-overlay,
    #opinion-modal.is-closing .opinion-modal-panel {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    @media (prefers-reduced-motion: reduce) {
        #opinion-modal,
        #opinion-modal .opinion-modal-overlay,
        #opinion-modal .opinion-modal-panel {
            transition: none !important;
        }

        #opinion-modal .opinion-modal-panel {
            transform: none !important;
        }
    }

    .opinion-modal-header {
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 18px 20px 14px;
        background: rgba(255, 255, 255, 0.90);
        backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    }

    .opinion-modal-number {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #1d4ed8;
        background: rgba(37, 99, 235, 0.08);
        border: 1px solid rgba(37, 99, 235, 0.16);
    }

    .opinion-modal-title {
        margin-top: 10px;
        font-size: 26px;
        line-height: 1.15;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .opinion-modal-date {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(15, 23, 42, 0.58);
    }

    .opinion-modal-close {
        width: 40px;
        height: 40px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(15, 23, 42, 0.10);
        color: rgba(15, 23, 42, 0.65);
        transition: background 160ms ease, color 160ms ease, transform 160ms ease;
    }

    .opinion-modal-close:hover {
        background: rgba(15, 23, 42, 0.10);
        color: rgba(15, 23, 42, 0.78);
        transform: translateY(-1px);
    }

    .opinion-modal-close:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 44, 118, 0.18);
    }

    .opinion-modal-scroll {
        padding: 18px 20px 22px;
    }

    @media (min-width: 640px) {
        .opinion-modal-header {
            padding: 22px 26px 16px;
        }

        .opinion-modal-scroll {
            padding: 22px 26px 28px;
        }
    }

    @keyframes lex-float {
        0%, 100% { transform: translate3d(0, 0, 0); }
        50% { transform: translate3d(0, -10px, 0); }
    }

    @keyframes lex-breathe {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.03); }
    }

    @keyframes lex-shadow {
        0%, 100% { transform: translateY(-0.45rem) scaleX(1); opacity: 0.8; }
        50% { transform: translateY(-0.25rem) scaleX(0.93); opacity: 0.52; }
    }

    @keyframes lex-wave {
        0% { transform: rotate(0deg) scale(1.01); }
        25% { transform: rotate(-3deg) scale(1.03); }
        50% { transform: rotate(2deg) scale(1.03); }
        75% { transform: rotate(-1deg) scale(1.02); }
        100% { transform: rotate(0deg) scale(1); }
    }

    @media (min-width: 1280px) {
        .lex-safe-zone { padding-right: 0; }
    }

    @media (max-width: 1024px) {
        .lex-assistant {
            --lex-size: 190px;
            right: 1rem;
            bottom: 6rem;
        }

        .lex-safe-zone { padding-right: 0; }
    }

    @media (max-width: 768px) {
        .lex-assistant {
            --lex-size: 146px;
            right: 0.8rem;
            bottom: 5.6rem;
        }

        .lex-assistant__bubble {
            max-width: 205px;
            font-size: 0.88rem;
            padding: 0.9rem 0.9rem 0.82rem;
        }

        .lex-safe-zone { padding-right: 0; }
    }

    @media (prefers-reduced-motion: reduce) {
        .lex-assistant__button,
        .lex-assistant__figure,
        .lex-assistant__shadow,
        .lex-assistant__button.is-idle .lex-assistant__figure {
            animation: none !important;
        }

        .lex-assistant__bubble,
        .lex-assistant__figure {
            transition: none !important;
        }
    }

    .chat-shell {
        background: {{ $isPro ? 'radial-gradient(circle at 14% 18%, rgba(77, 125, 255, 0.14), transparent 18%), radial-gradient(circle at 86% 12%, rgba(15, 58, 165, 0.08), transparent 22%), radial-gradient(circle at 50% 100%, rgba(191, 219, 254, 0.28), transparent 24%), linear-gradient(180deg, #f9fbff 0%, #eff4fc 100%)' : 'radial-gradient(circle at top left, rgba(14, 165, 233, 0.14), transparent 28%), radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.14), transparent 32%), linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%)' }};
    }

    .chat-panel {
        backdrop-filter: blur(18px);
        background: rgba(255, 255, 255, 0.44);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.72);
    }

    .pro-input-wrapper {
        background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,251,255,0.96) 100%);
        border: 1px solid rgba(196, 212, 241, 0.80);
        border-radius: 1.75rem;
        box-shadow: 0 20px 42px rgba(16, 36, 89, 0.08);
    }

    .pro-input-wrapper:focus-within {
        border-color: rgba(59, 130, 246, 0.42);
        box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.10), 0 24px 48px rgba(20, 54, 120, 0.14);
    }

    .chat-empty-state {
        position: relative;
        width: 100%;
        max-width: 1100px;
        padding-bottom: 0.35rem;
        margin-left: auto;
        margin-right: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .chat-empty-state::before {
        content: "";
        position: absolute;
        inset: 2rem 10% auto;
        height: 340px;
        background:
            radial-gradient(circle at center, rgba(59, 130, 246, 0.11), transparent 36%),
            radial-gradient(circle at 0% 0%, rgba(255, 255, 255, 0.92), transparent 32%);
        pointer-events: none;
        filter: blur(10px);
    }

    .chat-empty-state__brand {
        position: relative;
        z-index: 1;
    }

    .chat-empty-state__brand::before {
        content: "";
        position: absolute;
        inset: 50%;
        width: 138px;
        height: 138px;
        transform: translate(-50%, -50%);
        border-radius: 999px;
        background: radial-gradient(circle, rgba(59,130,246,0.18) 0%, rgba(59,130,246,0.06) 48%, transparent 72%);
        filter: blur(12px);
    }

    .chat-empty-state__cards {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: repeat(4, minmax(220px, 1fr));
        gap: 24px;
        width: 100%;
        margin-top: 1.5rem;
        margin-left: auto;
        margin-right: auto;
        justify-content: center;
    }

    .chat-empty-state {
        min-height: auto;
        justify-content: flex-start;
        padding-top: 7.5rem;
        padding-bottom: 0.75rem;
    }

    .chat-feature-card {
        min-height: 224px;
        padding: 26px 22px 22px;
        border-radius: 20px;
        border: 1px solid rgba(205, 220, 244, 0.92);
        background: linear-gradient(180deg, rgba(255,255,255,0.88) 0%, rgba(249,251,255,0.82) 100%);
        box-shadow: 0 18px 38px rgba(16, 36, 89, 0.08);
        backdrop-filter: blur(16px);
        transition: transform 200ms ease, box-shadow 200ms ease, border-color 200ms ease;
    }

    .chat-feature-card:hover {
        transform: translateY(-4px);
        border-color: rgba(145, 178, 241, 0.92);
        box-shadow: 0 24px 48px rgba(24, 60, 129, 0.12);
    }

    .chat-feature-card__icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 18px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
    }

    .chat-feature-card__icon svg {
        width: 34px;
        height: 34px;
    }

    .chat-feature-card__title {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #12275c;
    }

    .chat-feature-card__copy {
        margin-top: 10px;
        font-size: 16px;
        line-height: 1.7;
        color: #62759a;
    }

    .chat-suggestions {
        gap: 12px;
        align-items: center;
        margin-top: 24px;
        width: 100%;
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
    }

    .chat-suggestions__title {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 15px;
        color: #1f4fa8;
        text-align: center;
        width: 100%;
    }

    .chat-suggestions__grid {
        gap: 12px 16px;
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    #chat-form {
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
    }

    #chat-form > * {
        width: 100%;
    }

    .chat-suggestion-btn {
        min-height: 44px;
        padding: 0 16px 0 18px;
        border-radius: 999px;
        border-color: #cfddf4;
        box-shadow: 0 12px 26px rgba(17, 42, 95, 0.06);
        font-size: 15px;
    }

    .chat-suggestion-btn:hover {
        border-color: #9fc0f4;
        box-shadow: 0 18px 30px rgba(37, 99, 235, 0.10);
        transform: translateY(-2px);
    }

    .chat-composer-shell {
        border-radius: 30px;
        background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(249,251,255,0.95) 100%);
        border: 1px solid rgba(204, 220, 244, 0.84);
        box-shadow: 0 22px 44px rgba(18, 48, 108, 0.10);
    }

    .chat-send-premium {
        width: 58px;
        height: 58px;
        border-radius: 20px;
        background: linear-gradient(135deg, #1d5eff 0%, #0c45d7 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 14px 30px rgba(29, 94, 255, 0.28);
    }

    .chat-send-premium:hover {
        box-shadow: 0 18px 36px rgba(29, 94, 255, 0.34);
        transform: translateY(-1px);
    }

    .chat-send-premium svg {
        width: 24px;
        height: 24px;
        color: #ffffff !important;
    }

    .chat-send-premium[data-mode="stop"] {
        background: linear-gradient(135deg, #1f56ef 0%, #163eb8 100%) !important;
        box-shadow: 0 18px 38px rgba(22, 62, 184, 0.34);
    }

    .chat-send-premium[data-mode="stop"] svg {
        width: 26px;
        height: 26px;
    }

    @media (max-width: 760px) {
        .chat-empty-state__cards {
            grid-template-columns: 1fr;
            gap: 16px;
            margin-top: 1.25rem;
        }

        .chat-feature-card__title {
            font-size: 18px;
        }

        .chat-feature-card__copy {
            font-size: 15px;
        }

        .chat-suggestions__title {
            font-size: 14px;
        }

        .chat-suggestion-btn {
            min-height: 44px;
            font-size: 14px;
        }

        .chat-suggestions {
            margin-top: 0;
        }
    }

    @media (min-width: 1024px) {
        .chat-empty-state {
            padding-left: 18px;
        }
    }
</style>

<style>
    /* Keep stale chat loaders hidden, but allow intentional actions such as logout to show it. */
    #global-loading-overlay:not(.global-loading-active) {
        display: none !important;
    }
</style>

<div class="chat-shell chat-shell-with-lex h-full min-h-0 {{ $isPro ? '' : 'px-4 py-4 sm:px-6 lg:px-8' }}">
    <div class="mx-auto flex h-full min-h-0 w-full {{ $isPro ? 'max-w-full' : 'max-w-[1700px]' }}">
        <section class="chat-panel flex h-full min-h-0 w-full flex-col overflow-hidden rounded-none">
            <div class="flex flex-1 min-h-0 flex-col">
                <div class="h-2"></div>

            <div id="chat-scroll" class="chat-scrollbar flex-1 overflow-y-auto p-8">
                @if ($messages->isEmpty())
                    <div class="chat-empty-state text-center">
                        <div class="chat-empty-state__brand relative mb-6">
                            <div class="absolute inset-0 bg-[#002C76] blur-[40px] opacity-20 animate-pulse"></div>
                            <div class="relative flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-white shadow-2xl">
                                <img
                                    src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                    alt="DILG Seal"
                                    class="h-full w-full object-contain"
                                >
                            </div>
                        </div>
                        <h3 class="text-[3.75rem] font-black tracking-tight {{ $isPro ? 'text-slate-900' : 'text-slate-950' }} sm:text-[4.1rem]" style="letter-spacing:-0.04em;">What can Lex assist you today?</h3>
                        <p class="mt-4 max-w-md text-[1.45rem] text-slate-500 font-normal leading-relaxed sm:text-[1.55rem]">Ask about legal opinions.</p>

                        <div class="chat-empty-state__cards">
                            <div class="chat-feature-card">
                                <div class="chat-feature-card__icon" style="background:linear-gradient(180deg, rgba(218,232,255,0.98) 0%, rgba(235,242,255,0.92) 100%); color:#2563eb;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14h5" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20 21 16V6a2 2 0 0 0-2-2H5A2 2 0 0 0 3 6v10a2 2 0 0 0 2 2h12Z" />
                                    </svg>
                                </div>
                                <div class="chat-feature-card__title">Legal Opinions</div>
                                <div class="chat-feature-card__copy">Ask about legal interpretations and opinions.</div>
                            </div>
                            <div class="chat-feature-card">
                                <div class="chat-feature-card__icon" style="background:linear-gradient(180deg, rgba(219,247,235,0.98) 0%, rgba(233,250,241,0.92) 100%); color:#16a34a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 2.25H8.25A2.25 2.25 0 0 0 6 4.5v15A2.25 2.25 0 0 0 8.25 21.75h7.5A2.25 2.25 0 0 0 18 19.5V6l-3.75-3.75Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 2.25V6H18" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 12h4.5m-4.5 3h4.5" />
                                    </svg>
                                </div>
                                <div class="chat-feature-card__title">Document Review</div>
                                <div class="chat-feature-card__copy">Get help understanding legal documents.</div>
                            </div>
                            <div class="chat-feature-card">
                                <div class="chat-feature-card__icon" style="background:linear-gradient(180deg, rgba(255,238,220,0.98) 0%, rgba(255,245,233,0.92) 100%); color:#ea580c;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-6h12M8.25 6.75h7.5M9 3h6M7.5 21h9" />
                                    </svg>
                                </div>
                                <div class="chat-feature-card__title">Legal Guidance</div>
                                <div class="chat-feature-card__copy">Receive guidance on various legal matters.</div>
                            </div>
                            <div class="chat-feature-card">
                                <div class="chat-feature-card__icon" style="background:linear-gradient(180deg, rgba(241,232,255,0.98) 0%, rgba(248,243,255,0.92) 100%); color:#7c3aed;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h10.5A2.25 2.25 0 0 1 19.5 5.25v13.5A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75V5.25Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5h7.5M8.25 12h7.5M8.25 16.5h4.5" />
                                    </svg>
                                </div>
                                <div class="chat-feature-card__title">Knowledge Base</div>
                                <div class="chat-feature-card__copy">Search through your curated legal resources.</div>
                            </div>
                        </div>

                        <div id="chat-suggestions" class="chat-suggestions mx-auto w-full max-w-6xl" aria-label="Suggested questions">
                            <div class="chat-suggestions__title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18l-.813-2.096A4.5 4.5 0 0 0 6.096 13.81L4 13l2.096-.813A4.5 4.5 0 0 0 8.187 10.096L9 8l.813 2.096a4.5 4.5 0 0 0 2.091 2.091L14 13l-2.096.813a4.5 4.5 0 0 0-2.091 2.091Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6h.01M18 10h.01" />
                                </svg>
                                <span>Suggested questions</span>
                            </div>
                            <div class="chat-suggestions__grid">
                                @foreach ([
                                    'Is a verbal agreement legally binding?',
                                    'Is recording a conversation without consent legal?',
                                    'Is self-defense always a valid legal defense?',
                                    'Can an acting Punong Barangay receive honorarium?',
                                ] as $suggestedQuestion)
                                    <button type="button" class="chat-suggestion-btn" data-chat-suggestion="{{ $suggestedQuestion }}">
                                        <span>{{ $suggestedQuestion }}</span>
                                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div data-message-stack="true" class="mx-auto flex w-full max-w-6xl flex-col gap-10">
                        @foreach ($messages as $message)
                            <div class="message-enter {{ $message->role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto w-full' }}">
                                <div class="mb-4 flex items-center gap-4 px-2 {{ $message->role === 'user' ? 'flex-row-reverse text-right' : '' }}">
                                    <div class="shrink-0 {{ $message->role === 'user' ? ($isPro ? 'shadow-sm' : 'bg-slate-950') : ($isPro ? 'bg-slate-900/[0.04] ring-1 ring-slate-900/10' : 'bg-sky-100') }} flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest {{ $message->role === 'user' ? 'text-white' : ($isPro ? 'text-slate-800' : 'text-sky-800') }}" style="{{ $message->role === 'user' && $isPro ? 'background-color: #002C76 !important;' : '' }}">
                                        {{ $message->role === 'user' ? 'You' : 'LX' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-black uppercase tracking-[0.2em] {{ $isPro ? 'text-slate-900' : 'text-slate-900' }}">{{ $message->role === 'user' ? 'You' : 'Lex' }}</div>
                                    </div>
                                </div>

                                <div class="{{ $message->role === 'user' ? ($isPro ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white chat-user-bubble' : 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white chat-user-bubble') : 'message-bubble-ai text-slate-800' }} {{ $message->role === 'user' ? 'px-8 py-6 shadow-2xl' : 'px-2 py-1' }}" @if($message->role === 'user') data-user-message="{{ e($message->content) }}" @endif>
                                    @if ($message->role === 'user')
                                        <div class="chat-msg-tools" data-msg-tools>
                                            <button type="button" class="chat-msg-tools-btn" data-msg-copy data-tooltip="Copy message" aria-label="Copy message">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M7.75 3A2.75 2.75 0 005 5.75v7.5A2.75 2.75 0 007.75 16h5.5A2.75 2.75 0 0016 13.25v-7.5A2.75 2.75 0 0013.25 3h-5.5z"/>
                                                    <path d="M4 7.25A3.25 3.25 0 017.25 4H13a.75.75 0 010 1.5H7.25A1.75 1.75 0 005.5 7.25V13a.75.75 0 01-1.5 0V7.25z"/>
                                                </svg>
                                            </button>
                                            <button type="button" class="chat-msg-tools-btn" data-msg-edit data-tooltip="Edit message" aria-label="Edit message">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.25 8.25a1 1 0 01-.414.257l-3 1a1 1 0 01-1.257-1.257l1-3a1 1 0 01.257-.414l8.25-8.25z"/>
                                                    <path d="M11.5 5.5l3 3"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                    <div class="whitespace-pre-wrap leading-relaxed font-normal tracking-wide" style="font-size: 20px;">{!! $message->content !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <button type="button" id="chat-scroll-bottom-btn" class="chat-scroll-bottom-btn" aria-label="Go to latest" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 14.75a.75.75 0 0 1-.53-.22l-5-5a.75.75 0 1 1 1.06-1.06L10 12.94l4.47-4.47a.75.75 0 1 1 1.06 1.06l-5 5a.75.75 0 0 1-.53.22Z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="border-t {{ $isPro ? 'border-slate-900/[0.03] bg-white/50' : 'border-slate-200/50 bg-white/70' }} px-6 py-4 sm:px-8 sm:py-5">
                <form
                    id="chat-form"
                    data-loader-skip
                    class="mx-auto flex w-full max-w-6xl flex-col gap-4"
                    data-create-url="{{ route($createRoute ?? 'conversations.store') }}"
                    data-messages-url="{{ $activeConversation ? route($messagesRoute ?? 'messages.store', $activeConversation->id) : '' }}"
                    data-active-conversation-url="{{ $activeConversation ? route($showRoute ?? 'chat.show', $activeConversation->id) : '' }}"
                    data-conversation-id="{{ $activeConversation?->id }}"
                    data-document-review-url="{{ route($documentReviewRoute ?? 'document-review.store') }}"
                >
                    <div class="chat-composer-shell group relative overflow-hidden {{ $isPro ? 'pro-input-wrapper' : 'rounded-[2rem] border border-slate-200 bg-white' }} transition-all duration-500">
                        <div class="flex items-center gap-3 px-8 py-3">
                            <textarea
                                id="chat-prompt"
                                rows="1"
                                class="min-w-0 flex-1 resize-none border-0 bg-transparent p-0 text-base font-normal leading-6 {{ $isPro ? 'text-slate-900 placeholder:text-slate-400 focus:ring-0' : 'text-slate-800 placeholder:text-slate-400' }}"
                                placeholder="Type your legal inquiry here..."
                            ></textarea>
                            <button id="chat-send" type="submit" aria-label="Send" class="chat-send-premium group flex items-center justify-center transition-all duration-200">
                                <svg data-send-icon xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-0.5" style="width: 20px; height: 20px; display: block;">
                                    <path d="M3.478 2.405a.75.75 0 0 1 .81-.163l18 8.25a.75.75 0 0 1 0 1.362l-18 8.25A.75.75 0 0 1 3 19.5v-6.764a.75.75 0 0 1 .553-.724L12 9.75 3.553 7.488A.75.75 0 0 1 3 6.764V3a.75.75 0 0 1 .478-.595Z"/>
                                </svg>
                            </button>
                        </div>
                        @if ($canUseDocumentReview)
                            <div class="chat-tool-row">
                                <div class="chat-tool-actions">
                                    <button id="document-review-toggle" type="button" class="chat-doc-review-chip" aria-expanded="false" aria-controls="document-review-panel">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 2.25H8.25A2.25 2.25 0 0 0 6 4.5v15a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 19.5V6l-3.75-3.75Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 2.25V6H18" />
                                        </svg>
                                        Document Review
                                    </button>
                                </div>
                                <div class="chat-doc-review-copy">Upload a file or pick a legal opinion only when you need a structured review.</div>
                            </div>

                            <div id="document-review-panel" class="chat-doc-review-panel">
                                <input id="document-review-file" type="file" accept=".pdf,.pptx,.xlsx,.txt" class="hidden">

                                <div class="chat-doc-review-grid {{ $canSelectOpinionForReview ? '' : 'chat-doc-review-grid--upload-only' }}">
                                    <div class="chat-doc-review-field">
                                        <label class="chat-doc-review-label">Upload Document</label>
                                        <button id="document-review-file-btn" type="button" class="chat-doc-review-upload-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V6.75m0 0-3.75 3.75M12 6.75l3.75 3.75M3.75 15v1.5A2.25 2.25 0 0 0 6 18.75h12a2.25 2.25 0 0 0 2.25-2.25V15" />
                                            </svg>
                                            Choose File
                                        </button>
                                    </div>

                                    @if ($canSelectOpinionForReview)
                                        <div class="chat-doc-review-field">
                                            <label for="document-review-opinion" class="chat-doc-review-label">Or Select Legal Opinion</label>
                                            <select id="document-review-opinion" class="chat-doc-review-input">
                                                <option value="">Pick an existing opinion for review</option>
                                                @foreach (($reviewableOpinions ?? collect()) as $reviewOpinion)
                                                    <option value="{{ $reviewOpinion->id }}">{{ $reviewOpinion->title }}{{ $reviewOpinion->opinion_number ? ' - '.$reviewOpinion->opinion_number : '' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="chat-doc-review-field">
                                        <label for="document-review-focus" class="chat-doc-review-label">Focus</label>
                                        <input id="document-review-focus" type="text" class="chat-doc-review-input" placeholder="Example: summary, obligations, deadlines">
                                    </div>

                                    <div class="chat-doc-review-field">
                                        <label class="chat-doc-review-label">Action</label>
                                        <button id="document-review-submit" type="button" class="chat-doc-review-submit">
                                            Review Document
                                        </button>
                                    </div>

                                    <div class="chat-doc-review-status">
                                        <div id="document-review-meta" class="chat-doc-review-status-text">No file selected.</div>
                                        <button id="document-review-clear" type="button" class="chat-doc-review-clear">Clear</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
                <div id="chat-error" class="mx-auto hidden w-full max-w-6xl mt-4 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-6 py-4 text-sm font-bold text-rose-400"></div>
            </div>
            </div>
        </section>
    </div>

    <!-- Opinion Viewer Modal -->
    <div id="opinion-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="opinion-modal-overlay fixed inset-0 transition-opacity" id="opinion-modal-overlay"></div>

        <div class="opinion-modal-panel relative w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden rounded-[2rem] transition-all">
            <div class="opinion-modal-header">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div id="opinion-modal-number" class="opinion-modal-number"></div>
                        <h2 id="opinion-modal-title" class="opinion-modal-title"></h2>
                        <div id="opinion-modal-date" class="opinion-modal-date"></div>
                    </div>
                    <button type="button" id="close-opinion-modal" class="opinion-modal-close" aria-label="Close">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="opinion-modal-scroll flex-1 overflow-y-auto chat-scrollbar">
                <div id="opinion-modal-content" class="opacity-0 transition-opacity duration-300">
                    <div class="prose prose-slate max-w-none">
                        <div id="opinion-modal-body" class="whitespace-pre-wrap text-base leading-relaxed text-slate-700 font-normal"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    const form = document.getElementById('chat-form');
    const promptEl = document.getElementById('chat-prompt');
    const sendBtn = document.getElementById('chat-send');
    const scrollEl = document.getElementById('chat-scroll');
    const errorEl = document.getElementById('chat-error');
    const scrollBottomBtn = document.getElementById('chat-scroll-bottom-btn');
    const suggestionsEl = document.getElementById('chat-suggestions');
    const documentReviewToggleBtn = document.getElementById('document-review-toggle');
    const documentReviewPanel = document.getElementById('document-review-panel');
    const documentReviewFileInput = document.getElementById('document-review-file');
    const documentReviewFileBtn = document.getElementById('document-review-file-btn');
    const documentReviewOpinionEl = document.getElementById('document-review-opinion');
    const documentReviewFocusEl = document.getElementById('document-review-focus');
    const documentReviewSubmitBtn = document.getElementById('document-review-submit');
    const documentReviewMetaEl = document.getElementById('document-review-meta');
    const documentReviewClearBtn = document.getElementById('document-review-clear');
    const isPro = @json($isPro);
    const sidebarList = document.getElementById('sidebar-chats-list');
    const sidebarEmpty = document.getElementById('sidebar-chats-empty');
    const sendIconDefault = `
        <svg data-send-icon xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-0.5" style="width: 24px; height: 24px; display: block;">
            <path d="M3.478 2.405a.75.75 0 0 1 .81-.163l18 8.25a.75.75 0 0 1 0 1.362l-18 8.25A.75.75 0 0 1 3 19.5v-6.764a.75.75 0 0 1 .553-.724L12 9.75 3.553 7.488A.75.75 0 0 1 3 6.764V3a.75.75 0 0 1 .478-.595Z"/>
        </svg>
    `;
    const sendIconStop = `
        <svg data-send-icon xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-5 w-5" style="width: 26px; height: 26px; display: block;">
            <rect x="6.5" y="6.5" width="11" height="11" rx="2.5" fill="currentColor"/>
        </svg>
    `;

    let activeGeneration = null;

    const isCanceledRequest = (err) => {
        return !!(
            err?.code === 'ERR_CANCELED' ||
            err?.name === 'CanceledError' ||
            err?.message === 'canceled'
        );
    };

    const setComposerBusy = (busy) => {
        if (!sendBtn || !promptEl) return;
        sendBtn.dataset.mode = busy ? 'stop' : 'send';
        sendBtn.setAttribute('aria-label', busy ? 'Stop generating' : 'Send');
        sendBtn.title = busy ? 'Stop generating' : 'Send';
        sendBtn.innerHTML = busy ? sendIconStop : sendIconDefault;
        promptEl.readOnly = busy;
        if (documentReviewSubmitBtn) {
            documentReviewSubmitBtn.disabled = busy;
            documentReviewSubmitBtn.classList.toggle('opacity-60', busy);
        }
    };

    const showComposerError = (message) => {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    };

    const setDocumentReviewOpen = (open) => {
        if (!documentReviewPanel || !documentReviewToggleBtn) return;
        documentReviewPanel.classList.toggle('is-open', open);
        documentReviewToggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const fadeOutSuggestions = () => {
        if (!suggestionsEl || suggestionsEl.classList.contains('is-fading')) return;
        suggestionsEl.classList.add('is-fading');
        suggestionsEl.setAttribute('aria-hidden', 'true');
        window.setTimeout(() => suggestionsEl.remove(), 320);
    };

    const upsertSidebarConversation = ({ id, url, title, is_pinned, update_url, toggle_pin_url, toggle_save_url, delete_url }) => {
        if (window.__adminSidebarUpsertConversation) {
            return window.__adminSidebarUpsertConversation({ id, url, title, is_pinned, update_url, toggle_pin_url, toggle_save_url, delete_url });
        }

        if (!sidebarList || !id || !url) return null;

        const displayTitle = (title && String(title).trim()) ? String(title).trim() : 'Untitled Thread';
        const incomingMeaningful = displayTitle !== 'Untitled Thread';
        const selector = `[data-conversation-id="${id}"]`;
        let link = sidebarList.querySelector(selector);

        if (!link) {
            link = document.createElement('a');
            link.dataset.conversationId = String(id);
            link.href = url;
            link.className = 'block rounded-2xl border px-4 py-3 transition-all';
            link.style.borderColor = '#FFDE15';
            link.style.backgroundColor = 'white';
            link.style.color = '#002C76';
            link.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
            const text = document.createElement('div');
            text.className = 'truncate text-sm font-semibold tracking-tight';
            link.appendChild(text);
            link.dataset.fixedTitle = incomingMeaningful ? '1' : '0';
            if (incomingMeaningful) link.dataset.fixedTitleText = displayTitle;
            sidebarList.prepend(link);
        }

        const textEl = link.querySelector('div');
        if (textEl) {
            const currentTitle = String(textEl.textContent || '').trim();
            const currentMeaningful = currentTitle !== '' && currentTitle !== 'Untitled Thread';
            const isFixed = link.dataset.fixedTitle === '1' || currentMeaningful;

            if (currentMeaningful && link.dataset.fixedTitle !== '1') {
                link.dataset.fixedTitle = '1';
                link.dataset.fixedTitleText = currentTitle;
            }

            if (!isFixed) {
                textEl.textContent = displayTitle;
                if (incomingMeaningful) {
                    link.dataset.fixedTitle = '1';
                    link.dataset.fixedTitleText = displayTitle;
                }
            } else if (!currentMeaningful) {
                textEl.textContent = displayTitle;
                if (incomingMeaningful) {
                    link.dataset.fixedTitle = '1';
                    link.dataset.fixedTitleText = displayTitle;
                }
            }
        }

        if (sidebarEmpty) sidebarEmpty.remove();

        if (link !== sidebarList.firstElementChild) {
            sidebarList.prepend(link);
        }

        return link;
    };

    const normalizeTitle = (text) => {
        const cleaned = String(text || '').replace(/\s+/g, ' ').trim();
        return cleaned.length > 60 ? cleaned.slice(0, 60) : cleaned;
    };

    const renderMessage = (role, content) => {
        const container = document.createElement('div');
        container.className = 'message-enter ' + (role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto w-full');

        const meta = document.createElement('div');
        meta.className = 'mb-4 flex items-center gap-4 px-2 ' + (role === 'user' ? 'flex-row-reverse text-right' : '');

        const avatar = document.createElement('div');
        if (isPro) {
            avatar.className = (role === 'user'
                ? 'bg-[#002C76] shadow-sm text-white'
                : 'bg-slate-900/[0.04] ring-1 ring-slate-900/10 text-slate-800') + ' shrink-0 flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest';
        } else {
            avatar.className = (role === 'user'
                ? 'bg-slate-950 text-white'
                : 'bg-sky-100 text-sky-800') + ' flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest';
        }
        avatar.textContent = role === 'user' ? 'You' : 'LX';

        const metaText = document.createElement('div');
        metaText.className = 'min-w-0';

        const label = document.createElement('div');
        label.className = 'text-xs font-black uppercase tracking-[0.2em] ' + (isPro ? 'text-slate-900' : 'text-slate-900');
        label.textContent = role === 'user' ? 'You' : 'Lex';

        metaText.appendChild(label);
        meta.appendChild(avatar);
        meta.appendChild(metaText);

        const bubble = document.createElement('div');
        if (isPro) {
            bubble.className = (role === 'user'
                ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white chat-user-bubble'
                : 'message-bubble-ai text-slate-800') + (role === 'user' ? ' px-8 py-6 shadow-2xl' : ' px-2 py-1');
        } else {
            bubble.className = (role === 'user'
                ? 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white shadow-lg chat-user-bubble'
                : 'message-bubble-ai text-slate-800') + (role === 'user' ? ' px-8 py-6' : ' px-2 py-1');
        }
        if (role === 'user') {
            bubble.dataset.userMessage = String(content ?? '');
            const tools = document.createElement('div');
            tools.className = 'chat-msg-tools';
            tools.dataset.msgTools = 'true';
            tools.innerHTML = `
                <button type="button" class="chat-msg-tools-btn" data-msg-copy data-tooltip="Copy message" aria-label="Copy message">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7.75 3A2.75 2.75 0 005 5.75v7.5A2.75 2.75 0 007.75 16h5.5A2.75 2.75 0 0016 13.25v-7.5A2.75 2.75 0 0013.25 3h-5.5z"/>
                        <path d="M4 7.25A3.25 3.25 0 017.25 4H13a.75.75 0 010 1.5H7.25A1.75 1.75 0 005.5 7.25V13a.75.75 0 01-1.5 0V7.25z"/>
                    </svg>
                </button>
                <button type="button" class="chat-msg-tools-btn" data-msg-edit data-tooltip="Edit message" aria-label="Edit message">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.25 8.25a1 1 0 01-.414.257l-3 1a1 1 0 01-1.257-1.257l1-3a1 1 0 01.257-.414l8.25-8.25z"/>
                        <path d="M11.5 5.5l3 3"/>
                    </svg>
                </button>
            `;
            bubble.appendChild(tools);
        }

        const body = document.createElement('div');
        body.className = 'whitespace-pre-wrap leading-relaxed font-normal tracking-wide';
        body.style.fontSize = '20px';
        if (role === 'user') {
            body.textContent = content;
        } else {
            body.innerHTML = content;
        }

        bubble.appendChild(body);
        container.appendChild(meta);
        container.appendChild(bubble);

        let stack = scrollEl.querySelector('[data-message-stack]');
        if (!stack) {
            scrollEl.innerHTML = '';
            stack = document.createElement('div');
            stack.dataset.messageStack = 'true';
            stack.className = 'mx-auto flex w-full max-w-6xl flex-col gap-10';
            scrollEl.appendChild(stack);
        }

        stack.appendChild(container);
        return container;
    };

    const renderThinkingMessage = () => {
        const el = renderMessage('assistant', 'Thinking');
        const body = el.querySelector('.whitespace-pre-wrap');
        if (body) {
            body.innerHTML = '<span class="chat-thinking-dots" aria-label="Lex is typing"><span>.</span><span>.</span><span>.</span></span>';
        }
        el.dataset.thinking = 'true';
        return el;
    };

    const assistantHtmlToPlainText = (html) => {
        const tmp = document.createElement('div');
        tmp.innerHTML = String(html ?? '');
        const text = (tmp.innerText || tmp.textContent || '').replace(/\r\n/g, '\n');
        return text.trimEnd();
    };

    const isNearBottom = (el, threshold = 140) => {
        if (!el) return true;
        return (el.scrollHeight - el.scrollTop - el.clientHeight) < threshold;
    };

    const updateScrollBottomBtn = () => {
        if (!scrollEl || !scrollBottomBtn) return;
        const near = isNearBottom(scrollEl, 140);
        scrollBottomBtn.classList.toggle('is-visible', !near);
        scrollBottomBtn.setAttribute('aria-hidden', near ? 'true' : 'false');
    };

    const typeAssistantResponse = (bodyEl, html, options = {}) => {
        const { shouldStop } = options;
        const fullText = assistantHtmlToPlainText(html);
        const cutMatch = fullText.match(/(?:^|\n)\s*Other Related References That Might Help:\s*/i);
        const typedText = cutMatch ? fullText.slice(0, Math.max(0, cutMatch.index ?? 0)).trimEnd() : fullText;
        const safeText = typedText === '' ? ' ' : typedText;
        const length = safeText.length;

        const msPerChar = length > 900 ? 3 : (length > 400 ? 4 : 6);
        const minDuration = 320;
        const maxDuration = 5200;
        const duration = Math.min(maxDuration, Math.max(minDuration, length * msPerChar));
        const autoScroll = isNearBottom(scrollEl);

        bodyEl.classList.remove('chat-reply-fade-in');
        bodyEl.textContent = '';

        return new Promise((resolve) => {
            const start = performance.now();
            const tick = (now) => {
                if (typeof shouldStop === 'function' && shouldStop()) {
                    bodyEl.textContent = bodyEl.textContent.replace(/\|$/, '');
                    resolve(false);
                    return;
                }
                const elapsed = now - start;
                const progress = Math.min(1, elapsed / duration);
                const chars = Math.max(1, Math.min(length, Math.floor(elapsed / msPerChar)));
                bodyEl.textContent = safeText.slice(0, chars) + (progress < 1 ? '▍' : '');
                if (autoScroll) scrollToBottom();
                if (progress < 1 && chars < length) {
                    requestAnimationFrame(tick);
                    return;
                }
                resolve(true);
            };
            requestAnimationFrame(tick);
        }).then((completed) => {
            if (!completed) return;
            bodyEl.innerHTML = String(html ?? '');
            void bodyEl.offsetWidth;
            bodyEl.classList.add('chat-reply-fade-in');
            if (autoScroll) scrollToBottom();
        });
    };

    const scrollToBottom = () => {
        scrollEl.scrollTop = scrollEl.scrollHeight;
        updateScrollBottomBtn();
    };

    if (scrollEl) {
        let raf = 0;
        scrollEl.addEventListener('scroll', () => {
            if (raf) return;
            raf = requestAnimationFrame(() => {
                raf = 0;
                updateScrollBottomBtn();
            });
        }, { passive: true });
    }

    if (scrollBottomBtn) {
        scrollBottomBtn.addEventListener('click', () => {
            if (!scrollEl) return;
            scrollEl.scrollTo({ top: scrollEl.scrollHeight, behavior: 'smooth' });
        });
    }

    const ensureConversation = async (requestOptions = {}) => {
        const existingUrl = form.dataset.activeConversationUrl;
        const existingMessagesUrl = form.dataset.messagesUrl;
        const existingId = form.dataset.conversationId;

        if (existingUrl && existingMessagesUrl && existingId) {
            return { id: existingId, url: existingUrl, messagesUrl: existingMessagesUrl };
        }

        const resp = await window.axios.post(form.dataset.createUrl, {}, {
            headers: { 
                Accept: 'application/json',
                'X-Loader-Skip': 'true'
            },
            timeout: 45000,
            ...requestOptions,
        });
        form.dataset.conversationId = String(resp.data.id);
        form.dataset.activeConversationUrl = resp.data.url;
        form.dataset.messagesUrl = resp.data.messages_url;
        window.history.replaceState({}, '', resp.data.url);

        upsertSidebarConversation({
            id: resp.data.id,
            url: resp.data.url,
            title: resp.data.title,
            is_pinned: false,
            update_url: resp.data.update_url,
            toggle_pin_url: resp.data.toggle_pin_url,
            toggle_save_url: resp.data.toggle_save_url,
            delete_url: resp.data.delete_url,
        });

        return { id: resp.data.id, url: resp.data.url, messagesUrl: resp.data.messages_url };
    };

    const clearActiveGeneration = ({ focusPrompt = true } = {}) => {
        activeGeneration = null;
        setComposerBusy(false);
        promptEl.disabled = false;
        if (focusPrompt) promptEl.focus();
    };

    const stopActiveGeneration = ({ restorePrompt = true } = {}) => {
        if (!activeGeneration) return false;

        activeGeneration.stopped = true;
        activeGeneration.controller?.abort();

        if (restorePrompt && !promptEl.value.trim()) {
            promptEl.value = activeGeneration.prompt;
        }

        if (activeGeneration.thinkingEl?.dataset?.thinking === 'true') {
            activeGeneration.thinkingEl.remove();
        }

        clearActiveGeneration();
        return true;
    };

    const deliverAssistantResponse = async (thinkingEl, content, generation) => {
        if (!thinkingEl || generation.stopped) return;

        if (thinkingEl.dataset.thinking === 'true') {
            const body = thinkingEl.querySelector('.whitespace-pre-wrap');
            if (body) {
                await typeAssistantResponse(body, content, {
                    shouldStop: () => generation.stopped,
                });
            } else {
                thinkingEl.remove();
                const el = renderMessage('assistant', '');
                const body2 = el.querySelector('.whitespace-pre-wrap');
                if (body2) {
                    await typeAssistantResponse(body2, content, {
                        shouldStop: () => generation.stopped,
                    });
                } else {
                    el.remove();
                    if (!generation.stopped) renderMessage('assistant', content);
                }
            }
            delete thinkingEl.dataset.thinking;
            return;
        }

        const el = renderMessage('assistant', '');
        const body = el.querySelector('.whitespace-pre-wrap');
        if (body) {
            await typeAssistantResponse(body, content, {
                shouldStop: () => generation.stopped,
            });
        } else {
            el.remove();
            if (!generation.stopped) renderMessage('assistant', content);
        }
    };

    const updateDocumentReviewMeta = () => {
        if (!documentReviewMetaEl) return;

        const parts = [];
        const fileName = documentReviewFileInput?.files?.[0]?.name;
        const selectedText = documentReviewOpinionEl?.selectedOptions?.[0]?.textContent?.trim();

        if (fileName) {
            parts.push(`File: ${fileName}`);
        }

        if (documentReviewOpinionEl?.value && selectedText) {
            parts.push(`Selected opinion: ${selectedText}`);
        }

        documentReviewMetaEl.textContent = parts.length > 0 ? parts.join(' • ') : 'No file selected.';
        if (parts.length > 0) {
            setDocumentReviewOpen(true);
        }
    };

    const submitDocumentReview = async () => {
        if (!form?.dataset?.documentReviewUrl || activeGeneration) return;

        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        const file = documentReviewFileInput?.files?.[0] || null;
        const opinionId = String(documentReviewOpinionEl?.value || '').trim();
        const focus = String(documentReviewFocusEl?.value || '').trim();

        if (!file && !opinionId) {
            showComposerError('Please upload a document or choose an existing legal opinion first.');
            return;
        }

        fadeOutSuggestions();
        setComposerBusy(true);

        const summaryTitle = file ? `Please review this document: ${file.name}` : `Please review the selected legal opinion${documentReviewOpinionEl?.selectedOptions?.[0]?.textContent ? `: ${documentReviewOpinionEl.selectedOptions[0].textContent.trim()}` : ''}`;
        renderMessage('user', summaryTitle);
        const thinkingEl = renderThinkingMessage();
        scrollToBottom();

        try {
            const payload = new FormData();
            if (file) payload.append('document', file);
            if (opinionId) payload.append('opinion_id', opinionId);
            if (focus) payload.append('focus', focus);

            const resp = await window.axios.post(form.dataset.documentReviewUrl, payload, {
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'multipart/form-data',
                    'X-Loader-Skip': 'true',
                },
                timeout: 120000,
            });

            const reviewTitle = escapeHtml(resp?.data?.title || 'Document Review');
            const reviewBody = escapeHtml(String(resp?.data?.review || '').trim()).replace(/\n/g, '<br>');
            const content = `<p class="chat-faq-paragraph"><strong>${reviewTitle}</strong></p>${reviewBody !== '' ? `<p class="chat-faq-paragraph">${reviewBody}</p>` : ''}`;
            await deliverAssistantResponse(thinkingEl, content, { stopped: false });

            if (documentReviewFileInput) documentReviewFileInput.value = '';
            if (documentReviewOpinionEl) documentReviewOpinionEl.value = '';
            if (documentReviewFocusEl) documentReviewFocusEl.value = '';
            updateDocumentReviewMeta();
            setDocumentReviewOpen(false);
            scrollToBottom();
        } catch (err) {
            if (thinkingEl?.dataset?.thinking === 'true') {
                thinkingEl.remove();
            }
            showComposerError(err?.response?.data?.message || 'Unable to review the document right now.');
        } finally {
            setComposerBusy(false);
        }
    };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (activeGeneration) {
            stopActiveGeneration();
            return;
        }

        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        const prompt = (promptEl.value || '').trim();
        if (!prompt) return;

        fadeOutSuggestions();
        setComposerBusy(true);
        promptEl.disabled = false;

        const userMessageEl = renderMessage('user', prompt);
        promptEl.value = '';
        const thinkingEl = renderThinkingMessage();
        scrollToBottom();

        const controller = new AbortController();
        const generation = {
            controller,
            prompt,
            thinkingEl,
            userMessageEl,
            stopped: false,
        };
        activeGeneration = generation;

        try {
            const conv = await ensureConversation({ signal: controller.signal });
            generation.conversation = conv;

            const resp = await window.axios.post(conv.messagesUrl, { prompt }, {
                headers: { 
                    Accept: 'application/json',
                    'X-Loader-Skip': 'true'
                },
                timeout: 45000,
                signal: controller.signal,
            });

            if (generation.stopped || activeGeneration !== generation) return;

            const content = resp?.data?.assistant_message?.content ?? '';
            await deliverAssistantResponse(thinkingEl, content, generation);
            if (generation.stopped || activeGeneration !== generation) return;
            upsertSidebarConversation({ id: conv.id, url: conv.url, title: normalizeTitle(prompt), is_pinned: false });
            scrollToBottom();
        } catch (err) {
            if (isCanceledRequest(err) || generation.stopped) {
                return;
            }
            if (thinkingEl && thinkingEl.dataset.thinking === 'true') thinkingEl.remove();
            const message = err?.response?.data?.message || 'Something went wrong while contacting the AI provider.';
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        } finally {
            if (activeGeneration === generation) {
                clearActiveGeneration();
            }
        }
    });

    if (suggestionsEl) {
        suggestionsEl.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-chat-suggestion]');
            if (!btn || activeGeneration) return;
            const question = String(btn.getAttribute('data-chat-suggestion') || '').trim();
            if (!question) return;

            promptEl.value = question;
            fadeOutSuggestions();
            form.requestSubmit();
        });
    }

    if (documentReviewFileBtn && documentReviewFileInput) {
        documentReviewFileBtn.addEventListener('click', () => documentReviewFileInput.click());
        documentReviewFileInput.addEventListener('change', () => {
            if (documentReviewOpinionEl && documentReviewFileInput.files?.length) {
                documentReviewOpinionEl.value = '';
            }
            updateDocumentReviewMeta();
        });
    }

    if (documentReviewToggleBtn) {
        documentReviewToggleBtn.addEventListener('click', () => {
            const isOpen = documentReviewPanel?.classList.contains('is-open');
            setDocumentReviewOpen(!isOpen);
        });
    }

    if (documentReviewOpinionEl) {
        documentReviewOpinionEl.addEventListener('change', () => {
            if (documentReviewFileInput && documentReviewOpinionEl.value) {
                documentReviewFileInput.value = '';
            }
            updateDocumentReviewMeta();
        });
    }

    if (documentReviewSubmitBtn) {
        documentReviewSubmitBtn.addEventListener('click', submitDocumentReview);
    }

    if (documentReviewClearBtn) {
        documentReviewClearBtn.addEventListener('click', () => {
            if (documentReviewFileInput) documentReviewFileInput.value = '';
            if (documentReviewOpinionEl) documentReviewOpinionEl.value = '';
            if (documentReviewFocusEl) documentReviewFocusEl.value = '';
            updateDocumentReviewMeta();
            setDocumentReviewOpen(false);
        });
    }

    promptEl.addEventListener('keydown', (e) => {
        if (activeGeneration && e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            return;
        }
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    scrollEl.addEventListener('click', (e) => {
        const copyBtn = e.target.closest('[data-msg-copy]');
        if (copyBtn) {
            const bubble = copyBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const text = (contentEl && typeof contentEl.innerText === 'string' && contentEl.innerText.trim() !== '')
                ? contentEl.innerText
                : (bubble.dataset.userMessage || '');
            const originalHtml = copyBtn.dataset.originalHtml || copyBtn.innerHTML;
            copyBtn.dataset.originalHtml = originalHtml;
            const write = async () => {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(text);
                    return true;
                }
                return false;
            };
            write()
                .then((ok) => {
                    if (!ok) return;
                    copyBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 18px; height: 18px; display: block;"><path fill-rule="evenodd" d="M16.704 5.296a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 011.414-1.414l2.543 2.543 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    if (copyBtn.dataset.copyTimeoutId) {
                        clearTimeout(Number(copyBtn.dataset.copyTimeoutId));
                    }
                    const timeoutId = window.setTimeout(() => {
                        copyBtn.innerHTML = copyBtn.dataset.originalHtml || originalHtml;
                        delete copyBtn.dataset.copyTimeoutId;
                    }, 3000);
                    copyBtn.dataset.copyTimeoutId = String(timeoutId);
                })
                .catch(() => {});
            return;
        }

        const editBtn = e.target.closest('[data-msg-edit]');
        if (editBtn) {
            const bubble = editBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            if (activeGeneration && activeGeneration.userMessageEl?.contains(bubble)) {
                stopActiveGeneration({ restorePrompt: false });
            }
            if (bubble.classList.contains('is-editing')) return;

            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const originalText = (contentEl && typeof contentEl.innerText === 'string' && contentEl.innerText.trim() !== '')
                ? contentEl.innerText
                : (bubble.dataset.userMessage || '');

            bubble.dataset.originalUserMessage = originalText;
            bubble.classList.add('is-editing');
            const msgContainer = bubble.closest('.message-enter');
            if (msgContainer) msgContainer.classList.add('is-editing-wide');

            if (contentEl) {
                contentEl.style.display = 'none';
            }

            const textarea = document.createElement('textarea');
            textarea.className = 'chat-edit-textarea whitespace-pre-wrap leading-relaxed font-normal tracking-wide';
            textarea.value = originalText;
            textarea.style.fontSize = '20px';
            textarea.rows = 3;

            const actions = document.createElement('div');
            actions.className = 'chat-edit-actions';
            actions.innerHTML = `
                <button type="button" class="chat-edit-action-btn" data-msg-edit-cancel>Cancel</button>
                <button type="button" class="chat-edit-action-btn primary" data-msg-edit-send>Send</button>
            `;

            bubble.appendChild(textarea);
            bubble.appendChild(actions);

            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
            return;
        }

        const cancelEditBtn = e.target.closest('[data-msg-edit-cancel]');
        if (cancelEditBtn) {
            const bubble = cancelEditBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            const originalText = bubble.dataset.originalUserMessage || bubble.dataset.userMessage || '';
            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const textarea = bubble.querySelector('.chat-edit-textarea');
            const actions = bubble.querySelector('.chat-edit-actions');
            if (textarea) textarea.remove();
            if (actions) actions.remove();
            if (contentEl) {
                contentEl.style.display = '';
                contentEl.textContent = originalText;
            }
            bubble.dataset.userMessage = originalText;
            bubble.classList.remove('is-editing');
            const msgContainer = bubble.closest('.message-enter');
            if (msgContainer) msgContainer.classList.remove('is-editing-wide');
            return;
        }

        const sendEditBtn = e.target.closest('[data-msg-edit-send]');
        if (sendEditBtn) {
            const bubble = sendEditBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            const textarea = bubble.querySelector('.chat-edit-textarea');
            if (!textarea) return;
            const editedText = String(textarea.value || '').trim();
            if (!editedText) return;

            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const actions = bubble.querySelector('.chat-edit-actions');
            if (contentEl) {
                contentEl.style.display = '';
                contentEl.textContent = editedText;
            }
            bubble.dataset.userMessage = editedText;
            if (textarea) textarea.remove();
            if (actions) actions.remove();
            bubble.classList.remove('is-editing');
            const msgContainer = bubble.closest('.message-enter');
            if (msgContainer) msgContainer.classList.remove('is-editing-wide');

            const stack = scrollEl.querySelector('[data-message-stack]');
            if (msgContainer && stack) {
                let node = msgContainer.nextElementSibling;
                while (node) {
                    const next = node.nextElementSibling;
                    node.remove();
                    node = next;
                }
            }

            setComposerBusy(true);
            promptEl.disabled = false;

            const thinkingEl = renderThinkingMessage();
            scrollToBottom();

            const controller = new AbortController();
            const generation = {
                controller,
                prompt: editedText,
                thinkingEl,
                userMessageEl: msgContainer,
                stopped: false,
            };
            activeGeneration = generation;

            (async () => {
                try {
                    const conv = await ensureConversation({ signal: controller.signal });
                    generation.conversation = conv;
                    const resp = await window.axios.post(conv.messagesUrl, { prompt: editedText }, {
                        headers: {
                            Accept: 'application/json',
                            'X-Loader-Skip': 'true'
                        },
                        timeout: 45000,
                        signal: controller.signal,
                    });
                    if (generation.stopped || activeGeneration !== generation) return;
                    const content = resp?.data?.assistant_message?.content ?? '';
                    await deliverAssistantResponse(thinkingEl, content, generation);
                    if (generation.stopped || activeGeneration !== generation) return;
                    upsertSidebarConversation({ id: conv.id, url: conv.url, title: normalizeTitle(editedText), is_pinned: false });
                    scrollToBottom();
                } catch (err) {
                    if (isCanceledRequest(err) || generation.stopped) {
                        return;
                    }
                    if (thinkingEl && thinkingEl.dataset.thinking === 'true') thinkingEl.remove();
                    const message = err?.response?.data?.message || 'Something went wrong while contacting the AI provider.';
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                } finally {
                    if (activeGeneration === generation) {
                        clearActiveGeneration();
                    }
                }
            })();

            return;
        }

        const refItem = e.target.closest('.ref-accordion');
        if (!refItem) return;
        if (e.target.closest('a')) return;

        const body = refItem.querySelector('.ref-accordion-body');
        if (!body) return;

        body.removeAttribute('hidden');
        const willOpen = !refItem.classList.contains('is-open');
        if (willOpen) {
            refItem.classList.add('is-open');
            refItem.setAttribute('aria-expanded', 'true');
        } else {
            refItem.classList.remove('is-open');
            refItem.setAttribute('aria-expanded', 'false');
        }
    });

    document.querySelectorAll('.ref-accordion').forEach((item) => {
        if (!item.hasAttribute('tabindex')) item.setAttribute('tabindex', '0');
        if (!item.hasAttribute('role')) item.setAttribute('role', 'button');
        if (!item.hasAttribute('aria-expanded')) item.setAttribute('aria-expanded', 'false');
    });

    document.querySelectorAll('.ref-accordion-body[hidden]').forEach((el) => {
        el.removeAttribute('hidden');
    });

    updateDocumentReviewMeta();

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const target = e.target;
        if (!(target instanceof HTMLElement)) return;
        if (!target.classList.contains('ref-accordion')) return;
        e.preventDefault();
        target.click();
    });

    scrollToBottom();

    // Opinion Modal Logic
    const opinionModal = document.getElementById('opinion-modal');
    const opinionModalContent = document.getElementById('opinion-modal-content');
    const closeOpinionModalBtn = document.getElementById('close-opinion-modal');
    const opinionModalOverlay = document.getElementById('opinion-modal-overlay');
    let opinionModalCloseTimer = null;
    const opinionModalPanel = opinionModal?.querySelector('.opinion-modal-panel');
    let lastOpinionDockPoint = null;
    let opinionCloseAnimToken = 0;
    let opinionPanelAnim = null;
    let opinionOverlayAnim = null;
    const resetOpinionModalAnimationState = () => {
        if (opinionModalCloseTimer) {
            clearTimeout(opinionModalCloseTimer);
            opinionModalCloseTimer = null;
        }

        if (opinionPanelAnim) {
            try { opinionPanelAnim.cancel(); } catch (_) {}
            opinionPanelAnim = null;
        }
        if (opinionOverlayAnim) {
            try { opinionOverlayAnim.cancel(); } catch (_) {}
            opinionOverlayAnim = null;
        }

        if (opinionModalPanel) {
            opinionModalPanel.getAnimations().forEach((a) => a.cancel());
            opinionModalPanel.style.transformOrigin = '';
            opinionModalPanel.style.transition = '';
            opinionModalPanel.style.transform = '';
            opinionModalPanel.style.opacity = '';
            opinionModalPanel.style.clipPath = '';
            opinionModalPanel.style.filter = '';
        }

        if (opinionModalOverlay) {
            opinionModalOverlay.getAnimations().forEach((a) => a.cancel());
            opinionModalOverlay.style.transition = '';
            opinionModalOverlay.style.opacity = '';
        }

        if (opinionModal) {
            opinionModal.getAnimations().forEach((a) => a.cancel());
        }

        opinionModal?.classList.remove('is-closing');
    };

    const openOpinionModal = async (opinionId) => {
        // Force hide global loader if it's stuck
        if (window.__globalLoaderStop) window.__globalLoaderStop();

        resetOpinionModalAnimationState();

        opinionModal.classList.remove('hidden');
        requestAnimationFrame(() => {
            opinionModal.classList.add('is-open');
            opinionModal.classList.remove('is-closing');
        });
        opinionModalContent.classList.add('opacity-0');
        document.body.classList.add('overflow-hidden');
        document.body.classList.add('opinion-modal-open');

        try {
            const resp = await window.axios.get(`/api/opinions/${opinionId}`, {
                headers: { 'X-Loader-Skip': 'true' }
            });
            const data = resp.data;

            document.getElementById('opinion-modal-number').textContent = data.opinion_number;
            document.getElementById('opinion-modal-title').textContent = data.title;
            document.getElementById('opinion-modal-date').textContent = data.date;
            document.getElementById('opinion-modal-body').textContent = data.context;

            opinionModalContent.classList.remove('opacity-0');
        } catch (err) {
            console.error('Failed to fetch opinion details:', err);
            closeOpinionModal();
        }
    };

    const closeOpinionModal = () => {
        opinionCloseAnimToken++;
        const token = opinionCloseAnimToken;
        const duration = 520;

        if (opinionModal.classList.contains('is-closing')) {
            return;
        }

        opinionModal.classList.add('is-closing');

        if (opinionModalPanel) {
            const rect = opinionModalPanel.getBoundingClientRect();
            const fromX = rect.left + rect.width / 2;
            const fromY = rect.top + rect.height / 2;
            const target = lastOpinionDockPoint || { x: 96, y: window.innerHeight - 56 };
            const dx = target.x - fromX;
            const dy = target.y - fromY;
            const ox = dx * 1.06;
            const oy = dy * 1.06;

            opinionModalPanel.getAnimations().forEach((a) => a.cancel());
            opinionModalPanel.style.transformOrigin = '50% 0%';
            opinionModalPanel.style.transition = 'none';

            opinionPanelAnim = opinionModalPanel.animate(
                [
                    {
                        transform: 'translate3d(0px, 0px, 0) scale3d(1, 1, 1) skewX(0deg)',
                        opacity: 1,
                        offset: 0,
                    },
                    {
                        transform: `translate3d(${dx * 0.55}px, ${dy * 0.55}px, 0) scale3d(1.06, 0.78, 1) skewX(-6deg)`,
                        opacity: 1,
                        offset: 0.55,
                        easing: 'cubic-bezier(0.2, 0.9, 0.2, 1)',
                    },
                    {
                        transform: `translate3d(${ox}px, ${oy}px, 0) scale3d(0.22, 0.12, 1) skewX(10deg)`,
                        opacity: 0.85,
                        offset: 0.88,
                        easing: 'cubic-bezier(0.12, 0.85, 0.2, 1.2)',
                    },
                    {
                        transform: `translate3d(${dx}px, ${dy}px, 0) scale3d(0.05, 0.05, 1) skewX(0deg)`,
                        opacity: 0,
                        offset: 1,
                        easing: 'cubic-bezier(0.2, 0.8, 0.2, 1)',
                    },
                ],
                { duration, fill: 'forwards' }
            );
        }

        if (opinionModalOverlay) {
            opinionModalOverlay.getAnimations().forEach((a) => a.cancel());
            opinionModalOverlay.style.transition = 'none';
            opinionOverlayAnim = opinionModalOverlay.animate([{ opacity: 1 }, { opacity: 0 }], { duration: duration - 80, fill: 'forwards', easing: 'ease-in' });
        }

        if (opinionModalCloseTimer) clearTimeout(opinionModalCloseTimer);
        opinionModalCloseTimer = setTimeout(() => {
            if (token !== opinionCloseAnimToken) return;
            opinionModal.classList.remove('is-open');
            opinionModal.classList.remove('is-closing');
            opinionModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.body.classList.remove('opinion-modal-open');
            opinionModalCloseTimer = null;
            resetOpinionModalAnimationState();
        }, duration);
    };

    if (closeOpinionModalBtn) closeOpinionModalBtn.addEventListener('click', closeOpinionModal);
    if (opinionModalOverlay) opinionModalOverlay.addEventListener('click', closeOpinionModal);
    
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeOpinionModal();
    });

    document.addEventListener('click', (e) => {
        const link = e.target.closest('.opinion-link');
        if (link) {
            e.preventDefault();
            e.stopPropagation(); // Stop event bubbling
            const opinionId = link.dataset.opinionId;
            const r = link.getBoundingClientRect();
            lastOpinionDockPoint = { x: r.left + r.width / 2, y: r.top + r.height / 2 };
            openOpinionModal(opinionId);
            return false;
        }
    }, true); // Use capture phase to intercept early
</script>
