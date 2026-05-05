<div class="dock md:hidden">

    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="1 11 12 2 23 11"></polyline>
            <path d="M5 13v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"></path>
            <line x1="12" y1="22" x2="12" y2="18"></line>
        </svg>
        <span class="dock-label">Home</span>
    </a>

    <a href="{{ route('plants.index') }}"
       class="{{ request()->routeIs('plants.index') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22V12"></path>
            <path d="M5 12C5 7 8 3 12 3c4 0 7 4 7 9-2 0-5-1-7-3-2 2-5 3-7 3Z"></path>
        </svg>
        <span class="dock-label">Piante</span>
    </a>

    <a href="{{ route('plants.create') }}"
       class="{{ request()->routeIs('plants.create') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="8" y1="12" x2="16" y2="12"></line>
        </svg>
        <span class="dock-label">Aggiungi</span>
    </a>

    <a href="{{ route('settings') }}"
       class="{{ request()->routeIs('settings') ? 'dock-active' : '' }}">
        <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"></path>
        </svg>
        <span class="dock-label">Impostazioni</span>
    </a>

</div>
