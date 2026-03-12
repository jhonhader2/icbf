<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Office;
use App\Models\Persona;
use App\Models\Regional;
use App\Models\Title;
use App\Support\StringNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Importa personas desde All Users.xlsx (hoja "All Users", encabezados fila 7).
 * documento_identidad = Employee ID (col B), account_status (col D), Department (col F) → department_id 3FN.
 */
class AllUsersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function headingRow(): int
    {
        return 7;
    }

    public function model(array $row): ?Persona
    {
        $doc = trim((string) ($row['employee_id'] ?? ''));
        if ($doc === '') {
            return null;
        }
        $doc = StringNormalizer::normalize($doc);
        $nombre = StringNormalizer::normalize((string) ($row['full_name'] ?? ''));
        $accountStatus = StringNormalizer::normalize((string) ($row['account_status'] ?? ''));
        $deptNombre = StringNormalizer::normalize((string) ($row['department'] ?? ''));

        $departmentId = null;
        if ($deptNombre !== '') {
            $departmentId = Department::firstOrCreate(
                ['nombre' => $deptNombre],
                ['nombre' => $deptNombre]
            )->id;
        }

        $accountStatusVal = null;
        if ($accountStatus !== '') {
            $accountStatusVal = in_array(strtolower($accountStatus), ['1', 'active', 'activo', 'enabled', 'si', 'yes'], true) ? '1' : '0';
        }

        $titleNombre = StringNormalizer::normalize((string) ($row['title'] ?? ''));
        $titleId = $titleNombre !== '' ? Title::firstOrCreate(['nombre' => $titleNombre], ['nombre' => $titleNombre])->id : null;

        $officeNombre = StringNormalizer::normalize((string) ($row['office'] ?? ''));
        $officeId = $officeNombre !== '' ? Office::firstOrCreate(['nombre' => $officeNombre], ['nombre' => $officeNombre])->id : null;

        $regionalId = $this->resolveRegionalIdFromRow($row);

        $attrs = [
            'nombre' => $nombre ?: null,
            'department_id' => $departmentId,
            'regional_id' => $regionalId,
            'account_status' => $accountStatusVal,
            'employee_id' => $doc,
            'full_name' => $nombre ?: null,
            'email_address' => StringNormalizer::normalize((string) ($row['email_address'] ?? '')) ?: null,
            'office_id' => $officeId,
            'title_id' => $titleId,
            'description' => StringNormalizer::normalize((string) ($row['description'] ?? '')) ?: null,
            'sam_account_name' => StringNormalizer::normalize((string) ($row['sam_account_name'] ?? '')) ?: null,
        ];
        return Persona::query()->updateOrCreate(
            ['documento_identidad' => $doc],
            $attrs
        );
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['nullable', 'string'],
        ];
    }

    /**
     * Resuelve regional_id a partir del campo OU Name en la fila.
     * Crea la regional en tabla regionales si no existe.
     */
    private function resolveRegionalIdFromRow(array $row): ?int
    {
        $ouName = trim((string) ($row['ou_name'] ?? $row['ou name'] ?? ''));
        if ($ouName === '') {
            return null;
        }
        $regionalNombre = $this->extractRegionalFromOuName($ouName);
        if ($regionalNombre === null || $regionalNombre === '') {
            return null;
        }
        $regional = Regional::firstOrCreate(
            ['nombre' => $regionalNombre],
            ['nombre' => $regionalNombre]
        );

        return $regional->id;
    }

    /**
     * Extrae la regional del campo OU Name: texto entre el primer y el segundo "/".
     * Ejemplo: "/Regional Antioquia/" → "Regional Antioquia".
     */
    private function extractRegionalFromOuName(string $ouName): ?string
    {
        $parts = explode('/', trim($ouName), 3);
        $segment = isset($parts[1]) ? trim($parts[1]) : '';

        return $segment !== '' ? $segment : null;
    }
}
