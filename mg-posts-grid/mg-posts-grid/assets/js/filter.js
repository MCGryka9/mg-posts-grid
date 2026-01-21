document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.mgpg-filter').forEach(filter => {
        filter.addEventListener('change', e => {
            const value = e.target.value;
            const grid = e.target.closest('.mgpg-wrapper').querySelectorAll('.mgpg-card');

            grid.forEach(card => {
                if (value === 'all' || card.dataset.terms.includes(value)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
