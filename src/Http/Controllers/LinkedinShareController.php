<?php

namespace Neurohub\Apilinkedin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Neurohub\Apilinkedin\Classes\LinkedinHelper;

class LinkedinShareController extends Controller
{
    private $structureRequestSend;
    public function __construct()
    {
        $this->structureRequestSend = array();
    }
    public function getProfileId($id)
    {
        session(['attivitaId' => $id]);
        \Log::info('https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . config('linkedinsharecontent.client_id') . '&redirect_uri=' . config('linkedinsharecontent.redirect_uri') . '&scope=' . config('linkedinsharecontent.scopes'));
        return redirect('https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . config('linkedinsharecontent.client_id') . '&redirect_uri=' . config('linkedinsharecontent.redirect_uri') . '&scope=' . config('linkedinsharecontent.scopes'));
    }
    public function index(Request $request)
    {
        if ($request->has('code')) {
            $attivita = DB::table('attivita')->where('id', session('attivitaId'))->first();
            $allegati = DB::table('allegati')->where('id_attivita', session('attivitaId'))->whereIn('tipo_file', ['jpeg', 'jpg', 'png'])->get();
            $accessCode = LinkedinHelper::accessToken($request->code);
            $dataProfile = LinkedinHelper::profileId($accessCode);
            return view('share_post::share_post', ['attivita' => $attivita, 'allegati' => $allegati, 'profile_id' => $dataProfile['id'], 'profile_name' => $dataProfile['name']]);
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
            return redirect()->route('post.index')->with('status', 'Richiesta di pubblicazione inviata correttamente! Ti aggiorneremo appena verrà pubblicata');
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
                'profile_id' => $request['profile_id'],
                'profile_name' => $request['profile_name']
            ],
            'allegato_id' => $allegato['id_allegato'],
        ];
    }
}
