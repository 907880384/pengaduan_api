<div class="card-footer p-1">
  <div class="row">
    <div class="col-lg-6">
      <p class="p-3 mr-auto">
        <strong>Halaman : {{ $paginator->currentPage() }}</strong>, &nbsp;
        <strong>Jumlah Data : {{ $paginator->total() }}</strong>, &nbsp;
        <strong>Data Per Halaman : {{ $paginator->perPage() }}</strong> 
      </p>
    </div>

    @if ($paginator->hasPages())      
      <div class="col-lg-6">
        <div class="float-right">
          <nav class="d-inline-block">
            <ul class="pagination mb-0">
              
              @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                  <a href="#" class="page-link">
                    <i class="fas fa-chevron-left"></i>
                  </a>
                </li>
              @else
                <li class="page-item">
                  <a class="page-link" href="{{ $paginator->previousPageUrl() }}" tabindex="-1" rel="prev">
                    <i class="fas fa-chevron-left"></i></a>
                </li>
              @endif

              @foreach ($elements as $element)
                @if (is_string($element))
                  <li class="page-item disabled">
                    <a class="page-link" href="#">
                      {{ $element }}
                    </a>
                  </li>
                @endif

                @if (is_array($element))
                  @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                      <li class="page-item active">
                        <a class="page-link" href="#">
                          {{ $page }} 
                          <span class="sr-only">(current)</span>
                        </a>
                      </li>
                    @else
                      <li class="page-item">
                        <a class="page-link" href="{{ $url }}">
                          {{ $page }}
                        </a>
                      </li> 
                    @endif
                  @endforeach
                @endif
              @endforeach

              @if ($paginator->hasMorePages())
                <li class="page-item">
                  <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                    <i class="fas fa-chevron-right"></i>
                  </a>
                </li>
              @else
                <li class="page-item disabled">
                  <a class="page-link" href="#">
                    <i class="fas fa-chevron-right"></i>
                  </a>
                </li>
              @endif

            </ul>
          </nav>
        </div>
      </div>
    @endif
  
  </div>
</div>