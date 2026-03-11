<?php

namespace App\Domains\Endereco\Controllers;

use App\Domains\Endereco\Models\Endereco;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicEnderecoController extends Controller
{
    public function index(): JsonResponse
    {
        $enderecos = Endereco::where('user_id', auth()->id())
            ->orderByDesc('principal')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($enderecos);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'apelido' => 'nullable|string|max:50',
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:150',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $data['user_id'] = auth()->id();

        $hasEnderecos = Endereco::where('user_id', auth()->id())->exists();
        $data['principal'] = !$hasEnderecos;

        $endereco = Endereco::create($data);

        return response()->json($endereco, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $endereco = Endereco::where('user_id', auth()->id())->findOrFail($id);

        $data = $request->validate([
            'apelido' => 'nullable|string|max:50',
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:150',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'principal' => 'nullable|boolean',
        ]);

        if (!empty($data['principal']) && $data['principal']) {
            Endereco::where('user_id', auth()->id())
                ->where('id', '!=', $id)
                ->update(['principal' => false]);
        }

        $endereco->update($data);

        return response()->json($endereco);
    }

    public function destroy(string $id): JsonResponse
    {
        $endereco = Endereco::where('user_id', auth()->id())->findOrFail($id);
        $wasPrincipal = $endereco->principal;
        $endereco->delete();

        if ($wasPrincipal) {
            $next = Endereco::where('user_id', auth()->id())->first();
            $next?->update(['principal' => true]);
        }

        return response()->json(null, 204);
    }
}
