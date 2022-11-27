<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\User;
use App\Services\DesoService;
use Illuminate\Console\Command;

class ParseDesoBlockchain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:deso
                            {--count : maximum number of blocks to parse}
                            {--missed : Check for missing blocks}
                            {--refresh : Delete all blocks and users and generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $desoService;

    protected const CHECK_MISSING_BLOCKS_RANGE = 5000;

    protected const CONCURRENCY = 200;

    protected const MAX_BLOCK_PARSE = 100000;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('refresh')) {
            Block::truncate();
            User::truncate();
        }

        $this->desoService = app(DesoService::class);

        $lastParsedBlock = Block::latest('Height')->first();

        $startHeightToParse = $lastParsedBlock ? $lastParsedBlock->Height : 0;

        $heightList = $this->getMissedBlocks(
            $this->option('missed') ? 1 : $startHeightToParse - self::CHECK_MISSING_BLOCKS_RANGE,
            $startHeightToParse
        );

        for ($i = 0; $i < self::MAX_BLOCK_PARSE; $i++) {
            $heightList[] = ++$startHeightToParse;

            if (count($heightList) >= self::CONCURRENCY) {
                $timeStart = microtime(true);

                $this->desoService->blockInfoByHeightAsync(
                    $heightList,
                    self::CONCURRENCY,
                    function ($block) {
                        $this->parseBlock($block);
                    }
                );

                $timeEnd = microtime(true);

                $this->info('Blocks ' . array_shift($heightList) . '-' . end($heightList) . ' parsed. Time: ' . ($timeEnd - $timeStart) . ' seconds');

                $heightList = [];
            }
        }

        return self::SUCCESS;
    }

    protected function parseBlock($block)
    {
        $users = [];

        foreach ($block['Transactions'] as $transaction) {
            $userUpdateData = $this->parseUserByTransaction($transaction, $block);

            if ($userUpdateData) {
                $users[$userUpdateData['KeyBase58']] = $userUpdateData;
            }
        }

        User::insert($users);

        Block::create([
            'Height' => $block['Header']['Height'],
            'BlockHashHex' => $block['Header']['BlockHashHex'],
            'TransactionMerkleRootHex' => $block['Header']['TransactionMerkleRootHex'],
            'TstampSecs' => $block['Header']['TstampSecs']
        ]);
    }

    protected function parseUserByTransaction($transaction, $block)
    {
        if (!isset($transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'])) {
            return null;
        }

        if (!isset($transaction['TransactionMetadata']['UpdateProfileTxindexMetadata']['NewUsername'])) {
            return null;
        }

        $userPublicKey = $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'];

        if (User::where('KeyBase58', $userPublicKey)->exists()) {

            User::where('KeyBase58', $userPublicKey)->update([
                'Username' => $transaction['TransactionMetadata']['UpdateProfileTxindexMetadata']['NewUsername']
            ]);

            return null;
        }


        return [
            'KeyBase58' => $userPublicKey,
            'Username' => $transaction['TransactionMetadata']['UpdateProfileTxindexMetadata']['NewUsername']
        ];
    }

    protected function getMissedBlocks($startBlock, $endBlock)
    {
        if ($startBlock < 0) {
            $startBlock = 1;
        }

        if ($endBlock <= $startBlock) {
            return [];
        }

        $blockIds = Block::select('Height')
            ->where('Height', '>=', $startBlock)
            ->where('Height', '<=', $endBlock)
            ->get()
            ->pluck('Height')
            ->toArray();

        $blockRange = range($startBlock, $endBlock);

        return array_diff($blockRange, $blockIds);
    }
}
