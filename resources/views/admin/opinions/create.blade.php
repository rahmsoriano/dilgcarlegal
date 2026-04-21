<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.opinions.index') }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 text-slate-500 ring-1 ring-slate-900/5 hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Add Legal Opinion</h2>
                <p class="mt-1 text-slate-600">Create a new legal reference entry for the chatbot knowledge base.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="rounded-2xl bg-rose-500/10 p-4 ring-1 ring-rose-500/20 text-rose-600 text-sm font-medium">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div id="opinion-success" class="rounded-2xl bg-emerald-500/10 p-4 ring-1 ring-emerald-500/20 text-emerald-700 text-sm font-bold transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-[2.5rem] bg-white/80 backdrop-blur-xl p-10 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <form id="opinion-form" action="{{ route('admin.opinions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                <input id="save-new-input" type="hidden" name="save_new" value="0">

                <div class="flex items-center justify-end gap-4 pb-8 border-b border-slate-900/5">
                    <div id="opinion-loading" class="hidden mr-auto items-center gap-3 rounded-2xl bg-slate-900/5 px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-600 ring-1 ring-slate-900/10">
                        <svg class="h-4 w-4 animate-spin text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Saving...
                    </div>
                    <a href="{{ route('admin.opinions.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Cancel</a>
                    <button id="btn-extract" type="button" data-extract-url="{{ route('admin.opinions.extract') }}" class="inline-flex items-center justify-center gap-3 rounded-2xl bg-white/50 px-8 py-3 text-sm font-bold text-slate-600 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-sm">
                        <svg id="extract-spinner" class="hidden h-4 w-4 animate-spin text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="extract-label">Extract pdf,ppt,excel to text</span>
                    </button>
                    <input id="extract-doc-input" type="file" class="hidden" accept=".pdf,.ppt,.pptx,.xls,.xlsx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    <button id="btn-save-new" type="submit" class="inline-flex items-center justify-center rounded-2xl bg-white/50 px-8 py-3 text-sm font-bold text-slate-600 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-sm">
                        Save &amp; New Opinion
                    </button>
                    <button id="btn-save" type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-10 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                        Save Opinion
                    </button>
                </div>

                <div id="extract-panel" class="hidden rounded-[2rem] bg-slate-900/[0.02] p-8 ring-1 ring-slate-900/5">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Extract Document</h3>
                            <p class="mt-1 text-sm text-slate-500">Upload a PDF/PPTX/XLSX and we will extract Title, Opinion Number &amp; Year, Date, and Context.</p>
                        </div>
                        <button id="btn-extract-back" type="button" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Back</button>
                    </div>

                    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <button id="btn-extract-choose" type="button" class="inline-flex items-center justify-center rounded-2xl bg-white px-8 py-3 text-sm font-bold text-slate-700 ring-1 ring-slate-900/10 hover:bg-slate-50 transition-all duration-300 shadow-sm">
                            Choose File
                        </button>
                        <div id="extract-file-name" class="min-w-0 text-sm font-bold text-slate-500 truncate">No file selected</div>
                        <div id="extract-status" class="hidden ml-auto items-center gap-3 rounded-2xl bg-slate-900/5 px-5 py-3 text-xs font-bold uppercase tracking-widest text-slate-600 ring-1 ring-slate-900/10">
                            <svg class="h-4 w-4 animate-spin text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Extracting...
                        </div>
                    </div>
                    <div id="extract-error" class="hidden mt-4 rounded-2xl bg-rose-500/10 p-4 ring-1 ring-rose-500/20 text-rose-600 text-sm font-bold"></div>

                    <div class="mt-8 grid grid-cols-1 gap-8">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Title</label>
                            <input id="extract-title" type="text"
                                class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                        </div>

                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Opinion Number and Year</label>
                                <input id="extract-opinion-number" type="text"
                                    class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Date</label>
                                <input id="extract-date" type="text"
                                    class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Context</label>
                            <textarea id="extract-context" rows="14"
                                class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all resize-y"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <button id="btn-extract-apply" type="button" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-10 py-4 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                                Apply to Form
                            </button>
                        </div>
                    </div>
                </div>

                <div id="opinion-main" class="grid grid-cols-1 gap-8">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Opinion Number and Year</label>
                            <input type="text" name="opinion_number" value="{{ old('opinion_number') }}" placeholder="Opinion No. 06, s. 2003" required
                                class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Date</label>
                            <input type="text" name="date" value="{{ old('date') }}" placeholder="mm/dd/yyyy" inputmode="numeric" pattern="[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}" required
                                class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Context</label>
                        <textarea name="context" rows="14"
                            class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all resize-y">{{ old('context') }}</textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

<script>
const d = document.querySelector('input[name="date"]');
if (d) {
    const f = v => {
        const s = String(v || '').replace(/\D/g, '').slice(0, 8);
        const a = [s.slice(0, 2), s.slice(2, 4), s.slice(4, 8)].filter(Boolean);
        return a.join('/')
    };
    d.addEventListener('input', e => {
        const v = e.target.value;
        e.target.value = f(v)
    });
    d.addEventListener('keypress', e => {
        if (!/[0-9]/.test(e.key)) e.preventDefault()
    });
    d.addEventListener('blur', e => {
        e.target.value = f(e.target.value)
    })
}
const s = document.getElementById('opinion-success');
if (s) {
    setTimeout(() => {
        s.classList.add('opacity-0')
    }, 5000);
    setTimeout(() => {
        s.remove()
    }, 5600)
}
const form = document.getElementById('opinion-form');
const l = document.getElementById('opinion-loading');
const saveNewInput = document.getElementById('save-new-input');
const btnSaveNew = document.getElementById('btn-save-new');
const btnSave = document.getElementById('btn-save');
if (btnSaveNew && saveNewInput) {
    btnSaveNew.addEventListener('click', () => {
        saveNewInput.value = '1'
    })
}
if (btnSave && saveNewInput) {
    btnSave.addEventListener('click', () => {
        saveNewInput.value = '0'
    })
}
if (form) {
    form.addEventListener('submit', () => {
        if (l) {
            l.classList.remove('hidden');
            l.classList.add('flex')
        }
        for (const b of form.querySelectorAll('button[type="submit"]')) {
            b.disabled = true;
            b.classList.add('opacity-60', 'cursor-not-allowed')
        }
    })
}

const eb = document.getElementById('btn-extract');
const ei = document.getElementById('extract-doc-input');
const es = document.getElementById('extract-spinner');
const el = document.getElementById('extract-label');
const p = document.getElementById('extract-panel');
const main = document.getElementById('opinion-main');
const back = document.getElementById('btn-extract-back');
const choose = document.getElementById('btn-extract-choose');
const fname = document.getElementById('extract-file-name');
const status = document.getElementById('extract-status');
const err = document.getElementById('extract-error');
const et = document.getElementById('extract-title');
const eon = document.getElementById('extract-opinion-number');
const ed = document.getElementById('extract-date');
const ec = document.getElementById('extract-context');
const apply = document.getElementById('btn-extract-apply');
const titleInput = document.querySelector('input[name="title"]');
const opinionInput = document.querySelector('input[name="opinion_number"]');
const dateInput = document.querySelector('input[name="date"]');
const ctx = document.querySelector('textarea[name="context"]');

if (eb && ei && el && p && main && back && choose && fname && status && err && et && eon && ed && ec && apply && titleInput && opinionInput && dateInput && ctx) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const url = eb.getAttribute('data-extract-url') || '';
    let lastResult = null;

    const showPanel = () => {
        p.classList.remove('hidden');
        main.classList.add('hidden');
        eb.disabled = true;
        eb.classList.add('opacity-60', 'cursor-not-allowed');
        btnSaveNew?.classList.add('hidden');
        btnSave?.classList.add('hidden')
    };

    const hidePanel = () => {
        p.classList.add('hidden');
        main.classList.remove('hidden');
        eb.disabled = false;
        eb.classList.remove('opacity-60', 'cursor-not-allowed');
        btnSaveNew?.classList.remove('hidden');
        btnSave?.classList.remove('hidden')
    };

    const setBusy = b => {
        choose.disabled = b;
        choose.classList.toggle('opacity-60', b);
        choose.classList.toggle('cursor-not-allowed', b);
        status.classList.toggle('hidden', !b);
        status.classList.toggle('flex', b);
        back.disabled = b;
        back.classList.toggle('opacity-60', b);
        apply.disabled = b || !lastResult;
        apply.classList.toggle('opacity-60', b || !lastResult)
    };

    const startExtract = async file => {
        setBusy(true);
        err.classList.add('hidden');
        const fd = new FormData();
        fd.append('document', file);
        try {
            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            });
            const data = await resp.json();
            if (!resp.ok) throw new Error(data.message || 'Extraction failed');
            lastResult = data;
            et.value = data.title || '';
            eon.value = data.opinion_number || '';
            ed.value = data.date || '';
            ec.value = data.context || '';
            apply.disabled = false;
            apply.classList.remove('opacity-60')
        } catch (e) {
            err.textContent = e.message;
            err.classList.remove('hidden')
        } finally {
            setBusy(false)
        }
    };

    eb.addEventListener('click', showPanel);
    back.addEventListener('click', hidePanel);
    choose.addEventListener('click', () => ei.click());
    ei.addEventListener('change', () => {
        const file = ei.files[0];
        if (file) {
            fname.textContent = file.name;
            const filenameWithoutExtension = file.name.replace(/\.[^/.]+$/, "");
            et.value = filenameWithoutExtension; // Set the title field with the filename
            startExtract(file)
        }
    });
    apply.addEventListener('click', () => {
        if (!lastResult) return;
        titleInput.value = et.value;
        opinionInput.value = eon.value;
        dateInput.value = ed.value;
        ctx.value = ec.value;
        hidePanel()
    })
}
</script>
