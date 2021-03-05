@if (count($records) > 0)
<div class="row">
  @foreach ($records as $key => $row)
  <div class="col-12 col-sm-6 col-md-6 col-lg-3 mt-1 shadow-dark">
    <article class="article article-style-b">
      <div class="article-header">
        @if (count($row->fileImages) > 0)
        <div class="article-image" data-background="{{ asset('storage/'. $row->fileImages[0]->filepath) }}"></div>
        @else
        <div class="article-image" data-background="{{ asset('images/img01.jpg') }}"></div>
        @endif
      </div>
      <div class="article-details">
        <div class="article-title">
          <h2>
            <a href="#">
              {{ $row->product_name }}
            </a>
          </h2>
        </div>
        <p>
          Spesifikasi <i class="fas fa-long-arrow-alt-right"></i> {{ $row->spesification }} <br />
          Stok <i class="fas fa-long-arrow-alt-right"></i> {{ $row->stock }} <br />
          Satuan <i class="fas fa-long-arrow-alt-right"></i> {{ $row->satuan }} <br />
        </p>
        <div class="article-cta text-center">
          <a href="{{ url('products/'.$row->id.'/edit') }}" class="btn btn-sm btn-info">
            <i class="fas fa-edit"></i> Ubah
          </a>
          
          <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{ $row->id }}')">
            <i class="fas fa-trash"></i> Hapus
          </button>
        </div>
      </div>
    </article>
  </div>
  @endforeach
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 mt-2">
    {{ $records->links('customs.pagination') }}
  </div>
</div>

@else
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <div class="alert alert-info">Data Barang Kosong</div>
  </div>
</div>
@endif