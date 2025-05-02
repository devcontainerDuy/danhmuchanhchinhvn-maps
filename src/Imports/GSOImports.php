<?php

namespace KhanhDuy\DanhmuchanhchinhvnMaps\Imports;

use KhanhDuy\DanhmuchanhchinhvnMaps\Models\District;
use KhanhDuy\DanhmuchanhchinhvnMaps\Models\Province;
use KhanhDuy\DanhmuchanhchinhvnMaps\Models\Ward;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class GSOImports implements ToArray, WithChunkReading, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    protected array $districtMap = [];
    protected array $provinceMap = [];
    protected array $wardMap = [];
    protected $table;
    protected $columns;

    public function __construct()
    {
        $this->table = config('gso.tables');
        $this->columns = config('gso.columns');

        $this->loadProvinceMap();
        $this->loadDistrictMap();
        $this->loadWardMap();
    }


    public function onFailure( ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning("Import failure at row {$failure->row()}: " . implode(', ', $failure->errors()));
        }
    }

    public function array(array $rows): void
    {
        $wardsToInsert = [];

        foreach ($rows as $row) {
            if (!empty($row['ma_tp']) && !empty($row['ma_qh']) && !empty($row['ma_px'])) {
                if (isset($this->wardMap[$row['ma_px']])) {
                    // Update existing ward
                    Ward::where($this->columns['gso_id'], $row['ma_px'])
                        ->update([$this->columns['name'] => $row['phuong_xa']]);
                } else {
                    $districtId = $this->getDistrictId($row);

                    $wardsToInsert[] = [
                        $this->columns['name'] => $row['phuong_xa'],
                        $this->columns['gso_id'] => $row['ma_px'],
                        $this->columns['district_id'] => $districtId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($wardsToInsert)) {
            try {
                Ward::insert($wardsToInsert);
            } catch (\Exception $e) {
                Log::error('VietnamZoneImport error during ward insert: ' . $e->getMessage());
            }
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function getProvinceId(array $row): int
    {
        if (isset($this->provinceMap[$row['ma_tp']])) {
            return $this->provinceMap[$row['ma_tp']];
        }

        return $this->createProvince($row);
    }

    private function getDistrictId(array $row): int
    {
        if (isset($this->districtMap[$row['ma_qh']])) {
            return $this->districtMap[$row['ma_qh']];
        }

        return $this->createDistrict($row);
    }

    private function createProvince(array $row): int
    {
        $province = Province::create([
            $this->columns['name'] => $row['tinh_thanh_pho'],
            $this->columns['gso_id'] => $row['ma_tp'],
        ]);

        $this->provinceMap[$row['ma_tp']] = $province->id;

        return $province->id;
    }

    private function createDistrict(array $row): int
    {
        $provinceId = $this->getProvinceId($row);

        $district = District::create([
            $this->columns['name'] => $row['quan_huyen'],
            $this->columns['gso_id'] => $row['ma_qh'],
            $this->columns['province_id'] => $provinceId,
        ]);

        $this->districtMap[$row['ma_qh']] = $district->id;

        return $district->id;
    }

    private function loadProvinceMap(): void
    {
        $this->provinceMap = Province::pluck('id', $this->columns['gso_id'])->toArray();
    }

    private function loadDistrictMap(): void
    {
        $this->districtMap = District::pluck('id', $this->columns['gso_id'])->toArray();
    }

    private function loadWardMap(): void
    {
        $this->wardMap = Ward::pluck('id', $this->columns['gso_id'])->toArray();
    }
}
