@if(session()->has('message'))
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">提示</h5>

                <div class="alert alert-success" role="alert">
                    {{ session()->get('message') }}
                </div>

            </div>
        </div>
    </div>
@endif

@if(count($errors) > 0)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">錯誤訊息</h5>

                @foreach($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endif
