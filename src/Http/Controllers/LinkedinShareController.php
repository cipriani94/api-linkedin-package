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
        $attivita = \App\Attivita::find($request['id']);

        $allegato = null;
        if ($request->has('image')) {
            $allegato = $attivita->allegati->where('id', $request['image'])->first();
        }
        $this->setRequestBeforeSend(
            request()->all(),
            [
                'username' => $attivita->user->nome . ' ' . $attivita->user->cognome,
                'email' => $attivita->user->email
            ],
            [
                'id_allegato' => $allegato->id ?? 0,
                'allegato' => $allegato->public_link ?? ''
            ]
        );
        $client = new Client(['base_uri' => config('apiservice.url')]);
        $response = $client->request('POST', '/api/createRequestShare', [
            'headers' => [
                "Authorization" => config('apiservice.api_md5'),
                "Content-Type"  => "application/json",
                '_token' => csrf_token(),
                "sourcehosting" => config('app.url')
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
                'username' => $user['username'],
                'email' => $user['email'],
                'allegato' => $allegato['allegato'],
                'user_link' => $request['link_profile']
            ],
            'allegato_id' => $allegato['id_allegato'],
        ];
    }
}
