<nav class="navbar">
    <a href="{{ route('home') }}" class="nav-logo">
        <div class="logo-mark">📈</div>
        Launch<span class="logo-em">Pad</span>
    </a>
    <div class="nav-mid">
        <a href="{{ route('home') }}">Marketplace</a>
        <a href="{{ route('home') }}#how">How It Works</a>
        <a href="{{ route('investments.process') }}">Investment Process</a>

        @if(session('user_id'))
            <a href="{{ route('dashboard.index') }}">Dashboard</a>
            <a href="{{ route('favorites.index') }}">❤️ Favorites</a>
        @endif
        @if(session('user_role') === 'admin')
            <a href="{{ route('admin.index') }}" style="color:#C0392B;font-weight:700">⚙️ Admin</a>
        @endif
    </div>
    <div class="nav-right">
        @if(session('user_id'))
            <!-- Notifications Bell & Dropdown -->
            <div style="position:relative;margin-right:16px;display:inline-block">
                <button id="noti-bell-btn" style="background:none;border:none;font-size:20px;cursor:pointer;position:relative;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background-color .2s;color:var(--ink3)" onmouseover="this.style.backgroundColor='rgba(0,0,0,.04)';" onmouseout="this.style.backgroundColor='transparent';">
                    🔔
                    @php
                        $unreadCount = isset($navbarNotifications) ? $navbarNotifications->where('unread', true)->count() : 0;
                    @endphp
                    @if($unreadCount > 0)
                        <span style="position:absolute;top:2px;right:2px;background:#C0392B;color:#fff;font-size:10px;font-weight:700;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;border:1.5px solid #fff">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>

                <!-- Floating Dropdown -->
                <div id="noti-dropdown" style="display:none;position:absolute;top:44px;right:0;width:320px;background:#fff;border:1px solid var(--line);border-radius:var(--r2);box-shadow:var(--sh2);z-index:500;padding:12px 0">
                    <div style="padding:0 16px 10px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center">
                        <span style="font-weight:700;font-size:14px;color:var(--ink)">Notifications</span>
                        @if($unreadCount > 0)
                            <span style="font-size:11px;background:var(--green5);color:var(--green);padding:2px 8px;border-radius:100px;font-weight:600">{{ $unreadCount }} new</span>
                        @endif
                    </div>
                    <div style="max-height:280px;overflow-y:auto">
                        @if(!isset($navbarNotifications) || $navbarNotifications->isEmpty())
                            <div style="text-align:center;padding:32px 16px;color:var(--ink4);font-size:13px">
                                🔔 No notifications yet
                            </div>
                        @else
                            @foreach($navbarNotifications as $noti)
                                <a href="{{ $noti['url'] }}" style="display:flex;gap:12px;padding:12px 16px;border-bottom:1px solid var(--line);text-decoration:none;color:inherit;transition:background .2s;text-align:left" onmouseover="this.style.background='var(--green5)'" onmouseout="this.style.background='none'">
                                    <div style="font-size:18px;flex-shrink:0;padding-top:2px">
                                        @if($noti['type'] === 'inquiry')
                                            📩
                                        @elseif($noti['type'] === 'inquiry_reply')
                                            💬
                                        @elseif($noti['type'] === 'review')
                                            ⭐
                                        @else
                                            🔔
                                        @endif
                                    </div>
                                    <div style="flex:1">
                                        <div style="font-weight:700;font-size:12.5px;color:var(--ink)">{{ $noti['title'] }}</div>
                                        <div style="font-size:12px;color:var(--ink3);margin-top:2px;line-height:1.4">{{ $noti['message'] }}</div>
                                        <div style="font-size:10px;color:var(--ink4);margin-top:4px">{{ \Carbon\Carbon::parse($noti['time'])->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <a href="{{ route('profile.index') }}" class="nav-user" style="text-decoration:none;transition:color .2s" onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--ink3)'">👋 {{ session('user_name') }}</a>
            <form action="{{ route('auth.logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-outline-dark btn-sm">Log Out</button>
            </form>
        @else
            <a href="{{ route('auth.login') }}" class="btn btn-outline-dark btn-sm">Log In</a>
            <a href="{{ route('auth.register') }}" class="btn btn-dark btn-sm">Register ↗</a>
        @endif
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById("noti-bell-btn");
    const dropdown = document.getElementById("noti-dropdown");

    if (btn && dropdown) {
        btn.addEventListener("click", function(e) {
            e.stopPropagation();
            if (dropdown.style.display === "none" || dropdown.style.display === "") {
                dropdown.style.display = "block";
            } else {
                dropdown.style.display = "none";
            }
        });

        document.addEventListener("click", function(e) {
            if (!dropdown.contains(e.target) && e.target !== btn) {
                dropdown.style.display = "none";
            }
        });
    }
});
</script>