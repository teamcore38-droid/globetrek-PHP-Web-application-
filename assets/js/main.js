/**
 * GlobeTrek Adventures - Primary Interactive JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Password Confirmation Check
    const regForm = document.querySelector('#registerForm');
    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            const pwd = document.querySelector('#password').value;
            const confirmPwd = document.querySelector('#confirm_password').value;
            if (pwd !== confirmPwd) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'The password confirmation does not match the entered password.',
                    confirmButtonColor: '#028090'
                });
            }
        });
    }

    // 2. Dynamic Price Calculator on Booking Form
    const travelersInput = document.querySelector('#travelers_count');
    const unitPriceEl = document.querySelector('#unit_price');
    const totalPriceEl = document.querySelector('#total_price_display');
    const totalAmountHidden = document.querySelector('#total_amount_input');

    if (travelersInput && unitPriceEl && totalPriceEl) {
        function updateBookingTotal() {
            const count = parseInt(travelersInput.value, 10) || 1;
            const unitPrice = parseFloat(unitPriceEl.dataset.price) || 0;
            const total = count * unitPrice;
            totalPriceEl.textContent = '$' + total.toFixed(2);
            if (totalAmountHidden) {
                totalAmountHidden.value = total.toFixed(2);
            }
        }
        travelersInput.addEventListener('input', updateBookingTotal);
        travelersInput.addEventListener('change', updateBookingTotal);
        updateBookingTotal();
    }

    // 3. Confirm Delete / Status Change with SweetAlert
    const confirmButtons = document.querySelectorAll('.confirm-action');
    confirmButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const message = this.dataset.confirmMessage || 'Are you sure you want to proceed with this action?';
            
            Swal.fire({
                title: 'Confirm Action',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e76f51',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, proceed'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // 4. Live Filter on Packages Page
    const searchFilterInput = document.querySelector('#packageSearchInput');
    if (searchFilterInput) {
        searchFilterInput.addEventListener('keyup', function () {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.package-item-col');
            let visibleCount = 0;

            cards.forEach(card => {
                const title = card.dataset.title.toLowerCase();
                const destination = card.dataset.destination.toLowerCase();
                const description = card.dataset.description.toLowerCase();

                if (title.includes(query) || destination.includes(query) || description.includes(query)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const noResultEl = document.querySelector('#noResultsMessage');
            if (noResultEl) {
                noResultEl.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        });
    }
});
