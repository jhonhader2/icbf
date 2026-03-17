<?php

namespace App\Imports;

use App\Models\Cpu;
use App\Models\TvmAsset;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

/**
 * Importa CSV de Windows Defender TVM (export-tvm-vulnerability-exposed-assets).
 * Clave: Name (CSV) ↔ nombre_maquina (cpus).
 */
class TvmAssetsImport
{
    /** @return array{rows: int, linked: int} */
    public function import(UploadedFile $file): array
    {
        $rows = $this->parseCsvFile($file->getRealPath());
        $cpus = $this->buildCpusLookup($rows);
        $linked = $this->persistRows($rows, $cpus);

        return ['rows' => count($rows), 'linked' => $linked];
    }

    /**
     * @return array<int, array{name: string, dns_name: string|null, last_seen: Carbon|null, operating_system: string|null}>
     */
    private function parseCsvFile(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new TvmImportException('No se pudo abrir el archivo CSV.');
        }
        try {
            $this->skipMetadataAndValidateHeaders($handle);
            return $this->readDataRows($handle);
        } finally {
            fclose($handle);
        }
    }

    /** @param resource $handle */
    private function skipMetadataAndValidateHeaders($handle): void
    {
        fgetcsv($handle);
        $headers = fgetcsv($handle);
        if (! is_array($headers) || count($headers) < 4) {
            throw new TvmImportException('Encabezados CSV inesperados. Se esperan al menos: Name, DNS Name, Last seen, Operating System.');
        }
    }

    /**
     * @param resource $handle
     * @return array<int, array{name: string, dns_name: string|null, last_seen: Carbon|null, operating_system: string|null}>
     */
    private function readDataRows($handle): array
    {
        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $row = $this->parseRow($data);
            if ($row !== null) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * @param array<int, mixed> $data
     * @return array{name: string, dns_name: string|null, last_seen: Carbon|null, operating_system: string|null}|null
     */
    private function parseRow(array $data): ?array
    {
        if ($data === [null] || $data === []) {
            return null;
        }
        $name = trim((string) ($data[0] ?? ''));
        if ($name === '') {
            return null;
        }

        $dnsName = trim((string) ($data[1] ?? ''));
        $lastSeenRaw = trim((string) ($data[2] ?? ''));
        $os = trim((string) ($data[3] ?? ''));

        return [
            'name' => $name,
            'dns_name' => $dnsName !== '' ? $dnsName : null,
            'last_seen' => $this->parseLastSeen($lastSeenRaw),
            'operating_system' => $os !== '' ? $os : null,
        ];
    }

    private function parseLastSeen(string $raw): ?Carbon
    {
        if ($raw === '') {
            return null;
        }
        try {
            return Carbon::createFromFormat('d M, Y h:i:s A', $raw, 'UTC');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param array<int, array{name: string, ...}> $rows
     * @return Collection<string, Cpu>
     */
    private function buildCpusLookup(array $rows): Collection
    {
        $namesNormalized = collect($rows)->pluck('name')->filter()->unique()->map(fn(string $n) => mb_strtolower(trim($n)))->values()->all();

        return Cpu::query()
            ->whereNotNull('nombre_maquina')
            ->get()
            ->filter(fn(Cpu $c) => in_array(mb_strtolower(trim((string) $c->nombre_maquina)), $namesNormalized, true))
            ->keyBy(fn(Cpu $c) => mb_strtolower(trim((string) $c->nombre_maquina)));
    }

    /**
     * @param array<int, array{name: string, dns_name: string|null, last_seen: Carbon|null, operating_system: string|null}> $rows
     * @param Collection<string, Cpu> $cpus
     */
    private function persistRows(array $rows, Collection $cpus): int
    {
        $linked = 0;
        DB::transaction(function () use ($rows, $cpus, &$linked) {
            foreach ($rows as $row) {
                $key = mb_strtolower(trim($row['name']));
                $cpu = $cpus->get($key);
                if ($cpu !== null) {
                    $linked++;
                }
                TvmAsset::query()->create([
                    'cpu_id' => $cpu?->id,
                    'name' => $row['name'],
                    'dns_name' => $row['dns_name'],
                    'last_seen' => $row['last_seen'],
                    'operating_system' => $row['operating_system'],
                    'state' => TvmAsset::STATE_OPEN,
                ]);
            }
        });
        return $linked;
    }
}
