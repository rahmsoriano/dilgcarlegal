<x-admin-layout>
    <!-- Add Preview Scripts -->
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://unpkg.com/docx-preview/dist/docx-preview.js"></script>
    @endpush

    <div class="space-y-8" x-data="{ 
        editingLaw: null, 
        editingTitle: '', 
        viewingDoc: null,
        loadingPreview: false,
        errorPreview: false,
        
        get externalUrl() {
            if (!this.viewingDoc) return '#';
            // If it's a PDF, browsers can open it directly. 
            // If it's a DOCX, we use Google Docs Viewer to force it to open in browser instead of downloading.
            if (this.viewingDoc.endsWith('.pdf')) return this.viewingDoc;
            return 'https://docs.google.com/viewer?url=' + encodeURIComponent(window.location.origin + this.viewingDoc);
        },
        
        async previewDoc(url) {
            this.viewingDoc = url;
            this.loadingPreview = true;
            this.errorPreview = false;
            
            if (url.endsWith('.docx')) {
                // Wait for the container to be ready
                this.$nextTick(async () => {
                    const container = document.getElementById('docx-container');
                    if (!container) return;
                    
                    try {
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Failed to fetch document');
                        const blob = await response.blob();
                        
                        // Clear container
                        container.innerHTML = '';
                        
                        // Render docx
                        await docx.renderAsync(blob, container);
                        this.loadingPreview = false;
                    } catch (err) {
                        console.error('Docx Preview Error:', err);
                        this.errorPreview = true;
                        this.loadingPreview = false;
                    }
                });
            } else {
                this.loadingPreview = false;
            }
        }
    }">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Manage Law Documents</h1>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    View, rename, or delete your uploaded legal references.
                </p>
            </div>
            
            <div x-data="{ uploading: false }">
                <form id="lawUploadFormPage" action="{{ route('admin.laws.upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" id="lawFilesPage" name="documents[]" multiple accept=".pdf,.docx,.zip"
                           x-on:change="uploading = true; $el.form.submit()">
                </form>
                <button type="button" 
                        x-on:click="document.getElementById('lawFilesPage').click()"
                        class="flex items-center gap-3 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span x-show="!uploading">Upload New</span>
                    <span x-show="uploading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:leave="transition ease-in duration-1000"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="rounded-2xl bg-emerald-500/10 p-4 ring-1 ring-emerald-500/20 text-emerald-400 text-sm font-medium shadow-lg shadow-emerald-500/5">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Laws Table -->
        <div class="rounded-[2.5rem] bg-white/[0.02] backdrop-blur-xl ring-1 ring-white/10 shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-white/[0.03] text-[11px] font-bold uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="px-8 py-5">Document Name</th>
                            <th class="px-8 py-5">Format</th>
                            <th class="px-8 py-5">Uploaded At</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($laws as $law)
                            <tr class="group hover:bg-white/[0.02] transition-all">
                                <td class="px-8 py-5">
                                    <div x-show="editingLaw !== {{ $law->id }}" class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 ring-1 ring-white/10 group-hover:bg-blue-500/10 group-hover:ring-blue-500/20 transition-all">
                                            @if(Str::endsWith($law->file_path, '.pdf'))
                                                <svg class="h-5 w-5 text-rose-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                            @elseif(Str::endsWith($law->file_path, '.zip'))
                                                <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-white">{{ $law->title }}</span>
                                    </div>
                                    <div x-show="editingLaw === {{ $law->id }}" class="flex items-center gap-2">
                                        <form action="{{ route('admin.laws.update', $law) }}" method="POST" class="flex items-center gap-2 w-full">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="title" x-model="editingTitle" class="w-full rounded-xl bg-white/5 border-white/10 text-sm text-white focus:ring-blue-500 focus:border-blue-500">
                                            <button type="submit" class="p-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                            </button>
                                            <button type="button" x-on:click="editingLaw = null" class="p-2 rounded-lg bg-white/5 text-slate-400 hover:text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center rounded-md bg-white/5 px-2 py-1 text-xs font-medium text-slate-400 ring-1 ring-inset ring-white/10 capitalize">
                                        {{ pathinfo($law->file_path, PATHINFO_EXTENSION) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-slate-500 font-medium">
                                    {{ $law->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- View Button (Eye) -->
                                        <button type="button" 
                                                x-on:click="previewDoc('{{ Storage::url($law->file_path) }}')"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/5 text-slate-400 hover:bg-emerald-500/10 hover:text-emerald-400 transition-all group/btn"
                                                title="View Document">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>

                                        <!-- Edit Button -->
                                        <button x-on:click="editingLaw = {{ $law->id }}; editingTitle = '{{ $law->title }}'"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/5 text-slate-400 hover:bg-blue-500/10 hover:text-blue-400 transition-all"
                                                title="Edit Title">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.laws.destroy', $law) }}" method="POST" data-confirm="delete" data-confirm-title="Delete Document" data-confirm-message="Are you sure you want to delete this document?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/5 text-slate-400 hover:bg-rose-500/10 hover:text-rose-400 transition-all"
                                                    title="Delete Document">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.108 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-[2rem] bg-white/5 ring-1 ring-white/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-white">No documents found</p>
                                        <p class="text-sm text-slate-500">Upload your first law document to get started.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($laws->hasPages())
                <div class="px-8 py-5 border-t border-white/5 bg-white/[0.01]">
                    {{ $laws->links() }}
                </div>
            @endif
        </div>

        <!-- Document Viewer Modal -->
        <div x-show="viewingDoc" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" x-on:click="viewingDoc = null"></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-6xl h-full max-h-[90vh] bg-[#1e1e1e] rounded-[2.5rem] shadow-2xl ring-1 ring-white/10 flex flex-col overflow-hidden"
                 x-on:click.stop
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-8 py-6 border-b border-white/5 bg-[#171717]">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Document Preview</h3>
                            <p class="text-xs text-slate-500">Viewing legal reference</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="externalUrl" target="_blank" class="p-3 rounded-xl bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white transition-all" title="Open in Browser">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                        <button x-on:click="viewingDoc = null" class="p-3 rounded-xl bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 bg-white relative overflow-auto">
                    <!-- PDF Viewer -->
                    <template x-if="viewingDoc && viewingDoc.endsWith('.pdf')">
                        <embed :src="viewingDoc" type="application/pdf" class="w-full h-full" />
                    </template>
                    
                    <!-- DOCX Viewer Container -->
                    <div x-show="viewingDoc && viewingDoc.endsWith('.docx')" id="docx-container" class="w-full h-full p-8 bg-slate-100 overflow-auto"></div>

                    <!-- ZIP or Others -->
                    <template x-if="viewingDoc && !viewingDoc.endsWith('.pdf') && !viewingDoc.endsWith('.docx')">
                        <div class="flex flex-col items-center justify-center h-full gap-4 bg-[#1e1e1e]">
                            <div class="h-20 w-20 rounded-3xl bg-white/5 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <p class="text-white font-semibold text-lg">Preview not available for this format</p>
                            <a :href="viewingDoc" class="px-6 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-500 transition-all">Download to View</a>
                        </div>
                    </template>
                    
                    <!-- Loading Overlay -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-[#1e1e1e]/90 z-50" x-show="loadingPreview">
                        <svg class="animate-spin h-10 w-10 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-white/60 text-sm font-medium">Preparing document preview...</p>
                    </div>

                    <!-- Error State -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-[#1e1e1e] z-50" x-show="errorPreview">
                        <div class="h-16 w-16 rounded-2xl bg-rose-500/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-rose-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <p class="text-white font-bold">Failed to load preview</p>
                        <p class="text-white/40 text-xs mb-6">The file might be corrupted or in an unsupported format.</p>
                        <button x-on:click="previewDoc(viewingDoc)" class="px-6 py-2 rounded-xl bg-white/5 text-white font-bold hover:bg-white/10 transition-all border border-white/10">Retry</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
