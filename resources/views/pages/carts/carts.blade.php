@foreach ($orders as $order)

<div class="row">
  <div class="row main align-items-center">
    <div class="col-2">
      <img class="img-fluid" src="{{ asset('storage/'. $order->productFiles[0]->filepath) }}" />
    </div>
    <div class="col">
      <div class="row">
        <h6>{{ $order->product->product_name }}</h6>
      </div>

      <div class="row  text-muted">
        {{ $order->product->spesification }}
      </div>
    </div>
    
    
    <div class="col">
      {{ $order->quantity . ' ' . $order->product->satuan  }}

      <div class="close">
        <button type="button" class="btn btn-success btn-sm" onclick="setAgree('{{ $order->id }}')">
          <i class="fas fa-check"></i>
          TERIMA
        </button>

        <button type="button" class="btn btn-danger btn-sm" onclick="setDisagree('{{ $order->id }}')">
          <i class="fas fa-times"></i> 
          TOLAK
        </button>
      </div>

    </div>
  </div>
</div>


@endforeach




{{ $orders->links('customs.paginer') }}