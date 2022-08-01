<?php

namespace Neurohub\Apilinkedin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Neurohub\Apilinkedin\Classes\LinkedinHelper;

use function PHPSTORM_META\registerArgumentsSet;

class LinkedinShareController extends Controller
{
    private $structureRequestSend;
    public function __construct()
    {
        $this->structureRequestSend = array();
    }
    public function getProfileId(Request $request)
    {
        session(['attivitaId' => $request->id_attivita]);
        $attivita = \App\Attivita::find($request->attivita);
        if ($attivita->id_categoria == 3) {
            return redirect()->route('post.linkedin.store', ['category_id' => 3]);
        }
        \Log::info('https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . config('linkedinsharecontent.client_id') . '&redirect_uri=' . config('linkedinsharecontent.redirect_uri') . '&scope=' . config('linkedinsharecontent.scopes'));
        return redirect('https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . config('linkedinsharecontent.client_id') . '&redirect_uri=' . config('linkedinsharecontent.redirect_uri') . '&scope=' . config('linkedinsharecontent.scopes'));
    }
    public function index(Request $request)
    {
        if ($request->has('code') or $request->has('category_id')) {
            if (session('attivitaId') and !$request->has('category_id')) {
                $attivita = \App\Attivita::find(session('attivitaId'));
                $allegati = DB::table('allegati')->where('id_attivita', session('attivitaId'))->whereIn('tipo_file', ['jpeg', 'jpg', 'png'])->get();
                $accessCode = LinkedinHelper::accessToken($request->code);
                $dataProfile = LinkedinHelper::profileId($accessCode);
                return view('share_post::share_post', [
                    'attivita' => $attivita,
                    'allegati' => $allegati,
                    'profile_id' => $dataProfile['id'],
                    'profile_name' => $dataProfile['name'],
                    'meeting' => false
                ]);
            } else if (session('attivitaId') and $request->has('category_id')) {
                $attivita = \App\Attivita::find(session('attivitaId'));
                return view('share_post::share_post', ['attivita' => $attivita, 'meeting' => true]);
            }
            \Log::info('ERRORE SESSIONE PER PUBBLICARE POST LINKEDIN NON E STATO TROVATO L\'ID ATTIVTA');
        }
        return redirect()->route('post.index')->with('error', 'Non sono riuscito a collegarmi a linkedin');
    }

    public function store(Request $request)
    {
        if (!$request->has('post_text') or empty($request->post_text)) {
            return redirect()->route('post.linkedin.getprofile', ['id' => $request->id])->with('error', 'Non sono stati inseriti tutti i dati necessari');
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
            switch ($attivita->id_categoria) {
                case 1:
                    return redirect()->route('post.index')->with('status', 'Richiesta di pubblicazione inviata correttamente! Ti aggiorneremo appena verrà pubblicata');
                    break;
                case 2:
                    return redirect()->route('biblioteca.index')->with('status', 'Richiesta di pubblicazione inviata correttamente! Ti aggiorneremo appena verrà pubblicata');
                    break;
                case 3:
                    return redirect()->route('zoom.index')->with('status', 'Richiesta di pubblicazione inviata correttamente! Ti aggiorneremo appena verrà pubblicata');
                    break;
                case 4:
                    return redirect()->route('presentazioni.index')->with('status', 'Richiesta di pubblicazione inviata correttamente! Ti aggiorneremo appena verrà pubblicata');
                    break;
                case 5:
                    return redirect()->route('blog.index')->with('status', 'Richiesta di pubblicazione inviata correttamente! Ti aggiorneremo appena verrà pubblicata');
                    break;
            }
        }
        return redirect()->route('post.linkedin.getprofile', ['id' => $request->id])->with('error', 'Si è verificato un problema nella condivisione della richiesta contattare gli amministratori');
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
                'profile_id' => $request['profile_id'] ?? '',
                'profile_name' => $request['profile_name'] ?? ''
            ],
            'allegato_id' => $allegato['id_allegato'],
        ];
    }
}
