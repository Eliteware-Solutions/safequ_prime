@if (isset($footer) && $footer)
    <footer class="default-color-dark-bg pt-3 pb-2">
        <div class="text-center py-3">
            <a href="#" target="_blank"><i class="fab fa-facebook-f mx-1"></i></a>
            <a href="https://www.instagram.com/safequ.india/" target="_blank"><i class="fab fa-instagram mx-1"></i></a>
            <a href="https://wa.me/917498107182" target="_blank"><i class="fab fa-whatsapp mx-1"></i></a>
        </div>
        <div class="footer-nav text-center">
            <ul class="mb-0 d-flex justify-content-center flex-wrap">
                <li><a href="{{ route('home') }}" class="py-2">Home</a></li>
                <li><a href="#communities" class="py-2">Community</a></li>
                <li>
                    <a href="{{ static_asset('assets/docs/TermsAndConditionsBuyer.pdf') }}" target="_blank"
                        class="py-2">Terms & Conditions</a>
                </li>
                <li>
                    <a href="{{ static_asset('assets/docs/PrivacyPolicy.pdf') }}" target="_blank"
                        class="py-2">Privacy Policy</a>
                </li>
                <li>
                    <a href="{{ static_asset('assets/docs/ReturnAndRefund.pdf') }}" target="_blank"
                        class="py-2">Return & Refund Policy</a>
                </li>
            </ul>
        </div>

        <div class="container mt-3 py-3 text-center bt-1">
            <p class="mb-0 text-white">Copyright &copy; ELUCIIDAATE TECH PRIVATE LIMITED. All rights reserved.</p>
        </div>
    </footer>
@endif
