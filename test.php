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
    protected $signature = 'module:create {folder} {name}';

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
      $name = $this->argument('name');
      $folder = $this->argument('folder');

      $this->editGeneralComposer();
      $this->createFolders();
      $this->createComposer();
      $this->createRoutes();
      $this->createController();
      $this->createProvider();

      $this->info('Copia "'.$folder.stripcslashes("\\").$name.stripcslashes("\\").$name.'ServiceProvider::class," en config/app.php');
      $this->info('Corre "composer dump-autoload"');
    }

    public function editGeneralComposer(){
      $name = $this->argument('name');
      $folder = $this->argument('folder');

      $jsonString = file_get_contents(base_path().'/composer.json');
      $data = json_decode($jsonString, true);
      $data['autoload']['psr-4'][$folder.'\\'.$name.'\\'] = $folder.'/'.$name.'/src/';
      $newJsonString = json_encode($data,JSON_PRETTY_PRINT);
      file_put_contents(base_path().'/composer.json', $newJsonString);

      return true;
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
          $this->error('   Module all ready exist in '.$this->argument('folder').'!   ');
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

        $response['extra'] = (object)[
          "laravel" => (object) [
            "providers" => [
              "$folder\\$name"
            ]
          ]
        ];
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
        $text = "<?php\r\n\r\n";
        $text .= "Route::resource('".strtolower ($name)."','".$folder.stripcslashes("\\").$name.stripcslashes("\\").$name."Controller');";
        fwrite($fp, $text);
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
     }
   }";
      if (!file_exists(base_path().'/'.$folder.'/'.$name.'/src/'.$name.'ServiceProvider.php')){
        $fp = fopen(base_path().'/'.$folder.'/'.$name.'/src/'.$name.'ServiceProvider.php', 'w');
        fwrite($fp, $providerText);
        fclose($fp);
      }
      return true;
    }

    public function createController(){
      $name = $this->argument('name');
      $folder = $this->argument('folder');

      $text = '<?php

namespace '.$folder.stripcslashes("\\").$name.';

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class '.$name.'Controller extends Controller
{

    public function index()
    {
       dd("Soy index de '.$name.'");
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
       //
    }

    public function destroy($id)
    {
      //
    }


}
      ';
      if (!file_exists(base_path().'/'.$folder.'/'.$name.'/src/'.$name.'Controller.php')){
        $fp = fopen(base_path().'/'.$folder.'/'.$name.'/src/'.$name.'Controller.php', 'w');
        fwrite($fp, $text);
        fclose($fp);
      }
      return true;
    }
}
