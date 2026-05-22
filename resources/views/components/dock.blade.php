<div class="dock sticky z-40">
    <a href="/plants" class="@if(request()->routeIs("plants.*") || request()->routeIs("plants")) dock-active @endif">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 80 80" class="h-6 w-6 stroke-current">
            <path d="M35.9995 45.001V69.001" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
            <path fill-rule="evenodd" clip-rule="evenodd" d="M27.9998 54.0006C16.9541 54.0006 7.99987 45.0462 7.99987 34.0005V32.0019C7.99987 29.9112 8.32068 27.8953 8.91576 26.001C12.9713 26.1704 16.9935 27.2058 20.6091 29.048C25.8776 31.7325 30.2676 36.1225 32.952 41.3911C34.7943 45.0068 35.8297 49.0291 35.999 53.0847C34.1047 53.6798 32.089 54.0006 29.9983 54.0006H27.9998Z" fill="currentColor" />
            <path fill-rule="evenodd" clip-rule="evenodd" d="M46.2846 46.0005C60.4865 46.0005 71.9995 34.4874 71.9995 20.2853V17.7157C71.9995 15.0277 71.5871 12.436 70.822 10.0005C65.6076 10.2183 60.436 11.5495 55.7872 13.9182C49.0132 17.3698 43.3687 23.0143 39.9172 29.7884C37.5486 34.4371 36.2173 39.6086 35.9995 44.823C38.4351 45.588 41.0269 46.0005 43.715 46.0005H46.2846Z" fill="currentColor" />
        </svg>
        <span class="dock-label">Piante</span>
    </a>

    <a href="/dashboard" class="@if(request()->routeIs("dashboard.*") || request()->routeIs("dashboard")) dock-active @endif">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        <span class="dock-label">Home</span>
    </a>

    <a href="/settings" class="@if(request()->routeIs("settings.*") || request()->routeIs("settings")) dock-active @endif">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <span class="dock-label">Impostazioni</span>
    </a>
</div>
