<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="index.html">Pengaduan App</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="index.html">C-App</a>
    </div>
    <ul class="sidebar-menu">
      @foreach (routerCollections() as $r)
        @if (strtolower($r['slug']) == 'all')
          <li class="menu-header">
            {{ $r['label'] }}
          </li>
          @foreach ($r['components'] as $c)
            @if (strtolower($c['to']) == 'all')
              <li class="nav-item dropdown">
                <a href="{{ url($c['route']) }}" class="nav-link">
                  <i class="{{ $c['icon'] }}"></i>
                  <span>{{ $c['title'] }}</span>
                </a>
              </li>
            @else
              @if (strtolower($c['to']) == Auth::user()->roles()->first()->slug)
                <li class="nav-item dropdown">
                  <a href="{{ url($c['route']) }}" class="nav-link">
                    <i class="{{ $c['icon'] }}"></i>
                    <span>{{ $c['title'] }}</span>
                  </a>
                </li>
              @endif
            @endif
          @endforeach
        @else
          <li class="menu-header">
            {{ $r['label'] }}
          </li>
          @if (strtolower($r['slug']) == Auth::user()->roles()->first()->slug)
            @foreach ($r['components'] as $c)
              <li class="nav-item dropdown">
                <a href="{{ url($c['route']) }}" class="nav-link">
                  <i class="{{ $c['icon'] }}"></i>
                  <span>{{ $c['title'] }}</span>
                </a>
              </li>
            @endforeach
          @endif
        @endif

      @endforeach
      </ul>

      <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="{{ route('logout') }}" class="btn btn-danger btn-lg btn-block btn-icon-split">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
  </aside>
</div>