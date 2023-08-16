<?php

namespace HamCQ\AuthPhone\Console;
use Illuminate\Console\Command;

use HamCQ\AuthPhone\KeyDisk;

use Symfony\Component\Console\Output\NullOutput;

class BuildKeyCommand extends Command 
{
    protected $signature = 'hamcq:aesKey:build';
    protected $description = 'Generate aes key to disk.';

    public function handle()
    {
        $output = $this->getOutput()->getOutput();
        if (!$output) {
            $output = new NullOutput();
        }
        $disk = resolve(KeyDisk::class);
        if( !$disk->exists() ){
            $disk->store();
            $output->writeln('Completed');
            return;
        }
        $output->writeln('Already exists');
    }
}