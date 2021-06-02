<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:create {name} {folder=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $this->createFolders();
      $this->createComposer();
      $this->createRoutes();
      $this->createProvider();
    }

    public function createFolders(){
      $name = $this->argument('name');
      $folder = $this->argument('folder');
      if (!file_exists(base_path().'/'.$folder)) {
          mkdir(base_path().'/'.$folder, 0755, true);
      }

      if (!file_exists(base_path().'/'.$folder.'/'.$name)) {
          mkdir(base_path().'/'.$folder.'/'.$name, 0755, true);
      }else{
        //  $this->error('   Module all ready exist in '.$this->argument('folder').'!   ');
      }

      if (!file_exists(base_path().'/'.$folder.'/'.$name.'/src')) {
      mkdir(base_path().'/'.$folder.'/'.$name.'/src', 0755, true);
      }
      return true;
    }

    public function createComposer(){
      $name = $this->argument('name');
      $folder = $this->argument('folder');

      if (!file_exists(base_path().'/'.$folder.'/'.$name.'/composer.json')){
        $autoload[] = array('psr-4'=> array($folder.'\\'.$name.'\\' => 'src'));

        $response['name'] = strtolower ( $folder )."/".strtolower ( $name );
        $response['description'] = "A module for my proyect";
        $response['type'] = "library";
        $response['minimum-stability'] = "dev";
        $response['require'] = (object) array();
        $response['autoload'] = $autoload;

        $fp = fopen(base_path().'/'.$folder.'/'.$name.'/composer.json', 'w');
        fwrite($fp, json_encode($response,JSON_PRETTY_PRINT));
        fclose($fp);
      }
      return true;
    }

    public function createRoutes() {
      $name = $this->argument('name');
      $folder = $this->argument('folder');
      if (!file_exists(base_path().'/'.$folder.'/'.$name.'/src/routes.php')){
        $fp = fopen(base_path().'/'.$folder.'/'.$name.'/src/routes.php', 'w');
        fwrite($fp, "<?php\r\n\r\n");
        fclose($fp);
      }
      return true;
    }
    public function createProvider() {
      $name = $this->argument('name');
      $folder = $this->argument('folder');
      $providerText = "<?php\r\n\r\n";
      $providerText .= "namespace ".$folder.stripcslashes("\\").$name.";\r\n\r\n";
      $providerText .= "use Illuminate\Support\ServiceProvider;\r\n\r\n";
      $providerText .= "class ".$name."ServiceProvider extends ServiceProvider {\r\n\r\n";
      $providerText .= "     /**
     * Register services.
     *
     * @return void
     */
     public function register()
     {

     }

     /**
     * Bootstrap services.
     *
     * @return void
     */
     public function boot()
     {
       include __DIR__.'/routes.php';
      ";
      $providerText .= ' $this->loadViewsFrom'."(__DIR__.'/views', '".strtolower ( $name )."');
     }";
      if (!file_exists(base_path().'/'.$folder.'/'.$name.'/src/'.$name.'ServiceProvider.php')){
        $fp = fopen(base_path().'/'.$folder.'/'.$name.'/src/'.$name.'ServiceProvider.php', 'w');
        fwrite($fp, $providerText);
        fclose($fp);
      }
      return true;
    }
}
