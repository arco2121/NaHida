<div class="navbar bg-base-100 shadow-sm sticky top-0 z-40">
    @if(request()->routeIs("plants.*", "dashboard.*", "settings.*"))
        @php
            $previous = explode(".", Route::currentRouteName())[0];
        @endphp
        <div class="navbar-start">
            <a href="{{ route($previous) }}" class="btn btn-ghost btn-sm btn-square">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.2893 5.70708C13.8988 5.31655 13.2657 5.31655 12.8751 5.70708L7.98768 10.5993C7.20729 11.3805 7.20758 12.6463 7.98828 13.4271L12.8787 18.3175C13.2692 18.708 13.9024 18.708 14.2929 18.3175C14.6834 17.927 14.6834 17.2938 14.2929 16.9033L10.1073 12.7176C9.71677 12.3271 9.71677 11.6939 10.1073 11.3034L14.2893 7.12129C14.6798 6.73077 14.6798 6.0976 14.2893 5.70708Z" fill="currentColor"/>
                </svg>
            </a>
        </div>
    @endif
    <div class="navbar-center">
        <span class="text-lg font-bold text-base-content">{{ $title }}</span>
    </div>
    <div class="navbar-end">
        @include('components.theme_toggle')
    </div>
</div>
