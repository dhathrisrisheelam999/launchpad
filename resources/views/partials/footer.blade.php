<footer class="site-footer">
    <div class="footer-grid container">
        <div class="ft-brand">
            <div style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:14px">
                📈 Launch<span style="color:#BDD9BE">Pad</span>
            </div>
            <p>The premier marketplace for startup founders and investors — built on Laravel MVC.</p>
        </div>
        <div class="ft-col">
            <h5>Platform</h5>
            <a href="{{ route('home') }}">Browse Startups</a>
            <a href="{{ route('startups.create') }}">List a Startup</a>
        </div>
        <div class="ft-col">
            <h5>Account</h5>
            <a href="{{ route('auth.login') }}">Log In</a>
            <a href="{{ route('auth.register') }}">Register</a>
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
        </div>
        <div class="ft-col">
            <h5>Company</h5>
            <a href="#">About Us</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </div>
    <div class="ft-bottom container">
        <p>© {{ date('Y') }} LaunchPad Market. All rights reserved.</p>
        <span>Built with Laravel 11 · PHP 8.3 · MySQL 8.0</span>
    </div>
</footer>