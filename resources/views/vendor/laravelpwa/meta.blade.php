<!-- Web Application Manifest -->
<link rel="manifest" href="{{ route('laravelpwa.manifest') }}">
<!-- Chrome for Android theme color -->
<meta name="theme-color" content="{{ $config['theme_color'] }}">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="application-name" content="{{ $config['short_name'] }}">
<link rel="icon" sizes="{{ data_get(end($config['icons']), 'sizes') }}"
      href="{{ data_get(end($config['icons']), 'src') }}">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="apple-mobile-web-app-status-bar-style" content="{{  $config['status_bar'] }}">
<meta name="apple-mobile-web-app-title" content="{{ $config['short_name'] }}">
<link rel="apple-touch-icon" href="{{ data_get(end($config['icons']), 'src') }}">


<link href="{{ $config['splash']['640x1136'] }}"
      media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['750x1334'] }}"
      media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1242x2208'] }}"
      media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1125x2436'] }}"
      media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['828x1792'] }}"
      media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1242x2688'] }}"
      media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1536x2048'] }}"
      media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1668x2224'] }}"
      media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['1668x2388'] }}"
      media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>
<link href="{{ $config['splash']['2048x2732'] }}"
      media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)"
      rel="apple-touch-startup-image"/>

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="{{ $config['background_color'] }}">
<meta name="msapplication-TileImage" content="{{ data_get(end($config['icons']), 'src') }}">

<script type="text/javascript">
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('serviceworker.js', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            // console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            // console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }

    function showInstallPromotion() {
        $('.pwaPopup').show();
        $('.installPWABtn').show();
        if ($(document).width() < 992 && $('header.new-ui').length > 0) {
            $('.collapsible').css({'top': $("header").outerHeight(), 'min-height' : $(document).height() - $("header").outerHeight()})
        }
    }

    function hideInstallPromotion() {
        $('#pwaPopup').hide();
        $('.installPWABtn').hide();
        if ($(document).width() < 992 && $('header.new-ui').length > 0) {
            $('.collapsible').css({'top': $("header").outerHeight(), 'min-height' : $(document).height() - $("header").outerHeight()})
        }
    }

    async function installEvent() {
        // Hide the app provided install promotion
        hideInstallPromotion();
        $('.pwaPopup').hide();
        $('.installPWABtn').hide();
        // Show the install prompt
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        const {outcome} = await deferredPrompt.userChoice;

        if (outcome === 'dismissed') {
            localStorage.setItem('lastDismiss', new Date().getDate())
        }
        // Optionally, send analytics event with outcome of user choice
        // console.log(`User response to the install prompt: ${outcome}`);
        // We've used the prompt, and can't use it again, throw it away
        deferredPrompt = null;
    }

    // Initialize deferredPrompt for use later to show browser install prompt.
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-info bar from appearing on mobile
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI notify the user they can install the PWA
        showInstallPromotion();
        // Optionally, send analytics event that PWA install promo was shown.
        // console.log(`'beforeinstallprompt' event was fired.`);

        let buttonInstall = document.getElementById('installPWA');

        buttonInstall.addEventListener('click', async () => {
            installEvent();
        });

        let menuButtonInstall = document.getElementById('installPWAMenu');

        menuButtonInstall.addEventListener('click', async () => {
            installEvent();
        });

        if (localStorage.getItem('lastDismiss') && localStorage.getItem('lastDismiss') == new Date().getDate()) {
            hideInstallPromotion();
        }
    });

    window.onload = function () {
        // Detects if device is on iOS
        const isIos = () => {
            const userAgent = window.navigator.userAgent.toLowerCase();
            var criOS = userAgent.indexOf('crios/') > -1;
            return /iphone|ipad|ipod/.test(userAgent) && !criOS;
        }
        // Detects if device is in standalone mode
        const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

        // Checks if should display install popup notification:
        if (isIos() && !isInStandaloneMode()) {
            if (localStorage.getItem('lastDismiss') && localStorage.getItem('lastDismiss') == new Date().getDate()) {
                hideInstallPromotion();
            } else {
                $('.pwaPopup #msg').html('Install this webapp on your iPhone: tap <img src="{{static_asset('assets/img/safari-share.svg')}}" class="safari_share">  and then Add to homescreen.');
                $('.pwaPopup button').remove();
                showInstallPromotion();
            }
        }
    }
</script>



