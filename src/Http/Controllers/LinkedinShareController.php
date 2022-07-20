<?php

namespace Neurohub\Apilinkedin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LinkedinShareController extends Controller
{
    private $structureRequestSend;
    public function __construct()
    {
        $this->structureRequestSend = array();
    }
    public function index($id)
    {
        $attivita = DB::table('attivita')->where('id', $id)->first();
        $allegati = DB::table('allegati')->where('id_attivita', $id)->whereIn('tipo_file', ['jpeg', 'jpg', 'png'])->get();
        return view('share_post::share_post', ['attivita' => $attivita, 'allegati' => $allegati]);
    }

    public function store(Request $request)
    {
        if (!$request->has('post_text') or !$request->has('link_profile') or empty($request->post_text) or empty($request->link_profile)) {
            return redirect()->route('post.linkedin', ['id' => $request->id])->with('error', 'Non sono stati inseriti tutti i dati necessari');
        }

        if (!auth()->check()) {
            abort(403, 'Non sei ');
        }
        $attivita = DB::table('attivita')
            ->select('users.nome', 'users.cognome', 'users.email', 'allegati.id as id_allegato', 'allegati.path')
            ->join('users', 'users.id', '=', 'attivita.id_utente')
            ->where('attivita.id', request('id'))->first();
        $allegato = null;
        if (request()->has('image')) {
            $allegato = DB::table('allegati')->where('id', request('image'))->first();
        }
        $this->setRequestBeforeSend(
            request()->all(),
            [
                'userName' => $attivita->nome . ' ' . $attivita->cognome,
                'email' => $attivita->email
            ],
            [
                'id_allegato' => $allegato->id ?? 0,
                'allegato' => is_null($allegato) ? ''  : str_replace('public', 'storage', $allegato->path)
            ]
        );
        $client = new Client(['base_uri' => config('apiservice.url')]);
        $response = $client->request('POST', '/oauth/v2/accessToken', [
            'headers' => [
                "Authorization" => config('apiservice.api_md5'),
                "Content-Type"  => "application/json",
            ],
            'body' => json_encode($this->structureRequestSend, true),
        ]);
        if (json_decode($response->getBody()->getContents(), true) == 'OK') {
            return redirect()->route('post.index')->with('status', 'Richiesta di pubblicazione inviata correttamente! Ti aggiorneremo appena verrà pubblicata');
        }
        return redirect()->route('post.linkedin', ['id' => $request->id])->with('error', 'Si è verificato un problema nella condivisione della richiesta contattare gli amministratori');
    }

    private function setRequestBeforeSend(array $request, array $user, array $allegato): void
    {
        $this->structureRequestSend = [
            'attivita_id' => $request['id'],
            'extra' => [
                'name' => $request['post_text'],
                'userName' => $user['userName'],
                'email' => $user['email'],
                'allegato' => $allegato['allegato'],
                'extra.userLink' => $request['link_profile']
            ],
            'id_allegato' => $allegato['id_allegato'],
        ];
    }
}
