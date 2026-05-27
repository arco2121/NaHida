@extends('layouts.guest')
@section('title', $title)
@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <img src="assets/NaHida_Logo.png" alt="">
                <p class="py-6">
                    Progetto IoT per il monitoraggio intelligente delle piante, sviluppato da Colombara e Grammatica.
                </p>
                @auth
                    <a href="/dashboard">
                        <button class="btn btn-primary">Vai alla Dashboard</button>
                    </a>
                    <form style="display: contents" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-neutral" type="submit">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="/register">
                        <button class="btn btn-primary">Registrati</button>
                    </a>
                    <a href="/login">
                        <button class="btn btn-neutral">Accedi</button>
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <div class="flex justify-center pb-20 px-4 bg-base-200">
        <div class="flex flex-col sm:flex-row shadow rounded-box overflow-hidden w-full sm:max-w-4xl">

            <div class="stat bg-base-100 flex-1">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 80 80"
                         class="inline-block h-8 w-8 stroke-current">
                        <path d="M35.9995 45.001V69.001" stroke="currentColor" stroke-width="6"
                              stroke-linecap="round" stroke-linejoin="round" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M27.9998 54.0006C16.9541 54.0006 7.99987 45.0462 7.99987 34.0005V32.0019C7.99987 29.9112 8.32068 27.8953 8.91576 26.001C12.9713 26.1704 16.9935 27.2058 20.6091 29.048C25.8776 31.7325 30.2676 36.1225 32.952 41.3911C34.7943 45.0068 35.8297 49.0291 35.999 53.0847C34.1047 53.6798 32.089 54.0006 29.9983 54.0006H27.9998Z"
                              fill="currentColor" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M46.2846 46.0005C60.4865 46.0005 71.9995 34.4874 71.9995 20.2853V17.7157C71.9995 15.0277 71.5871 12.436 70.822 10.0005C65.6076 10.2183 60.436 11.5495 55.7872 13.9182C49.0132 17.3698 43.3687 23.0143 39.9172 29.7884C37.5486 34.4371 36.2173 39.6086 35.9995 44.823C38.4351 45.588 41.0269 46.0005 43.715 46.0005H46.2846Z"
                              fill="currentColor" />
                    </svg>
                </div>
                <div class="stat-title">Total Plants</div>
                <div class="stat-value text-primary">25.6K</div>
                <div class="stat-desc">All around the globe</div>
            </div>

            <div class="stat bg-base-100 flex-1 border-t sm:border-t-0 sm:border-l border-base-200">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80"
                         class="inline-block h-8 w-8 stroke-current">
                        <path
                            d="M60 70L20 70C17.7909 70 16 68.2091 16 66C16 59.3836 20.1048 53.4615 26.3003 51.1395L27.5304 50.6785C35.5704 47.6651 44.4296 47.6651 52.4696 50.6785L53.6997 51.1395C59.8952 53.4615 64 59.3836 64 66C64 68.2091 62.2091 70 60 70Z"
                            fill="currentColor" stroke-width="4" stroke-linecap="square" stroke-linejoin="round" />
                        <path
                            d="M33.9015 38.8673C37.7294 40.8336 42.2706 40.8336 46.0985 38.8673C49.6611 37.0373 52.2136 33.7042 53.0516 29.7878L53.2752 28.7425C54.1322 24.7375 53.2168 20.5576 50.7644 17.2774L50.4053 16.797C47.9525 13.5163 44.0962 11.5845 40 11.5845C35.9038 11.5845 32.0475 13.5163 29.5947 16.797L29.2356 17.2774C26.7832 20.5576 25.8678 24.7375 26.7248 28.7425L26.9484 29.7878C27.7864 33.7042 30.3389 37.0373 33.9015 38.8673Z"
                            fill="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="stat-title">Total Users</div>
                <div class="stat-value text-secondary">9.4K</div>
                <div class="stat-desc">We're growing places together</div>
            </div>

            <div class="stat bg-base-100 flex-1 border-t sm:border-t-0 sm:border-l border-base-200">
                <div class="stat-figure text-secondary">
                    <div class="avatar">
                        <div class="w-16 rounded-full">
                            <img src="assets/NaHida_CuteFlower.png" />
                        </div>
                    </div>
                </div>
                <div class="stat-value">86%</div>
                <div class="stat-title">CUTER</div>
                <div class="stat-desc text-secondary">Than similar products</div>
            </div>

        </div>
    </div>

@endsection
