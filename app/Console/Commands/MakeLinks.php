<?php

namespace App\Console\Commands;
use League\Flysystem\Exception;
use Psy\Exception\ErrorException;
use Storage;

use App\Services\WargameService;
use Illuminate\Console\Command;
use Wargame\WargameServiceProvider;

class MakeLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:links {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link to battle maps.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    const targetDir = "battle-mapz";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $force = $this->option('force');
        $mapDirs = WargameService::getBattleMaps();
        foreach($mapDirs as $mapDir){
            $srcFiles = scandir($mapDir);

            foreach($srcFiles as $srcFile){
                if($srcFile === "." || $srcFile === ".." || preg_match("/^\./",$srcFile)){
                    continue;
                }
                $publicPath = public_path(self::targetDir) . "/$srcFile";
                if(file_exists($publicPath)){
                    if($force){
                        unlink($publicPath);
                    }else{
                        continue;
                    }
                }
                $this->info( "$mapDir/".$srcFile);
                symlink("$mapDir/" . $srcFile, public_path(self::targetDir) . "/$srcFile");
            }
        }
    }
}
