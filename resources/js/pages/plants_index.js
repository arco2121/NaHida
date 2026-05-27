document.addEventListener('DOMContentLoaded', function () {
    const searchInput  = document.getElementById('search_input');
    const grid         = document.getElementById('plants_grid');
    const noResults    = document.getElementById('no_results');
    const countLabel   = document.getElementById('plants_count_label');
    const btnReset     = document.getElementById('btn_reset_filters');

    if (!searchInput || !grid) return;

    const cards = Array.from(grid.querySelectorAll('.plant-card'));
    const total = cards.length;

    // Stato filtri attivi
    const activeFilters = { health: 'all', device: 'all' };

    // ── Chip click ──────────────────────────────────────────────
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const group = chip.dataset.filter;
            const value = chip.dataset.value;

            // Aggiorna stato
            activeFilters[group] = value;

            // Aggiorna stile chip nello stesso gruppo
            document.querySelectorAll(`.filter-chip[data-filter="${group}"]`).forEach(c => {
                const isActive = c.dataset.value === value;
                c.classList.toggle('btn-primary', isActive);
                c.classList.toggle('btn-ghost',   !isActive);
                c.classList.toggle('active-chip', isActive);
            });

            applyFilters();
        });
    });

    // ── Reset ────────────────────────────────────────────────────
    btnReset?.addEventListener('click', () => {
        searchInput.value = '';
        activeFilters.health = 'all';
        activeFilters.device = 'all';

        document.querySelectorAll('.filter-chip[data-value="all"]').forEach(c => {
            c.classList.add('btn-primary', 'active-chip');
            c.classList.remove('btn-ghost');
        });
        document.querySelectorAll('.filter-chip:not([data-value="all"])').forEach(c => {
            c.classList.remove('btn-primary', 'active-chip');
            c.classList.add('btn-ghost');
        });

        applyFilters();
    });

    // ── Search ───────────────────────────────────────────────────
    searchInput.addEventListener('input', applyFilters);

    // ── Core filter logic ────────────────────────────────────────
    function applyFilters() {
        const query  = searchInput.value.trim().toLowerCase();
        let visible  = 0;

        cards.forEach(card => {
            const name   = card.dataset.name   ?? '';
            const notes  = card.dataset.notes  ?? '';
            const health = card.dataset.health ?? 'ok';
            const device = card.dataset.device ?? 'none';

            const matchSearch = !query
                || name.includes(query)
                || notes.includes(query);

            const matchHealth = activeFilters.health === 'all'
                || health === activeFilters.health;

            const matchDevice = activeFilters.device === 'all'
                || device === activeFilters.device;

            const show = matchSearch && matchHealth && matchDevice;
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        // Aggiorna contatore
        if (countLabel) {
            countLabel.textContent = visible === total
                ? `${total} ${total === 1 ? 'pianta registrata' : 'piante registrate'}`
                : `${visible} di ${total} ${total === 1 ? 'pianta' : 'piante'}`;
        }

        // Mostra/nascondi "nessun risultato"
        noResults?.classList.toggle('hidden',  visible > 0);
        noResults?.classList.toggle('flex',    visible === 0);

        // Mostra/nascondi tasto reset
        const isFiltered = query || activeFilters.health !== 'all' || activeFilters.device !== 'all';
        btnReset?.classList.toggle('hidden', !isFiltered);
    }
});
