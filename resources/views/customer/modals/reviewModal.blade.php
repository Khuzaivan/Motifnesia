<style>
    .review-modal-sm {
        max-width: 500px !important;
    }

    .review-modal-content {
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

    .review-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 22px 26px;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
        background: linear-gradient(180deg, rgba(201, 168, 76, .08), rgba(255, 255, 255, .01));
    }

    .review-modal-header h2 {
        margin: 0;
        color: #f6f0e4;
        font-size: 20px;
        font-weight: 800;
        font-family: "Playfair Display", serif;
    }

    .review-modal-body {
        padding: 24px 26px;
        background: rgba(19, 19, 19, 0.6);
        color: rgba(255, 255, 255, .8);
    }

    .review-product-name {
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 20px;
        color: #c9a84c;
        font-family: "Playfair Display", serif;
    }

    .rating-section {
        margin-bottom: 22px;
    }

    .rating-section p {
        margin-bottom: 10px;
        font-weight: 600;
        font-size: 14px;
        color: rgba(255, 255, 255, .7);
    }

    .stars {
        display: flex;
        gap: 12px;
        font-size: 34px;
    }

    .stars .fa-star {
        cursor: pointer;
        color: rgba(255, 255, 255, .15);
        transition: color 0.2s, transform 0.2s;
    }
    
    .stars .fa-star:hover {
        transform: scale(1.15);
    }

    .stars .fa-star.checked,
    .stars .fa-star:hover,
    .stars .fa-star.checked ~ .fa-star:hover {
        color: #c9a84c !important;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group p {
        margin-bottom: 10px;
        font-weight: 600;
        font-size: 14px;
        color: rgba(255, 255, 255, .7);
    }

    .review-textarea {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, .05);
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 12px;
        color: #fff;
        font-family: inherit;
        font-size: 14px;
        resize: vertical;
        outline: none;
        transition: border-color 0.2s;
    }

    .review-textarea:focus {
        border-color: #c9a84c;
    }

    .btn-submit-review {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #c9a84c, #a8832d);
        color: #111;
        border: none;
        border-radius: 999px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: .02em;
        transition: all .25s;
    }

    .btn-submit-review:hover {
        background: linear-gradient(135deg, #d7b957, #b99535);
    }
    
    .btn-submit-review:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<div id="reviewModal" class="custom-modal" style="display:none;">
    <div class="review-modal-content">
        <div class="review-modal-header">
            <h2 id="rmTitle">Beri Ulasan</h2>
            <span class="close-btn btn-magnetic" onclick="closeReviewModal()">&times;</span>
        </div>

        <div class="review-modal-body">
            <h3 id="rmProductName" class="review-product-name">Produk</h3>

            <div class="rating-section">
                <p>Beri Rating:</p>
                <div class="stars" id="rmStars">
                    <span class="fa fa-star" data-value="1"></span>
                    <span class="fa fa-star" data-value="2"></span>
                    <span class="fa fa-star" data-value="3"></span>
                    <span class="fa fa-star" data-value="4"></span>
                    <span class="fa fa-star" data-value="5"></span>
                </div>
            </div>

            <div class="form-group">
                <p>Deskripsi Ulasan:</p>
                <textarea id="rmComment" class="review-textarea" rows="4" placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
            </div>

            <input type="hidden" id="rmOrderItemId" value="">
            <input type="hidden" id="rmProductId" value="">

            <div style="margin-top: 24px;">
                <button class="btn-submit-review btn-magnetic" id="rmSubmit">Kirim Ulasan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const RM_CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
    let rmRating = 0;

    function openReviewModal(orderItemId, productName, productId) {
        console.log('Opening modal with:', { orderItemId, productName, productId });
        
        const modal = document.getElementById('reviewModal');
        const content = modal.querySelector('.review-modal-content');
        
        document.getElementById('rmOrderItemId').value = orderItemId;
        document.getElementById('rmProductId').value = productId;
        document.getElementById('rmProductName').textContent = productName || 'Produk';
        
        console.log('Set hidden values - OrderItemId:', document.getElementById('rmOrderItemId').value, 'ProductId:', document.getElementById('rmProductId').value);
        
        modal.style.zIndex = 10005;
        modal.style.pointerEvents = 'auto';
        modal.style.display = 'flex';
        
        // Reset form
        rmRating = 0;
        document.getElementById('rmComment').value = '';
        updateStarUI(0);

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
    }

    function closeReviewModal() {
        const modal = document.getElementById('reviewModal');
        const content = modal.querySelector('.review-modal-content');
        
        const tl = gsap.timeline({
            onComplete: () => {
                modal.style.display = 'none';
            }
        });
        tl.to(content, { opacity: 0, y: 40, scale: 0.95, rotationX: 10, duration: 0.3, ease: 'power2.in' });
        tl.to(modal, { opacity: 0, duration: 0.2 }, '-=0.1');
    }

    function updateStarUI(value) {
        const stars = document.querySelectorAll('#rmStars .fa-star');
        stars.forEach(s => {
            const v = parseInt(s.getAttribute('data-value'));
            if (v <= value) {
                s.classList.add('checked');
            } else {
                s.classList.remove('checked');
            }
        });
    }

    // Star rating click handler
    document.addEventListener('DOMContentLoaded', function() {
        const starsContainer = document.getElementById('rmStars');
        if (starsContainer) {
            starsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('fa-star')) {
                    rmRating = parseInt(e.target.getAttribute('data-value')) || 0;
                    console.log('Rating selected:', rmRating);
                    updateStarUI(rmRating);
                }
            });
        }
        
        // Button click handler for opening review modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-open-review') || e.target.closest('.btn-open-review')) {
                const btn = e.target.classList.contains('btn-open-review') ? e.target : e.target.closest('.btn-open-review');
                const orderItemId = btn.getAttribute('data-order-item-id');
                const productId = btn.getAttribute('data-produk-id');
                const productName = btn.getAttribute('data-product-name');
                console.log('Button clicked, opening modal with:', { orderItemId, productId, productName });
                openReviewModal(orderItemId, productName, productId);
            }
        });
    });

    document.getElementById('rmSubmit').addEventListener('click', function() {
        const orderItemId = document.getElementById('rmOrderItemId').value;
        const productId = document.getElementById('rmProductId').value;
        const comment = document.getElementById('rmComment').value.trim();
        const rating = rmRating;

        console.log('Submit clicked - Rating:', rating, 'OrderItemId:', orderItemId);

        if (!orderItemId || rating < 1) {
            alert('Pilih rating terlebih dahulu (1-5 bintang)');
            return;
        }

        if (!comment) {
            alert('Mohon isi deskripsi ulasan Anda');
            return;
        }

        // Disable button while submitting
        const submitBtn = document.getElementById('rmSubmit');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';

        fetch('{{ route('customer.order.reviews.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': RM_CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                order_item_id: orderItemId,
                product_id: productId,
                rating: rating,
                deskripsi_ulasan: comment
            })
        }).then(r => r.json()).then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Kirim Ulasan';
            
            if (data.success) {
                alert('✅ Ulasan berhasil dikirim! Terima kasih atas feedback Anda.');
                closeReviewModal();
                closePurchaseHistoryModal();
                // Reload page to update purchase history
                window.location.reload();
            } else {
                alert(data.message || 'Gagal mengirim ulasan. Silakan coba lagi.');
            }
        }).catch(err => {
            console.log(err);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Kirim Ulasan';
            alert('Gagal mengirim ulasan. Silakan coba lagi.');
        });
    });

    // Close modal on outside click
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('reviewModal');
        if (event.target === modal) {
            closeReviewModal();
        }
    });
</script>