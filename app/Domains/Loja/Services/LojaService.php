<?php

namespace App\Domains\Loja\Services;

use App\Domains\Loja\Models\Loja;
use App\Domains\Shared\Services\BaseService;

class LojaService extends BaseService
{
    public function __construct(private readonly Loja $loja)
    {
        $this->setModel($this->loja);
    }

    public function show(string $id)
    {
        return parent::show($id)->load(['horarios']);
    }

    /**
     * Create a store with horarios.
     */
    public function createWithHorarios(array $data)
    {
        \DB::beginTransaction();
        try {
            // Extract horarios from data
            $horarios = $data['horarios'] ?? [];
            unset($data['horarios']);

            // Create the store
            $loja = $this->loja::create($data);

            // Create horarios if provided
            if (! empty($horarios)) {
                foreach ($horarios as $horario) {
                    $loja->horarios()->create($horario);
                }
            }

            \DB::commit();

            return $loja->load('horarios');

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update a store with horarios.
     */
    public function updateWithHorarios($id, array $data)
    {
        \DB::beginTransaction();
        try {
            $loja = $this->loja::findOrFail($id);

            $user = auth()->user();
            if ($user && isset($user->role['slug']) && $user->role['slug'] === 'logista') {
                if ($user->loja_id !== $loja->id) {
                    throw new \Exception('Você não tem permissão para editar esta loja.', 403);
                }
            }

            // Extract horarios from data
            $horarios = $data['horarios'] ?? null;
            unset($data['horarios']);

            // Update the store
            $loja->update($data);

            // Update horarios if provided
            if ($horarios !== null) {
                // Delete existing horarios and create new ones
                $loja->horarios()->delete();

                foreach ($horarios as $horario) {
                    $loja->horarios()->create($horario);
                }
            }

            \DB::commit();

            return $loja->load('horarios');

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
