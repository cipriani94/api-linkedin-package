@extends('layouts.main')

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" integrity="sha512-Velp0ebMKjcd9RiCoaHhLXkR1sFoCCWXNp6w4zj1hfMifYB5441C+sKeBl/T/Ka6NjBiRfBBQRaQq65ekYz3UQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@section('container_page')

    @if (session('error'))
        <div class="alert alert-danger" style="text-align: center">
            <ul>
                <li>{{ session('error') }}</li>
            </ul>
        </div>
    @endif
    <div class="container">
        <div class="card-primary">
            <div class="card-header">
                <h3 class="card-title">Richiesta di pubblicazione su linkedin del caso <strong> {{ $attivita->nome }}
                    </strong> <br>
                    <small>La richiesta verrà inviata agli amministratori di neurohub che controllerranno la correttezza
                        dei dati e provvederanno alla promozione del caso con il vostro tag associato al post.</small>
                </h3>
            </div>
            <form action="{{ route('post.linkedin.store') }}" method="post">
                <div class="card-body">
                    @csrf
                    @if (!$meeting)
                        <input type="hidden" name="profile_id" value="{{ $profile_id }}">
                        <input type="hidden" name="profile_name" value="{{ $profile_name }}">
                    @endif
                    <input type="hidden" name="id" value="{{ $attivita->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="nome_post">Testo del post</label>
                                    <input type="text" class="form-control" id="nome_post" name="post_text"
                                        placeholder="Nome del tuo post">

                                </div>
                            </div>

                        </div>
                        @if ($attivita->id_categoria != 3)
                            @if (empty($allegati))
                                <p>Non ci sono immagini nel post</p>
                            @else
                                <div class="row">
                                    @foreach ($allegati as $image)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="radio" id="linkedin-image-{{ $image->id }}" name="image"
                                                    value="{{ $image->id }}">
                                                <label
                                                    for="linkedin-image-{{ $image->id }}">{{ $image->nome }}</label><br>
                                                <img src="{{ asset(str_replace('public', 'storage', $image->path)) }}"
                                                    class="img-thumbnail" alt="">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            @if (empty($attivita->link_2))
                                <p>Non ci sono immagini nel post</p>
                            @else
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="radio" id="linkedin-image-{{ $attivita->id }}" name="image_link"
                                            value="{{ asset($attivita->link_2) }}">
                                        <label for="linkedin-image-{{ $attivita->id }}">Locandina del meeting</label><br>
                                        <img src="{{ asset($attivita->link_2) }}" class="img-thumbnail" alt="">
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Invia la richiesta di pubblicazione</button>
                    <a href="{{ route('casiclinici') }}" class="btn btn-danger">Annulla la pubblicazione</a>
                </div>
            </form>
        </div>
    </div>
@endsection
