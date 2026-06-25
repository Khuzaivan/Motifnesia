@extends('admin.layouts.mainLayout')

@section('title', 'Konten Statis')

@section('content')
<div class="space-y-6">
    {{-- Toolbar --}}
    <div class="glass-card rounded-2xl p-4 flex items-center justify-between animate-fade-slide-up">
        <h2 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-white">Konten Slideshow</h2>
        <button id="btn-add" class="flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-all duration-200 shadow-lg shadow-amber-500/20 hover:scale-[1.02] text-sm">
            <i class="ri-add-line text-lg"></i> Tambah Slide
        </button>
    </div>

    {{-- Table --}}
    <div class="glass-card rounded-3xl overflow-hidden animate-fade-slide-up" style="animation-delay: 0.1s;">
        <div class="overflow-x-auto custom-scrollbar pb-2">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-xl font-bold">Preview</th>
                        <th class="px-6 py-4 font-bold">Nama Slide</th>
                        <th class="px-6 py-4 text-center rounded-tr-xl font-bold w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody id="slides-tbody" class="divide-y divide-white/5">
                    @forelse($slides as $s)
                        <tr class="hover:bg-slate-700/50 transition-colors group" data-id="{{ $s->id }}">
                            <td class="px-6 py-4">
                                <div class="w-32 h-20 rounded-xl overflow-hidden bg-slate-900 border border-white/10 shadow-md group-hover:border-amber-500/50 transition-colors">
                                    <img src="{{ asset($s->gambar) }}" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-white">{{ $s->judul }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button class="btn-edit w-9 h-9 bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white rounded-lg flex items-center justify-center transition-all" data-id="{{ $s->id }}" title="Edit">
                                        <i class="ri-edit-line text-lg"></i>
                                    </button>
                                    <button class="btn-delete w-9 h-9 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white rounded-lg flex items-center justify-center transition-all" data-id="{{ $s->id }}" title="Delete">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                <i class="ri-image-line text-4xl block mb-2 opacity-50"></i>
                                Belum ada slide.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="slide-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 items-center justify-center p-4 hidden">
    <div class="bg-slate-800 border border-white/10 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="slide-modal-content">
        {{-- Header --}}
        <div class="bg-slate-900/50 px-6 py-5 flex items-center justify-between border-b border-white/5">
            <h3 id="modal-title" class="text-lg font-bold font-['Plus_Jakarta_Sans'] text-white">Tambah Slide</h3>
            <button class="btn-cancel text-slate-400 hover:text-white transition-colors">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        {{-- Body --}}
        <div class="p-6">
            <input type="hidden" id="modal-id" value="">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-300 mb-2">Nama Judul</label>
                <input type="text" id="modal-judul" class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors" placeholder="Masukkan judul slide" />
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-300 mb-2">File Gambar</label>
                <input type="file" id="modal-gambar" class="w-full px-4 py-2.5 bg-slate-900/50 border border-white/10 rounded-xl text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-500/20 file:text-amber-400 hover:file:bg-amber-500/30 transition-colors" />
                <div id="modal-preview" class="mt-4 rounded-xl overflow-hidden border border-white/10 hidden bg-slate-900 flex justify-center"></div>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="bg-slate-900/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-white/5">
            <button class="btn-cancel bg-white/5 hover:bg-white/10 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">Batal</button>
            <button id="modal-save" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-lg shadow-amber-500/20 hover:scale-[1.02]">Simpan</button>
        </div>
    </div>
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';
    const modal = document.getElementById('slide-modal');
    const modalContent = document.getElementById('slide-modal-content');

    function openModal(mode='create', data=null){
        modal.style.display = 'flex';
        setTimeout(() => modalContent.classList.remove('scale-95'), 10);
        
        document.getElementById('modal-id').value = data ? data.id : '';
        document.getElementById('modal-judul').value = data ? (data.judul ?? '') : '';
        document.getElementById('modal-title').innerText = mode === 'create' ? 'Tambah Slide' : 'Edit Slide';
        document.getElementById('modal-save').innerText = mode === 'create' ? 'Tambah' : 'Simpan';
        
        const preview = document.getElementById('modal-preview'); 
        preview.innerHTML = '';
        preview.classList.add('hidden');
        
        if (data && data.gambar) {
            const img = document.createElement('img'); 
            img.src = '{{ url('') }}/' + data.gambar; 
            img.className = 'w-full object-cover max-h-48'; 
            preview.appendChild(img);
            preview.classList.remove('hidden');
        }
        document.getElementById('modal-gambar').value = '';
        document.getElementById('modal-save').dataset.mode = mode;
    }

    function closeModal(){ 
        modalContent.classList.add('scale-95');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    document.getElementById('btn-add').addEventListener('click', ()=> openModal('create'));
    document.querySelectorAll('.btn-cancel').forEach(btn => btn.addEventListener('click', (e)=>{ e.preventDefault(); closeModal(); }));

    document.getElementById('modal-save').addEventListener('click', async (e)=>{
        e.preventDefault();
        const btn = e.target;
        const originalText = btn.innerText;
        btn.innerText = 'Menyimpan...';
        btn.disabled = true;

        const mode = btn.dataset.mode || 'create';
        const id = document.getElementById('modal-id').value;
        const judul = document.getElementById('modal-judul').value;
        const fileInput = document.getElementById('modal-gambar');
        const fd = new FormData();
        fd.append('judul', judul);
        if (fileInput.files.length>0) fd.append('gambar', fileInput.files[0]);

        let url = '';
        if (mode === 'create') url = '{{ url('/admin/konten/slides/create') }}';
        else url = '{{ url('/admin/konten/slides') }}/' + id + '/update';

        try {
            const res = await fetch(url, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrfToken, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }, body: fd });
            const json = await res.json();
            if (json.success) {
                location.reload();
            } else {
                alert('Gagal menyimpan');
            }
        } catch(e) {
            alert('Gagal menyimpan');
        } finally {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    });

    document.querySelectorAll('.btn-edit').forEach(b => {
        b.onclick = async (e)=>{
            const id = b.closest('button').dataset.id;
            const tr = document.querySelector(`tr[data-id="${id}"]`);
            const img = tr.querySelector('img');
            const judul = tr.children[1].innerText;
            const data = { id: id, judul: judul, gambar: img ? img.getAttribute('src').replace(location.origin+'/', '') : null };
            openModal('edit', data);
        };
    });

    document.querySelectorAll('.btn-delete').forEach(b => {
        b.onclick = async (e)=>{
            if (!confirm('Hapus slide ini?')) return;
            const id = b.closest('button').dataset.id;
            const res = await fetch('{{ url('/admin/konten/slides') }}/' + id + '/delete', { method:'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
            const json = await res.json();
            if (json.success) {
                location.reload();
            } else alert('Gagal menghapus');
        };
    });
</script>
@endsection
