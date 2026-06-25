<style>
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        inset: 0;
        width: 100%;
        height: 100%;
        background: rgba(10, 10, 10, 0.65);
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        padding: 40px 18px;
        align-items: center;
        justify-content: center;
    }

    .hist-modal-content {
        width: min(920px, 100%);
        max-height: min(82vh, 840px);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.03) !important;
        backdrop-filter: blur(16px) saturate(120%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(120%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 20px;
        box-shadow: 0 28px 80px rgba(0, 0, 0, .65);
        transform-style: preserve-3d;
    }

    .hist-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 22px 26px;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
        background: linear-gradient(180deg, rgba(201, 168, 76, .08), rgba(255, 255, 255, .01));
    }

    .hist-modal-header h2 {
        margin: 0;
        color: #f6f0e4;
        font-size: 22px;
        font-weight: 800;
        font-family: "Playfair Display", serif;
    }

    .hist-close-btn {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        color: rgba(255, 255, 255, .58);
        background: rgba(255, 255, 255, .05);
        border: 1px solid rgba(255, 255, 255, .08);
        cursor: pointer;
        font-size: 24px;
        line-height: 1;
        transition: all .2s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .hist-close-btn:hover {
        color: #111;
        background: #c9a84c;
        border-color: #c9a84c;
    }

    .hist-modal-body {
        padding: 22px 26px 26px;
        overflow-y: auto;
        flex: 1;
        background: rgba(19, 19, 19, 0.6);
    }

    .history-item-glass {
        display: grid;
        grid-template-columns: 82px minmax(0, 1fr) 180px;
        align-items: center;
        gap: 18px;
        padding: 16px;
        border: 1px solid rgba(255, 255, 255, .05) !important;
        border-radius: 16px;
        margin-bottom: 14px;
        background: rgba(255, 255, 255, .015) !important;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .history-item-glass:hover {
        transform: translateY(-3px) scale(1.005);
        border-color: rgba(201, 168, 76, 0.3) !important;
        background: rgba(255, 255, 255, 0.035) !important;
        box-shadow: 0 10px 25px rgba(201, 168, 76, 0.06);
    }

    .product-thumb-hist {
        width: 82px;
        height: 82px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, .10);
        background: #0f0f0f;
    }

    .product-info-hist {
        min-width: 0;
    }

    .product-name-hist {
        font-weight: 800;
        font-size: 16px;
        display: block;
        margin-bottom: 6px;
        color: #f5efe2;
        overflow-wrap: anywhere;
    }

    .product-details-hist {
        font-size: 13px;
        color: rgba(255, 255, 255, .58);
        line-height: 1.55;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: fit-content;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        margin-top: 10px;
        border: 1px solid rgba(255, 255, 255, .08);
    }

    .status-pending { background: rgba(148, 163, 184, .12); color: #cbd5e1; }
    .status-diproses { background: rgba(59, 130, 246, .12); color: #93c5fd; }
    .status-dikemas { background: rgba(168, 85, 247, .12); color: #c4b5fd; }
    .status-dalam-perjalanan { background: rgba(217, 179, 76, .13); color: #e8ca72; }
    .status-sampai { background: rgba(52, 211, 153, .12); color: #86efac; }

    .history-actions {
        display: flex;
        flex-direction: column;
        gap: 9px;
        align-items: stretch;
    }

    .btn-review {
        width: 100%;
        min-height: 40px;
        padding: 8px 16px;
        border: 0;
        border-radius: 10px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: transform .2s ease, opacity .2s ease, background .2s ease;
        line-height: 1.15;
    }

    .btn-review:hover {
        transform: translateY(-1px);
    }

    .btn-ulasan {
        background: linear-gradient(135deg, #c9a84c, #a8832d);
        color: #111;
    }
    .btn-ulasan:hover {
        background: linear-gradient(135deg, #d7b957, #b99535);
    }

    .btn-view-ulasan {
        background: rgba(255, 255, 255, .05);
        color: #c9a84c;
        border: 1px solid rgba(201, 168, 76, .2);
    }
    .btn-view-ulasan:hover {
        background: rgba(201, 168, 76, .1);
    }

    .btn-confirm-arrival {
        background: rgba(52, 211, 153, .12);
        color: #6ee7b7;
        border: 1px solid rgba(52, 211, 153, .22);
    }
    .btn-confirm-arrival:hover {
        background: rgba(52, 211, 153, .2);
    }

    .btn-retur {
        background: rgba(245, 158, 11, .12);
        color: #fcd34d;
        border: 1px solid rgba(245, 158, 11, .25);
    }
    .btn-retur:hover {
        background: rgba(245, 158, 11, .2);
    }

    .btn-retur-approved {
        background: rgba(52, 211, 153, .12);
        color: #6ee7b7;
        border: 1px solid rgba(52, 211, 153, .22);
    }

    .btn-retur-rejected {
        background: rgba(239, 68, 68, .12);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, .22);
    }

    .btn-retur-muted,
    .btn-review:disabled {
        background: rgba(255, 255, 255, .05);
        color: rgba(255, 255, 255, .3);
        border: 1px solid rgba(255, 255, 255, .06);
        cursor: not-allowed;
        transform: none;
    }

    .return-deadline {
        margin-top: 8px;
        color: rgba(255, 255, 255, .4);
        font-size: 12px;
    }

    .history-empty {
        text-align: center;
        color: rgba(255, 255, 255, .45);
        padding: 46px 0;
        font-weight: 600;
    }

    @media (max-width: 760px) {
        .custom-modal {
            padding: 16px 12px;
        }

        .hist-modal-content {
            max-height: 88vh;
            border-radius: 16px;
        }

        .hist-modal-header,
        .hist-modal-body {
            padding-left: 16px;
            padding-right: 16px;
        }

        .history-item-glass {
            grid-template-columns: 68px minmax(0, 1fr);
            align-items: start;
        }

        .product-thumb-hist {
            width: 68px;
            height: 68px;
        }

        .history-actions {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="purchaseHistoryModal" class="custom-modal">
    <div class="hist-modal-content">
        <div class="hist-modal-header">
            <h2>Riwayat Pembelian</h2>
            <span class="hist-close-btn btn-magnetic" onclick="closePurchaseHistoryModal()">&times;</span>
        </div>

        <div class="hist-modal-body">
            @forelse ($purchaseHistory as $item)
                @php
                    $deadlineText = $item['return_deadline_at'] ? \Carbon\Carbon::parse($item['return_deadline_at'])->format('d M Y') : null;
                    $returnStatus = $item['return_status'] ?? null;
                    $returnStage = $item['return_stage'] ?? null;
                @endphp

                <div class="history-item-glass">
                    <img src="{{ $item['gambar_url'] ?? \App\Support\AssetUrl::product($item['gambar'] ?? null) }}" alt="{{ $item['nama'] }}" class="product-thumb-hist">

                    <div class="product-info-hist">
                        <span class="product-name-hist">{{ $item['nama'] }}</span>
                        <div class="product-details-hist">
                            Ukuran: {{ $item['ukuran'] }} | Qty: {{ $item['qty'] }} | Rp {{ number_format($item['harga'], 0, ',', '.') }}
                        </div>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $item['status_nama'])) }}">
                            {{ $item['status_nama'] }}
                        </span>
                        @if($deadlineText && $item['can_return'])
                            <div class="return-deadline">Tenggat retur: {{ $deadlineText }}</div>
                        @endif
                    </div>

                    <div class="history-actions">
                        @if ($item['can_confirm_arrival'])
                            <form action="{{ route('customer.orders.confirmArrived', $item['order_id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-review btn-confirm-arrival btn-magnetic">
                                    Konfirmasi Sampai
                                </button>
                            </form>
                        @endif

                        @if ($item['status_ulasan'] === 'beri')
                            <button class="btn-review btn-ulasan btn-open-review btn-magnetic"
                                    data-order-item-id="{{ $item['order_item_id'] }}"
                                    data-produk-id="{{ $item['produk_id'] }}"
                                    data-product-name="{{ $item['nama'] }}">
                                Beri Ulasan
                            </button>
                        @elseif ($item['status_ulasan'] === 'lihat')
                            <button class="btn-review btn-view-ulasan btn-open-view-review btn-magnetic"
                                    data-order-item-id="{{ $item['order_item_id'] }}"
                                    data-product-name="{{ $item['nama'] }}">
                                Lihat Ulasan
                            </button>
                        @else
                            <button class="btn-review btn-ulasan btn-magnetic" disabled title="Konfirmasi pesanan sampai terlebih dahulu">
                                Beri Ulasan
                            </button>
                        @endif

                        @if ($item['can_return'])
                            <a href="{{ route('customer.returns.create', $item['order_item_id']) }}" class="btn-review btn-retur btn-magnetic">
                                Ajukan Retur
                            </a>
                        @elseif($item['has_return'])
                            @if($returnStatus === 'Disetujui' && $returnStage !== 'courier_proof_submitted')
                                <a href="{{ route('customer.returns.index') }}" class="btn-review btn-retur-approved btn-magnetic">
                                    Upload Bukti Kurir
                                </a>
                            @elseif($returnStatus === 'Ditolak')
                                <button class="btn-review btn-retur-rejected" disabled>
                                    Retur Ditolak
                                </button>
                            @elseif($returnStatus === 'Disetujui')
                                <button class="btn-review btn-retur-approved" disabled>
                                    Retur Disetujui
                                </button>
                            @elseif($returnStatus === 'Diproses')
                                <button class="btn-review btn-retur-muted" disabled>
                                    Retur Diproses
                                </button>
                            @elseif($returnStatus === 'Selesai')
                                <button class="btn-review btn-retur-muted" disabled>
                                    Retur Selesai
                                </button>
                            @else
                                <button class="btn-review btn-retur-muted" disabled>
                                    Menunggu Retur
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <p class="history-empty">Belum ada riwayat pembelian</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    function openPurchaseHistoryModal() {
        const modal = document.getElementById('purchaseHistoryModal');
        const content = modal.querySelector('.hist-modal-content');
        
        modal.style.display = 'flex';
        
        // GSAP 3D Entrance Animation
        gsap.set(modal, { perspective: 1000 });
        
        const tl = gsap.timeline();
        tl.fromTo(modal, 
            { opacity: 0 }, 
            { opacity: 1, duration: 0.3, ease: 'power2.out' }
        );
        tl.fromTo(content, 
            { opacity: 0, y: -60, scale: 0.9, rotationX: -15 }, 
            { opacity: 1, y: 0, scale: 1, rotationX: 0, duration: 0.5, ease: 'back.out(1.2)' },
            '-=0.2'
        );

        // Bind magnetic button behaviors dynamically
        modal.querySelectorAll('.btn-magnetic').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                btn.style.transform = `translate(${x * 0.25}px, ${y * 0.25}px)`;
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = '';
            });
        });
    }

    function closePurchaseHistoryModal() {
        const modal = document.getElementById('purchaseHistoryModal');
        const content = modal.querySelector('.hist-modal-content');
        
        const tl = gsap.timeline({
            onComplete: () => {
                modal.style.display = 'none';
            }
        });
        
        tl.to(content, { opacity: 0, y: 40, scale: 0.95, rotationX: 10, duration: 0.3, ease: 'power2.in' });
        tl.to(modal, { opacity: 0, duration: 0.2 }, '-=0.1');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const openBtn = document.getElementById('openHistoryModalBtn');
        if (openBtn) {
            openBtn.onclick = null; // Unbind conflicting modal.js click handlers!
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openPurchaseHistoryModal();
            });
        }

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('purchaseHistoryModal');
            if (event.target === modal) {
                closePurchaseHistoryModal();
            }
        });
    });
</script>
