<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="index.html">{{ config('app.name', 'Laravel') }}</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="index.html">APP</a>
    </div>
    <ul class="sidebar-menu">
      @foreach (routerCollections() as $r)

        @if (count($r['slug']) > 0)
          
          @if (in_array(Auth::user()->roles()->first()->slug, $r['slug']))
            <li class="menu-header">
              {{ $r['label'] }}
            </li>

            <!-- Count Route > 0 -->
            @foreach ($r['components'] as $c)
              @if (count($c['to']) > 0)
                
                @if (in_array(Auth::user()->roles()->first()->slug, $c['to']))
                  <li class="nav-item dropdown">
                    <a href="{{ url($c['route']) }}" class="nav-link">
                      <i class="{{ $c['icon'] }}"></i>
                      <span>{{ $c['title'] }}</span>
                    </a>
                  </li>
                @endif

              @else
                <li class="nav-item dropdown">
                  <a href="{{ url($c['route']) }}" class="nav-link">
                    <i class="{{ $c['icon'] }}"></i>
                    <span>{{ $c['title'] }}</span>
                  </a>
                </li>
              @endif
            @endforeach
          @endif

        @else
          <li class="menu-header">
            {{ $r['label'] }}
          </li>

          @foreach ($r['components'] as $c)
            <li class="nav-item dropdown">
              <a href="{{ url($c['route']) }}" class="nav-link">
                <i class="{{ $c['icon'] }}"></i>
                <span>{{ $c['title'] }}</span>
              </a>
            </li>
          @endforeach
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