<?php

namespace App\Http\Controllers;

use App\Models\TvmAsset;
use Illuminate\Http\RedirectResponse;

class TvmAssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tvm.resolve');
    }

    /**
     * Marca una ocurrencia TVM como resuelta. Comprueba que el CPU pertenezca a la regional del usuario.
     */
    public function resolve(TvmAsset $tvmAsset): RedirectResponse
    {
        $tvmAsset->load('cpu.activoCrv');
        $cpu = $tvmAsset->cpu;
        if (! $cpu) {
            abort(404);
        }
        $rid = $this->userRegionalId();
        if ($rid !== null && (int) ($cpu->activoCrv?->regional_id) !== $rid) {
            abort(404);
        }
        if (! $tvmAsset->isOpen()) {
            return redirect()->route('cpus.show', $cpu)->with('info', __('Este registro ya estaba marcado como resuelto.'));
        }
        $tvmAsset->markResolved();

        return redirect()->route('cpus.show', $cpu)->with('success', __('Registro TVM marcado como resuelto.'));
    }
}
