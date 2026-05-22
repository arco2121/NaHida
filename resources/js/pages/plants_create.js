function updateModel() {
    PlantViewer.setAppearance({
        plant_variant: parseInt(document.getElementById('range_variant').value),
        plant_color:   parseInt(document.getElementById('range_plant_color').value),
        flower_color:  parseInt(document.getElementById('range_flower').value),
        pot_color:     parseInt(document.getElementById('range_pot').value),
    });
}

// ---- Template condizioni ----
const TEMPLATES = {
    tropicale:    { hum_min: 60, hum_max: 90, temp_min: 20, temp_max: 35, soil_min: 50, soil_max: 80 },
    mediterraneo: { hum_min: 40, hum_max: 70, temp_min: 15, temp_max: 28, soil_min: 35, soil_max: 65 },
    succulente:   { hum_min: 10, hum_max: 40, temp_min: 15, temp_max: 38, soil_min: 10, soil_max: 35 },
    custom:       null,
};

let _selectedTemplate = null;

function selectTemplate(btn, key) {
    // Reset tutti
    document.querySelectorAll('.template-btn').forEach(b => {
        b.classList.remove('border-primary', 'bg-primary/10');
        b.classList.add('border-base-200', 'bg-base-200');
    });
    // Attiva questo
    btn.classList.add('border-primary', 'bg-primary/10');
    btn.classList.remove('border-base-200', 'bg-base-200');

    _selectedTemplate = key;
    document.getElementById('summary_template').textContent = btn.querySelector('p.font-bold').textContent;

    const tpl = TEMPLATES[key];
    if (tpl) {
        document.getElementById('hum_min').value  = tpl.hum_min;
        document.getElementById('hum_max').value  = tpl.hum_max;
        document.getElementById('temp_min').value = tpl.temp_min;
        document.getElementById('temp_max').value = tpl.temp_max;
        document.getElementById('soil_min').value = tpl.soil_min;
        document.getElementById('soil_max').value = tpl.soil_max;
        // Readonly se non custom
        document.querySelectorAll('#hum_min,#hum_max,#temp_min,#temp_max,#soil_min,#soil_max').forEach(i => {
            i.readOnly = true;
            i.classList.add('opacity-60');
        });
    } else {
        // Custom: libera gli input
        document.querySelectorAll('#hum_min,#hum_max,#temp_min,#temp_max,#soil_min,#soil_max').forEach(i => {
            i.readOnly = false;
            i.classList.remove('opacity-60');
        });
    }
}

// ---- Unità annaffiatura ----
let _unit = 'ore';

function setUnit(unit) {
    _unit = unit;
    const btnOre    = document.getElementById('btn_ore');
    const btnGiorni = document.getElementById('btn_giorni');
    if (unit === 'ore') {
        btnOre.classList.add('bg-primary', 'text-primary-content');
        btnOre.classList.remove('bg-base-200', 'text-base-content');
        btnGiorni.classList.add('bg-base-200', 'text-base-content');
        btnGiorni.classList.remove('bg-primary', 'text-primary-content');
    } else {
        btnGiorni.classList.add('bg-primary', 'text-primary-content');
        btnGiorni.classList.remove('bg-base-200', 'text-base-content');
        btnOre.classList.add('bg-base-200', 'text-base-content');
        btnOre.classList.remove('bg-primary', 'text-primary-content');
    }
    updateWaterPreview();
}

function updateWaterPreview() {
    const val = document.getElementById('water_interval').value;
    const preview = document.getElementById('water_preview');
    const summary = document.getElementById('summary_water');
    if (val) {
        const txt = `Verrà annaffiata ogni ${val} ${_unit}`;
        preview.textContent = txt;
        summary.textContent = `ogni ${val} ${_unit}`;
    } else {
        preview.textContent = `Verrà annaffiata ogni — ${_unit}`;
        summary.textContent = '—';
    }
}

document.getElementById('water_interval').addEventListener('input', updateWaterPreview);

// ---- Modale conferma: aggiorna riepilogo ----
document.querySelector('[onclick="document.getElementById(\'modal_confirm\').showModal()"]').addEventListener('click', () => {
    const name = document.getElementById('plant_name').value || '—';
    document.getElementById('summary_name').textContent = name;
    if (!document.getElementById('summary_water').textContent || document.getElementById('summary_water').textContent === '—') {
        updateWaterPreview();
    }
});
