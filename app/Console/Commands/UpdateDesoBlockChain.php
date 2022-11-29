<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateDesoBlockChain extends ParseDesoBlockchain
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:deso
                            {--count : maximum number of blocks to parse}
                            {--missed : Check for missing blocks}
                            {--refresh : Delete all blocks and users and generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating DeSo blockchain data';

    protected const CHECK_MISSING_BLOCKS_RANGE = 50;

    protected const CONCURRENCY = 4;

    protected const MAX_BLOCK_PARSE = 10;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        parent::handle();
    }
}
