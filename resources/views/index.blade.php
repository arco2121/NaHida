@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title', $title)
@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <img src="assets/NaHida_Logo.png" alt="">
                <p class="py-6">
                    Progetto IoT per il monitoraggio intelligente delle piante, sviluppato da Colombara e Grammatica.
                </p>
                <a href="/register">
                    <button class="btn btn-primary">Registrati</button>
                </a>
                <a href="/login">
                    <button class="btn btn-neutral">Accedi</button>
                </a>
            </div>
        </div>
    </div>

    <div class="hero bg-base-200 pb-20">
        <div class="hero-content text-center">
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 80 80"
                             class="inline-block h-8 w-8 stroke-current">
                            <path d="M35.9995 45.001V69.001" stroke="currentColor" stroke-width="6"
                                  stroke-linecap="round" stroke-linejoin="round" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M27.9998 54.0006C16.9541 54.0006 7.99987 45.0462 7.99987 34.0005V32.0019C7.99987 29.9112 8.32068 27.8953 8.91576 26.001C12.9713 26.1704 16.9935 27.2058 20.6091 29.048C25.8776 31.7325 30.2676 36.1225 32.952 41.3911C34.7943 45.0068 35.8297 49.0291 35.999 53.0847C34.1047 53.6798 32.089 54.0006 29.9983 54.0006H27.9998Z"
                                  fill="currentColor" />
                            <path
                                d="M8.91576 26.001L7.00768 25.4016L7.46722 23.9387L8.99922 24.0027L8.91576 26.001ZM20.6091 29.048L19.7011 30.83L20.6091 29.048ZM32.952 41.3911L31.17 42.2991L32.952 41.3911ZM35.999 53.0847L37.9973 53.0013L38.0613 54.5333L36.5984 54.9928L35.999 53.0847ZM9.99987 34.0005C9.99987 43.9417 18.0587 52.0006 27.9998 52.0006V56.0006C15.8495 56.0006 5.99987 46.1508 5.99987 34.0005H9.99987ZM9.99987 32.0019V34.0005H5.99987V32.0019H9.99987ZM10.8238 26.6004C10.289 28.303 9.99987 30.1168 9.99987 32.0019H5.99987C5.99987 29.7055 6.35238 27.4877 7.00768 25.4016L10.8238 26.6004ZM19.7011 30.83C16.3473 29.1211 12.607 28.1569 8.83229 27.9992L8.99922 24.0027C13.3355 24.1838 17.6397 25.2904 21.517 27.266L19.7011 30.83ZM31.17 42.2991C28.6773 37.4068 24.5932 33.3227 19.7011 30.83L21.517 27.266C27.1619 30.1422 31.8579 34.8383 34.734 40.4831L31.17 42.2991ZM34.0008 53.1682C33.8431 49.3933 32.8789 45.653 31.17 42.2991L34.734 40.4831C36.7097 44.3606 37.8162 48.6649 37.9973 53.0013L34.0008 53.1682ZM29.9983 52.0006C31.8833 52.0006 33.697 51.7115 35.3996 51.1766L36.5984 54.9928C34.5124 55.6481 32.2946 56.0006 29.9983 56.0006V52.0006ZM27.9998 52.0006H29.9983V56.0006H27.9998V52.0006Z"
                                fill="currentColor" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M46.2846 46.0005C60.4865 46.0005 71.9995 34.4874 71.9995 20.2853V17.7157C71.9995 15.0277 71.5871 12.436 70.822 10.0005C65.6076 10.2183 60.436 11.5495 55.7872 13.9182C49.0132 17.3698 43.3687 23.0143 39.9172 29.7884C37.5486 34.4371 36.2173 39.6086 35.9995 44.823C38.4351 45.588 41.0269 46.0005 43.715 46.0005H46.2846Z"
                                  fill="currentColor" />
                            <path
                                d="M70.822 10.0005L72.7301 9.40114L72.2706 7.93825L70.7386 8.00223L70.822 10.0005ZM55.7872 13.9182L54.8792 12.1362L55.7872 13.9182ZM39.9172 29.7884L38.1351 28.8804L39.9172 29.7884ZM35.9995 44.823L34.0013 44.7395L33.9373 46.2715L35.4001 46.731L35.9995 44.823ZM69.9995 20.2853C69.9995 33.3829 59.3819 44.0005 46.2846 44.0005V48.0005C61.5911 48.0005 73.9995 35.592 73.9995 20.2853H69.9995ZM69.9995 17.7157V20.2853H73.9995V17.7157H69.9995ZM68.914 10.5998C69.6188 12.8437 69.9995 15.2333 69.9995 17.7157H73.9995C73.9995 14.8221 73.5554 12.0284 72.7301 9.40114L68.914 10.5998ZM56.6952 15.7002C61.0822 13.4649 65.9718 12.2048 70.9055 11.9987L70.7386 8.00223C65.2434 8.23173 59.7897 9.63412 54.8792 12.1362L56.6952 15.7002ZM41.6992 30.6964C44.959 24.2986 50.2975 18.96 56.6952 15.7002L54.8792 12.1362C47.7289 15.7795 41.7784 21.73 38.1351 28.8804L41.6992 30.6964ZM37.9978 44.9064C38.2039 39.9729 39.464 35.0833 41.6992 30.6964L38.1351 28.8804C35.6332 33.7909 34.2308 39.2444 34.0013 44.7395L37.9978 44.9064ZM43.715 44.0005C41.2325 44.0005 38.8428 43.6198 36.5989 42.9149L35.4001 46.731C38.0274 47.5563 40.8212 48.0005 43.715 48.0005V44.0005ZM46.2846 44.0005H43.715V48.0005H46.2846V44.0005Z"
                                fill="currentColor" />
                        </svg>
                    </div>
                    <div class="stat-title">Total Plants</div>
                    <div class="stat-value text-primary">25.6K</div>
                    <div class="stat-desc">All around the globe</div>
                </div>

                <div class="stat">
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

                <div class="stat">
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
    </div>

@endsection
