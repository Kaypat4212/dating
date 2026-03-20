<?php

namespace Database\Seeders;

use App\Models\CryptoWallet;
use Illuminate\Database\Seeder;

class CryptoWalletSeeder extends Seeder
{
    public function run(): void
    {
        $wallets = [
            [
                'currency'   => 'BTC',
                'network'    => 'Bitcoin Mainnet',
                'address'    => '1A1zP1eP5QGefi2DMPTfTL5SLmv7Divfna',
                'label'      => 'Bitcoin',
                'is_active'  => true,
                'sort_order' => 1,
            ],
            [
                'currency'   => 'ETH',
                'network'    => 'Ethereum (ERC-20)',
                'address'    => '0x742d35Cc6634C0532925a3b8D4C9B785f4A5D7bE',
                'label'      => 'Ethereum',
                'is_active'  => true,
                'sort_order' => 2,
            ],
            [
                'currency'   => 'USDT',
                'network'    => 'Tron (TRC-20)',
                'address'    => 'TLbwK7A8ZXrUMX3SZ9r8JkExampleTRC20',
                'label'      => 'Tether USD (TRC-20)',
                'is_active'  => true,
                'sort_order' => 3,
            ],
            [
                'currency'   => 'USDC',
                'network'    => 'Ethereum (ERC-20)',
                'address'    => '0x952b47F27313FdD5fEbFf8C9a1d0C5B785F9A4C2',
                'label'      => 'USD Coin',
                'is_active'  => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($wallets as $wallet) {
            CryptoWallet::firstOrCreate(
                ['currency' => $wallet['currency'], 'network' => $wallet['network']],
                $wallet
            );
        }
    }
}
