@extends('layouts.app')

@section('style')
    <!-- <link rel="stylesheet" href="https://joshuajohnson.co.uk/Choices/assets/styles/choices.min.css?version=7.0.0"> -->
    <link rel="stylesheet" href="{{ asset('css/post.css?2') }}">
@endsection

@section('content')

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
                <h3 class="card-title">Richiesta di pubblicazione su linkedin del caso {{ $attivita->nome }} <br>
                    <small>La richiesta verrà inviata agli amministratori di neurohub che controllerrano la corretteza dei
                        dati richiesti e provvederanno alla pubblicazione del caso con il vostro tag associato al
                        post.</small>
                </h3>
            </div>
            <form action="{{ route('post.linkedin.store') }}" method="post">
                <div class="card-body">
                    @csrf
                    <input type="hidden" name="id" value="{{ $attivita->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="nome_post">Testo del post</label>
                                    <input type="text" class="form-control" id="nome_post" name="post_text"
                                        placeholder="Nome del tuo post">

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link_profile">Link del tuo profilo linkedin</label>
                                    <input type="text" class="form-control" id="link_profile" name="link_profile"
                                        placeholder="Inserisci il link del profilo">
                                    <p><small>Il link è necessario per permettere a neurohub di fare il tag</small></p>
                                </div>
                            </div>

                        </div>
                        @if (empty($allegati))
                            <p>Non ci sono immagini nel post</p>
                        @else
                            <div class="row">
                                @foreach ($allegati as $image)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="radio" id="linkedin-image-{{ $image->id }}" name="image"
                                                value="{{ $image->id }}">
                                            <label for="linkedin-image-{{ $image->id }}">{{ $image->nome }}</label><br>
                                            <img src="{{ asset(str_replace('public', 'storage', $image->path)) }}"
                                                class="img-thumbnail" alt="">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Invia la richiesta di pubblicazione</button>
                    <a href="{{ route('post.index') }}" class="btn btn-danger">Annulla la pubblicazione</a>
                </div>
            </form>
        </div>
    </div>
@endsection
