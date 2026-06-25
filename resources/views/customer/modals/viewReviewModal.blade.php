<style>
    .view-review-modal-content {
        width: min(500px, 100%);
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

    .view-review-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 22px 26px;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
        background: linear-gradient(180deg, rgba(201, 168, 76, .08), rgba(255, 255, 255, .01));
    }

    .view-review-modal-header h2 {
        margin: 0;
        color: #f6f0e4;
        font-size: 20px;
        font-weight: 800;
        font-family: "Playfair Display", serif;
    }

    .view-review-modal-body {
        padding: 24px 26px;
        background: rgba(19, 19, 19, 0.6);
        color: rgba(255, 255, 255, .8);
    }

    .back-btn-gold {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        background: rgba(201, 168, 76, .1);
        border: 1px solid rgba(201, 168, 76, .25);
        color: #c9a84c;
        border-radius: 999px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        transition: all .2s;
    }

    .back-btn-gold:hover {
        background: rgba(201, 168, 76, .2);
    }

    .review-detail-card {
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
        padding: 18px;
        background: rgba(255, 255, 255, 0.01);
        margin-top: 14px;
    }

    .review-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .review-detail-user {
        font-weight: 700;
        font-size: 15px;
        color: #fff;
    }

    .review-detail-date {
        color: rgba(255, 255, 255, .4);
        font-size: 12px;
    }

    .review-detail-stars {
        display: flex;
        gap: 6px;
        margin-bottom: 12px;
        font-size: 20px;
    }
    
    .review-detail-stars .fa-star {
        color: rgba(255, 255, 255, .15);
    }

    .review-detail-stars .fa-star.checked {
        color: #c9a84c !important;
    }

    .review-detail-text {
        color: rgba(255, 255, 255, .7);
        line-height: 1.6;
        font-size: 14px;
        background: rgba(255, 255, 255, 0.02);
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.03);
    }
</style>

<div id="viewReviewModal" class="custom-modal" style="display:none;">
    <div class="view-review-modal-content">
        <div class="view-review-modal-header">
            <button class="back-btn-gold btn-magnetic" onclick="backToPurchaseHistory()">← Kembali</button>
            <h2 id="vrTitle">Ulasan Anda</h2>
            <span class="close-btn btn-magnetic" onclick="closeViewReviewModal()">&times;</span>
        </div>

        <div class="view-review-modal-body">
            <h3 id="vrProductName" class="review-product-name" style="margin-bottom: 10px;">Produk</h3>
            <div id="vrContent">
                <p style="color:rgba(255,255,255,.4); font-size: 14px;">Memuat ulasan...</p>
            </div>
        </div>
    </div>
</div>

<script>
    function openViewReviewModal(orderItemId, productName) {
        document.getElementById('vrProductName').textContent = productName || 'Produk';
        
        const modal = document.getElementById('viewReviewModal');
        const content = modal.querySelector('.view-review-modal-content');
        
        modal.style.zIndex = 10005;
        modal.style.display = 'flex';
        
        // GSAP 3D Entrance Animation
        gsap.set(modal, { perspective: 1000 });
        const tl = gsap.timeline();
        tl.fromTo(modal, { opacity: 0 }, { opacity: 1, duration: 0.3, ease: 'power2.out' });
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

        loadUserReview(orderItemId);
    }

    function closeViewReviewModal() {
        const modal = document.getElementById('viewReviewModal');
        const content = modal.querySelector('.view-review-modal-content');
        
        const tl = gsap.timeline({
            onComplete: () => {
                modal.style.display = 'none';
            }
        });
        tl.to(content, { opacity: 0, y: 40, scale: 0.95, rotationX: 10, duration: 0.3, ease: 'power2.in' });
        tl.to(modal, { opacity: 0, duration: 0.2 }, '-=0.1');
    }

    function backToPurchaseHistory() {
        closeViewReviewModal();
        // Delay opening of history modal slightly to let this modal finish closing animation
        setTimeout(() => {
            openPurchaseHistoryModal();
        }, 320);
    }

    function loadUserReview(orderItemId) {
        const content = document.getElementById('vrContent');
        content.innerHTML = '<p style="color:rgba(255,255,255,.4); font-size:14px;">Memuat ulasan...</p>';
        
        console.log('Loading review for orderItemId:', orderItemId);
        
        fetch('/order-reviews/' + orderItemId, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(r => {
            console.log('Response status:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success && data.review) {
                const rv = data.review;
                let html = '<div class="review-detail-card">';
                html += '<div class="review-detail-header">';
                html += '<span class="review-detail-user">Anda</span>';
                html += '<span class="review-detail-date">' + rv.created_at + '</span>';
                html += '</div>';
                html += '<div class="review-detail-stars">';
                for (let i = 1; i <= 5; i++) {
                    html += '<span class="fa fa-star' + (i <= rv.rating ? ' checked' : '') + '"></span>';
                }
                html += '</div>';
                html += '<div class="review-detail-text">' + (rv.deskripsi_ulasan || 'Tidak ada deskripsi') + '</div>';
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = '<p style="color:rgba(255,255,255,.4); font-size:14px;">Ulasan tidak ditemukan.</p>';
            }
        })
        .catch(err => {
            console.error(err);
            content.innerHTML = '<p style="color:rgba(255,239,239,.6); font-size:14px;">Gagal memuat ulasan.</p>';
        });
    }

    // Event delegation for "Lihat Ulasan" button
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-open-view-review') || e.target.closest('.btn-open-view-review')) {
                const btn = e.target.classList.contains('btn-open-view-review') ? e.target : e.target.closest('.btn-open-view-review');
                const orderItemId = btn.getAttribute('data-order-item-id');
                const productName = btn.getAttribute('data-product-name');
                console.log('View review button clicked:', { orderItemId, productName });
                openViewReviewModal(orderItemId, productName);
            }
        });
    });

    // Close modal on outside click
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('viewReviewModal');
        if (event.target === modal) {
            closeViewReviewModal();
        }
    });
</script>