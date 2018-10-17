<ul>
    @if(!empty($errors))

        @foreach($errors->all() as $error)


            <div class="alert alert-warning" role="alert">
                <strong>{{ $error }}</strong>
            </div>
        @endforeach

        @endif
</ul>