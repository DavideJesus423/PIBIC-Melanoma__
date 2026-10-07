<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalysisController extends Controller
{
    private function sendIa($imageFile)
    {
        $iaUrl = env('IA_SERVICE_URL', 'http://ia_service:8000');

        $response = Http::attach(
            'imagem',
            file_get_contents($imageFile->getRealPath()),
            $imageFile->getClientOriginalName()
        )->post("{$iaUrl}/analisar");

        if ($response->failed()) {
            throw new \Exception('Erro ao conectar com o serviço de IA.');
        }

        return $response->json();
    }

    private function saveBase64Image($imageFile)
    {
        $base64Image = base64_encode(file_get_contents($imageFile));
        $mime = $imageFile->getMimeType();
        return "data:$mime;base64,$base64Image";
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        $imageFile = $request->file('image');

        $result = $this->sendIa($imageFile);

        $result['image_path'] = $this->saveBase64Image($imageFile);

        if (Auth::check()) {
            Diagnostic::create([
                'user_id'    => Auth::id(),
                'result'     => $result['resultado'],
                'confianca'  => $result['confianca'],
                'image_path' => $imageFile->store('', 's3'),
            ]);
        }

        return view('diagnostics.result', compact('result'));
    }
}