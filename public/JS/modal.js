// public/js/modals.js

document.addEventListener('DOMContentLoaded', function() {
    // Definisikan semua variabel modal dan tombol
    const historyModal = document.getElementById('purchaseHistoryModal');
    const reviewModal = document.getElementById('reviewModal');
    const viewReviewModal = document.getElementById('viewReviewModal');
    const openHistoryBtn = document.getElementById('openHistoryModalBtn');

    // Cek apakah ada premium GSAP handler untuk modal ini
    // Jika ada, serahkan seluruh penanganan ke script di view blade masing-masing untuk mencegah bentrok/tumpang tindih
    if (typeof openPurchaseHistoryModal === 'function') {
        console.log('Premium GSAP modals detected. Standard modal.js bindings deactivated.');
        return;
    }

    // fallback binding untuk modal lama (jika ada halaman lain yang menggunakannya secara konvensional)
    if (openHistoryBtn) {
        const openModal = (modal) => {
            modal.style.display = 'block';
        };

        const closeModal = (modal) => {
            modal.style.display = 'none';
        };

        openHistoryBtn.onclick = () => openModal(historyModal);
        
        document.querySelectorAll('.custom-modal .close-btn').forEach(btn => {
            btn.onclick = (e) => closeModal(e.target.closest('.custom-modal'));
        });

        document.querySelectorAll('.btn-ulasan').forEach(btn => {
            btn.onclick = () => {
                const productId = btn.getAttribute('data-product-id');
                const productNameEl = btn.closest('.history-item') ? btn.closest('.history-item').querySelector('.product-name-hist') : null;
                const productName = productNameEl ? productNameEl.textContent.trim() : '';
                closeModal(historyModal);
                openModal(reviewModal);
            };
        });

        document.querySelectorAll('.btn-view-ulasan').forEach(btn => {
            btn.onclick = () => {
                const productId = btn.getAttribute('data-product-id');
                const productNameEl = btn.closest('.history-item') ? btn.closest('.history-item').querySelector('.product-name-hist') : null;
                const productName = productNameEl ? productNameEl.textContent.trim() : '';
                closeModal(historyModal);
                openModal(viewReviewModal);
            };
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('custom-modal')) {
                event.target.style.display = 'none';
            }
        }
    }
});