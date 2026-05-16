<?php
namespace Database\Seeders;
use App\Models\FieldUnit;
use App\Models\Vendor;
use App\Models\RabProposal;
use App\Models\RabItem;
use App\Models\VirtualAccount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RABPaySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@rabpay.test'],
            [
                'name' => 'Admin RABPay',
                'password' => Hash::make('password'),
            ]
        );

        $kemahasiswaan = FieldUnit::create([
            'code' => 'BDG001',
            'name' => 'Bidang Kemahasiswaan',
            'rfid_uid' => null,
            'pic_name' => 'Dedy',
            'status' => 'active',
        ]);

        $akademik = FieldUnit::create([
            'code' => 'BDG002',
            'name' => 'Bidang Akademik',
            'rfid_uid' => null,
            'pic_name' => 'Agus',
            'status' => 'active',
        ]);

        Vendor::create([
            'code' => 'VND001',
            'name' => 'Toko ATK Mitra',
            'category' => 'ATK',
            'qr_code' => 'VND001',
            'status' => 'active',
        ]);

        Vendor::create([
            'code' => 'VND002',
            'name' => 'Warung Konsumsi Bali',
            'category' => 'Konsumsi',
            'qr_code' => 'VND002',
            'status' => 'active',
        ]);

        $rab = RabProposal::create([
            'field_unit_id' => $kemahasiswaan->id,
            'title' => 'RAB Kegiatan Seminar Kampus',
            'description' => 'RAB untuk kegiatan seminar mahasiswa.',
            'status' => 'approved',
            'created_by' => $user->id,
        ]);

        RabItem::create([
            'rab_proposal_id' => $rab->id,
            'item_name' => 'Kertas A4',
            'category' => 'ATK',
            'quantity' => 1,
            'unit_price' => 100000,
            'total_price' => 100000,
        ]);

        RabItem::create([
            'rab_proposal_id' => $rab->id,
            'item_name' => 'Konsumsi Peserta',
            'category' => 'Konsumsi',
            'quantity' => 1,
            'unit_price' => 500000,
            'total_price' => 500000,
        ]);

        $rab->update([
            'total_budget' => 600000,
        ]);

        VirtualAccount::create([
            'field_unit_id' => $kemahasiswaan->id,
            'initial_balance' => 600000,
            'current_balance' => 600000,
            'status' => 'active',
        ]);

        VirtualAccount::create([
            'field_unit_id' => $akademik->id,
            'initial_balance' => 0,
            'current_balance' => 0,
            'status' => 'active',
        ]);
    }
}
